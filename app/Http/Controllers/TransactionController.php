<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BillOfMaterial;
use App\Models\JitNotification;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\MaterialConsumptionLog;

class TransactionController extends Controller
{
    public function index()
    {
        $ongoingTransactions = Transaction::where('status', 'in_progress')
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(20);

        return view('content.transaction.main.index', compact('ongoingTransactions'));
    }

    public function create()
    {
        $products = Product::where('is_active', 1)->get();
        return view('content.transaction.main.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => 'required|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'total_amount' => 'required|string',
            'payment_method' => 'required|string|in:cash,credit_card,bank_transfer,qris,debit_card',
            'payment_status' => 'required|string|in:unpaid,paid',
        ]);

        try {
            $affectedMaterials = [];

            $transaction = DB::transaction(function () use ($request, $validatedData, &$affectedMaterials) {
                
                $cleanedTotal = preg_replace('/[^\d.]/', '', $request->total_amount);

                $transaction = Transaction::create([
                    'code' => 'TRX-' . strtoupper(Str::random(6)),
                    'total_amount' => $cleanedTotal,
                    'payment_method' => $validatedData['payment_method'], 
                    'payment_status' => $validatedData['payment_status'],
                    'user_id' => auth()->id() ?? '1',
                    'customer_name' => $validatedData['customer_name'], 
                    'table_number' => $validatedData['table_number'],  
                ]);

                foreach ($request->products as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $quantitySold = (int) $item['quantity'];
                    
                    $transactionDetail = TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'quantity' => $quantitySold,
                        'total_price' => $product->selling_price * $quantitySold,
                    ]);
                    
                    $bomItems = BillOfMaterial::where('product_id', $product->id)->get();
                    
                    if ($bomItems->isEmpty()) {
                        continue; 
                    }
                    
                    foreach ($bomItems as $bomItem) {
                        // Kunci optimasi: Gunakan findOrFail untuk melempar error jika bahan baku tidak ada
                        $rawMaterial = RawMaterial::findOrFail($bomItem->raw_material_id);
                        $quantityToConsume = $bomItem->quantity * $quantitySold;

                        if ($rawMaterial->stock < $quantityToConsume) {
                            throw new \Exception("Stok tidak cukup untuk bahan baku '{$rawMaterial->name}' dalam produk '{$product->name}'.");
                        }

                        $rawMaterial->decrement('stock', $quantityToConsume);
                        
                        // 4. Tambahkan bahan baku yang terpengaruh ke dalam array
                        $affectedMaterials[$rawMaterial->id] = $rawMaterial->fresh(); // ->fresh() untuk mendapatkan data terbaru setelah decrement

                        MaterialConsumptionLog::create([
                            'raw_material_id'       => $rawMaterial->id,
                            'transaction_detail_id' => $transactionDetail->id,
                            'quantity_used'         => $quantityToConsume,
                            'consumption_date'      => now()->toDateString(),
                        ]);
                    }
                }
                
                return $transaction;
            });

            // 5. SETELAH TRANSAKSI BERHASIL, panggil fungsi untuk cek sinyal JIT
            if (!empty($affectedMaterials)) {
                $this->checkJitSignalForAffectedMaterials($affectedMaterials);
            }

            return redirect()->route('transactions.show', $transaction->id)->with('success', 'Transaksi berhasil dibuat dan stok telah diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    private function checkJitSignalForAffectedMaterials(array $materials): void
    {
        $flaskApiUrl = env('FLASK_API_URL', 'https://cafehub-forecast-api.vercel.app');

        foreach ($materials as $material) {
            try {
                $payload = [
                    'product_name' => $material->name,
                    'current_stock' => $material->stock,
                    'signal_point' => $material->signal_point,
                    'replenish_quantity' => $material->replenish_quantity,
                ];

                $response = Http::timeout(10)->post("{$flaskApiUrl}/jit-signal-event", $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['action_required']) && $data['action_required'] === 'INITIATE_JIT_REPLENISHMENT') {
                        JitNotification::firstOrCreate(
                            [
                                'raw_material_id' => $material->id,
                                'status' => 'unread',
                            ],
                            [
                                'message' => "Stok {$material->name} mencapai titik kritis ({$material->stock} / {$material->signal_point}). Segera lakukan pemesanan ulang sebanyak {$material->replenish_quantity} unit.",
                            ]
                        );
                    }
                } else {
                    Log::error("JIT API call failed for material ID {$material->id}: " . $response->body());
                }
            } catch (Exception $e) {
                Log::error("Exception during JIT API call for material ID {$material->id}: " . $e->getMessage());
            }
        }
    }
    
    
    public function show($id) 
    {
        $transaction = Transaction::with(['transactionDetail.product', 'user'])->find($id);

        if (!$transaction) {
            abort(404, 'Transaksi dengan ID ' . $id . ' tidak ditemukan.');
        }

        return view('content.transaction.main.show', compact('transaction'));
    }

    public function updateStatus(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'required|string|in:in_progress,cancelled,done',
        ]);

        $transaction->update(['status' => $validatedData['status']]);

        return redirect()->route('transactions')->with('success', 'Transaction status updated successfully.');
    }

    public function historyIndex(Request $request)
    {
        $query = Transaction::query()->where('status', '!=', 'in_progress');

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('code', 'LIKE', $searchTerm)
                  ->orWhere('customer_name', 'LIKE', $searchTerm)
                  ->orWhere('table_number', 'LIKE', $searchTerm);
            });
        }

        if ($request->filled('order_status')) {
            $query->where('status', $request->input('order_status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        $pastTransactions = $query->latest()->paginate(20)->withQueryString();

        return view('content.transaction.history.index', compact('pastTransactions'));
    }

    public function historyShow($id)
    {
        return view('content.transaction.history.show');
    }
}
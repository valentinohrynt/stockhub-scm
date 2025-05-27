<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use App\Models\BillOfMaterial;
use Illuminate\Support\Facades\DB;

class BillOfMaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->whereHas('billOfMaterial')
            ->with(['category', 'billOfMaterial' => function($q) {
                $q->where('is_active', true);
            }]);

        $currentStatusForView = $request->input('status', '1');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }
        if ($currentStatusForView !== 'all') {
            $query->where('is_active', (bool)$currentStatusForView);
        }

        $productsWithBOM = $query->latest('products.created_at')->paginate(18)->withQueryString();

        foreach ($productsWithBOM as $product) {
            $possibleUnits = PHP_INT_MAX;
            $activeBOMItems = $product->billOfMaterial->where('is_active', true);

            if ($activeBOMItems->isEmpty()) {
                $possibleUnits = 0;
            } else {
                foreach ($activeBOMItems as $bomItem) {
                    if ($bomItem->rawMaterial && $bomItem->rawMaterial->conversion_factor > 0) {
                        $availableStockInUsageUnit = ($bomItem->rawMaterial->stock ?? 0) * $bomItem->rawMaterial->conversion_factor;
                        $requiredQtyInUsageUnit = $bomItem->quantity;
                        if ($requiredQtyInUsageUnit > 0) {
                            $possibleUnits = min($possibleUnits, floor($availableStockInUsageUnit / $requiredQtyInUsageUnit));
                        } else {
                             $possibleUnits = 0;
                             break;
                        }
                    } else {
                         $possibleUnits = 0;
                         break;
                    }
                }
            }
            $product->possible_units = ($possibleUnits === PHP_INT_MAX) ? 0 : $possibleUnits;
        }
        $categories = Category::where('type', 'product')->orderBy('name')->get();
        return view('content.bill_of_material.index', [
            'productsWithBOM' => $productsWithBOM,
            'categories' => $categories,
            'currentStatus' => $currentStatusForView
        ]);
    }

    public function create(Request $request)
    {
        $products = Product::where('is_active', true)
                            ->whereDoesntHave('billOfMaterial', function($q) {
                                $q->where('is_active', true); 
                            })
                            ->orderBy('name')->get();
        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('name')->get();
        $selectedProductId = $request->query('product_id');

        return view('content.bill_of_material.create', compact('products', 'rawMaterials', 'selectedProductId'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id|unique:bill_of_materials,product_id,NULL,id,is_active,1',
            'raw_materials' => 'required|array|min:1',
            'raw_materials.*.id' => 'required|exists:raw_materials,id',
            'raw_materials.*.quantity' => 'required|numeric|gt:0',
        ], [
            'product_id.unique' => 'A Bill of Material already exists and is active for this product. Please edit the existing one or deactivate it first.'
        ]);

        DB::beginTransaction();
        try {
            $totalBasePriceForProduct = 0;
            foreach ($validatedData['raw_materials'] as $materialData) {
                $rawMaterial = RawMaterial::findOrFail($materialData['id']);
                $quantityInUsageUnit = (float)$materialData['quantity'];

                if (!$rawMaterial->conversion_factor || $rawMaterial->conversion_factor <= 0) {
                    throw new \Exception("Conversion factor for {$rawMaterial->name} is invalid.");
                }
                if ($rawMaterial->unit_price === null) {
                    throw new \Exception("Unit price for {$rawMaterial->name} is not set.");
                }
                $quantityInStockUnit = $quantityInUsageUnit / $rawMaterial->conversion_factor;
                $itemCost = $quantityInStockUnit * $rawMaterial->unit_price;

                BillOfMaterial::create([
                    'product_id' => $validatedData['product_id'],
                    'raw_material_id' => $rawMaterial->id,
                    'quantity' => $quantityInUsageUnit,
                    'total_cost' => $itemCost,
                    'is_active' => true,
                ]);
                $totalBasePriceForProduct += $itemCost;
            }
            $product = Product::findOrFail($validatedData['product_id']);
            $product->base_price = $totalBasePriceForProduct;
            $product->save();
            DB::commit();
            return redirect()->route('bill_of_materials')->with('success', 'Bill of Material created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to create BoM: ' . $e->getMessage());
        }
    }

    public function edit($productSlug)
    {
        $product = Product::where('slug', $productSlug)->firstOrFail();
        $billOfMaterial = BillOfMaterial::where('product_id', $product->id)
                                        ->with('rawMaterial')
                                        ->get();
        if($billOfMaterial->isEmpty()){
             return redirect()->route('bill_of_materials.create', ['product_id' => $product->id])->with('info', 'No active BoM found for this product. You can create one.');
        }

        $allProducts = Product::where('is_active', true)->orderBy('name')->get();
        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('name')->get();
        return view('content.bill_of_material.edit', compact('product', 'billOfMaterial', 'allProducts', 'rawMaterials'));
    }

    public function update(Request $request, $productSlug)
    {
        $product = Product::where('slug', $productSlug)->firstOrFail();
        $validatedData = $request->validate([
            'raw_materials' => 'required|array|min:1',
            'raw_materials.*.id' => 'required|exists:raw_materials,id',
            'raw_materials.*.quantity' => 'required|numeric|gt:0',
        ]);

        DB::beginTransaction();
        try {
            BillOfMaterial::where('product_id', $product->id)->update(['is_active' => false]);
            $totalBasePriceForProduct = 0;

            foreach ($validatedData['raw_materials'] as $materialData) {
                $rawMaterial = RawMaterial::findOrFail($materialData['id']);
                $quantityInUsageUnit = (float)$materialData['quantity'];

                if (!$rawMaterial->conversion_factor || $rawMaterial->conversion_factor <= 0) {
                    throw new \Exception("Conversion factor for {$rawMaterial->name} is invalid.");
                }
                if ($rawMaterial->unit_price === null) {
                    throw new \Exception("Unit price for {$rawMaterial->name} is not set.");
                }
                $quantityInStockUnit = $quantityInUsageUnit / $rawMaterial->conversion_factor;
                $itemCost = $quantityInStockUnit * $rawMaterial->unit_price;

                BillOfMaterial::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'raw_material_id' => $rawMaterial->id,
                    ],
                    [
                        'quantity' => $quantityInUsageUnit,
                        'total_cost' => $itemCost,
                        'is_active' => true,
                    ]
                );
                $totalBasePriceForProduct += $itemCost;
            }
            $product->base_price = $totalBasePriceForProduct;
            $product->save();
            DB::commit();
            return redirect()->route('bill_of_materials.show', $product->slug)->with('success', 'Bill of Material updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to update BoM: ' . $e->getMessage());
        }
    }

    public function show($productSlug)
    {
        $product = Product::where('slug', $productSlug)->with([
            'category',
            'billOfMaterial' => function ($query) {
                $query->where('is_active', true)->with('rawMaterial');
            }
        ])->firstOrFail();

        $billOfMaterials = $product->billOfMaterial;
        $base_price = $product->base_price;

        if ($billOfMaterials->isEmpty()) {
             return redirect()->route('bill_of_materials.create', ['product_id' => $product->id])
                            ->with('info', "No active Bill of Material found for {$product->name}. Please create one.");
        }
        return view('content.bill_of_material.show', compact('product', 'billOfMaterials', 'base_price'));
    }

    public function destroy($productSlug)
    {
        $product = Product::where('slug', $productSlug)->firstOrFail();
        BillOfMaterial::where('product_id', $product->id)->update(['is_active' => false]);
        $product->base_price = 0;
        $product->save();
        return redirect()->route('bill_of_materials')->with('success', 'Bill of Material for ' . $product->name . ' deactivated.');
    }
}
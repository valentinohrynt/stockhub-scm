<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use App\Models\BillOfMaterial;

class BillOfMaterialController extends Controller
{
    public function index(Request $request)
    {
        $queryBuilder = BillOfMaterial::query()
            ->select('product_id')
            ->with('product.category')
            ->groupBy('product_id');

        $currentStatusForView = '1'; 

        $queryBuilder->whereHas('product', function ($q) use ($request) {
            if ($request->filled('search')) {
                $q->where('name', 'LIKE', '%' . $request->input('search') . '%');
            }
            if ($request->filled('category')) {
                $q->where('category_id', $request->input('category'));
            }
        });

        if (!$request->has('status')) {
            $queryBuilder->where('is_active', true);
        } else {
            $statusParamValue = $request->input('status');
            if ($statusParamValue === 'all') {
                $currentStatusForView = 'all';
            } elseif ($statusParamValue === '0' || $statusParamValue === '1') {
                $queryBuilder->where('is_active', $statusParamValue === '1');
                $currentStatusForView = $statusParamValue;
            } else {
                $queryBuilder->where('is_active', true);
            }
        }
        
        $billOfMaterials = $queryBuilder->latest('id')->paginate(50)->withQueryString();
        
        foreach ($billOfMaterials as $bomGroup) {
            $details = BillOfMaterial::where('product_id', $bomGroup->product_id)
                                     ->with('rawMaterial')
                                     ->get();
            $bomGroup->base_price = $details->sum('total_cost');
            
            if ($details->isNotEmpty()) {
                $bomGroup->is_active = $details->first()->is_active;
            } else {
                $bomGroup->is_active = null; 
            }

            $possibleUnits = PHP_INT_MAX;
            if ($details->isEmpty()) {
                $possibleUnits = 0;
            } else {
                foreach ($details as $detail) {
                    $available = $detail->rawMaterial->stock ?? 0;
                    $required = $detail->quantity;
                    if ($required > 0) {
                        $possibleUnits = min($possibleUnits, floor($available / $required));
                    }
                }
            }
            $bomGroup->possible_units = $possibleUnits === PHP_INT_MAX ? 0 : $possibleUnits;
        }

        $categories = Category::where('type', 'product')->get();

        return view('content.bill_of_material.index', [
            'billOfMaterials' => $billOfMaterials,
            'categories' => $categories,
            'currentStatus' => $currentStatusForView
        ]);
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $rawMaterials = RawMaterial::where('is_active', true)->get();

        return view('content.bill_of_material.create', compact('products', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'raw_materials' => 'required|array|min:1',
            'raw_materials.*' => 'required|exists:raw_materials,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
        ]);

        BillOfMaterial::where('product_id', $validatedData['product_id'])->delete();

        foreach ($validatedData['raw_materials'] as $index => $rawMaterialId) {
            $quantity = $validatedData['quantities'][$index];
            $unitPrice = RawMaterial::findOrFail($rawMaterialId)->unit_price;

            BillOfMaterial::create([
                'product_id' => $validatedData['product_id'],
                'raw_material_id' => $rawMaterialId,
                'quantity' => $quantity,
                'total_cost' => $unitPrice * $quantity,
                'is_active' => true,
            ]);
        }

        return redirect()->route('bill_of_materials')->with('success', 'Bill of Material created successfully.');
    }

    public function edit($productSlug)
    {
        $product = Product::where('slug', $productSlug)->firstOrFail();

        $billOfMaterial = BillOfMaterial::with('rawMaterial')
            ->where('product_id', $product->id)
            ->get();

        $products = Product::where('is_active',true)->get();
        $rawMaterials = RawMaterial::where('is_active', true)->get();

        return view('content.bill_of_material.edit', compact('billOfMaterial', 'product', 'products', 'rawMaterials'));
    }

    public function update(Request $request, $productSlug)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'raw_materials' => 'required|array|min:1',
            'raw_materials.*' => 'required|exists:raw_materials,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validatedData['product_id']);

        BillOfMaterial::where('product_id', $product->id)->delete();

        foreach ($validatedData['raw_materials'] as $index => $rawMaterialId) {
            $quantity = $validatedData['quantities'][$index];
            $unitPrice = RawMaterial::findOrFail($rawMaterialId)->unit_price;

            BillOfMaterial::create([
                'product_id' => $product->id,
                'raw_material_id' => $rawMaterialId,
                'quantity' => $quantity,
                'total_cost' => $unitPrice * $quantity,
                'is_active' => true,
            ]);
        }

        return redirect()->route('bill_of_materials')->with('success', 'Bill of Material updated successfully.');
    }

    public function destroy($productSlug)
    {
        $product = Product::where('slug', $productSlug)->firstOrFail();
        BillOfMaterial::where('product_id', $product->id)->update(['is_active' => false]);
        return redirect()->route('bill_of_materials')->with('success', 'Bill of Material deleted successfully.');
    }

    public function show($productSlug)
    {
        $product = Product::where('slug', $productSlug)->firstOrFail();

        $billOfMaterials = BillOfMaterial::where('product_id', $product->id)
            ->with(['rawMaterial', 'product'])
            ->get();

        if ($billOfMaterials->isEmpty()) {
            abort(404);
        }

        $base_price = $billOfMaterials->sum('total_cost');

        return view('content.bill_of_material.show', compact('billOfMaterials', 'base_price'));
    }
}
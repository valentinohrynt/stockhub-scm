<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Keep if used by trait
use App\Traits\HandlesImageUploads;

class ProductController extends Controller
{
    use HandlesImageUploads;

    public function index(Request $request)
    {
        $query = Product::query()->with([
            'category',
            'billOfMaterial' => function ($query) {

                $query->where('is_active', true)->with('rawMaterial');
            }
        ]);

        $currentStatusForView = '1';

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->input('status') === 'all') {
            $currentStatusForView = 'all';
        } elseif ($request->input('status') === '0') {
            $query->where('is_active', false);
            $currentStatusForView = '0';
        } else {
            $query->where('is_active', true);
            $currentStatusForView = '1';
        }

        $products = $query->latest('products.created_at')->paginate(18)->withQueryString();

        foreach ($products as $product) {
            $activeBoms = $product->billOfMaterial->where('is_active', true);


            $possibleUnits = PHP_INT_MAX;

            if ($activeBoms->isEmpty()) {
                $possibleUnits = 0;
            } else {
                foreach ($activeBoms as $bomItem) {
                    $rawMaterial = $bomItem->rawMaterial;

                    if (!$rawMaterial || !$rawMaterial->is_active) {
                        $possibleUnits = 0; 
                        break;
                    }

                    $requiredQtyInUsageUnit = (float)$bomItem->quantity;

                    if ($requiredQtyInUsageUnit <= 0) {
                        continue;
                    }

                    $availableStockInStockUnit = (float)($rawMaterial->stock ?? 0);
                    $conversionFactor = (float)($rawMaterial->conversion_factor ?? 0);

                    $availableStockInUsageUnit = 0;
                    if ($rawMaterial->stock_unit === $rawMaterial->usage_unit) {
                        $availableStockInUsageUnit = $availableStockInStockUnit;
                    } elseif ($conversionFactor > 0) {
                        $availableStockInUsageUnit = $availableStockInStockUnit * $conversionFactor;
                    } else {
                        $possibleUnits = 0;
                        break; 
                    }

                    if ($requiredQtyInUsageUnit > 0) {
                        $unitsForThisMaterial = floor($availableStockInUsageUnit / $requiredQtyInUsageUnit);
                        $possibleUnits = min($possibleUnits, $unitsForThisMaterial);
                    }
                }
            }

            $product->possible_units = ($possibleUnits === PHP_INT_MAX) ? 0 : $possibleUnits;
        }

        $categories = Category::where('type', 'product')->orderBy('name')->get();

        return view('content.product.index', [
            'products' => $products,
            'categories' => $categories,
            'currentStatus' => $currentStatusForView
        ]);
    }

    public function create()
    {
        $categories = Category::where('type', 'product')->get();
        return view('content.product.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'selling_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_path' => 'nullable|image',
            'is_active' => 'boolean',
        ]);

        $validatedData['code'] = strtoupper(Str::random(8));

        $product = new Product($validatedData);

        $product->is_active = $request->has('is_active') ? (bool)$request->input('is_active') : true;


        $imagePath = $this->handleImageUpload($request);
        if ($imagePath) {
            $product->image_path = $imagePath;
        }
        $product->save();


        return redirect()->route('products')->with('success', 'Product created successfully.');
    }

    public function edit($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $categories = Category::where('type', 'product')->get();

        return view('content.product.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $slug)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'selling_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_path' => 'nullable|image',
            'is_active' => 'boolean',
        ]);

        $product = Product::where('slug', $slug)->firstOrFail();

        if ($request->hasFile('image_path')) {
            $imagePath = $this->handleImageUpload($request, $product);
            $validatedData['image_path'] = $imagePath;
        } else {
            $validatedData['image_path'] = $product->image_path;
        }
        
        $product->fill($validatedData);
        $product->is_active = $request->has('is_active') ? (bool)$request->input('is_active') : $product->is_active; 
        $product->save();


        return redirect()->route('products.show', $product->slug)->with('success', 'Product updated successfully.');
    }


    public function destroy($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $product->is_active = false;
        $product->save();

        if ($product->billOfMaterial()->where('is_active', true)->exists()) {
            $product->billOfMaterial()->update(['is_active' => false]);
        }

        return redirect()->route('products')->with('success', 'Product and its active BOM (if any) deactivated successfully.');
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->with([
                'category',
                'billOfMaterial' => function ($query) {
                    $query->where('is_active', true)->with('rawMaterial');
                }
            ])
            ->firstOrFail();
        
        $activeBoms = $product->billOfMaterial->where('is_active', true);
        $possibleUnits = PHP_INT_MAX;
        if ($activeBoms->isEmpty()) {
            $possibleUnits = 0;
        } else {
            foreach ($activeBoms as $bomItem) {
                $rawMaterial = $bomItem->rawMaterial;
                if (!$rawMaterial || !$rawMaterial->is_active) {
                    $possibleUnits = 0; break;
                }
                $requiredQtyInUsageUnit = (float)$bomItem->quantity;
                if ($requiredQtyInUsageUnit <= 0) continue;

                $availableStockInStockUnit = (float)($rawMaterial->stock ?? 0);
                $conversionFactor = (float)($rawMaterial->conversion_factor ?? 0);
                $availableStockInUsageUnit = 0;

                if ($rawMaterial->stock_unit === $rawMaterial->usage_unit) {
                    $availableStockInUsageUnit = $availableStockInStockUnit;
                } elseif ($conversionFactor > 0) {
                    $availableStockInUsageUnit = $availableStockInStockUnit * $conversionFactor;
                } else {
                    $possibleUnits = 0; break;
                }
                $unitsForThisMaterial = floor($availableStockInUsageUnit / $requiredQtyInUsageUnit);
                $possibleUnits = min($possibleUnits, $unitsForThisMaterial);
            }
        }
        $product->possible_units = ($possibleUnits === PHP_INT_MAX) ? 0 : $possibleUnits;


        return view('content.product.show', compact('product'));
    }

    public function toggleActive($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $product->is_active = !$product->is_active;
        $product->save();

        return redirect()->route('products')->with('success', 'Product status updated successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\HandlesImageUploads;

class ProductController extends Controller
{
    use HandlesImageUploads;

    public function index(Request $request)
    {
        $query = Product::query()->with(['category', 'billOfMaterial.rawMaterial']);
        
        $currentStatusForView = '1';

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if (!$request->has('status')) {
            $query->where('is_active', true);
        } else {
            $statusParamValue = $request->input('status');
            if ($statusParamValue === 'all') {
                $currentStatusForView = 'all';
            } elseif ($statusParamValue === '0' || $statusParamValue === '1') {
                $query->where('is_active', $statusParamValue === '1');
                $currentStatusForView = $statusParamValue;
            } else {
                $query->where('is_active', true);
            }
        }
        
        $products = $query->latest()->paginate(18)->withQueryString();

        foreach ($products as $product) {
            $boms = $product->billOfMaterial;
            $product->base_price = $boms->sum('total_cost');

            $possibleUnits = PHP_INT_MAX;
            if ($boms->isEmpty()) {
                $possibleUnits = 0;
            } else {
                foreach ($boms as $bom) {
                    $availableStock = $bom->rawMaterial->stock ?? 0;
                    $requiredQty = $bom->quantity;
                    if ($requiredQty > 0) {
                        $possibleUnits = min($possibleUnits, floor($availableStock / $requiredQty));
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
            'slug' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validatedData['code'] = strtoupper(Str::random(8));

        $product = Product::create($validatedData);

        // // Local image handling
        // if ($request->hasFile('image_path')) {
        //     $imageName = handleProductImage($request->file('image_path'), $product->code);

        //     if ($imageName) {
        //         $product->update(['image_path' => $imageName]);
        //     }
        // }

        // S3 image handling
        $imagePath = $this->handleImageUpload($request);
        if ($imagePath) {
            $product->update(['image_path' => $imagePath]);
        }

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
            'slug' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $product = Product::where('slug', $slug)->firstOrFail();

        $product->update($validatedData);

        // // Local image handling
        // if ($request->hasFile('image_path')) {
        //     if ($product->image_path && Storage::exists('public/product_img/' . $product->image_path)) {
        //         Storage::delete('public/product_img/' . $product->image_path);
        //     }

        //     $imageName = handleProductImage($request->file('image_path'), $product->code);

        //     if ($imageName) {
        //         $product->update(['image_path' => $imageName]);
        //     }
        // }

        // S3 image handling
        $imagePath = $this->handleImageUpload($request, $product);

        $validatedData['image_path'] = $imagePath;
        $product->update($validatedData);

        return redirect()->route('products')->with('success', 'Product updated successfully.');
    }


    public function destroy($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $product->is_active = false;
        $product->save();

        return redirect()->route('products')->with('success', 'Product deleted successfully.');
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

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

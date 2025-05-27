<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\RawMaterial;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\HandlesImageUploads;
use App\Traits\CalculatesJitParameters;
use Illuminate\Support\Facades\Storage;
use App\Services\InventoryAnalyticsService;

class RawMaterialController extends Controller
{
    use CalculatesJitParameters;
    use HandlesImageUploads;

    public function index(Request $request)
    {
        $query = RawMaterial::query()->with(['category', 'supplier']);
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
        if ($request->filled('needs_order')) {
            if ($request->input('needs_order') == '1') {
                $query->whereNotNull('signal_point')->whereRaw('stock <= signal_point');
            } elseif ($request->input('needs_order') == '0') {
                $query->whereNotNull('signal_point')->whereRaw('stock > signal_point');
            }
        }

        $rawMaterials = $query->latest()->paginate(18)->withQueryString();
        $categories = Category::where('type', 'raw_material')->orderBy('name')->get();

        return view('content.raw_material.index', [
            'rawMaterials' => $rawMaterials,
            'categories' => $categories,
            'currentStatus' => $currentStatusForView
        ]);
    }

    public function create()
    {
        $categories = Category::where('type', 'raw_material')->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('content.raw_material.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'stock' => 'required|numeric|min:0',
            'stock_unit' => 'required|string|max:50',
            'usage_unit' => 'required|string|max:50',
            'conversion_factor' => 'required|numeric|gt:0',
            'unit_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'image_path' => 'nullable|image|max:2048',
            'replenish_quantity' => 'nullable|integer|min:1',
            'lead_time' => 'required|integer|min:0',
            'safety_stock_days' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validatedData['code'] = strtoupper(Str::random(8));
        if ($request->hasFile('image_path')) {
            $imagePath = $this->handleImageUpload($request);
            $validatedData['image_path'] = $imagePath;
        } else {
            $validatedData['image_path'] = null;
        }

        $rawMaterial = RawMaterial::create($validatedData);
        $jitParameters = $this->calculateJitParameters($request, $rawMaterial);
        if ($jitParameters) {
            $rawMaterial->update($jitParameters);
        }
        return redirect()->route('raw_materials')->with('success', 'Raw Material created successfully with JIT parameters calculated.');
    }

    public function edit($slug)
    {
        $rawMaterial= RawMaterial::where('slug', $slug)->firstOrFail();
        $categories = Category::where('type', 'raw_material')->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('content.raw_material.edit', compact('rawMaterial', 'categories', 'suppliers'));
    }

    public function update(Request $request, $slug)
    {
        $rawMaterial = RawMaterial::where('slug', $slug)->firstOrFail();
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'stock' => 'required|numeric|min:0',
            'stock_unit' => 'required|string|max:50',
            'usage_unit' => 'required|string|max:50',
            'conversion_factor' => 'required|numeric|gt:0',
            'unit_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'image_path' => 'nullable|image|max:2048',
            'replenish_quantity' => 'nullable|integer|min:1',
            'lead_time' => 'required|integer|min:0',
            'safety_stock_days' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $imagePathToSave = $this->handleImageUpload($request, $rawMaterial);
        $validatedData['image_path'] = $imagePathToSave;
        $rawMaterial->update($validatedData);
        $jitParameters = $this->calculateJitParameters($request, $rawMaterial->fresh());
        if ($jitParameters) {
            $rawMaterial->update($jitParameters);
        }
        return redirect()->route('raw_materials')->with('success', 'Raw Material updated successfully with JIT parameters recalculated.');
    }

    public function destroy($slug)
    {
        $rawMaterial= RawMaterial::where('slug', $slug)->firstOrFail();
        $rawMaterial->is_active = false;
        $rawMaterial->save();
        return redirect()->route('raw_materials')->with('success', 'Raw Material deleted successfully.');
    }

    public function show($slug)
    {
        $rawMaterial= RawMaterial::where('slug', $slug)->firstOrFail();
        return view('content.raw_material.show', compact('rawMaterial'));
    }

    public function toggleActive($slug)
    {
        $rawMaterial= RawMaterial::where('slug', $slug)->firstOrFail();
        $rawMaterial->is_active = !$rawMaterial->is_active;
        $rawMaterial->save();
        return redirect()->route('raw_materials')->with('success', 'Raw Material status updated successfully.');
    }

    public function forceRecalculateAnalytics(InventoryAnalyticsService $analyticsService)
    {
        $resultMessage = $analyticsService->runCalculations();
        return redirect()->back()->with('success', $resultMessage);
    }
}
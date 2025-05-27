<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        $currentStatusForView = '1';

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('email', 'LIKE', $searchTerm)
                  ->orWhere('phone', 'LIKE', $searchTerm);
            });
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
        
        $suppliers = $query->latest()->paginate(20)->withQueryString();

        return view('content.supplier.index', [
            'suppliers' => $suppliers,
            'currentStatus' => $currentStatusForView
        ]);
    }

    public function create()
    {
        return view('content.supplier.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
        ]);

        Supplier::create($validatedData);

        return redirect()->route('suppliers')->with('success', 'Supplier created successfully.');
    }

    public function edit($slug)
    {
        $supplier = Supplier::where('slug', $slug)->firstOrFail();

        return view('content.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $slug)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $supplier = Supplier::where('slug', $slug)->firstOrFail();
        $supplier->update($validatedData);

        return redirect()->route('suppliers')->with('success', 'Supplier updated successfully.');
    }

    public function destroy($slug)
    {
        $supplier = Supplier::where('slug', $slug)->firstOrFail();
        $supplier->is_active = false;
        $supplier->save();


        return redirect()->route('suppliers')->with('success', 'Supplier deleted successfully.');
    }

    public function show($slug)
    {
        $supplier = Supplier::where('slug', $slug)->firstOrFail();
        $supplier->load('rawMaterial');

        return view('content.supplier.show', compact('supplier'));
    }
}

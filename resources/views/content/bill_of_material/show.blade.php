@extends('layouts.master')

@php
    $product = $billOfMaterials->first()?->product;
@endphp

@section('title', $product ? 'BOM: ' . $product->name : 'Bill of Materials Details')

@section('content')
    <div class="container py-4">
        @if ($product)
            <div class="modern-container">
                <div class="header-section-subtle">
                    @if ($product->category)
                        <p class="header-category">{{ $product->category->name }}</p>
                    @endif

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h2 class="page-title">{{ $product->name }}</h2>
                            <div class="header-meta">
                                <span
                                    class="badge {{ $billOfMaterials->first()->is_active ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                    {{ $billOfMaterials->first()->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span>Code: <strong>{{ $product->code }}</strong></span>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="{{ route('bill_of_materials') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                <a href="{{ route('bill_of_materials.edit', $product->slug) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('bill_of_materials.delete', $product->slug) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this Bill of Material? This action will mark it as inactive.');"
                                    style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="content-block">
                                <h5 class="content-block-title">Recipe Ingredients</h5>
                                <div class="table-responsive">
                                    <table class="table modern-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Raw Material</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-end">Unit Price</th>
                                                <th class="text-end">Total Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($billOfMaterials as $index => $bom)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $bom->rawMaterial->name ?? '-' }}</td>
                                                    <td class="text-center">{{ $bom->quantity }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($bom->rawMaterial->unit_price ?? 0, 2) }}</td>
                                                    <td class="text-end fw-bold">Rp
                                                        {{ number_format($bom->total_cost, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-summary">
                                    <span>Total Base Price</span>
                                    <span class="price-value">Rp {{ number_format($base_price, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            @if ($product->image_path)
                                <div class="content-block mb-4">
                                    <h5 class="content-block-title">Product Image</h5>
                                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}"
                                        class="img-fluid rounded border">
                                </div>
                            @endif

                            <div class="content-block">
                                <h5 class="content-block-title">Details</h5>
                                <ul class="info-list">
                                    <li>
                                        <span class="label">Lead Time</span>
                                        <span class="value">{{ $product->lead_time ?? 'N/A' }} days</span>
                                    </li>
                                    <li>
                                        <span class="label">Selling Price</span>
                                        <span class="value">Rp {{ number_format($product->selling_price, 2) }}</span>
                                    </li>
                                    <li>
                                        <span class="label">Created At</span>
                                        <span class="value">{{ $product->created_at->format('d M Y, H:i') }}</span>
                                    </li>
                                    <li>
                                        <span class="label">Last Updated</span>
                                        <span class="value">{{ $product->updated_at->format('d M Y, H:i') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="modern-container">
                <div class="content-section">
                    <div class="alert alert-warning text-center">
                        Product not found or Bill of Materials is empty.
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

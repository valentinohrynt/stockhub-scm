@extends('layouts.master')

@php
    $productInstance = $product ?? $billOfMaterials->first()?->product;
@endphp

@section('title', $productInstance ? 'BOM: ' . $productInstance->name : 'Bill of Materials Details')

@section('content')
    <div class="container py-4">
        @if ($productInstance)
            <div class="modern-container">
                <div class="header-section-subtle">
                    @if ($productInstance->category)
                        <p class="header-category">{{ $productInstance->category->name }}</p>
                    @endif

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h2 class="page-title">{{ $productInstance->name }}</h2>
                            <div class="header-meta">
                                <span
                                    class="badge {{ $billOfMaterials->firstWhere('is_active', true) ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                    {{ $billOfMaterials->firstWhere('is_active', true) ? 'Active' : 'Inactive' }}
                                </span>
                                <span>Code: <strong>{{ $productInstance->code }}</strong></span>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="{{ route('bill_of_materials') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                <a href="{{ route('bill_of_materials.edit', $productInstance->slug) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <form method="POST"
                                    action="{{ route('bill_of_materials.delete', $productInstance->slug) }}"
                                    onsubmit="return confirm('Are you sure you want to deactivate this Bill of Material?');"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE') {{-- Sesuai route:web.php Anda menggunakan delete untuk BoM --}}
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-1"></i> Deactivate
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
                                @if ($billOfMaterials->where('is_active', true)->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table modern-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Raw Material</th>
                                                    <th class="text-center">Quantity (Usage Unit)</th>
                                                    <th class="text-end">Unit Price (per Stock Unit)</th>
                                                    <th class="text-end">Total Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($billOfMaterials->where('is_active', true) as $index => $bom)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $bom->rawMaterial->name ?? '-' }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($bom->quantity, 2, ',', '.') }}
                                                            {{ $bom->rawMaterial->usage_unit ?? '' }}</td>
                                                        <td class="text-end">Rp
                                                            {{ number_format($bom->rawMaterial->unit_price ?? 0, 2) }}
                                                            /{{ $bom->rawMaterial->stock_unit ?? '' }}</td>
                                                        <td class="text-end fw-bold">Rp
                                                            {{ number_format($bom->total_cost, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-summary mt-3">
                                        <span>Total Base Price</span>
                                        <span class="price-value">Rp {{ number_format($base_price, 2) }}</span>
                                    </div>
                                @else
                                    <div class="alert alert-info">This product does not have an active Bill of Material. <a
                                            href="{{ route('bill_of_materials.edit', $productInstance->slug) }}">Set one up
                                            now.</a></div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4">
                            @if ($productInstance->image_path)
                                <div class="content-block mb-4">
                                    <h5 class="content-block-title">Product Image</h5>
                                    <img src="{{ Storage::url($productInstance->image_path) }}"
                                        alt="{{ $productInstance->name }}" class="img-fluid rounded border">
                                </div>
                            @endif

                            <div class="content-block">
                                <h5 class="content-block-title">Details</h5>
                                <ul class="info-list">
                                    <li>
                                        <span class="label">Base Price</span>
                                        <span class="value">Rp {{ number_format($productInstance->base_price, 2) }}</span>
                                    </li>
                                    <li>
                                        <span class="label">Selling Price</span>
                                        <span class="value">Rp
                                            {{ number_format($productInstance->selling_price, 2) }}</span>
                                    </li>
                                    <li>
                                        <span class="label">Created At</span>
                                        <span
                                            class="value">{{ $productInstance->created_at->format('d M Y, H:i') }}</span>
                                    </li>
                                    <li>
                                        <span class="label">Last Updated</span>
                                        <span
                                            class="value">{{ $productInstance->updated_at->format('d M Y, H:i') }}</span>
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
                        Product not found or Bill of Materials is not set up for this product.
                        <a href="{{ route('bill_of_materials') }}">Go back to BOM List</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

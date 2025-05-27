@extends('layouts.master')

@section('title', 'Raw Material: ' . $rawMaterial->name)

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section-subtle">
                @if ($rawMaterial->category)
                    <p class="header-category">{{ $rawMaterial->category->name }}</p>
                @endif

                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h2 class="page-title">{{ $rawMaterial->name }}</h2>
                        <div class="header-meta">
                            <span class="badge {{ $rawMaterial->is_active ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                {{ $rawMaterial->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span>Code: <strong>{{ $rawMaterial->code }}</strong></span>
                        </div>
                    </div>
                    <div class="price-display-header">
                        <span>Unit Price (per {{ $rawMaterial->stock_unit }})</span>
                        <h4>Rp{{ number_format($rawMaterial->unit_price, 2) }}</h4>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('raw_materials') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-arrow-left me-1"></i> Back</a>
                        @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                            <a href="{{ route('raw_materials.edit', $rawMaterial->slug) }}" class="btn btn-primary"><i
                                    class="fas fa-edit me-1"></i> Edit</a>
                            <form method="POST" action="{{ route('raw_materials.delete', $rawMaterial->slug) }}"
                                onsubmit="return confirm('Are you sure you want to delete this Raw Material? This action cannot be undone.');"
                                style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>
                                    Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content-section">
                <div class="row g-4">
                    <div class="col-lg-7">
                        @if ($rawMaterial->image_path)
                            <div class="content-block mb-4">
                                <h5 class="content-block-title">Image</h5>
                                <img src="{{ Storage::url($rawMaterial->image_path) }}" alt="{{ $rawMaterial->name }}"
                                    class="img-fluid rounded border">
                            </div>
                        @endif
                        <div class="content-block mb-4">
                            <h5 class="content-block-title">Description</h5>
                            <div class="prose">
                                {!! $rawMaterial->description
                                    ? nl2br(e($rawMaterial->description))
                                    : '<p class="text-muted">No description provided.</p>' !!}
                            </div>
                        </div>
                        @if ($rawMaterial->products && $rawMaterial->products->count() > 0)
                            <div class="content-block">
                                <h5 class="content-block-title">Used in Products (Recipe in {{ $rawMaterial->usage_unit }})
                                </h5>
                                <div class="table-responsive">
                                    <table class="table modern-table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-center">Quantity Required</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rawMaterial->products as $product)
                                                @php
                                                    // Ambil pivot data dari relasi
                                                    $pivotData = $product
                                                        ->billOfMaterial()
                                                        ->where('raw_material_id', $rawMaterial->id)
                                                        ->first();
                                                @endphp
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td class="text-center">
                                                        {{ $pivotData ? number_format($pivotData->quantity, 2, ',', '.') . ' ' . $rawMaterial->usage_unit : 'N/A' }}
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('products.show', $product->slug) }}"
                                                            class="btn btn-sm btn-outline-primary">View Product</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-5">
                        <div class="content-block mb-4">
                            <h5 class="content-block-title">Inventory & JIT Parameters (in {{ $rawMaterial->stock_unit }})
                            </h5>
                            <ul class="info-list info-list-dense">
                                <li>
                                    <span class="label">Current Stock</span>
                                    <span
                                        class="value {{ $rawMaterial->stock <= ($rawMaterial->signal_point ?? 0) && $rawMaterial->is_active ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                        {{ number_format($rawMaterial->stock, 2, ',', '.') }}
                                        {{ $rawMaterial->stock_unit }}
                                    </span>
                                </li>
                                <li>
                                    <span class="label">Avg. Daily Usage</span>
                                    <span class="value">{{ number_format($rawMaterial->average_daily_usage ?? 0, 2) }}
                                        {{ $rawMaterial->stock_unit }}
                                        <small class="text-muted">(Calculated)</small></span>
                                </li>
                                <li>
                                    <span class="label">Replenish Quantity</span>
                                    <span class="value">{{ number_format($rawMaterial->replenish_quantity ?? 0) }}
                                        {{ $rawMaterial->stock_unit }}</span>
                                </li>
                                <li>
                                    <span class="label">Lead Time (Policy)</span>
                                    <span class="value">{{ $rawMaterial->lead_time ?? 0 }} days</span>
                                </li>
                                <li>
                                    <span class="label">Safety Stock Days (Policy)</span>
                                    <span class="value">{{ $rawMaterial->safety_stock_days ?? 0 }} days</span>
                                </li>
                                <li>
                                    <span class="label">Safety Stock <small class="text-muted">(Calculated)</small></span>
                                    <span class="value">{{ number_format($rawMaterial->safety_stock ?? 0, 2, ',', '.') }}
                                        {{ $rawMaterial->stock_unit }}</span>
                                </li>
                                <li>
                                    <span class="label">Signal Point <small class="text-muted">(Calculated)</small></span>
                                    <span
                                        class="value fw-bold">{{ number_format($rawMaterial->signal_point ?? 0, 2, ',', '.') }}
                                        {{ $rawMaterial->stock_unit }}</span>
                                </li>
                            </ul>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Usage Unit for Recipes: {{ $rawMaterial->usage_unit }} <br>
                                    Conversion: 1 {{ $rawMaterial->stock_unit }} =
                                    {{ number_format($rawMaterial->conversion_factor, 2, ',', '.') }}
                                    {{ $rawMaterial->usage_unit }}
                                </small>
                            </div>
                        </div>
                        <div class="content-block">
                            <h5 class="content-block-title">Additional Details</h5>
                            <ul class="info-list">
                                <li>
                                    <span class="label">Supplier</span>
                                    <span class="value">
                                        @if ($rawMaterial->supplier)
                                            <a
                                                href="{{ route('suppliers.show', ['slug' => $rawMaterial->supplier->slug]) }}">
                                                {{ $rawMaterial->supplier->name }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </li>
                                <li>
                                    <span class="label">Created At</span>
                                    <span class="value">{{ $rawMaterial->created_at->format('d M Y, H:i') }}</span>
                                </li>
                                <li>
                                    <span class="label">Last Updated</span>
                                    <span class="value">{{ $rawMaterial->updated_at->format('d M Y, H:i') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

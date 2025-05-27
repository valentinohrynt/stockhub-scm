@extends('layouts.master')

@section('title', 'Product Details: ' . $product->name)

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section-subtle">
                @if ($product->category)
                    <p class="header-category">{{ $product->category->name }}</p>
                @endif

                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h2 class="page-title">{{ $product->name }}</h2>
                        <div class="header-meta">
                            <span class="badge {{ $product->is_active ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span>Code: <strong>{{ $product->code }}</strong></span>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('products') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-arrow-left me-1"></i> Back</a>
                        @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                            <a href="{{ route('products.edit', $product->slug) }}" class="btn btn-primary"><i
                                    class="fas fa-edit me-1"></i> Edit</a>
                            <form method="POST" action="{{ route('products.delete', $product->slug) }}"
                                onsubmit="return confirm('Are you sure you want to delete this Product? This action cannot be undone.');"
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
                        @if ($product->image_path)
                            <div class="content-block mb-4">
                                <h5 class="content-block-title">Product Image</h5>
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}"
                                    class="img-fluid rounded border">
                            </div>
                        @endif
                        <div class="content-block">
                            <h5 class="content-block-title">Description</h5>
                            <div class="prose">
                                {!! $product->description
                                    ? nl2br(e($product->description))
                                    : '<p class="text-muted">No description provided.</p>' !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="content-block mb-4">
                            <h5 class="content-block-title">Pricing & Inventory</h5>
                            <ul class="info-list">
                                <li>
                                    <span class="label">Selling Price</span>
                                    <span class="value price-value">Rp
                                        {{ number_format($product->selling_price, 2) }}</span>
                                </li>
                                <li>
                                    <span class="label">Base Price (from BOM)</span>
                                    <span class="value">Rp {{ number_format($product->base_price ?? 0, 2) }}</span>
                                </li>
                                <li>
                                    <span class="label">Est. Producible Units</span>
                                    <span class="value">{{ $product->possible_units ?? 0 }} units</span>
                                </li>
                            </ul>
                        </div>
                        <div class="content-block">
                            <h5 class="content-block-title">Additional Details</h5>
                            <ul class="info-list">
                                <li>
                                    <span class="label">Preparation Time</span>
                                    <span class="value">{{ $product->lead_time ?? 'N/A' }} minutes</span>
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
    </div>
@endsection

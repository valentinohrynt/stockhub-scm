@extends('layouts.master')

@section('title', 'Product List')

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title mb-0">Product List</h2>
                    @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                        <a href="{{ route('products.create') }}" class="add-btn">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    @endif
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-card">
                    <form action="{{ route('products') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" name="search"
                                        placeholder="Search by product name..." value="{{ request('search') }}"
                                        aria-label="Search by product">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" name="category" aria-label="Filter by Category">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-select" name="status" aria-label="Filter by Status">
                                    <option value="" {{ ($currentStatus ?? '') === '' ? 'selected' : '' }}>All
                                        Statuses</option>
                                    <option value="1" {{ ($currentStatus ?? '1') === '1' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ ($currentStatus ?? '') === '0' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary flex-fill" type="submit">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('products') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-section">
                @if ($products->count())
                    <div class="row g-4">
                        @foreach ($products as $product)
                            <div class="col-md-6 col-lg-4">
                                <div class="product-card shadow-sm h-100">
                                    @if ($product->image_path)
                                        <img src="{{ Storage::url($product->image_path) }}" class="card-img-top"
                                            alt="{{ $product->name }}">
                                    @else
                                        <div class="card-img-top-placeholder">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                    @endif

                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0" title="{{ $product->name }}">{{ $product->name }}
                                            </h5>
                                            @if ($product->is_active)
                                                <span class="badge bg-success-subtle">Active</span>
                                            @else
                                                <span class="badge bg-danger-subtle">Inactive</span>
                                            @endif
                                        </div>
                                        <p class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</p>

                                        <div class="info-grid">
                                            <div>
                                                <span class="info-label">Base Price</span>
                                                <span
                                                    class="info-value">Rp{{ number_format($product->base_price ?? 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <span class="info-label">Selling Price</span>
                                                <span
                                                    class="info-value">Rp{{ number_format($product->selling_price ?? 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <span class="info-label">Can Produce</span>
                                                <span class="info-value">{{ $product->possible_units ?? 0 }} units</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end align-items-center gap-2">
                                        <a href="{{ route('products.show', $product->slug) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                        @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                            <a href="{{ route('products.edit', $product->slug) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No Products found matching your criteria.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
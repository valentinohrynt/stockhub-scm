@extends('layouts.master')

@section('title', 'BOM List')

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title mb-0">Bill of Materials List</h2>
                    @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                        <a href="{{ route('bill_of_materials.create') }}" class="add-btn">
                            <i class="fas fa-plus"></i> Add BOM
                        </a>
                    @endif
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-card">
                    <form action="{{ route('bill_of_materials') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" name="search"
                                        placeholder="Search by product..." value="{{ request('search') }}"
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
                                    <a href="{{ route('bill_of_materials') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-section">
                @if ($billOfMaterials->count())
                    <div class="row g-4">
                        @foreach ($billOfMaterials as $bom)
                            <div class="col-md-6 col-lg-4">
                                <div class="bom-card shadow-sm h-100 border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title mb-0">{{ $bom->product->name }}</h5>
                                            @if ($bom->is_active)
                                                <span
                                                    class="badge bg-success-subtle text-success-emphasis rounded-pill">Active</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger-emphasis rounded-pill">Inactive</span>
                                            @endif
                                        </div>
                                        <p class="product-category">{{ $bom->product->category->name ?? 'Uncategorized' }}
                                        </p>
                                        <div class="price-info">
                                            <div class="text-muted mb-1" style="font-size: 0.875rem;">Base price:</div>
                                            <div class="price-value">Rp {{ number_format($bom->base_price, 2) }}</div>
                                        </div>
                                    </div>
                                    <div
                                        class="card-footer bg-transparent border-0 d-flex justify-content-end align-items-center gap-2 pb-3">
                                        <a href="{{ route('bill_of_materials.show', $bom->product->slug) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                        @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                            <a href="{{ route('bill_of_materials.edit', $bom->product->slug) }}"
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
                        {{ $billOfMaterials->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No Bill of Materials found matching your criteria.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@extends('layouts.master')

@section('title', 'Raw Material List')

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h2 class="page-title mb-0">Raw Material List</h2>
                    <div class="d-flex gap-2">
                        @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                            <a href="{{ route('raw_materials.recalculate') }}" class="add-btn"
                                onclick="return confirm('This process will recalculate all inventory analysis data. Continue?')">
                                <i class="fas fa-calculator"></i> Recalculate Analysis
                            </a>
                            <a href="{{ route('raw_materials.create') }}" class="add-btn">
                                <i class="fas fa-plus"></i> Add Material
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-card">
                    <form action="{{ route('raw_materials') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" name="search"
                                        placeholder="Search by name..." value="{{ request('search') }}"
                                        aria-label="Search by name">
                                </div>
                            </div>

                            <div class="col-md-2">
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

                            <div class="col-md-2">
                                <select class="form-select" name="needs_order" aria-label="Filter by Order Status">
                                    <option value="">Order Status</option>
                                    <option value="1" {{ request('needs_order') === '1' ? 'selected' : '' }}>Needs
                                        Order</option>
                                    <option value="0" {{ request('needs_order') === '0' ? 'selected' : '' }}>Stock OK
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary flex-fill" type="submit"><i
                                            class="fas fa-filter me-1"></i> Filter</button>
                                    <a href="{{ route('raw_materials') }}" class="btn btn-outline-secondary"><i
                                            class="fas fa-undo me-1"></i> Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-section">
                @if ($rawMaterials->count())
                    <div class="row g-4">
                        @foreach ($rawMaterials as $raw)
                            <div class="col-md-6 col-lg-4">
                                <div class="product-card shadow-sm h-100">
                                    @if ($raw->image_path)
                                        <img src="{{ Storage::url($raw->image_path) }}"
                                            class="card-img-top" alt="{{ $raw->name }}">
                                    @else
                                        <div class="card-img-top-placeholder"><i class="fas fa-boxes"></i></div>
                                    @endif

                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0" title="{{ $raw->name }}">{{ $raw->name }}
                                            </h5>
                                            @if ($raw->is_active)
                                                <span class="badge bg-success-subtle">Active</span>
                                            @else
                                                <span class="badge bg-danger-subtle">Inactive</span>
                                            @endif
                                        </div>
                                        <p class="product-category">{{ $raw->category->name ?? 'Uncategorized' }}</p>

                                        @if ($raw->stock <= $raw->signal_point && $raw->signal_point > 0)
                                            <div class="alert alert-warning p-2 text-center small mb-3 fw-bold">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Needs Re-ordering
                                            </div>
                                        @endif

                                        <div class="info-grid">
                                            <div>
                                                <span class="info-label">Unit Price</span>
                                                <span
                                                    class="info-value">Rp{{ number_format($raw->unit_price ?? 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <span class="info-label">Stock</span>
                                                <span
                                                    class="info-value fw-bold {{ $raw->stock <= $raw->signal_point ? 'text-danger' : '' }}">{{ number_format($raw->stock ?? 0) }}</span>
                                            </div>
                                            <div>
                                                <span class="info-label">Signal Point</span>
                                                <span
                                                    class="info-value">{{ number_format($raw->signal_point ?? 0) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end align-items-center gap-2">
                                        <a href="{{ route('raw_materials.show', $raw->slug) }}"
                                            class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i> View</a>
                                        @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                            <a href="{{ route('raw_materials.edit', $raw->slug) }}"
                                                class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit me-1"></i>
                                                Edit</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $rawMaterials->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No Raw Materials found matching your criteria.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
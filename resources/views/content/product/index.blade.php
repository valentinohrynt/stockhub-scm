@extends('layouts.master')

@section('title', __('messages.product_list'))

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title mb-0">{{ __('messages.product_list') }}</h2>
                    @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                        <a href="{{ route('products.create') }}" class="add-btn">
                            <i class="fas fa-plus"></i> {{ __('messages.add_product') }}
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
                                        placeholder="{{ __('messages.search_by_product_name') }}"
                                        value="{{ request('search') }}"
                                        aria-label="{{ __('messages.search_by_product_name') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" name="category"
                                    aria-label="{{ __('messages.filter_button') }} {{ __('messages.category') }}">
                                    <option value="">{{ __('messages.all_categories') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-select" name="status"
                                    aria-label="{{ __('messages.filter_button') }} {{ __('messages.status') }}">
                                    <option value="" {{ ($currentStatus ?? '') === '' ? 'selected' : '' }}>
                                        {{ __('messages.all_statuses') }}</option>
                                    <option value="1" {{ ($currentStatus ?? '1') === '1' ? 'selected' : '' }}>
                                        {{ __('messages.active') }}</option>
                                    <option value="0" {{ ($currentStatus ?? '') === '0' ? 'selected' : '' }}>
                                        {{ __('messages.inactive') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary flex-fill" type="submit">
                                        <i class="fas fa-filter me-1"></i> {{ __('messages.filter_button') }}
                                    </button>
                                    <a href="{{ route('products') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i> {{ __('messages.reset_button') }}
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
                                                <span class="badge bg-success-subtle">{{ __('messages.active') }}</span>
                                            @else
                                                <span class="badge bg-danger-subtle">{{ __('messages.inactive') }}</span>
                                            @endif
                                        </div>
                                        <p class="product-category">
                                            {{ $product->category->name ?? __('messages.uncategorized') }}</p>

                                        <div class="info-grid">
                                            <div>
                                                <span class="info-label">{{ __('messages.base_price') }}</span>
                                                <span
                                                    class="info-value">Rp{{ number_format($product->base_price ?? 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <span class="info-label">{{ __('messages.selling_price') }}</span>
                                                <span
                                                    class="info-value">Rp{{ number_format($product->selling_price ?? 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <span class="info-label">{{ __('messages.producible_units') }}</span>
                                                <span class="info-value">{{ $product->possible_units ?? 0 }}
                                                    {{ Str::lower(__('messages.stock_unit')) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end align-items-center gap-2">
                                        <a href="{{ route('products.show', $product->slug) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> {{ __('messages.view_button') }}
                                        </a>
                                        @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                            <a href="{{ route('products.edit', $product->slug) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit me-1"></i> {{ __('messages.edit_button') }}
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
                        {{ __('messages.no_products_criteria') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

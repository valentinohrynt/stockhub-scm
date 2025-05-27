@extends('layouts.master')

@section('title', __('messages.bom_list_title'))

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title mb-0">{{ __('messages.bom_list_title') }}</h2>
                    @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                        <a href="{{ route('bill_of_materials.create') }}" class="add-btn">
                            <i class="fas fa-plus"></i> {{ __('messages.add_bom_button') }}
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
                                        placeholder="{{ __('messages.search_by_product_for_bom') }}" value="{{ request('search') }}"
                                        aria-label="{{ __('messages.search_by_product_for_bom') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="category" aria-label="{{ __('messages.filter_button') }} {{ __('messages.category') }}">
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
                                <select class="form-select" name="status" aria-label="{{ __('messages.filter_button') }} {{ __('messages.product_status') }}">
                                    <option value="" {{ ($currentStatus ?? '') === '' ? 'selected' : '' }}>{{ __('messages.all_product_statuses') }}</option>
                                    <option value="1" {{ ($currentStatus ?? '1') === '1' ? 'selected' : '' }}>{{ __('messages.active_products') }}</option>
                                    <option value="0" {{ ($currentStatus ?? '') === '0' ? 'selected' : '' }}>{{ __('messages.inactive_products') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary flex-fill" type="submit">
                                        <i class="fas fa-filter me-1"></i> {{ __('messages.filter_button') }}
                                    </button>
                                    <a href="{{ route('bill_of_materials') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i> {{ __('messages.reset_button') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-section">
                @if ($productsWithBOM->count())
                    <div class="row g-4">
                        @foreach ($productsWithBOM as $product)
                            <div class="col-md-6 col-lg-4">
                                <div class="bom-card shadow-sm h-100 border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title mb-0">{{ $product->name }}</h5>
                                            @if ($product->billOfMaterial()->where('is_active', true)->exists())
                                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill">{{ __('messages.bom_active_badge') }}</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">{{ __('messages.bom_inactive_badge') }}</span>
                                            @endif
                                        </div>
                                        <p class="product-category">{{ $product->category->name ?? __('messages.uncategorized') }}
                                        </p>
                                        <div class="price-info">
                                            <div class="text-muted mb-1" style="font-size: 0.875rem;">{{ __('messages.base_price') }}:</div>
                                            <div class="price-value">Rp {{ number_format($product->base_price, 2) }}</div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">{{ __('messages.can_produce_units', ['units' => $product->possible_units ?? 0]) }}</small>
                                        </div>
                                    </div>
                                    <div
                                        class="card-footer bg-transparent border-0 d-flex justify-content-end align-items-center gap-2 pb-3">
                                        <a href="{{ route('bill_of_materials.show', $product->slug) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> {{ __('messages.view_bom_button') }}
                                        </a>
                                        @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                            <a href="{{ route('bill_of_materials.edit', $product->slug) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit me-1"></i> {{ __('messages.edit_bom_button') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $productsWithBOM->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('messages.no_boms_criteria') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
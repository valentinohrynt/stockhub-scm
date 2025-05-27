@extends('layouts.master')

@section('title', __('messages.raw_material_list'))

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h2 class="page-title mb-0">{{ __('messages.raw_material_list') }}</h2>
                    <div class="d-flex gap-2">
                        @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                            <a href="{{ route('raw_materials.recalculate') }}" class="add-btn"
                                onclick="return confirm('{{ __('messages.recalculate_analysis_confirm') }}')">
                                <i class="fas fa-calculator"></i> {{ __('messages.recalculate_analysis_button') }}
                            </a>
                            <a href="{{ route('raw_materials.create') }}" class="add-btn">
                                <i class="fas fa-plus"></i> {{ __('messages.add_raw_material') }}
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
                                        placeholder="{{ __('messages.search_by_raw_material_name') }}"
                                        value="{{ request('search') }}"
                                        aria-label="{{ __('messages.search_by_raw_material_name') }}">
                                </div>
                            </div>

                            <div class="col-md-2">
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

                            <div class="col-md-2">
                                <select class="form-select" name="needs_order"
                                    aria-label="{{ __('messages.filter_button') }} {{ __('messages.order_status') }}">
                                    <option value="">{{ __('messages.order_status') }}</option>
                                    <option value="1" {{ request('needs_order') === '1' ? 'selected' : '' }}>
                                        {{ __('messages.needs_order') }}</option>
                                    <option value="0" {{ request('needs_order') === '0' ? 'selected' : '' }}>
                                        {{ __('messages.stock_ok') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary flex-fill" type="submit"><i
                                            class="fas fa-filter me-1"></i> {{ __('messages.filter_button') }}</button>
                                    <a href="{{ route('raw_materials') }}" class="btn btn-outline-secondary"><i
                                            class="fas fa-undo me-1"></i> {{ __('messages.reset_button') }}</a>
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
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0" title="{{ $raw->name }}">
                                                {{ Str::limit($raw->name, 25) }}
                                            </h5>
                                            @if ($raw->is_active)
                                                <span class="badge bg-success-subtle">{{ __('messages.active') }}</span>
                                            @else
                                                <span class="badge bg-danger-subtle">{{ __('messages.inactive') }}</span>
                                            @endif
                                        </div>
                                        <p class="product-category">
                                            {{ $raw->category->name ?? __('messages.uncategorized') }}</p>

                                        @if ($raw->stock <= $raw->signal_point && $raw->signal_point > 0 && $raw->is_active)
                                            <div class="alert alert-warning p-2 text-center small mb-3 fw-bold">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ __('messages.needs_reordering_alert') }}
                                            </div>
                                        @endif

                                        <div class="info-grid">
                                            <div>
                                                <span class="info-label">{{ __('messages.unit_price') }}</span>
                                                <span class="info-value">Rp{{ number_format($raw->unit_price ?? 0, 2) }}
                                                    /{{ $raw->stock_unit }}</span>
                                            </div>
                                            <div>
                                                <span class="info-label">{{ __('messages.current_stock') }}</span>
                                                <span
                                                    class="info-value fw-bold {{ $raw->stock <= $raw->signal_point && $raw->is_active ? 'text-danger' : '' }}">
                                                    {{ number_format($raw->stock, 2, ',', '.') }} {{ $raw->stock_unit }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="info-label">{!! __('messages.raw_material_signal_point_calculated_show') !!}</span>
                                                <span class="info-value">
                                                    {{ number_format($raw->signal_point ?? 0, 2, ',', '.') }}
                                                    {{ $raw->stock_unit }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end align-items-center gap-2">
                                        <a href="{{ route('raw_materials.show', $raw->slug) }}"
                                            class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>
                                            {{ __('messages.view_button') }}</a>
                                        @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                            <a href="{{ route('raw_materials.edit', $raw->slug) }}"
                                                class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit me-1"></i>
                                                {{ __('messages.edit_button') }}</a>
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
                        {{ __('messages.no_raw_materials_criteria') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

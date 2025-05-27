@extends('layouts.master')

@section('title', __('messages.stock_adjustment_history_title'))

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title mb-0">{{ __('messages.stock_adjustment_history_title') }}</h2>
                    <a href="{{ route('stock_adjustments.create') }}" class="add-btn">
                        <i class="fas fa-plus"></i> {{ __('messages.create_new_adjustment_button') }}
                    </a>
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-card">
                    <form action="{{ route('stock_adjustments') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search_raw_material"
                                    class="form-label small">{{ __('messages.raw_material_name_label_filter') }}</label>
                                <input type="text" class="form-control" name="search_raw_material"
                                    placeholder="{{ __('messages.search_raw_material_name_placeholder') }}"
                                    value="{{ request('search_raw_material') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label small">{{ __('messages.movement_type') }}</label>
                                <select class="form-select" name="type">
                                    <option value="">{{ __('messages.all_types') }}</option>
                                    <option value="addition" {{ request('type') == 'addition' ? 'selected' : '' }}>
                                        {{ __('messages.stock_addition') }}</option>
                                    <option value="deduction" {{ request('type') == 'deduction' ? 'selected' : '' }}>
                                        {{ __('messages.stock_deduction') }}</option>
                                    <option value="initial_stock"
                                        {{ request('type') == 'initial_stock' ? 'selected' : '' }}>
                                        {{ __('messages.initial_stock') }}</option>
                                    <option value="correction" {{ request('type') == 'correction' ? 'selected' : '' }}>
                                        {{ __('messages.correction') }}</option>
                                    <option value="production_usage"
                                        {{ request('type') == 'production_usage' ? 'selected' : '' }}>
                                        {{ __('messages.production_usage') }}</option>
                                    <option value="breakage" {{ request('type') == 'breakage' ? 'selected' : '' }}>
                                        {{ __('messages.breakage') }}</option>
                                    <option value="manual_adjustment"
                                        {{ request('type') == 'manual_adjustment' ? 'selected' : '' }}>
                                        {{ __('messages.manual_adjustment') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">{{ __('messages.start_date') }}</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">{{ __('messages.end_date') }}</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2 align-self-end">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary flex-fill" type="submit"><i
                                            class="fas fa-filter me-1"></i></button>
                                    <a href="{{ route('stock_adjustments') }}"
                                        class="btn btn-outline-secondary flex-fill"><i class="fas fa-undo me-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div class="content-section">
                @if ($stockMovements->count())
                    <div class="table-responsive">
                        <table class="table modern-table align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.adjustment_date') }}</th>
                                    <th>{{ __('messages.raw_material_name') }}</th>
                                    <th>{{ __('messages.movement_type') }}</th>
                                    <th class="text-center">{{ __('messages.quantity_stock_unit') }}</th>
                                    <th>{{ __('messages.unit_price_at_movement_per_stock_unit') }}</th>
                                    <th>{{ __('messages.total_value') }}</th>
                                    <th>{{ __('messages.by_user') }}</th>
                                    <th>{{ __('messages.notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stockMovements as $movement)
                                    <tr>
                                        <td>{{ $movement->movement_date->format('d M Y, H:i') }}</td>
                                        <td>
                                            @if ($movement->rawMaterial)
                                                <a href="{{ route('raw_materials.show', $movement->rawMaterial->slug) }}">
                                                    {{ $movement->rawMaterial->name }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $movement->quantity > 0 ? 'success' : ($movement->quantity < 0 ? 'danger' : 'secondary') }}-subtle">
                                                {{ Str::title(str_replace('_', ' ', __('messages.' . $movement->type))) }}
                                            </span>
                                        </td>
                                        <td
                                            class="text-center {{ $movement->quantity > 0 ? 'text-success' : ($movement->quantity < 0 ? 'text-danger' : '') }}">
                                            {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity, 2, ',', '.') }}
                                            {{ $movement->rawMaterial->stock_unit ?? '' }}
                                        </td>
                                        <td>Rp{{ number_format($movement->unit_price_at_movement ?? 0, 2) }}
                                            /{{ $movement->rawMaterial->stock_unit ?? '' }}</td>
                                        <td>Rp{{ number_format(abs($movement->quantity) * ($movement->unit_price_at_movement ?? 0), 2) }}
                                        </td>
                                        <td>{{ $movement->user->name ?? 'System' }}</td>
                                        <td>{{ Str::limit($movement->notes, 50) ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $stockMovements->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('messages.no_stock_movement_history') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

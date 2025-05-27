@extends('layouts.master')

@section('title', __('messages.raw_material_details_for', ['name' => $rawMaterial->name]))

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
                                {{ $rawMaterial->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                            <span>{{ __('messages.raw_material_code_label', ['code' => $rawMaterial->code]) }}</strong></span>
                        </div>
                    </div>
                    <div class="price-display-header">
                        <span>{{ __('messages.raw_material_unit_price_per_unit', ['unit' => $rawMaterial->stock_unit]) }}</span>
                        <h4>Rp{{ number_format($rawMaterial->unit_price, 2) }}</h4>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('raw_materials') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-arrow-left me-1"></i> {{ __('messages.back_button') }}</a>
                        @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                            <a href="{{ route('raw_materials.edit', $rawMaterial->slug) }}" class="btn btn-primary"><i
                                    class="fas fa-edit me-1"></i> {{ __('messages.edit_button') }}</a>
                            <form method="POST" action="{{ route('raw_materials.delete', $rawMaterial->slug) }}"
                                onsubmit="return confirm('{{ __('messages.confirm_delete_action_cannot_be_undone', ['item' => Str::lower(__('messages.nav_inventory'))]) }}');"
                                style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>
                                    {{ __('messages.delete_button') }}</button>
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
                                <h5 class="content-block-title">{{ __('messages.image') }}</h5>
                                <img src="{{ Storage::url($rawMaterial->image_path) }}" alt="{{ $rawMaterial->name }}"
                                    class="img-fluid rounded border">
                            </div>
                        @endif
                        <div class="content-block mb-4">
                            <h5 class="content-block-title">{{ __('messages.description') }}</h5>
                            <div class="prose">
                                {!! $rawMaterial->description
                                    ? nl2br(e($rawMaterial->description))
                                    : '<p class="text-muted">' . __('messages.no_description_provided') . '</p>' !!}
                            </div>
                        </div>
                        @if ($rawMaterial->products && $rawMaterial->products->count() > 0)
                            <div class="content-block">
                                <h5 class="content-block-title">
                                    {{ __('messages.used_in_products_recipe_in_unit', ['unit' => $rawMaterial->usage_unit]) }}
                                </h5>
                                <div class="table-responsive">
                                    <table class="table modern-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('messages.product') }}</th>
                                                <th class="text-center">{{ __('messages.quantity_required') }}</th>
                                                <th class="text-end">{{ __('messages.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rawMaterial->products as $product)
                                                @php
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
                                                            class="btn btn-sm btn-outline-primary">{{ __('messages.view_product') }}</a>
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
                            <h5 class="content-block-title">{!! __('messages.inventory_management_jit') !!}
                                ({{ __('messages.quantity_in_unit_label', ['unit_label' => 'Kuantitas ' . $rawMaterial->stock_unit]) }})
                            </h5>
                            <ul class="info-list info-list-dense">
                                <li>
                                    <span class="label">{{ __('messages.current_stock') }}</span>
                                    <span
                                        class="value {{ $rawMaterial->stock <= ($rawMaterial->signal_point ?? 0) && $rawMaterial->is_active ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                        {{ number_format($rawMaterial->stock, 2, ',', '.') }}
                                        {{ $rawMaterial->stock_unit }}
                                    </span>
                                </li>
                                <li>
                                    <span class="label">{{ __('messages.raw_material_usage_unit_for_recipes') }}</span>
                                    <span class="value">
                                        {{ $rawMaterial->usage_unit ?? 'N/A' }}</span>
                                    </span>
                                </li>
                                <li>
                                    <span class="label">{{ __('messages.conversion_factor') }}</span>
                                    <span
                                        class="value">{{ __('messages.raw_material_conversion_info', ['stock_unit' => $rawMaterial->stock_unit, 'factor' => number_format($rawMaterial->conversion_factor, 2, ',', '.'), 'usage_unit' => $rawMaterial->usage_unit]) }}</span>
                                </li>
                                <li>
                                    <span class="label">{!! __('messages.raw_material_avg_daily_usage_calculated_show') !!}</span>
                                    <span class="value">{{ number_format($rawMaterial->average_daily_usage ?? 0, 2) }}
                                        {{ $rawMaterial->stock_unit }}
                                    </span>
                                </li>
                                <li>
                                    <span class="label">{{ __('messages.raw_material_replenish_quantity_show') }}</span>
                                    <span class="value">{{ number_format($rawMaterial->replenish_quantity ?? 0) }}
                                        {{ $rawMaterial->stock_unit }}</span>
                                </li>
                                <li>
                                    <span class="label">{{ __('messages.raw_material_lead_time_policy') }}</span>
                                    <span class="value">{{ $rawMaterial->lead_time ?? 0 }}
                                        {{ Str::lower(__('messages.lead_time_days')) }}</span>
                                </li>
                                <li>
                                    <span class="label">{{ __('messages.raw_material_safety_stock_days_policy') }}</span>
                                    <span class="value">{{ $rawMaterial->safety_stock_days ?? 0 }}
                                        {{ Str::lower(__('messages.lead_time_days')) }}</span>
                                </li>
                                <li>
                                    <span class="label">{!! __('messages.raw_material_safety_stock_calculated_show') !!}</span>
                                    <span class="value">{{ number_format($rawMaterial->safety_stock ?? 0, 2, ',', '.') }}
                                        {{ $rawMaterial->stock_unit }}</span>
                                </li>
                                <li>
                                    <span class="label">{!! __('messages.raw_material_signal_point_calculated_show') !!}</span>
                                    <span
                                        class="value fw-bold">{{ number_format($rawMaterial->signal_point ?? 0, 2, ',', '.') }}
                                        {{ $rawMaterial->stock_unit }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="content-block">
                            <h5 class="content-block-title">{{ __('messages.additional_details') }}</h5>
                            <ul class="info-list">
                                <li>
                                    <span class="label">{{ __('messages.raw_material_supplier') }}</span>
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
                                    <span class="label">{{ __('messages.created_at') }}</span>
                                    <span class="value">{{ $rawMaterial->created_at->format('d M Y, H:i') }}</span>
                                </li>
                                <li>
                                    <span class="label">{{ __('messages.updated_at') }}</span>
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

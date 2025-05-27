@extends('layouts.master')

@php
    $productInstance = $product ?? $billOfMaterials->first()?->product;
@endphp

@section('title', $productInstance ? __('messages.bom_details_for_product', ['product_name' => $productInstance->name]) : __('messages.bom_list_title'))

@section('content')
    <div class="container py-4">
        @if ($productInstance)
            <div class="modern-container">
                <div class="header-section-subtle">
                    @if ($productInstance->category)
                        <p class="header-category">{{ $productInstance->category->name }}</p>
                    @endif

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h2 class="page-title">{{ $productInstance->name }}</h2>
                            <div class="header-meta">
                                <span
                                    class="badge {{ $billOfMaterials->firstWhere('is_active', true) ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                    {{ $billOfMaterials->firstWhere('is_active', true) ? __('messages.bom_status_active') : __('messages.bom_status_inactive') }}
                                </span>
                                <span>{{ __('messages.bom_product_code_label', ['code' => $productInstance->code]) }}</span>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="{{ route('bill_of_materials') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_button') }}
                            </a>
                            @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                <a href="{{ route('bill_of_materials.edit', $productInstance->slug) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i> {{ __('messages.edit_button') }}
                                </a>
                                <form method="POST"
                                    action="{{ route('bill_of_materials.delete', $productInstance->slug) }}"
                                    onsubmit="return confirm('{{ __('messages.deactivate_bom_confirm') }}');"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-1"></i> {{ __('messages.deactivate_bom_button') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="content-block">
                                <h5 class="content-block-title">{{ __('messages.recipe_ingredients') }}</h5>
                                @if ($billOfMaterials->where('is_active', true)->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table modern-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('messages.raw_material_name') }}</th>
                                                    <th class="text-center">{{ __('messages.quantity') }} ({{ __('messages.usage_unit') }})</th>
                                                    <th class="text-end">{{ __('messages.bom_unit_price_per_stock_unit', ['unit' => $billOfMaterials->firstWhere('is_active', true)?->rawMaterial?->stock_unit ?? '']) }}</th>
                                                    <th class="text-end">{{ __('messages.bom_total_cost') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($billOfMaterials->where('is_active', true) as $index => $bom)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $bom->rawMaterial->name ?? '-' }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($bom->quantity, 2, ',', '.') }}
                                                            {{ $bom->rawMaterial->usage_unit ?? '' }}</td>
                                                        <td class="text-end">Rp
                                                            {{ number_format($bom->rawMaterial->unit_price ?? 0, 2) }}
                                                            /{{ $bom->rawMaterial->stock_unit ?? '' }}</td>
                                                        <td class="text-end fw-bold">Rp
                                                            {{ number_format($bom->total_cost, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-summary mt-3">
                                        <span>{{ __('messages.total_base_price') }}</span>
                                        <span class="price-value">Rp {{ number_format($base_price, 2) }}</span>
                                    </div>
                                @else
                                    <div class="alert alert-info">{{ __('messages.no_active_bom_for_product') }} <a
                                            href="{{ route('bill_of_materials.edit', $productInstance->slug) }}">{{ __('messages.setup_bom_link_text') }}</a></div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4">
                            @if ($productInstance->image_path)
                                <div class="content-block mb-4">
                                    <h5 class="content-block-title">{{ __('messages.product_image') }}</h5>
                                    <img src="{{ Storage::url($productInstance->image_path) }}"
                                        alt="{{ $productInstance->name }}" class="img-fluid rounded border">
                                </div>
                            @endif

                            <div class="content-block">
                                <h5 class="content-block-title">{{ __('messages.details') }}</h5>
                                <ul class="info-list">
                                    <li>
                                        <span class="label">{{ __('messages.base_price') }}</span>
                                        <span class="value">Rp {{ number_format($productInstance->base_price, 2) }}</span>
                                    </li>
                                    <li>
                                        <span class="label">{{ __('messages.selling_price') }}</span>
                                        <span class="value">Rp
                                            {{ number_format($productInstance->selling_price, 2) }}</span>
                                    </li>
                                    <li>
                                        <span class="label">{{ __('messages.created_at') }}</span>
                                        <span
                                            class="value">{{ $productInstance->created_at->format('d M Y, H:i') }}</span>
                                    </li>
                                    <li>
                                        <span class="label">{{ __('messages.updated_at') }}</span>
                                        <span
                                            class="value">{{ $productInstance->updated_at->format('d M Y, H:i') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="modern-container">
                <div class="content-section">
                    <div class="alert alert-warning text-center">
                        {{ __('messages.product_not_found_or_bom_not_set_up') }}
                        <a href="{{ route('bill_of_materials') }}">{{ __('messages.go_back_to_bom_list') }}</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
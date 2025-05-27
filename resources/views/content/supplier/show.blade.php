@extends('layouts.master')

@section('title', __('messages.supplier_details_title', ['name' => $supplier->name]))

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section-subtle">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h2 class="page-title">{{ $supplier->name }}</h2>
                        <div class="header-meta">
                            @if (isset($supplier->is_active))
                                <span class="badge {{ $supplier->is_active ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                    {{ $supplier->is_active ? __('messages.active') : __('messages.inactive') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('suppliers') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-arrow-left me-1"></i> {{ __('messages.back_button') }}</a>
                        @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                            <a href="{{ route('suppliers.edit', $supplier->slug) }}" class="btn btn-primary"><i
                                    class="fas fa-edit me-1"></i> {{ __('messages.edit_button') }}</a>
                            <form method="POST" action="{{ route('suppliers.delete', $supplier->slug) }}"
                                onsubmit="return confirm('{{ __('messages.confirm_delete_action_cannot_be_undone', ['item' => Str::lower(__('messages.nav_suppliers'))]) }}');" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> {{ __('messages.delete_button') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content-section">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="content-block mb-4">
                            <h5 class="content-block-title">{{ __('messages.contact_information') }}</h5>
                            <ul class="info-list">
                                <li><span class="label">{{ __('messages.contact_person') }}</span> <span
                                        class="value">{{ $supplier->contact_person ?: 'N/A' }}</span></li>
                                <li><span class="label">{{ __('messages.phone') }}</span> <span
                                        class="value">{{ $supplier->phone ?: 'N/A' }}</span></li>
                                <li><span class="label">{{ __('messages.email') }}</span> <span
                                        class="value">{{ $supplier->email ?: 'N/A' }}</span></li>
                            </ul>
                        </div>
                        <div class="content-block">
                            <h5 class="content-block-title">{{ __('messages.address_details') }}</h5>
                            <ul class="info-list">
                                <li><span class="label">{{ __('messages.full_address') }}</span> <span
                                        class="value">{{ $supplier->address ?: 'N/A' }}</span></li>
                                <li><span class="label">{{ __('messages.city') }}</span> <span
                                        class="value">{{ $supplier->city ?: 'N/A' }}</span></li>
                                <li><span class="label">{{ __('messages.state_province') }}</span> <span
                                        class="value">{{ $supplier->state ?: 'N/A' }}</span></li>
                                <li><span class="label">{{ __('messages.zip_postal_code') }}</span> <span
                                        class="value">{{ $supplier->zip_code ?: 'N/A' }}</span></li>
                                <li><span class="label">{{ __('messages.country') }}</span> <span
                                        class="value">{{ $supplier->country ?: 'N/A' }}</span></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        @if ($supplier->rawMaterial && $supplier->rawMaterial->count() > 0)
                            <div class="content-block mb-4">
                                <h5 class="content-block-title">{{ __('messages.supplied_raw_materials') }}</h5>
                                <div class="table-responsive">
                                    <table class="table modern-table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ __('messages.material_name') }}</th>
                                                <th class="text-end">{{ __('messages.unit_price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($supplier->rawMaterial as $material)
                                                <tr>
                                                    <td><a href="{{ route('raw_materials.show', $material->slug) }}"
                                                            class="text-decoration-none">{{ $material->name }}</a></td>
                                                    <td class="text-end">Rp{{ number_format($material->unit_price, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        <div class="content-block">
                            <h5 class="content-block-title">{{ __('messages.record_timestamps') }}</h5>
                            <ul class="info-list">
                                <li><span class="label">{{ __('messages.created_at') }}</span> <span
                                        class="value">{{ $supplier->created_at->format('d M Y, H:i') }}</span></li>
                                <li><span class="label">{{ __('messages.updated_at') }}</span> <span
                                        class="value">{{ $supplier->updated_at->format('d M Y, H:i') }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.master')

@section('title', __('messages.edit_supplier_title') . ': ' . $supplier->name)

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
             <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="page-title">{{ __('messages.edit_supplier_title') }}</h2>
                    <p class="header-subtitle mb-0">{{ __('messages.modifying_supplier', ['name' => $supplier->name]) }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('suppliers.show', $supplier->slug) }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white; padding: 0.6rem 1.2rem;">
                        <i class="fas fa-eye me-1"></i> {{ __('messages.view_button') }}
                    </a>
                    <a href="{{ route('suppliers') }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white; padding: 0.6rem 1.2rem;">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="content-section">
             @include('content.supplier.components.form', [
                'isEdit' => true,
                'supplier' => $supplier
            ])
        </div>
    </div>
</div>
@endsection
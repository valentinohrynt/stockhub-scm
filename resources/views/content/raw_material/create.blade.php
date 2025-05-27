@extends('layouts.master')

@section('title', __('messages.add_new_raw_material_title'))

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title">{{ __('messages.add_new_raw_material_title') }}</h2>
                <a href="{{ route('raw_materials') }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white;">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_list') }}
                </a>
            </div>
            <p class="header-subtitle">{{ __('messages.add_new_raw_material_subtitle') }}</p>
        </div>

        <div class="content-section">
            @include('content.raw_material.components.form', [
                'isEdit' => false,
                'categories' => $categories,
                'suppliers' => $suppliers
            ])
        </div>
    </div>
</div>
@endsection
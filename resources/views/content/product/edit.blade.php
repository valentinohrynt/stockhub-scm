@extends('layouts.master')

@section('title', __('messages.edit_product_title') . ': ' . $product->name)

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
             <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="page-title">{{ __('messages.edit_product_title') }}</h2>
                    <p class="header-subtitle mb-0">{{ __('messages.modifying_product', ['name' => $product->name]) }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('products.show', $product->slug) }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white; padding: 0.6rem 1.2rem;">
                        <i class="fas fa-eye me-1"></i> {{ __('messages.view_button') }}
                    </a>
                    <a href="{{ route('products') }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white; padding: 0.6rem 1.2rem;">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="content-section">
            @include('content.product.components.form', [
                'isEdit' => true,
                'product' => $product,
                'categories' => $categories
            ])
        </div>
    </div>
</div>
@endsection
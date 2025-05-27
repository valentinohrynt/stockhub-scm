@extends('layouts.master')

@section('title', 'Edit Bill of Material: ' . $product->name)

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
             <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title">Edit Recipe for: {{ $product->name }}</h2>
                <a href="{{ route('bill_of_materials') }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white;">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <p class="header-subtitle">Modify the ingredients and quantities for this product's recipe.</p>
        </div>

        <div class="content-section">
            @include('content.bill_of_material.components.form', [
                'isEdit' => true,
                'product' => $product, 
                'billOfMaterial' => $billOfMaterial,
                'products' => $products,
                'rawMaterials' => $rawMaterials
            ])
        </div>
    </div>
</div>
@endsection
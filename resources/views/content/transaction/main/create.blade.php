@extends('layouts.master')

@section('title', 'Add New Transaction')

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title">Add New Transaction (POS)</h2>
                <a href="{{ route('transactions') }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Transaction Menu
                </a>
            </div>
            <p class="header-subtitle">Enter customer details and add products to the order.</p>
        </div>

        <div class="content-section">
            @include('content.transaction.main.components.form', [
                'isEdit' => false
            ])
        </div>
    </div>
</div>
@endsection
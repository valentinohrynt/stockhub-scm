@extends('layouts.master')

@section('title', 'Add New Supplier')

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title">Add New Supplier</h2>
                <a href="{{ route('suppliers') }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white;">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <p class="header-subtitle">Enter the details for the new supplier.</p>
        </div>

        <div class="content-section">
            @include('content.supplier.components.form', [
                'isEdit' => false
            ])
        </div>
    </div>
</div>
@endsection
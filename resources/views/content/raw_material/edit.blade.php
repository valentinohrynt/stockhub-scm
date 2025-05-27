@extends('layouts.master')

@section('title', 'Edit Raw Material: ' . $rawMaterial->name)

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
             <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="page-title">Edit Raw Material</h2>
                    <p class="header-subtitle mb-0">Modifying: {{ $rawMaterial->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('raw_materials.show', $rawMaterial->slug) }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white; padding: 0.6rem 1.2rem;">
                        <i class="fas fa-eye me-1"></i> View
                    </a>
                    <a href="{{ route('raw_materials') }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white; padding: 0.6rem 1.2rem;">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="content-section">
            @include('content.raw_material.components.form', [
                'isEdit' => true,
                'rawMaterial' => $rawMaterial
            ])
        </div>
    </div>
</div>
@endsection
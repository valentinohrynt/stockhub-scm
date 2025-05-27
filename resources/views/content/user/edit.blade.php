@extends('layouts.master')

@section('title', 'Edit User: ' . $user->name)

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
             <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="page-title">Edit User</h2>
                    <p class="header-subtitle mb-0">Modifying: {{ $user->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.show', $user->id) }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white; padding: 0.6rem 1.2rem;">
                        <i class="fas fa-eye me-1"></i> View
                    </a>
                    <a href="{{ route('users') }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white; padding: 0.6rem 1.2rem;">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="content-section">
             @include('content.user.components.form', [
                'isEdit' => true,
                'user' => $user,
                'roles' => $roles
            ])
        </div>
    </div>
</div>
@endsection
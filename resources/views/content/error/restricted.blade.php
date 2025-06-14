@extends('layouts.master')

@section('title', __('messages.access_restricted_title'))

@push('styles')
<style>
    .restricted-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh; 
        text-align: center;
        padding: 2rem;
    }

    .restricted-icon {
        font-size: 5rem;
        color: var(--danger-color, #ef4444);
        margin-bottom: 1.5rem;
        animation: pulse-danger 2s infinite;
    }

    .restricted-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-primary, #111827); 
        margin-bottom: 0.75rem;
    }

    .restricted-message {
        font-size: 1.1rem;
        color: var(--text-secondary, #6b7280); 
        margin-bottom: 2rem;
        max-width: 500px;
    }

    .restricted-actions .btn {
        margin: 0 0.5rem;
    }

    @keyframes pulse-danger {
        0% {
            transform: scale(1);
            opacity: 0.7;
        }
        50% {
            transform: scale(1.1);
            opacity: 1;
        }
        100% {
            transform: scale(1);
            opacity: 0.7;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="modern-container">
        <div class="restricted-container">
            <div class="restricted-icon">
                <i class="fas fa-hand-paper"></i>
            </div>
            <h1 class="restricted-title">{{ __('messages.access_denied_title') }}</h1>

            @if (session('error'))
                <p class="restricted-message">{{ session('error') }}</p>
            @else
                <p class="restricted-message">
                    {{ __('messages.access_denied_message_default') }}
                    {{ __('messages.access_denied_message_contact_admin') }}
                </p>
            @endif

            <div class="restricted-actions">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-home me-1"></i> {{ __('messages.go_to_homepage_button') }}
                </a>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.go_back_button') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
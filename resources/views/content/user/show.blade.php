@extends('layouts.master')

@section('title', __('messages.user_details_page_title', ['name' => $user_data->name]))

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section-subtle">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h2 class="page-title">{{ $user_data->name }}</h2>
                        <div class="header-meta">
                           <span class="badge bg-info-subtle text-info-emphasis">{{ Str::title($user_data->role->name ?? 'N/A') }}</span>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('users') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_list') }}</a>
                        @if(auth()->user()->role->name == 'admin')
                            <a href="{{ route('users.edit', $user_data->id) }}" class="btn btn-primary"><i
                                    class="fas fa-edit me-1"></i> {{ __('messages.edit_button') }} {{ Str::lower(__('messages.nav_users')) }}</a>
                            <form method="POST" action="{{ route('users.delete', $user_data->id) }}"
                                onsubmit="return confirm('{{ __('messages.delete_user_confirm') }}');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> {{ __('messages.delete_button') }} {{ Str::lower(__('messages.nav_users')) }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content-section">
                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="content-block mb-4">
                            <h5 class="content-block-title">{{ __('messages.user_information') }}</h5>
                            <ul class="info-list">
                                <li><span class="label">{{ __('messages.full_name') }}</span> <span
                                        class="value">{{ $user_data->name ?: 'N/A' }}</span></li>
                                <li><span class="label">{{ __('messages.email_address') }}</span> <span
                                        class="value">{{ $user_data->email ?: 'N/A' }}</span></li>
                                <li><span class="label">{{ __('messages.role') }}</span> <span
                                        class="value">{{ Str::title($user_data->role->name ?? 'N/A') }}</span></li>
                                <li><span class="label">{{ __('messages.joined_on') }}</span> <span
                                        class="value">{{ $user_data->created_at ? $user_data->created_at->format('d M Y, H:i') : 'N/A' }}</span></li>
                                <li><span class="label">{{ __('messages.updated_at') }}</span> <span
                                        class="value">{{ $user_data->updated_at ? $user_data->updated_at->format('d M Y, H:i') : 'N/A' }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
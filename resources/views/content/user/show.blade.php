@extends('layouts.master')

@section('title', 'User Details: ' . $user_data->name) {{-- Menggunakan $user_data --}}

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section-subtle">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h2 class="page-title">{{ $user_data->name }}</h2> {{-- Menggunakan $user_data --}}
                        <div class="header-meta">
                           <span class="badge bg-info-subtle text-info-emphasis">{{ Str::title($user_data->role->name ?? 'N/A') }}</span> {{-- Menggunakan $user_data --}}
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('users') }}" class="btn btn-outline-secondary"><i {{-- Menggunakan route('users') --}}
                                class="fas fa-arrow-left me-1"></i> Back to List</a>
                        {{-- Tampilkan tombol Edit dan Delete hanya untuk admin --}}
                        @if(auth()->user()->role->name == 'admin')
                            <a href="{{ route('users.edit', $user_data->id) }}" class="btn btn-primary"><i {{-- Menggunakan $user_data --}}
                                    class="fas fa-edit me-1"></i> Edit User</a>
                            <form method="POST" action="{{ route('users.delete', $user_data->id) }}" {{-- Menggunakan route users.delete dan $user_data --}}
                                onsubmit="return confirm('Are you sure you want to delete this User? This action cannot be undone.');" style="display:inline;">
                                @csrf
                                @method('DELETE') {{-- Method diubah menjadi DELETE --}}
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Delete User</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content-section">
                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="content-block mb-4">
                            <h5 class="content-block-title">User Details</h5>
                            <ul class="info-list">
                                <li><span class="label">Full Name</span> <span
                                        class="value">{{ $user_data->name ?: 'N/A' }}</span></li> {{-- Menggunakan $user_data --}}
                                <li><span class="label">Email Address</span> <span
                                        class="value">{{ $user_data->email ?: 'N/A' }}</span></li> {{-- Menggunakan $user_data --}}
                                <li><span class="label">Role</span> <span
                                        class="value">{{ Str::title($user_data->role->name ?? 'N/A') }}</span></li> {{-- Menggunakan $user_data --}}
                                <li><span class="label">Joined On</span> <span
                                        class="value">{{ $user_data->created_at ? $user_data->created_at->format('d M Y, H:i') : 'N/A' }}</span></li> {{-- Menggunakan $user_data --}}
                                <li><span class="label">Last Updated</span> <span
                                        class="value">{{ $user_data->updated_at ? $user_data->updated_at->format('d M Y, H:i') : 'N/A' }}</span></li> {{-- Menggunakan $user_data --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
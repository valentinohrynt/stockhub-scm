@extends('layouts.master')

@section('title', 'User List')

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title mb-0">User List</h2>
                    @if(auth()->user()->role->name == 'admin')
                        <a href="{{ route('users.create') }}" class="add-btn">
                            <i class="fas fa-plus"></i> Add User
                        </a>
                    @endif
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-card">
                    <form action="{{ route('users') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" name="search"
                                        placeholder="Search by name or email..." value="{{ request('search') }}"
                                        aria-label="Search">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" name="role_id" aria-label="Filter by Role">
                                    <option value="">All Roles</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ Str::title($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary flex-fill" type="submit"><i
                                            class="fas fa-filter me-1"></i> Filter</button>
                                    <a href="{{ route('users') }}" class="btn btn-outline-secondary flex-fill"><i {{-- Menggunakan route('users') --}}
                                            class="fas fa-undo me-1"></i> Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-section">
                @if ($users->count())
                    <div class="table-responsive">
                        <table class="table modern-table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user_data)
                                    <tr>
                                        <td>{{ $users->firstItem() + $index }}</td>
                                        <td>{{ $user_data->name }}</td>
                                        <td>{{ $user_data->email }}</td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info-emphasis">{{ Str::title($user_data->role->name ?? 'N/A') }}</span>
                                        </td>
                                        <td>{{ $user_data->created_at->format('d M Y') }}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('users.show', $user_data->id) }}"
                                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>
                                                    View</a>
                                                @if(auth()->user()->role->name == 'admin')
                                                    <a href="{{ route('users.edit', $user_data->id) }}"
                                                        class="btn btn-sm btn-outline-secondary"><i
                                                            class="fas fa-edit me-1"></i> Edit</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No Users found matching your criteria.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
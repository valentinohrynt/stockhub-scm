@extends('layouts.master')

@section('title', 'Supplier List')

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title mb-0">Supplier List</h2>
                    @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                        <a href="{{ route('suppliers.create') }}" class="add-btn">
                            <i class="fas fa-plus"></i> Add Supplier
                        </a>
                    @endif
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-card">
                    <form action="{{ route('suppliers') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" name="search"
                                        placeholder="Search by name, email, or phone..." value="{{ request('search') }}"
                                        aria-label="Search">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" name="status" aria-label="Filter by Status">
                                    <option value="" {{ ($currentStatus ?? '') === '' ? 'selected' : '' }}>All
                                        Statuses</option>
                                    <option value="1" {{ ($currentStatus ?? '1') === '1' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ ($currentStatus ?? '') === '0' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary flex-fill" type="submit"><i
                                            class="fas fa-filter me-1"></i> Filter</button>
                                    <a href="{{ route('suppliers') }}" class="btn btn-outline-secondary"><i
                                            class="fas fa-undo me-1"></i> Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-section">
                @if ($suppliers->count())
                    <div class="table-responsive">
                        <table class="table modern-table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suppliers as $index => $supplier)
                                    <tr>
                                        <td>{{ $suppliers->firstItem() + $index }}</td>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->phone }}</td>
                                        <td>{{ $supplier->email }}</td>
                                        <td>{{ Str::limit($supplier->address, 40) }}</td>
                                        <td>
                                            @if ($supplier->is_active)
                                                <span class="badge bg-success-subtle">Active</span>
                                            @else
                                                <span class="badge bg-danger-subtle">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('suppliers.show', $supplier->slug) }}"
                                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>
                                                    View</a>
                                                @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) != 'staff')
                                                    <a href="{{ route('suppliers.edit', $supplier->slug) }}"
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
                        {{ $suppliers->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No Suppliers found matching your criteria.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
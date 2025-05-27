@extends('layouts.master')

@section('title', 'Transaction History')

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title mb-0">Transaction History</h2>
                <a href="{{ route('transactions') }}" class="add-btn" style="background: var(--primary-dark); color:white; border-color: var(--primary-dark)">
                    <i class="fas fa-arrow-left"></i> Back to POS
                </a>
            </div>
        </div>

        <div class="filter-section">
            <div class="filter-card">
                <form action="{{ route('transactions.history') }}" method="GET">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" name="search" 
                                       placeholder="Search by Code, Customer, or Table..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="order_status">
                                <option value="">All Order Statuses</option>
                                <option value="done" {{ request('order_status') == 'done' ? 'selected' : '' }}>Done</option>
                                <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="payment_status">
                                <option value="">All Payment Statuses</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-primary flex-fill" type="submit"><i class="fas fa-filter me-1"></i> Filter</button>
                                <a href="{{ route('transactions.history') }}" class="btn btn-outline-secondary flex-fill"><i class="fas fa-undo me-1"></i> Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="content-section">
            @if ($pastTransactions->count())
            <div class="table-responsive">
                <table class="table modern-table align-middle">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Customer / Table</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-center">Payment</th>
                            <th class="text-center">Order Status</th>
                            <th>Time</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pastTransactions as $transaction)
                            <tr>
                                <td>
                                    <a href="{{ route('transactions.show', $transaction->id) }}" class="fw-bold text-decoration-none text-primary">{{ $transaction->code }}</a>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $transaction->customer_name ?: 'N/A' }}</div>
                                    <small class="text-muted">Table: {{ $transaction->table_number ?: '-' }}</small>
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $transaction->payment_status == 'paid' ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                        {{ Str::ucfirst($transaction->payment_status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusClass = 'bg-secondary-subtle';
                                        if ($transaction->status == 'done') $statusClass = 'bg-success-subtle';
                                        if ($transaction->status == 'cancelled') $statusClass = 'bg-danger-subtle';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ Str::ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $transaction->created_at->format('d M Y, H:i') }}</div>
                                    <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $pastTransactions->links() }}
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                No transaction history found matching your criteria.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
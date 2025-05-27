@extends('layouts.master')

@section('title', 'Transactions Hub')

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <h2 class="page-title mb-0">Transaction Menu</h2>
                <p class="header-subtitle">Create new orders or view ongoing and past transactions.</p>
            </div>

            <div class="alert-section">
                @if (session('success'))
                    <div class="modern-alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="modern-alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>

            <div class="menu-section">
                <div class="row g-4">
                    <div class="col-md-6">
                        <a href="{{ route('transactions.create') }}" class="text-decoration-none">
                            <div class="menu-card card-cashier">
                                <div class="menu-card-icon"><i class="fas fa-cash-register"></i></div>
                                <div class="menu-card-content">
                                    <h5 class="menu-card-title">Cashier (POS)</h5>
                                    <p class="menu-card-text">Create new orders and process payments.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('transactions.history') }}" class="text-decoration-none">
                            <div class="menu-card card-history">
                                <div class="menu-card-icon"><i class="fas fa-history"></i></div>
                                <div class="menu-card-content">
                                    <h5 class="menu-card-title">Transaction History</h5>
                                    <p class="menu-card-text">View completed and past transactions.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <h3 class="section-title">Ongoing Transactions</h3>
                @if (isset($ongoingTransactions) && !$ongoingTransactions->isEmpty())
                    <div class="table-responsive">
                        <table class="table modern-table align-middle">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Customer / Table</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Payment</th>
                                    <th>Time</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ongoingTransactions as $transaction)
                                    <tr>
                                        <td>
                                            <a href="{{ route('transactions.show', $transaction->id) }}"
                                                class="fw-bold text-decoration-none text-primary">{{ $transaction->code }}</a>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $transaction->customer_name ?: 'N/A' }}</div>
                                            <small class="text-muted">Table: {{ $transaction->table_number ?: '-' }}</small>
                                        </td>
                                        <td class="text-end fw-bold">Rp
                                            {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge {{ $transaction->payment_status == 'paid' ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                                {{ Str::ucfirst($transaction->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $transaction->created_at->format('H:i') }}</div>
                                            <small
                                                class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('transactions.show', $transaction->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('transactions.updateStatus', $transaction->id) }}"
                                                    method="POST" style="display:inline;"
                                                    onsubmit="return confirm('Mark this transaction as DONE?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="done">
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                        title="Mark as Done">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-check-circle me-2"></i> No ongoing transactions at the moment.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

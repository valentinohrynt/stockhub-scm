@extends('layouts.master')

@section('title', 'Home')

@section('content')
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="container">
                <h1 class="dashboard-title">Welcome to StockHub</h1>
                <p class="dashboard-subtitle">Your central command center for managing the cafe inventory.</p>
            </div>
        </div>

        <div class="container py-4">
            <div class="row g-4">
                <div class="col-lg-9">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                        <div class="col">
                            <a href="{{ route('stock_adjustments.create') }}" class="text-decoration-none">
                                <div class="dashboard-card h-100">
                                    <div class="dashboard-card-icon" style="--icon-bg: #FFF7ED; --icon-color: #F97316;">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <h5 class="dashboard-card-title">Stock Adjustment</h5>
                                    <p class="dashboard-card-text">Manually record stock increases or decreases.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('products') }}" class="text-decoration-none">
                                <div class="dashboard-card h-100">
                                    <div class="dashboard-card-icon" style="--icon-bg: #DCFCE7; --icon-color: #22C55E;">
                                        <i class="fas fa-coffee"></i>
                                    </div>
                                    <h5 class="dashboard-card-title">Products</h5>
                                    <p class="dashboard-card-text">Manage your cafe products like drinks and snacks.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('raw_materials') }}" class="text-decoration-none">
                                <div class="dashboard-card h-100">
                                    <div class="dashboard-card-icon" style="--icon-bg: #FEF3C7; --icon-color: #F59E0B;">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <h5 class="dashboard-card-title">Raw Materials</h5>
                                    <p class="dashboard-card-text">Track and update your inventory and stock levels.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('bill_of_materials') }}" class="text-decoration-none">
                                <div class="dashboard-card h-100">
                                    <div class="dashboard-card-icon" style="--icon-bg: #E0E7FF; --icon-color: #6366F1;">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <h5 class="dashboard-card-title">Recipes (BOM)</h5>
                                    <p class="dashboard-card-text">Define recipes and material usage for products.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('suppliers') }}" class="text-decoration-none">
                                <div class="dashboard-card h-100">
                                    <div class="dashboard-card-icon" style="--icon-bg: #FEE2E2; --icon-color: #EF4444;">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <h5 class="dashboard-card-title">Suppliers</h5>
                                    <p class="dashboard-card-text">View and manage your raw material suppliers.</p>
                                </div>
                            </a>
                        </div>
                        @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) == 'admin')
                            <div class="col">
                                <a href="{{ route('users') }}" class="text-decoration-none">
                                    <div class="dashboard-card h-100">
                                        <div class="dashboard-card-icon" style="--icon-bg: #EDE9FE; --icon-color: #8B5CF6;">
                                            <i class="fas fa-users-cog"></i>
                                        </div>
                                        <h5 class="dashboard-card-title">Users</h5>
                                        <p class="dashboard-card-text">Manage user accounts and their roles.</p>
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="notification-panel">
                        <div class="notification-panel-header">
                            <h6 class="notification-panel-title"><i class="fas fa-bell"></i> JIT Signals</h6>
                            @if (isset($dashboardUnreadJitNotifications) && $dashboardUnreadJitNotifications->count() > 0)
                                <span
                                    class="badge notification-count">{{ $dashboardUnreadJitNotifications->count() }}</span>
                            @endif
                        </div>
                        @if (isset($dashboardUnreadJitNotifications) && $dashboardUnreadJitNotifications->isNotEmpty())
                            <div class="notification-panel-body">
                                @foreach ($dashboardUnreadJitNotifications as $notification)
                                    <a href="{{ route('jit_notifications.mark_as_read', $notification->id) }}"
                                        class="notification-item">
                                        <div class="notification-item-icon">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                        <div class="notification-item-content">
                                            <p class="notification-item-title">
                                                Order: {{ $notification->rawMaterial->name ?? 'Item' }}
                                            </p>
                                            <p class="notification-item-message">
                                                {{ Str::limit($notification->message, 50) }}</p>
                                            <small
                                                class="notification-item-time">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="notification-panel-empty">
                                <div class="icon"><i class="fas fa-check-circle"></i></div>
                                <p class="title">All Good!</p>
                                <p class="subtitle">No active JIT signals. Inventory is optimal.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
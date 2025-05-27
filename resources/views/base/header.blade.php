<header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Cafe<span>Hub</span></a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <button class="mobile-menu-close d-lg-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Close menu">
                    <i class="fas fa-times"></i>
                </button>
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('home') || request()->is('/') ? 'active' : '' }}"
                            href="{{ url('/') }}">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('transactions*') ? 'active' : '' }}"
                            href="{{ route('transactions') }}">
                            <i class="fas fa-cash-register me-1"></i> Transactions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}"
                            href="{{ route('products') }}">
                            <i class="fas fa-mug-hot me-1"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('raw-materials*') ? 'active' : '' }}"
                            href="{{ route('raw_materials') }}">
                            <i class="fas fa-boxes me-1"></i> Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('bill-of-materials*') ? 'active' : '' }}"
                            href="{{ route('bill_of_materials') }}">
                            <i class="fas fa-clipboard-list me-1"></i> Recipes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}"
                            href="{{ route('suppliers') }}">
                            <i class="fas fa-truck me-1"></i> Suppliers
                        </a>
                    </li>
                    @if(Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) == 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}"
                                href="{{ route('users') }}">
                                <i class="fas fa-users me-1"></i> Users
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown me-3">
                        <a class="nav-link position-relative" href="#" role="button" id="notificationsDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell fs-5"></i>
                            @if (isset($unreadJitNotificationCountGlobal) && $unreadJitNotificationCountGlobal > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger navbar-notification-badge">
                                    {{ $unreadJitNotificationCountGlobal }}
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationsDropdown"
                            style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                            <li>
                                <h6 class="dropdown-header">JIT Re-Order Signals</h6>
                            </li>
                            @if (isset($unreadJitNotificationsGlobal_navbar) && $unreadJitNotificationsGlobal_navbar->count() > 0)
                                @foreach ($unreadJitNotificationsGlobal_navbar as $notification)
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('jit_notifications.mark_as_read', $notification->id) }}"
                                            style="white-space: normal; font-size: 0.9rem;">
                                            <div class="fw-bold">
                                                @if ($notification->rawMaterial)
                                                    {{ $notification->rawMaterial->name }}
                                                @else
                                                    Attention Needed
                                                @endif
                                            </div>
                                            <div class="small text-muted">
                                                {{ Str::limit($notification->message, 100) }}
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <i
                                                    class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li>
                                    <p class="dropdown-item text-muted mb-0">No new JIT signals.</p>
                                </li>
                            @endif
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-center" href="{{ route('home') }}">View all JIT
                                    Signals</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <a href="#" class="user-avatar" role="button" id="userDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=059669&color=fff&size=38"
                                alt="{{ auth()->user()->name ?? 'User' }}">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li>
                                <h6 class="dropdown-header">Hey, {{ auth()->user()->name ?? 'Guest' }}</h6>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="GET" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
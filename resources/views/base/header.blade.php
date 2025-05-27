<header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand d-lg-none" href="{{ url('/') }}">{{ __('messages.app_name') }}<span></span></a>
            <a class="navbar-brand d-none d-lg-block"
                href="{{ url('/') }}">{{ __('messages.app_name') }}<span></span></a> 

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="mobile-sidebar-fixed-header d-lg-none">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <a class="navbar-brand-sidebar"
                            href="{{ url('/') }}">{{ __('messages.app_name') }}<span></span></a>
                        <div class="mobile-actions-topbar d-flex align-items-center">
                            <div class="dropdown me-2">
                                <a class="nav-link position-relative" href="#" role="button"
                                    id="mobileNotificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell fs-5"></i>
                                    @if (isset($unreadJitNotificationCountGlobal) && $unreadJitNotificationCountGlobal > 0)
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger navbar-notification-badge">
                                            {{ $unreadJitNotificationCountGlobal }}
                                        </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow"
                                    aria-labelledby="mobileNotificationsDropdown"
                                    style="min-width: 300px; max-height: 350px; overflow-y: auto;">
                                    <li>
                                        <h6 class="dropdown-header">{{ __('messages.jit_reorder_signals') }}</h6>
                                    </li>
                                    @forelse ($unreadJitNotificationsGlobal_navbar ?? [] as $notification)
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('jit_notifications.mark_as_read', $notification->id) }}"
                                                style="white-space: normal; font-size: 0.9rem;">
                                                <div class="fw-bold">
                                                    {{ $notification->rawMaterial->name ?? __('messages.attention_needed') }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ Str::limit($notification->message, 100) }}</div>
                                                <div class="small text-muted mt-1"><i
                                                        class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li>
                                            <p class="dropdown-item text-muted mb-0">
                                                {{ __('messages.no_new_jit_signals') }}</p>
                                        </li>
                                    @endforelse
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-center"
                                            href="{{ route('home') }}">{{ __('messages.view_all_jit_signals') }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="user-avatar" role="button" id="mobileUserDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=4F46E5&color=fff&size=32"
                                        alt="{{ auth()->user()->name ?? 'User' }}" style="width: 32px; height: 32px;">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="mobileUserDropdown">
                                    <li>
                                        <h6 class="dropdown-header">
                                            {{ __('messages.hey_user', ['name' => auth()->user()->name ?? 'Tamu']) }}
                                        </h6>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>{{ __('messages.logout_button') }}
                                        </a>
                                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="GET"
                                            class="d-none">@csrf</form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <button class="mobile-menu-close btn-close" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="true"
                            aria-label="Close menu"></button>
                    </div>
                </div>

                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('home') || request()->is('/') ? 'active' : '' }}"
                            href="{{ url('/') }}">
                            <i class="fas fa-home me-1"></i> {{ __('messages.nav_home') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('stock-adjustments*') ? 'active' : '' }}"
                            href="{{ route('stock_adjustments') }}">
                            <i class="fas fa-exchange-alt me-1"></i> {{ __('messages.nav_stock_adjustment') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}"
                            href="{{ route('products') }}">
                            <i class="fas fa-mug-hot me-1"></i> {{ __('messages.nav_products') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('raw-materials*') ? 'active' : '' }}"
                            href="{{ route('raw_materials') }}">
                            <i class="fas fa-boxes me-1"></i> {{ __('messages.nav_inventory') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('bill-of-materials*') ? 'active' : '' }}"
                            href="{{ route('bill_of_materials') }}">
                            <i class="fas fa-clipboard-list me-1"></i> {{ __('messages.nav_recipes') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}"
                            href="{{ route('suppliers') }}">
                            <i class="fas fa-truck me-1"></i> {{ __('messages.nav_suppliers') }}
                        </a>
                    </li>
                    @if (Auth::check() && Auth::user()->role && strtolower(Auth::user()->role->name) == 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}"
                                href="{{ route('users') }}">
                                <i class="fas fa-users me-1"></i> {{ __('messages.nav_users') }}
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="d-none d-lg-flex align-items-center ms-auto">
                    <div class="dropdown me-3">
                        <a class="nav-link position-relative" href="#" role="button"
                            id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell fs-5"></i>
                            @if (isset($unreadJitNotificationCountGlobal) && $unreadJitNotificationCountGlobal > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger navbar-notification-badge">
                                    {{ $unreadJitNotificationCountGlobal }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationsDropdown"
                            style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                            <li>
                                <h6 class="dropdown-header">{{ __('messages.jit_reorder_signals') }}</h6>
                            </li>
                            @forelse ($unreadJitNotificationsGlobal_navbar ?? [] as $notification)
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('jit_notifications.mark_as_read', $notification->id) }}"
                                        style="white-space: normal; font-size: 0.9rem;">
                                        <div class="fw-bold">
                                            {{ $notification->rawMaterial->name ?? __('messages.attention_needed') }}
                                        </div>
                                        <div class="small text-muted">{{ Str::limit($notification->message, 100) }}
                                        </div>
                                        <div class="small text-muted mt-1"><i
                                                class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li>
                                    <p class="dropdown-item text-muted mb-0">{{ __('messages.no_new_jit_signals') }}
                                    </p>
                                </li>
                            @endforelse
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-center"
                                    href="{{ route('home') }}">{{ __('messages.view_all_jit_signals') }}</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <a href="#" class="user-avatar" role="button" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=4F46E5&color=fff&size=38"
                                alt="{{ auth()->user()->name ?? 'User' }}">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li>
                                <h6 class="dropdown-header">
                                    {{ __('messages.hey_user', ['name' => auth()->user()->name ?? 'Tamu']) }}</h6>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form-desktop').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>{{ __('messages.logout_button') }}
                                </a>
                                <form id="logout-form-desktop" action="{{ route('logout') }}" method="GET"
                                    class="d-none">@csrf</form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

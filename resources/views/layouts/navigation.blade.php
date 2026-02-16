<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold fs-4" href="{{ route('dashboard') }}">
            <i class="fas fa-graduation-cap me-2 text-primary"></i>
            ACADEMOS
        </a>

        <!-- Toggle mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu Desktop -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>

                @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/admin/reports"><i class="fas fa-file-alt me-2"></i>Tous les rapports</a></li>
                            <li><a class="dropdown-item" href="/admin/users"><i class="fas fa-users me-2"></i>Utilisateurs</a></li>
                            <li><a class="dropdown-item" href="/admin/stats"><i class="fas fa-chart-bar me-2"></i>Stats</a></li>
                        </ul>
                    </li>
                @endif
            </ul>

            <!-- Profil Dropdown -->
            <ul class="navbar-nav ms-3">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=007bff&color=fff&size=32&rounded=true"
                             class="rounded-circle me-2" width="32" height="32" alt="">
                        <span class="d-none d-md-inline">{{ Str::limit(auth()->user()->name, 20) }}</span>
                        <i class="fas fa-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="fas fa-user text-primary me-2"></i>
                                {{ auth()->user()->name }}
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <span class="dropdown-item-text text-success px-3 py-2">
                                <i class="fas fa-mask me-2"></i>
                                {{ auth()->user()->roles->first()->name ?? 'Aucun rôle' }}
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2 text-danger"></i>
                                    Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

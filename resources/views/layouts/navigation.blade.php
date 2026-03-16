<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom p-0 topbar">
    <div class="container-fluid p-0" style="max-height: 60px;">
        <!-- Menu Desktop -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/academo.svg') }}" alt="Logo Academos" width="250">
            </a>
            <ul class="navbar-nav  me-auto" style="margin-left: 33% !important;">
                <li class="nav-item">
                    <a class="nav-link fw-bold {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        Bienvenu <span class="text-success" >sur</span> ACADEMOS
                    </a>
                </li>
            </ul>

            <!-- Profil Dropdown -->
            <ul class="navbar-nav">
                <li class="">
                    <a class="nav-link d-flex align-items-center text-warning" href="#" role="button">
                        <i class="fa fa-bell"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav  ms-1 me-4">
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center" href="#" role="button"
                        data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=007bff&color=fff&size=32&rounded=true"
                            class="rounded-circle me-2" width="32" height="32" alt="">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="fas fa-user text-primary me-2"></i>
                                {{ auth()->user()->name }}
                            </h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <span class="dropdown-item-text text-success px-3 py-2">
                                <i class="fas fa-mask me-2"></i>
                                {{ auth()->user()->roles->first()->name ?? 'Aucun rôle' }}
                            </span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
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

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom p-0 topbar">
    <div class="container-fluid p-0" style="max-height: 60px;">
        <!-- Menu Desktop -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Logo - Route conditionnelle selon le rôle -->
           <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/academo.svg') }}" alt="Logo Academos" width="250">
            </a>

            <!-- Lien Bienvenue - Route conditionnelle -->
            <ul class="navbar-nav me-auto" style="margin-left: 33% !important;">
                <li class="nav-item">
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                        <a class="nav-link fw-bold {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS Admin
                        </a>
                    @elseif(auth()->user()->hasRole('teacher'))
                        <a class="nav-link fw-bold {{ request()->routeIs('teacher.*') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS Enseignant
                        </a>
                    @elseif(auth()->user()->hasRole('student'))
                        <a class="nav-link fw-bold {{ request()->routeIs('student.*') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS Étudiant
                        </a>
                    @else
                        <a class="nav-link fw-bold {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS
                        </a>
                    @endif
                </li>
            </ul>

            <!-- Notifications -->
            <ul class="navbar-nav">
                <li class="">
                    <a class="nav-link d-flex align-items-center text-warning" href="#" role="button">
                        <i class="fa fa-bell"></i>
                    </a>
                </li>
            </ul>

            <!-- Profil Dropdown -->
            <ul class="navbar-nav ms-1 me-4">
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
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
                                {{ ucfirst(auth()->user()->roles->first()->name ?? 'Utilisateur') }}
                            </span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        {{-- Lien Profil conditionnel --}}
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                            <li><a class="dropdown-item" href="{{ url('/admin/profile') }}">
                                <i class="fas fa-user-circle me-2"></i>Mon profil Admin
                            </a></li>
                        @elseif(auth()->user()->hasRole('teacher'))
                            <li><a class="dropdown-item" href="{{ url('/teacher/profile') }}">
                                <i class="fas fa-user-circle me-2"></i>Mon profil
                            </a></li>
                        @else
                            <li><a class="dropdown-item" href="{{ url('/admin/profile') }}">
                                <i class="fas fa-user-circle me-2"></i>Mon profil
                            </a></li>
                        @endif
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

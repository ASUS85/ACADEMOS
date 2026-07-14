<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom p-0 topbar">
    <div class="container-fluid p-0 px-3 px-lg-4" style="max-height: 60px;">
        @php
        $currentUser = auth()->user();
        $hasNotificationsTable = \Illuminate\Support\Facades\Schema::hasTable('notifications');
        $unreadCount = $currentUser && $hasNotificationsTable && method_exists($currentUser, 'unreadNotifications') ? $currentUser->unreadNotifications->count() : 0;
        $profileRoute = $currentUser?->hasRole('admin') || $currentUser?->hasRole('superadmin')
        ? route('admin.profile.admin')
        : ($currentUser?->hasRole('teacher')
        ? route('teacher.profile')
        : ($currentUser?->hasRole('student') ? route('student.profile.student') : route('profile.edit')));
        @endphp
        <div class="d-flex align-items-center w-100 gap-3">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none flex-shrink-0">
                <img src="{{ asset('images/academo.svg') }}" alt="ACADEMOS" style="height: 34px; width: auto;" class="me-2">
                <span class="fw-bold text-primary d-none d-md-inline">ACADEMOS</span>
            </a>

            <!-- Menu Desktop -->
            <div class="collapse navbar-collapse show" id="navbarNav">
                <!-- Lien Bienvenue - Route conditionnelle -->
                <ul class="navbar-nav me-auto justify-content-center flex-grow-1" style="margin-left: 0 !important;">
                    <li class="nav-item">
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                        <a class="nav-link fw-bold {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS Admin
                        </a>
                        @elseif(auth()->user()->hasRole('teacher'))
                        <a class="nav-link fw-bold {{ request()->routeIs('teacher.*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS Enseignant
                        </a>
                        @elseif(auth()->user()->hasRole('student'))
                        <a class="nav-link fw-bold {{ request()->routeIs('student.*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS Étudiant
                        </a>
                        @else
                        <a class="nav-link fw-bold {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS
                        </a>
                        @endif
                    </li>
                </ul>

                <!-- Notifications -->
                <ul class="navbar-nav me-2">
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative d-flex align-items-center text-warning" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-bell"></i>
                            @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: .6rem;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 p-2" style="min-width: 320px;">
                            <li class="px-3 py-2">
                                <div class="fw-bold">Notifications</div>
                                <small class="text-muted">{{ $unreadCount > 0 ? $unreadCount.' notification(s) non lue(s)' : 'Aucune notification non lue' }}</small>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2" href="{{ route('dashboard') }}">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fa fa-file text-primary mt-1"></i>
                                        <div>
                                            <div class="fw-semibold">Consulter le tableau de bord</div>
                                            <small class="text-muted">Accès rapide aux données du rôle courant</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2" href="{{ $profileRoute }}">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fa fa-user text-success mt-1"></i>
                                        <div>
                                            <div class="fw-semibold">Ouvrir le profil</div>
                                            <small class="text-muted">Modifier vos informations personnelles</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fa fa-sign-out-alt text-danger mt-1"></i>
                                        <div>
                                            <div class="fw-semibold">Déconnexion</div>
                                            <small class="text-muted">Fermer la session courante</small>
                                        </div>
                                    </div>
                                </a>
                                <form id="logout-form-nav" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Profil Dropdown -->
                <ul class="navbar-nav ms-1 me-4">
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name={{ $currentUser->name }}&background=007bff&color=fff&size=32&rounded=true" class="rounded-circle me-2" width="32" height="32" alt="">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    {{ $currentUser->name }}
                                </h6>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <span class="dropdown-item-text text-success px-3 py-2">
                                    <i class="fas fa-mask me-2"></i>
                                    {{ ucfirst($currentUser->roles->first()->name ?? 'Utilisateur') }}
                                </span>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            {{-- Lien Profil conditionnel --}}
                            @if($currentUser->hasRole('admin') || $currentUser->hasRole('superadmin'))
                            <li><a class="dropdown-item" href="{{ route('admin.profile.admin') }}">
                                    <i class="fas fa-user-circle me-2"></i>Mon profil Admin
                                </a></li>
                            @elseif($currentUser->hasRole('teacher'))
                            <li><a class="dropdown-item" href="{{ route('teacher.profile') }}">
                                    <i class="fas fa-user-circle me-2"></i>Mon profil
                                </a></li>
                            @else
                            <li><a class="dropdown-item" href="{{ route('admin.profile.admin') }}">
                                    <i class="fas fa-user-circle me-2"></i>Mon profil
                                </a></li>
                            @endif
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
    </div>
</nav>

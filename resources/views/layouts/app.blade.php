<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ACADEMOS - Système Gestion Rapports</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts (optionnel) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #EEECF3;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            height: 60px;
        }

        .sidebar {
            width: 75px;
            min-height: 100vh;
            background: linear-gradient(to bottom, #0c337c, #1b75eb);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            top: 60px;
            left: 0;
            z-index: 1040;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .sidebar.expanded {
            width: 200px;
        }

        /* Style du bouton Toggle */
        #toggleSidebar {
            cursor: pointer;
            padding: 10px 0;
            transition: background 0.3s;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        #toggleSidebar:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        #toggleSidebar i {
            font-size: 1.5rem;
            transition: transform 0.5s ease;
            /* Animation de rotation */
        }

        /* Rotation de l'icône quand le menu est ouvert */
        .sidebar.expanded #toggleSidebar i {
            transform: rotate(180deg);
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 30px;
            transition: all 0.2s;
            white-space: nowrap;
            /* Évite que le texte saute à la ligne */
        }

        .sidebar a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            padding-left: 20px;
            /* Petit effet de glissement au survol */
        }

        .sidebar a i {
            font-size: 18px;
            min-width: 40px;
            text-align: center;
        }

        .sidebar .menu-text {
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .sidebar.expanded .menu-text {
            opacity: 1;
            pointer-events: auto;
            margin-left: 10px;
        }

        .content-area {
            position: relative;
            margin-top: 60px;
            margin-left: 80px;
            padding: 30px;
            transition: margin-left 0.3s;
        }

        .sidebar.expanded~.content-area {
            margin-left: 180px;
            margin-top: 60px;
        }

        .mt-auto-custom {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 70px;
            /* Pour éviter que ce soit collé au bord si la sidebar est longue */
        }

        /* Style spécifique pour le bouton logout (bouton transparent qui ressemble aux liens) */
        .logout-btn {
            background: none;
            border: none;
            width: 100%;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            padding: 15px;
            transition: all 0.2s;
            text-align: center;
        }

        .logout-btn:hover {
            color: #ff4d4d;
            /* Rouge clair au survol */
            background: rgba(255, 255, 255, 0.1);
        }

        /* Loader ciblé sur content-area uniquement */
        #globalLoader {
            position: absolute;
            inset: 0;
            z-index: 100;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #globalLoader.loading {
            pointer-events: auto;
            opacity: 1;
        }

        #globalLoader .loader-overlay {
            position: absolute;
            inset: 0;
            background: rgba(248, 250, 252, 0.42);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #globalLoader .loader-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            color: #36d17c;
            font-weight: 600;
        }

        #globalLoader .loader-spinner {
            width: 100px;
            height: 100px;
            border-width: 3px;
            font-weight: 600;
        }

    </style>
</head>

<body>
    <!-- TOPBAR -->
    {{-- <nav class="navbar navbar-dark bg-light px-4">
        <img src="{{ asset('images/logo1.png') }}"
    alt="Logo Academos"
    width="80"
    class="me-2">

    <div class="text-dark">
        <i class="fa fa-user"></i>
        {{ Auth::user()->name ?? 'Utilisateur' }}
    </div>
    </nav>
    @include('layouts.navigation') --}}


    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom p-0 topbar">
        <div class="container-fluid p-0" style="max-height: 60px;">
            <!-- Menu Desktop -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Logo - Route conditionnelle selon le rôle -->
                {{-- <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/academo.svg') }}" alt="Logo Academos" width="250">
                </a> --}}

                <!-- Lien Bienvenue - Route conditionnelle -->
                <ul class="navbar-nav me-auto" style="margin-left: 33% !important;">
                    <li class="nav-item">
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                        <a class="nav-link fw-bold {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('dashboard') }}"  data-loader-target="#globalLoader">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS Admin
                        </a>
                        @elseif(auth()->user()->hasRole('teacher'))
                        <a class="nav-link fw-bold {{ request()->routeIs('teacher.*') ? 'active' : '' }}" href="{{ route('dashboard') }}"  data-loader-target="#globalLoader">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS Enseignant
                        </a>
                        @elseif(auth()->user()->hasRole('student'))
                        <a class="nav-link fw-bold {{ request()->routeIs('student.*') ? 'active' : '' }}" href="{{ route('dashboard') }}"  data-loader-target="#globalLoader">
                            Bienvenu <span class="text-success">sur</span> ACADEMOS Étudiant
                        </a>
                        @else
                        <a class="nav-link fw-bold {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"  data-loader-target="#globalLoader">
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
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=007bff&color=fff&size=32&rounded=true" class="rounded-circle me-2" width="32" height="32" alt="">
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
                            <li><a class="dropdown-item" href="{{ url('/admin/profile') }}"  data-loader-target="#globalLoader">
                                    <i class="fas fa-user-circle me-2"></i>Mon profil Admin
                                </a></li>
                            @elseif(auth()->user()->hasRole('teacher'))
                            <li><a class="dropdown-item" href="{{ url('/teacher/profile') }}"  data-loader-target="#globalLoader">
                                    <i class="fas fa-user-circle me-2"></i>Mon profil
                                </a></li>
                            @else
                            <li><a class="dropdown-item" href="{{ url('/admin/profile') }}"  data-loader-target="#globalLoader">
                                    <i class="fas fa-user-circle me-2"></i>Mon profil
                                </a></li>
                            @endif
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item"  data-loader-target="#globalLoader">
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


    <!-- SIDEBAR -->

    <div class="sidebar" id="sidebar">

        <div id="toggleSidebar" class="mb-4 text-light d-flex justify-content-center align-items-center">
            <i class="bi bi-list"></i>
        </div>

        <a href="{{ route('dashboard') }}"  data-loader-target="#globalLoader">
            <i class="fa fa-house"></i>
            <span class="menu-text">Dashboard</span>
        </a>

        @switch(Auth::user()->role_name)
        @case('admin')
        <a href="{{ url('/admin/users') }}"  data-loader-target="#globalLoader">
            <i class="fa fa-users"></i>
            <span class="menu-text">Utilisateurs</span>
        </a>
        <a href="{{ url('/admin/reports') }}"  data-loader-target="#globalLoader">
            <i class="fa fa-file"></i>
            <span class="menu-text">Rapports</span>
        </a>
        <a href="{{ url('/admin/juries') }}"  data-loader-target="#globalLoader">
            <i class="fa fa-gavel"></i>
            <span class="menu-text">Groupe de jury</span>
        </a>
        <a href="{{ url('/admin/profile') }}"  data-loader-target="#globalLoader">
            <i class="fa fa-user"></i>
            <span class="menu-text">Profil</span>
        </a>
        @break

        @case('teacher')
        <a href="{{ url('/teacher/reports') }}"  data-loader-target="#globalLoader">
            <i class="fa fa-file"></i>
            <span class="menu-text">Rapports</span>
        </a>
        <a href="/reports"  data-loader-target="#globalLoader">
            <i class="fa fa-message"></i>
            <span class="menu-text">Commentaires</span>
        </a>
        <a href="/profile"  data-loader-target="#globalLoader">
            <i class="fa fa-gavel"></i>
            <span class="menu-text">Groupe de jury</span>
        </a>
        <a href="{{ url('/teacher/profile') }}"  data-loader-target="#globalLoader">
            <i class="fa fa-user"></i>
            <span class="menu-text">Profil</span>
        </a>
        @break

        @case('student')
        <a href="/reports"  data-loader-target="#globalLoader">
            <i class="fa fa-file"></i>
            <span class="menu-text">Mes Rapports</span>
        </a>
        <a href="/reports"  data-loader-target="#globalLoader">
            <i class="fa fa-message"></i>
            <span class="menu-text">Commentaires</span>
        </a>
        <a href="/profile"  data-loader-target="#globalLoader">
            <i class="fa fa-history"></i>
            <span class="menu-text">Historique</span>
        </a>
        <a href="{{ url('/student/profile') }}"  data-loader-target="#globalLoader">
            <i class="fa fa-user"></i>
            <span class="menu-text">Profil</span>
        </a>
        @break

        @case('superadmin')
        <a href="{{ url('/superadmin/users') }}"  data-loader-target="#globalLoader">
            <i class="fa fa-users"></i>
            <span class="menu-text">Utilisateurs</span>
        </a>
        <a href="/reports"  data-loader-target="#globalLoader">
            <i class="fa fa-file"></i>
            <span class="menu-text">Rapports</span>
        </a>
        {{-- <a href="/students">
                    <i class="fa fa-user-graduate"></i>
                    <span class="menu-text">Étudiants</span>
                </a>

                <a href="/teachers">
                    <i class="fa fa-chalkboard-teacher"></i>
                    <span class="menu-text">Enseignants</span>
                </a> --}}


        <a href="/profile"  data-loader-target="#globalLoader">
            <i class="fa fa-gavel"></i>
            <span class="menu-text">Groupe de jury</span>
        </a>
        <a href="/profile"  data-loader-target="#globalLoader">
            <i class="fa fa-gear"></i>
            <span class="menu-text">Paramètres</span>
        </a>
        <a href="/profile"  data-loader-target="#globalLoader">
            <i class="fa fa-user"></i>
            <span class="menu-text">Profil</span>
        </a>
        @break

        @default
        <span class="menu-text">Aucun menu disponible</span>
        @endswitch

        <div class="mt-auto-custom">
            <button type="button" class="logout-btn" data-bs-toggle="modal">
                <i class="fa fa-power-off" style="font-size: 18px; min-width: 40px;"></i>
                <span class="menu-text">Déconnexion</span>
            </button>
        </div>
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                    <p class="mt-3">Êtes-vous sûr de vouloir vous déconnecter ?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger"  data-loader-target="#globalLoader">Oui, me déconnecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    {{-- <div class="container-fluid min-vh-100 bg-light px-0">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow-sm border-bottom">
                <div class="container py-4">
                    <h2 class="h3 fw-bold text-primary mb-0">{{ $header }}</h2>
    </div>
    </header>
    @endisset

    <!-- Page Content -->
    <main class="container my-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                {{ $slot }}
            </div>
        </div>
    </main>
    </div> --}}


    <!-- Page Content -->
    <div class="content-area">
        <div id="globalLoader">
            <div class="loader-overlay">
                <div class="loader-box">
                    <div class="spinner-border loader-spinner text-secondary fw-bold" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>
        {{ $slot }}
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("toggleSidebar");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("expanded");
        });


        document.addEventListener('DOMContentLoaded', () => {
            const showLoader = () => {
                console.log('🌀 Loader activé');
                document.getElementById('globalLoader').classList.add('loading');
            };

            const hideLoader = () => {
                document.getElementById('globalLoader').classList.remove('loading');
            };

            // 1. CLIC : boutons/liens avec data-loader-target
            document.addEventListener('click', (e) => {
                const trigger = e.target.closest('[data-loader-target]');
                if (!trigger) return;

                // Validation si bouton submit
                if (trigger.type === 'submit') {
                    const form = trigger.closest('form');
                    if (form && !form.checkValidity()) {
                        form.classList.add('was-validated');
                        return;
                    }
                }
                showLoader();
            });

            // 2. SUBMIT : fermer modale + loader (NOUVEAU)
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!(form instanceof HTMLFormElement)) return;

                // Validation
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                // ✅ TROUVE LA MODALE + FERME-LA
                const modal = form.closest('.modal');
                if (modal) {
                    console.log('🚪 Fermeture modale:', modal.id);
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        modal.classList.remove('show');
                        modal.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                    }
                }

                // Loader après fermeture modale (100ms délai)
                setTimeout(() => {
                    const anyBtn = form.querySelector('[data-loader-target]');
                    if (anyBtn) {
                        console.log('✅ Loader après modale:', anyBtn.textContent);
                        showLoader();
                    }
                }, 150);
            });

            // Reset loader au retour page
            window.addEventListener('pageshow', (e) => {
                if (e.persisted) hideLoader();
            });
        });

    </script>

    @if (session('success'))
    <div id="toast-success" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
        <div class="toast show border-0" style="background-color:#d1e7dd; color:#0f5132;">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(() => {
            let toast = document.getElementById('toast-success');
            if (toast) {
                toast.style.transition = "opacity 0.5s";
                toast.style.opacity = "0";
                setTimeout(() => toast.remove(), 500);
            }
        }, 3000); // ⏱️ 3 secondes

    </script>
    @endif
</body>

</html>

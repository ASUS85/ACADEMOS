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
        }

        .sidebar {
            width: 75px;
            min-height: 100vh;
            background: linear-gradient(to bottom, #0c337c, #1b75eb);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            top: 60px;
            left: 0;
            z-index: 2000;
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
    </style>
</head>

<body>
    <!-- TOPBAR -->
    {{--  <nav class="navbar navbar-dark bg-light px-4">
        <img src="{{ asset('images/logo1.png') }}"
         alt="Logo Academos"
         width="80"
         class="me-2">

        <div class="text-dark">
            <i class="fa fa-user"></i>
            {{ Auth::user()->name ?? 'Utilisateur' }}
        </div>
    </nav> --}}
    @include('layouts.navigation')

    <!-- SIDEBAR -->

    <div class="sidebar" id="sidebar">

        <div id="toggleSidebar" class="mb-4 text-light d-flex justify-content-center align-items-center">
            <i class="bi bi-list"></i>
        </div>

        <a href="{{ route('dashboard') }}">
            <i class="fa fa-house"></i>
            <span class="menu-text">Dashboard</span>
        </a>

        @switch(Auth::user()->role_name)
            @case('admin')
                <a href="{{ url('/admin/users') }}">
                    <i class="fa fa-users"></i>
                    <span class="menu-text">Utilisateurs</span>
                </a>
                <a href="{{ url('/admin/reports') }}">
                    <i class="fa fa-file"></i>
                    <span class="menu-text">Rapports</span>
                </a>
                <a href="{{ url('/admin/juries') }}">
                   <i class="fa fa-gavel"></i>
                    <span class="menu-text">Groupe de jury</span>
                </a>
                <a href="{{ url('/admin/profile') }}">
                    <i class="fa fa-user"></i>
                    <span class="menu-text">Profil</span>
                </a>
            @break

            @case('teacher')
                <a href="{{ url('/teacher/reports') }}">
                    <i class="fa fa-file"></i>
                    <span class="menu-text">Rapports</span>
                </a>
                <a href="/reports">
                    <i class="fa fa-message"></i>
                    <span class="menu-text">Commentaires</span>
                </a>
                <a href="/profile">
                   <i class="fa fa-gavel"></i>
                    <span class="menu-text">Groupe de jury</span>
                </a>
                <a href="{{ url('/teacher/profile') }}">
                    <i class="fa fa-user"></i>
                    <span class="menu-text">Profil</span>
                </a>
            @break

            @case('student')
                <a href="/reports">
                    <i class="fa fa-file"></i>
                    <span class="menu-text">Mes Rapports</span>
                </a>
                <a href="/reports">
                    <i class="fa fa-message"></i>
                    <span class="menu-text">Commentaires</span>
                </a>
                <a href="/profile">
                   <i class="fa fa-history"></i>
                    <span class="menu-text">Historique</span>
                </a>
                <a href="{{ url('/student/profile') }}">
                    <i class="fa fa-user"></i>
                    <span class="menu-text">Profil</span>
                </a>
            @break

            @case('superadmin')
                <a href="{{ url('/superadmin/users') }}">
                    <i class="fa fa-users"></i>
                    <span class="menu-text">Utilisateurs</span>
                </a>
                <a href="/reports">
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


                <a href="/profile">
                   <i class="fa fa-gavel"></i>
                    <span class="menu-text">Groupe de jury</span>
                </a>
                <a href="/profile">
                    <i class="fa fa-gear"></i>
                    <span class="menu-text">Paramètres</span>
                </a>
                <a href="/profile">
                    <i class="fa fa-user"></i>
                    <span class="menu-text">Profil</span>
                </a>
            @break

            @default
                <span class="menu-text">Aucun menu disponible</span>
        @endswitch

        <div class="mt-auto-custom">
            <button type="button" class="logout-btn" data-bs-toggle="modal" data-bs-target="#logoutModal">
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
                        <button type="submit" class="btn btn-danger">Oui, me déconnecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    {{--  <div class="container-fluid min-vh-100 bg-light px-0">
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
        {{ $slot }}
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("toggleSidebar");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("expanded");
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

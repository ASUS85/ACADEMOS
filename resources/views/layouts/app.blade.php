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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            width: 70px;
            min-height: 100vh;
            background: #0d6efd;
            transition: width 0.3s;
        }

        .sidebar.expanded {
            width: 200px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px;
        }

        .sidebar a i {
            font-size: 20px;
            min-width: 40px;
            text-align: center;
        }

        .sidebar .menu-text {
            display: none;
        }

        .sidebar.expanded .menu-text {
            display: inline;
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

    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">

            <button id="toggleSidebar" class="btn bg-primary btn-light me-3 w-100 text-light d-flex justify-content-between">
                <i class="fa fa-list"></i>
                <span class="menu-text">List</span>
            </button>

            <a href="/dashboard">
                <i class="fa fa-house"></i>
                <span class="menu-text">Dashboard</span>
            </a>

            <a href="/reports">
                <i class="fa fa-file"></i>
                <span class="menu-text">Rapports</span>
            </a>

            <a href="/students">
                <i class="fa fa-user-graduate"></i>
                <span class="menu-text">Étudiants</span>
            </a>

            <a href="/teachers">
                <i class="fa fa-chalkboard-teacher"></i>
                <span class="menu-text">Enseignants</span>
            </a>

            <a href="/profile">
                <i class="fa fa-user"></i>
                <span class="menu-text">Profil</span>
            </a>

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
        <main class="container my-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    {{ $slot }}
                </div>
            </div>
        </main>

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
</body>

</html>

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

        .sidebar a.is-active {
            color: #fff;
            background: rgba(255, 255, 255, 0.16);
            border-right: 3px solid rgba(255, 255, 255, 0.9);
        }

        .sidebar a.is-active i {
            color: #fff;
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

        #globalLoader {
            display: none;
        }

        #globalLoader.is-active {
            display: block;
        }

        .loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(14, 20, 35, 0.42);
            backdrop-filter: blur(2px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-box {
            width: 84px;
            height: 84px;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-spinner {
            width: 2.5rem;
            height: 2.5rem;
            border-width: 0.28rem;
        }

    </style>
</head>

<body>
    @include('layouts.navigation')

    <!-- SIDEBAR -->

    <div class="sidebar" id="sidebar">
        @php
        $currentRole = Auth::user()->role_name;
        $sidebarMenus = [
        'admin' => [
        ['route' => 'dashboard', 'icon' => 'fa-house', 'label' => 'Dashboard', 'active' => ['dashboard']],
        ['route' => 'admin.users.index', 'icon' => 'fa-users', 'label' => 'Utilisateurs', 'active' => ['admin.users.*']],
        ['route' => 'admin.students.index', 'icon' => 'fa-user-graduate', 'label' => 'Étudiants', 'active' => ['admin.students.*']],
        ['route' => 'admin.teachers.index', 'icon' => 'fa-chalkboard-teacher', 'label' => 'Enseignants', 'active' => ['admin.teachers.*']],
        ['route' => 'reports.index', 'icon' => 'fa-file', 'label' => 'Rapports', 'active' => ['reports.*', 'admin.reports.*', 'superadmin.reports', 'teacher.reports.*', 'jury.reports.*']],
        ['route' => 'admin.juries.index', 'icon' => 'fa-gavel', 'label' => 'Jurys', 'active' => ['admin.juries.*']],
        ['route' => 'admin.stats.index', 'icon' => 'fa-chart-bar', 'label' => 'Statistiques', 'active' => ['admin.stats.*']],
        ['route' => 'admin.profile.admin', 'icon' => 'fa-user', 'label' => 'Profil', 'active' => ['admin.profile.*']],
        ],
        'teacher' => [
        ['route' => 'dashboard', 'icon' => 'fa-house', 'label' => 'Dashboard', 'active' => ['dashboard']],
        ['route' => 'reports.index', 'icon' => 'fa-file', 'label' => 'Rapports', 'active' => ['reports.*', 'teacher.reports.*']],
        ['route' => 'teacher.jury.index', 'icon' => 'fa-gavel', 'label' => 'Jury', 'active' => ['teacher.jury.*']],
        ['route' => 'teacher.profile', 'icon' => 'fa-user', 'label' => 'Profil', 'active' => ['teacher.profile*']],
        ],
        'student' => [
        ['route' => 'dashboard', 'icon' => 'fa-house', 'label' => 'Dashboard', 'active' => ['dashboard']],
        ['route' => 'reports.index', 'icon' => 'fa-file', 'label' => 'Rapports', 'active' => ['reports.*', 'student.reports.*']],
        ['route' => 'student.history.index', 'icon' => 'fa-clock-rotate-left', 'label' => 'Historique', 'active' => ['student.history.*']],
        ['route' => 'student.profile.student', 'icon' => 'fa-user', 'label' => 'Profil', 'active' => ['student.profile.*']],
        ],
        'jury' => [
        ['route' => 'dashboard', 'icon' => 'fa-house', 'label' => 'Dashboard', 'active' => ['dashboard']],
        ['route' => 'reports.index', 'icon' => 'fa-file', 'label' => 'Rapports', 'active' => ['reports.*', 'jury.reports.*']],
        ['route' => 'profile.edit', 'icon' => 'fa-user', 'label' => 'Profil', 'active' => ['profile.*']],
        ],
        'superadmin' => [
        ['route' => 'dashboard', 'icon' => 'fa-house', 'label' => 'Dashboard', 'active' => ['dashboard']],
        ['route' => 'superadmin.users.index', 'icon' => 'fa-users', 'label' => 'Utilisateurs', 'active' => ['superadmin.users.*']],
        ['route' => 'superadmin.students.index', 'icon' => 'fa-user-graduate', 'label' => 'Étudiants', 'active' => ['superadmin.students.*']],
        ['route' => 'superadmin.teachers.index', 'icon' => 'fa-chalkboard-teacher', 'label' => 'Enseignants', 'active' => ['superadmin.teachers.*']],
        ['route' => 'superadmin.admins.index', 'icon' => 'fa-user-shield', 'label' => 'Admins', 'active' => ['superadmin.admins.*']],
        ['route' => 'reports.index', 'icon' => 'fa-file', 'label' => 'Rapports', 'active' => ['reports.*', 'superadmin.reports', 'admin.reports.*']],
        ['route' => 'superadmin.stats', 'icon' => 'fa-chart-bar', 'label' => 'Statistiques', 'active' => ['superadmin.stats']],
        ['route' => 'admin.profile.admin', 'icon' => 'fa-user', 'label' => 'Profil', 'active' => ['profile.*']],
        ],
        ];
        $menuItems = $sidebarMenus[$currentRole]?? [
        ['route' => 'dashboard', 'icon' => 'fa-house', 'label' => 'Dashboard', 'active' => ['dashboard']],
        ];
        @endphp

        <div id="toggleSidebar" class="mb-4 text-light d-flex justify-content-center align-items-center">
            <i class="bi bi-list"></i>
        </div>

        @foreach ($menuItems as $item)
        @php
        $isActive = collect($item['active'])->contains(fn ($pattern) => request()->routeIs($pattern));
        @endphp
        <a href="{{ route($item['route']) }}" data-loader-target="#globalLoader" class="{{ $isActive? 'is-active' : '' }}">
            <i class="fa {{ $item['icon'] }}"></i>
            <span class="menu-text">{{ $item['label'] }}</span>
        </a>
        @endforeach

        @if (empty($menuItems))
        <span class="menu-text">Aucun menu disponible</span>
        @endif

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
                    <p class="mt-3">Êtes-vous sûr de vouloir vous déconnecter?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                    <form method="POST" action="{{ route('logout') }}" data-loader-target="#globalLoader">
                        @csrf
                        <button type="submit" class="btn btn-danger">Oui, me déconnecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="globalConfirmModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="globalConfirmModalMessage">Voulez-vous confirmer cette action?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="globalConfirmModalSubmit">Confirmer</button>
                </div>
            </div>
        </div>
    </div>

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
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const globalLoader = document.getElementById('globalLoader');
        const LOADER_MAX_VISIBLE_MS = 1000;
        let globalLoaderTimer = null;

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('expanded');
            });
        }

        const hideGlobalLoader = () => {
            if (globalLoader) {
                globalLoader.classList.remove('is-active');
            }
            if (globalLoaderTimer) {
                clearTimeout(globalLoaderTimer);
                globalLoaderTimer = null;
            }
        };

        const showGlobalLoader = () => {
            if (globalLoader) {
                globalLoader.classList.add('is-active');

                if (globalLoaderTimer) {
                    clearTimeout(globalLoaderTimer);
                }

                globalLoaderTimer = setTimeout(() => {
                    hideGlobalLoader();
                }, LOADER_MAX_VISIBLE_MS);
            }
        };

        document.querySelectorAll('[data-loader-target], [data-show-loader="true"]').forEach((el) => {
            const isForm = el.tagName === 'FORM';
            const eventName = isForm ? 'submit' : 'click';

            el.addEventListener(eventName, () => {
                if (el.hasAttribute('data-bs-toggle')) {
                    return;
                }
                showGlobalLoader();
            });
        });

        document.querySelectorAll('.sidebar a, .pagination a').forEach((link) => {
            link.addEventListener('click', (event) => {
                if (link.hasAttribute('data-bs-toggle')) {
                    return;
                }

                const href = link.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript:')) {
                    return;
                }

                if (event.ctrlKey || event.metaKey || event.shiftKey || link.target === '_blank') {
                    return;
                }

                showGlobalLoader();
            });
        });

        document.querySelectorAll('form:not([data-no-loader="true"])').forEach((form) => {
            form.addEventListener('submit', () => {
                showGlobalLoader();
            });
        });

        window.addEventListener('beforeunload', () => {
            showGlobalLoader();
        });

        window.addEventListener('pageshow', () => {
            hideGlobalLoader();
        });

        const confirmModalEl = document.getElementById('globalConfirmModal');
        const confirmModalMessage = document.getElementById('globalConfirmModalMessage');
        const confirmModalTitle = document.getElementById('globalConfirmModalLabel');
        const confirmModalSubmit = document.getElementById('globalConfirmModalSubmit');

        let confirmAction = null;
        window.openGlobalConfirm = (options = {}) => {
            if (!confirmModalEl || !confirmModalSubmit) {
                return false;
            }

            const message = options.message || 'Voulez-vous confirmer cette action?';
            const title = options.title || 'Confirmation';
            const submitLabel = options.submitLabel || 'Confirmer';

            confirmModalMessage.textContent = message;
            confirmModalTitle.textContent = title;
            confirmModalSubmit.textContent = submitLabel;
            confirmAction = typeof options.onConfirm === 'function' ? options.onConfirm : null;

            new bootstrap.Modal(confirmModalEl).show();
            return true;
        };

        if (confirmModalEl && confirmModalSubmit) {
            const confirmModal = new bootstrap.Modal(confirmModalEl);

            document.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-confirm-message]');
                if (!trigger) {
                    return;
                }

                event.preventDefault();

                const message = trigger.getAttribute('data-confirm-message') || 'Voulez-vous confirmer cette action?';
                const title = trigger.getAttribute('data-confirm-title') || 'Confirmation';
                const submitLabel = trigger.getAttribute('data-confirm-submit-label') || 'Confirmer';

                confirmModalMessage.textContent = message;
                confirmModalTitle.textContent = title;
                confirmModalSubmit.textContent = submitLabel;

                confirmAction = () => {
                    const formId = trigger.getAttribute('data-confirm-form-id');
                    const action = trigger.getAttribute('data-confirm-action');
                    const method = (trigger.getAttribute('data-confirm-method') || 'POST').toUpperCase();

                    if (formId) {
                        const form = document.getElementById(formId);
                        if (form) {
                            form.submit();
                            return;
                        }
                    }

                    if (action) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = action;

                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;
                        form.appendChild(tokenInput);

                        if (method !== 'POST') {
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = method;
                            form.appendChild(methodInput);
                        }

                        document.body.appendChild(form);
                        form.submit();
                        return;
                    }

                    const href = trigger.getAttribute('href');
                    if (href && href !== '#') {
                        window.location.href = href;
                    }
                };

                confirmModal.show();
            });

            confirmModalSubmit.addEventListener('click', () => {
                showGlobalLoader();
                if (typeof confirmAction === 'function') {
                    confirmAction();
                }
            });
        }

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

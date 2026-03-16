<style>
    body {
        overflow: hidden;
        /* Empêche le scroll sur le body entier */
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .topbar {
        height: 60px;
        /* Hauteur fixe pour ta barre de menu horizontale */
    }

    .main-wrapper {
        display: flex;
        flex: 1;
        /* Prend tout le reste de la hauteur */
        overflow: hidden;
    }

    .sidebar {
        width: 250px;
        height: 100%;
        overflow-y: auto;
        /* Scroll uniquement si le menu est trop long */
    }

    .content-area {
        flex: 1;
        overflow-y: auto;
        /* Seul le contenu défile, pas toute la page */
        padding: 20px;
    }
</style>

<x-app-layout>

    {{-- DASHBOARD PAR ROLE --}}
    @if (auth()->user()->hasRole('admin'))
        @include('admin.admins.acceuil')
    @elseif(auth()->user()->hasRole('student'))
        @include('dashboard.roles.student')
    @elseif(auth()->user()->hasRole('teacher'))
        @include('dashboard.roles.teacher')
    @elseif(auth()->user()->hasRole('jury'))
        @include('dashboard.roles.jury')
    @elseif(auth()->user()->hasRole('superadmin'))
        @include('admin.superadmin.acceuil')
    @else
        <div class="alert alert-warning">
            Aucun rôle attribué²
        </div>
    @endif

    </div>

</x-app-layout>

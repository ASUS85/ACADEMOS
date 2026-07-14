<style>
    body {
        overflow: hidden;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .topbar {
        height: 60px;
    }

    .main-wrapper {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    .sidebar {
        width: 250px;
        height: 100%;
        overflow-y: auto;
    }

    .content-area {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

</style>

<x-app-layout>

    {{-- DASHBOARD PRINCIPAL ADMIN / SUPERADMIN --}}
    @if (auth()->user()->hasRole('admin'))
    @include('admin.admins.acceuil')
    @elseif(auth()->user()->hasRole('superadmin'))
    @include('admin.superadmin.acceuil')
    @else
    <div class="alert alert-warning">
        Tableau de bord non disponible pour ce rôle.
    </div>
    @endif

    </div>

</x-app-layout>

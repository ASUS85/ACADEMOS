<x-app-layout>
    <div class="container-fluid py-4">
        <h1>👨‍💼 Gestion Admins</h1>

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Liste Admins</h5>
                @auth
                    @if (auth()->user()->hasRole('superadmin'))
                        <a href="{{ route('superadmin.admins.create') }}" class="btn btn-success mb-3">
                            + Ajouter Admin
                        </a>
                    @endif
                @endauth
            </div>
            <div class="card-body">
                <!-- Tableau admins existants -->
            </div>
        </div>
    </div>
</x-app-layout>

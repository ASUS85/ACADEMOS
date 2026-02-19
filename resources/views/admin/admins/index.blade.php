<x-app-layout>
<div class="container-fluid py-4">
    <h1>👨‍💼 Gestion Admins</h1>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Liste Admins</h5>
            @if(auth()->user()->hasRole('superAdmin'))
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                ➕ Nouvel Admin
            </a>
            @endif
        </div>
        <div class="card-body">
            <!-- Tableau admins existants -->
        </div>
    </div>
</div>
</x-app-layout>

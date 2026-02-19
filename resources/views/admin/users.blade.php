<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users fa-2x text-primary me-3"></i>
                    <h1 class="h3 mb-1 fw-bold">👥 Gestion utilisateurs</h1>
                    @if (auth()->user()->hasRole('admin'))
                        <div class="ms-auto">
                            <a href="{{ url('/admin/teachers/create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i> Nouvel Enseignant
                            </a>
                        </div>
                    @endif
                    <p class="text-muted mb-0">{{ $users->total() }} utilisateur(s)</p>
                </div>
            </div>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des utilisateurs</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Avatar</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôles</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=007bff&color=fff&size=40"
                                            class="rounded-circle" width="40" height="40">
                                    </td>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            <span
                                                class="badge bg-{{ $role->name == 'admin' ? 'danger' : 'primary' }} me-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">Editer</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Aucun utilisateur</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-0 py-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

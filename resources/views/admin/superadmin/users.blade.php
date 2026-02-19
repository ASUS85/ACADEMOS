<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h1 class="h2 fw-bold">👑 SuperAdmin - Utilisateurs</h1>
                        <p class="text-muted">Gestion complète ({{ $users->total() }} utilisateurs)</p>
                    </div>
                    <a href="{{ route('superadmin.admins.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i> Nouvel Admin
                    </a>
                </div>

                <div class="card border-0 shadow-lg">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôles</th>
                                    <th>Créé</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                <span
                                                    class="badge bg-{{ $role->name == 'superAdmin' ? 'danger' : 'primary' }}">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">Éditer</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center p-5">Aucun utilisateur</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

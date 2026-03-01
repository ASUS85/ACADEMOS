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
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                                        <td>
                                            <a href="{{ route('superadmin.users.edit', $user) }}">Éditer</a>
                                            <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

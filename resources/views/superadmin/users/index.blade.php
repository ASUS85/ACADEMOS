<x-app-layout>
    <div class="container-fluid py-5">
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

                <!-- FILTRES -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <form method="GET" action="{{ route('superadmin.users') }}">

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label">Rôle</label>
                                    <select name="role" class="form-control">
                                        <option value="">Tous les rôles</option>

                                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>
                                            Étudiant</option>
                                        <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>
                                            Enseignant</option>
                                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Chef
                                            Département</option>
                                        <option value="jury" {{ request('role') == 'jury' ? 'selected' : '' }}>Jury
                                        </option>

                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Département</label>
                                    <select name="department_id" class="form-control">

                                        <option value="">Tous départements</option>

                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ request('department_id') == $department->id ? 'selected' : '' }}>

                                                {{ $department->name }}

                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Spécialité</label>
                                    <select name="specialite" class="form-control">

                                        <option value="">Toutes spécialités</option>

                                        @foreach ($filieres as $filiere)
                                            <option value="{{ $filiere->name}}"
                                                {{ request('specialite') == $filiere->name ? 'selected' : '' }}>

                                                {{ $filiere->name }}

                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-12 text-end mt-3">

                                    <button class="btn btn-primary">
                                        🔎 Filtrer
                                    </button>

                                    <a href="{{ route('superadmin.users') }}" class="btn btn-secondary">
                                        Réinitialiser
                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>
                </div>


                <!-- TABLEAU UTILISATEURS -->
                <div class="card border-0 shadow-lg">
                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead class="table-light">

                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Département</th>
                                    <th>Filière</th>
                                    <th>Actions</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($users as $user)
                                    <tr>

                                        <td>{{ $user->name }}</td>

                                        <td>{{ $user->email }}</td>

                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $user->roles->pluck('name')->join(', ') }}
                                            </span>
                                        </td>

                                        <td>{{ $user->department->name ?? '-' }}</td>

                                        <td>{{ $user->filiere->name ?? '-' }}</td>

                                        <td>

                                            <a href="{{ route('superadmin.users.edit', $user) }}"
                                                class="btn btn-sm btn-warning">
                                                ✏️
                                            </a>

                                            <form action="{{ route('superadmin.users.destroy', $user) }}"
                                                method="POST" style="display:inline;">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Supprimer cet utilisateur ?')">

                                                    🗑

                                                </button>

                                            </form>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                        <div class="p-3">

                            {{ $users->withQueryString()->links() }}

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

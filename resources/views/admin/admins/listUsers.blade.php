<x-app-layout>

    <div class="container py-5">

        <div class="d-flex justify-content-between mb-4">
            <h3>👨‍🏫 Utilisateurs du Département</h3>

            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                ➕ Ajouter enseignant
            </a>
        </div>

        <div class="card shadow border-0">
            <div class="table-responsive">

                <form method="GET" class="row mb-4">

                    <div class="col-md-4">

                        <select name="role" class="form-control">

                            <option value="">Tous les rôles</option>

                            <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>
                                Enseignant
                            </option>

                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>
                                Étudiant
                            </option>

                        </select>

                    </div>

                    <div class="col-md-4">

                        <select name="specialite" class="form-control">

                            <option value="">Toutes les spécialités</option>

                            @foreach ($filieres as $f)
                                <option value="{{ $f->id }}" {{ request('specialite') == $f->id ? 'selected' : '' }}>

                                    {{ $f->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-4">

                        <button class="btn btn-primary">Filtrer</button>

                        <a href="{{ url('/admin/users') }}" class="btn btn-secondary">
                            Reset
                        </a>

                    </div>

                </form>

                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Matricule</th>
                            <th>Sexe</th>
                            <th>Spécialité</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->matricule ?? '-' }}</td>
                                <td>{{ $user->sexe ?? '-' }}</td>
                                <td>{{ $user->filiere->name ?? '-' }}</td>

                                <td class="text-center">

                                    <a href="{{ route('admin.teachers.edit', $user) }}"
                                        class="btn btn-warning btn-sm me-1">
                                        ✏️
                                    </a>

                                    <form action="{{ route('admin.teachers.destroy', $user) }}" method="POST"
                                        style="display:inline">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Supprimer cet utilisateur ?')">
                                            🗑
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center text-muted p-4">
                                    Aucun utilisateur trouvé
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>

    </div>

</x-app-layout>

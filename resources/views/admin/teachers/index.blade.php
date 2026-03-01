<x-app-layout>

    <div class="container py-5">

        <div class="d-flex justify-content-between mb-4">
            <h3>👨‍🏫 Mes Enseignants</h3>

            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                ➕ Ajouter enseignant
            </a>
        </div>

        <table class="table table-bordered shadow-sm">

            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Matricule</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->matricule }}</td>
                        <td>{{ $user->grade }}</td>

                        <td>

                            <a href="{{ route('admin.teachers.edit', $user) }}" class="btn btn-sm btn-warning">
                                Modifier
                            </a>

                            <form action="{{ route('admin.teachers.destroy', $user) }}" method="POST"
                                style="display:inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</x-app-layout>

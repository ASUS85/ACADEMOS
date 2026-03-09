<x-app-layout>

    <div class="container py-5">

        <div class="card shadow border-0">
            <div class="card-body p-5">

                <h3 class="mb-4">➕ Ajouter un Admin</h3>

                <form action="{{ route('superadmin.admins.store') }}" method="POST">
                    @csrf

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Nom</label>
                            <input name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input name="email" type="email" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Mot de passe</label>
                            <input name="password" type="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Confirmer mot de passe</label>
                            <input name="password_confirmation" type="password" class="form-control" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Département</label>

                            <select name="department_id" class="form-control" required>
                                <option value="">Choisir département</option>

                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">
                                        {{ $department->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>

                    <button class="btn btn-primary">
                        Créer Admin
                    </button>

                </form>

            </div>
        </div>

    </div>

</x-app-layout>

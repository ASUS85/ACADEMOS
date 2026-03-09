<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">

                        <h2 class="mb-4">✏️ Éditer Utilisateur</h2>

                        <form action="{{ route('superadmin.users.update', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <!-- NOM -->
                            <div class="mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ $user->name }}"
                                       required>
                            </div>

                            <!-- EMAIL -->
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       value="{{ $user->email }}"
                                       required>
                            </div>

                            <!-- DEPARTEMENT -->
                            <div class="mb-3">
                                <label class="form-label">Département</label>

                                <select name="department_id" class="form-select">
                                    <option value="">-- Aucun --</option>

                                    @foreach ($departments as $department)

                                        <option value="{{ $department->id }}"
                                            {{ $user->department_id == $department->id ? 'selected' : '' }}>

                                            {{ $department->name }}

                                        </option>

                                    @endforeach
                                </select>
                            </div>


                            <!-- ⭐ CHAMPS SPÉCIAUX STUDENT -->
                            @if($user->hasRole('student'))

                                <!-- MATRICULE -->
                                <div class="mb-3">
                                    <label class="form-label">Matricule</label>

                                    <input type="text"
                                           name="matricule"
                                           class="form-control"
                                           value="{{ $user->matricule }}">
                                </div>

                                <!-- FILIERE -->
                                <div class="mb-3">
                                    <label class="form-label">Filière / Spécialité</label>

                                    <select name="specialite" class="form-select">

                                        <option value="">Choisir spécialité</option>

                                        @foreach($filieres as $filiere)

                                            <option value="{{ $filiere->name }}"
                                                {{ $user->specialite == $filiere->name ? 'selected' : '' }}>

                                                {{ $filiere->name }}

                                            </option>

                                        @endforeach

                                    </select>
                                </div>

                            @endif


                            <!-- ROLE -->
                            <div class="mb-3">
                                <label class="form-label">Rôle</label>

                                <select name="role" class="form-select">

                                    @foreach ($roles as $role)

                                        <option value="{{ $role }}"
                                            {{ $user->roles->pluck('name')->contains($role) ? 'selected' : '' }}>

                                            {{ ucfirst($role) }}

                                        </option>

                                    @endforeach

                                </select>
                            </div>


                            <!-- ACTIONS -->
                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('superadmin.users') }}"
                                   class="btn btn-secondary me-2">
                                    Annuler
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Mettre à jour
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

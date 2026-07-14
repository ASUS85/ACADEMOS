<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header border-0 text-white" style="background: linear-gradient(135deg, #0f172a, #2563eb);">
                        <div class="p-3 p-lg-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                                    <i class="fas fa-chalkboard-teacher fa-lg"></i>
                                </div>
                                <div>
                                    <h2 class="h4 fw-bold mb-1">Éditer l'enseignant</h2>
                                    <p class="mb-0 opacity-90">{{ $user->name }} • {{ $user->matricule ?? 'Matricule non renseigné' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-lg-5">
                        @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-4">
                            <div class="fw-semibold mb-2">Veuillez corriger les erreurs suivantes :</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('superadmin.teachers.update', $user) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PATCH')

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom complet</label>
                                <input type="text" name="name" class="form-control form-control-lg" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Matricule</label>
                                <input type="text" name="matricule" class="form-control form-control-lg" value="{{ old('matricule', $user->matricule) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Grade</label>
                                <input type="text" name="grade" class="form-control form-control-lg" value="{{ old('grade', $user->grade) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Spécialité</label>
                                <input type="text" name="specialite" class="form-control form-control-lg" value="{{ old('specialite', $user->specialite) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Département</label>
                                <select name="department_id" class="form-select form-select-lg" required>
                                    <option value="">Choisir un département</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected((string) old('department_id', $user->department_id) === (string) $department->id)>
                                        {{ $department->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Filière</label>
                                <select name="filiere_id" class="form-select form-select-lg">
                                    <option value="">Choisir une filière</option>
                                    @foreach ($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" @selected((string) old('filiere_id', $user->filiere_id) === (string) $filiere->id)>
                                        {{ $filiere->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-outline-secondary btn-lg" data-loader-target="#globalLoader">Annuler</a>
                                <button type="submit" class="btn btn-primary btn-lg" data-loader-target="#globalLoader">Mettre à jour</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

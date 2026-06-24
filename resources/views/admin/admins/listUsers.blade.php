<x-app-layout>
    <style>
        .card-neo {
            border-radius: 1rem;
            border: 0;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .table thead th {
            border-bottom: 0;
            font-size: 0.75rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6b7280;
            background: #f9fafb;
        }

        .table-hover tbody tr:hover {
            background-color: #f3f4ff;
        }

        .avatar-ring {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
        }

        .chip-role {
            border-radius: 999px;
            padding: 0.15rem 0.6rem;
            font-size: 0.7rem;
        }

        .chip-role-teacher {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .chip-role-student {
            background: #dcfce7;
            color: #15803d;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        #add-teacher {
            cursor: pointer;
        }

        /* dropdown multi select avec checkbox */
        .multi-select-dropdown .dropdown-toggle {
            width: 100%;
        }

        .multi-select-dropdown .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
            z-index: 1060;
            background-color: #fff;
            border: 1px solid #dee2e6;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08);
        }

        .multi-select-dropdown .dropdown-item {
            color: #212529;
        }

        .multi-select-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #212529;
        }

        .multi-select-dropdown .dropdown-item input[type="checkbox"] {
            margin-right: 8px;
        }

        .multi-select-dropdown .selected-label {
            font-weight: 500;
            color: #2563eb;
        }

        .liste {
            border: 1px solid #2563eb;
            color: #2563eb;
            background: #fff;
        }

        .liste:hover,
        .liste.show {
            color: #fff;
            border: 1px solid #2563eb;
        }

        .liste.btn.dropdown-toggle.show {
            border: 1px solid #2563eb;
            color: #2563eb;
        }

        .btn.btn-sm.dropdown-toggle.px-0.show {
            border: none;
            color: #2563eb;
        }

    </style>

    <div class="bg-light py-3 px-3 px-md-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                    <i class="fas fa-users-cog fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Utilisateurs du département</h3>
                    <p class="text-muted mb-0 small">
                        Gestion centralisée des enseignants et étudiants de votre département
                    </p>
                </div>
            </div>

            <button type="button" id="add-teacher" class="btn btn-primary d-flex align-items-center gap-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
                <i class="fas fa-plus"></i>
                <span>Nouvel enseignant</span>
            </button>
        </div>

        <div class="card card-neo">
            {{-- Bandeau filtres --}}
            <div class="card-body border-bottom pb-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">
                            Rôle
                        </label>
                        <select name="role" class="form-select">
                            <option value="">Tous les rôles</option>
                            <option value="teacher" {{ request('role')=='teacher' ? 'selected' : '' }}>
                                Enseignant
                            </option>
                            <option value="student" {{ request('role')=='student' ? 'selected' : '' }}>
                                Étudiant
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">
                            Spécialité
                        </label>
                        <select name="specialite" class="form-select">
                            <option value="">Toutes les spécialités</option>
                            @foreach ($filieres as $f)
                            <option value="{{ $f->id }}" {{ request('specialite')==$f->id ? 'selected' : '' }}>
                                {{ $f->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">
                            &nbsp;
                        </label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-grow-1" data-loader-target="#globalLoader">
                                <i class="fas fa-filter me-1"></i> Filtrer
                            </button>
                            <a href="{{ url('/admin/users') }}" class="btn btn-outline-secondary" data-loader-target="#globalLoader">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tableau --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Utilisateur</th>
                            <th>Email</th>
                            <th>Matricule</th>
                            <th>Sexe</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $user)
                        @php
                        $isTeacher = $user->hasRole('teacher');
                        $roleLabel = $isTeacher ? 'Enseignant' : 'Étudiant';
                        $roleClass = $isTeacher ? 'chip-role chip-role-teacher' : 'chip-role chip-role-student';
                        @endphp
                        <tr>
                            {{-- Col utilisateur avec avatar + rôle --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563EB&color=fff&size=40&rounded=true" class="rounded-circle me-3 avatar-ring" width="40" height="40" alt="Avatar">
                                    <div>
                                        <div class="fw-semibold text-dark">
                                            {{ $user->name }}
                                        </div>
                                        <span class="{{ $roleClass }}">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $roleLabel }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="text-muted">
                                {{ $user->email }}
                            </td>

                            <td>
                                <span class="badge bg-light text-dark rounded-pill">
                                    {{ $user->matricule ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill px-3 py-2">
                                    {{ $user->sexe ?? '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="btn btn-light border btn-icon" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->id }}">
                                        <i class="fas fa-pen text-warning"></i>
                                    </button>

                                    <button type="button" class="btn btn-light border btn-icon" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <div class="mb-2">
                                    <i class="fas fa-users-slash fa-2x opacity-50"></i>
                                </div>
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($users, 'links'))
            <div class="card-footer bg-white border-0 pt-3 d-flex justify-content-end">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>

        {{-- MODAL ÉDITION UNIFIÉE (student / teacher) --}}
        @foreach($users as $user)
        @php
        $isTeacher = $user->hasRole('teacher');
        $route = $isTeacher ? route('admin.teachers.update', $user) : route('admin.students.update', $user);
        @endphp

        <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header bg-primary bg-opacity-10 border-0">
                        <h5 class="modal-title">Modifier {{ $user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ $route }}" method="POST" class="needs-validation">
                        @csrf
                        @method('PATCH')

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                {{-- Nom commun --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Nom complet <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email commun --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Matricule commun --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Matricule <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="matricule" class="form-control @error('matricule') is-invalid @enderror" value="{{ old('matricule', $user->matricule) }}" required>
                                    @error('matricule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Sexe commun --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Sexe <span class="text-danger">*</span>
                                    </label>
                                    <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                                        <option value="">Choisir le sexe</option>
                                        <option value="Homme" {{ old('sexe', $user->sexe) == 'Homme' ? 'selected' : '' }}>
                                            Homme
                                        </option>
                                        <option value="Femme" {{ old('sexe', $user->sexe) == 'Femme' ? 'selected' : '' }}>
                                            Femme
                                        </option>
                                    </select>
                                    @error('sexe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Champs spécifiques STUDENT --}}
                                @if(!$isTeacher)
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Spécialité (Filière) <span class="text-danger">*</span>
                                    </label>
                                    <select name="filiere_id" class="form-select @error('filiere_id') is-invalid @enderror" required>
                                        <option value="">Choisir une filière</option>
                                        @foreach ($filieres as $f)
                                        <option value="{{ $f->id }}" {{ old('filiere_id', $user->filiere?->id) == $f->id ? 'selected' : '' }}>
                                            {{ $f->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('filiere_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Niveau <span class="text-danger">*</span>
                                    </label>
                                    <select name="niveau" class="form-select @error('niveau') is-invalid @enderror" required>
                                        <option value="">Choisir le niveau</option>
                                        <option value="BTS" {{ old('niveau', $user->niveau) == 'BTS' ? 'selected' : '' }}>
                                            BTS
                                        </option>
                                        <option value="Licence1" {{ old('niveau', $user->niveau) == 'Licence1' ? 'selected' : '' }}>
                                            Licence 1
                                        </option>
                                        <option value="Licence2" {{ old('niveau', $user->niveau) == 'Licence2' ? 'selected' : '' }}>
                                            Licence 2
                                        </option>
                                        <option value="Licence3" {{ old('niveau', $user->niveau) == 'Licence3' ? 'selected' : '' }}>
                                            Licence 3
                                        </option>
                                        <option value="Master1" {{ old('niveau', $user->niveau) == 'Master1' ? 'selected' : '' }}>
                                            Master 1
                                        </option>
                                        <option value="Master2" {{ old('niveau', $user->niveau) == 'Master2' ? 'selected' : '' }}>
                                            Master 2
                                        </option>
                                    </select>
                                    @error('niveau')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

                                {{-- Champs spécifiques TEACHER --}}
                                @if($isTeacher)
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Grade <span class="text-danger">*</span>
                                    </label>
                                    <select name="grade" class="form-select @error('grade') is-invalid @enderror" required>
                                        <option value="">Choisir un grade</option>
                                        <option value="Assistant" {{ old('grade', $user->grade) == 'Assistant' ? 'selected' : '' }}>
                                            Assistant
                                        </option>
                                        <option value="Chargé de cours" {{ old('grade', $user->grade) == 'Chargé de cours' ? 'selected' : '' }}>
                                            Chargé de cours
                                        </option>
                                        <option value="Maître de conférences" {{ old('grade', $user->grade) == 'Maître de conférences' ? 'selected' : '' }}>
                                            Maître de conférences
                                        </option>
                                        <option value="Professeur" {{ old('grade', $user->grade) == 'Professeur' ? 'selected' : '' }}>
                                            Professeur
                                        </option>
                                    </select>
                                    @error('grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Liste des filières assignées (dropdown multi-select) --}}
                                @php
                                $assignedFiliereIds = $user->filieres ? $user->filieres->pluck('id')->toArray() : [];
                                @endphp
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Spécialités (Filières)
                                    </label>
                                    <div class="multi-select-dropdown">
                                        <div class="dropdown">
                                            <button class="liste btn dropdown-toggle" type="button" id="filieresDropdown{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="selected-label" id="filieresLabel{{ $user->id }}">
                                                    Aucune sélection
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="filieresDropdown{{ $user->id }}">
                                                @foreach ($filieres as $filiere)
                                                <li>
                                                    <label class="dropdown-item d-flex align-items-center">
                                                        <input type="checkbox" name="filieres[]" value="{{ $filiere->id }}" class="me-2" {{ in_array($filiere->id, $assignedFiliereIds) ? 'checked' : '' }}>
                                                        {{ $filiere->name }}
                                                    </label>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Liste des matières assignées --}}
                                @php
                                $assignedMatiereIds = $user->matieres ? $user->matieres->pluck('id')->toArray() : [];
                                @endphp
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Matières
                                    </label>
                                    <div class="multi-select-dropdown">
                                        <div class="dropdown">
                                            <button class="liste btn dropdown-toggle" type="button" id="matieresDropdown{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="selected-label" id="matieresLabel{{ $user->id }}">
                                                    Aucune sélection
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="matieresDropdown{{ $user->id }}">
                                                @foreach ($matieres as $matiere)
                                                <li>
                                                    <label class="dropdown-item d-flex align-items-center">
                                                        <input type="checkbox" name="matieres[]" value="{{ $matiere->id }}" class="me-2" {{ in_array($matiere->id, $assignedMatiereIds) ? 'checked' : '' }}>
                                                        {{ $matiere->name }}
                                                    </label>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top px-4 py-3 justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Annuler
                            </button>
                            <button type="submit" class="btn btn-primary" data-loader-target="#globalLoader">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        {{-- MODAL SUPPRESSION UNIFIÉE (student / teacher) --}}
        @foreach($users as $user)
        @php
        $route = route('admin.teachers.destroy', $user);
        @endphp
        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header bg-danger bg-opacity-10 border-0">
                        <h5 class="modal-title text-danger">Supprimer {{ $user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ $route }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="modal-body p-4 text-center">
                            <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                            <p class="text-muted small">{{ $user->name }} - {{ $user->email }}</p>
                        </div>

                        <div class="modal-footer bg-light border-top px-4 py-3 justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Annuler
                            </button>
                            <button type="submit" class="btn btn-danger" data-loader-target="#globalLoader">
                                Supprimer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach


        {{-- MODAL CRÉATION ENSEIGNANT (inchangée) --}}
        <div class="modal fade" id="createTeacherModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header bg-primary bg-opacity-10 border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Nouvel enseignant
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="createTeacherForm" method="POST" action="{{ route('admin.teachers.store') }}" novalidate>
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                {{-- NOM --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Nom complet <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Ex : Jean DUPONT" value="{{ old('name') }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- EMAIL --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Ex : jean.dupont@univ.cm" value="{{ old('email') }}" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- MATRICULE --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Matricule <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="matricule" class="form-control @error('matricule') is-invalid @enderror" placeholder="Ex : ENS-2024-001" value="{{ old('matricule') }}" required>
                                    @error('matricule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- GRADE --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Grade <span class="text-danger">*</span>
                                    </label>
                                    <select name="grade" class="form-select @error('grade') is-invalid @enderror" required>
                                        <option value="">Choisir un grade</option>
                                        <option value="Professeur" {{ old('grade') == 'Professeur' ? 'selected' : '' }}>
                                            Professeur
                                        </option>
                                        <option value="Maître de conférences" {{ old('grade') == 'Maître de conférences' ? 'selected' : '' }}>
                                            Maître de conférences
                                        </option>
                                        <option value="Chargé de cours" {{ old('grade') == 'Chargé de cours' ? 'selected' : '' }}>
                                            Chargé de cours
                                        </option>
                                        <option value="Assistant" {{ old('grade') == 'Assistant' ? 'selected' : '' }}>
                                            Assistant
                                        </option>
                                    </select>
                                    @error('grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- SPÉCIALITÉ (filieres) --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Spécialités (Filières)
                                    </label>
                                    <div class="multi-select-dropdown">
                                        <div class="dropdown">
                                            <button class="liste btn dropdown-toggle" type="button" id="filieresDropdownNew" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="selected-label" id="filieresLabelNew">
                                                    Aucune sélection
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="filieresDropdownNew">
                                                @foreach($filieres as $filiere)
                                                <li>
                                                    <label class="dropdown-item d-flex align-items-center">
                                                        <input type="checkbox" name="filieres[]" value="{{ $filiere->id }}" class="me-2">
                                                        {{ $filiere->name }}
                                                    </label>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- MATIÈRES --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Matières
                                    </label>
                                    <div class="multi-select-dropdown">
                                        <div class="dropdown">
                                            <button class="liste btn dropdown-toggle" type="button" id="matieresDropdownNew" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="selected-label" id="matieresLabelNew">
                                                    Aucune sélection
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="matieresDropdownNew">
                                                @foreach($matieres as $matiere)
                                                <li>
                                                    <label class="dropdown-item d-flex align-items-center">
                                                        <input type="checkbox" name="matieres[]" value="{{ $matiere->id }}" class="me-2">
                                                        {{ $matiere->name }}
                                                    </label>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- SEXE --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        Sexe <span class="text-danger">*</span>
                                    </label>
                                    <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                                        <option value="">Choisir le sexe</option>
                                        <option value="Homme" {{ old('sexe') == 'Homme' ? 'selected' : '' }}>
                                            Homme
                                        </option>
                                        <option value="Femme" {{ old('sexe') == 'Femme' ? 'selected' : '' }}>
                                            Femme
                                        </option>
                                    </select>
                                    @error('sexe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Info mot de passe --}}
                                <div class="col-12 alert alert-info border-0 rounded-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Mot de passe par défaut :</strong>
                                    <code>password123</code>
                                    <br>
                                    <small>L'enseignant pourra le modifier dans son profil.</small>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top px-4 py-3 justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Annuler
                            </button>
                            <button type="submit" class="btn btn-primary px-4" data-loader-target="#globalLoader">
                                <i class="fas fa-save me-2"></i>Créer l'enseignant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Script JS (nettoyé, sans doublons) --}}
        <script>
            // Stop event propagation sur le menu dropdown
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            function updateLabelAll(dropdown, label, type) {
                const checked = dropdown.querySelectorAll('input[type="checkbox"]:checked').length;
                label.textContent = checked > 0 ?
                    checked + ' ' + type + (checked > 1 ? 's sélectionnées' : ' sélectionnée') :
                    'Aucune sélection';
            }

            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.multi-select-dropdown').forEach(wrapper => {
                    const dropdown = wrapper.querySelector('.dropdown');
                    const label = wrapper.querySelector('.selected-label');
                    const type = label.id.includes('filieres') ? 'filière' : 'matière';

                    dropdown.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                        cb.addEventListener('change', () => updateLabelAll(dropdown, label, type));
                    });

                    // Init label
                    updateLabelAll(dropdown, label, type);
                });

                document.getElementById('createTeacherModal').addEventListener('hidden.bs.modal', function() {
                    this.querySelectorAll('.dropdown-toggle.show').forEach(el => el.classList.remove('show'));
                    this.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
                });

                document.querySelectorAll('[id^="editModal"]').forEach(modal => {
                    modal.addEventListener('hidden.bs.modal', function() {
                        this.querySelectorAll('.dropdown-toggle.show').forEach(el => el.classList.remove('show'));
                        this.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
                    });
                });
            });

            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (!form.matches('form')) return;

                if (form.id === 'createTeacherForm' && !form.checkValidity()) {
                    form.classList.add('was-validated');
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);

        </script>
</x-app-layout>

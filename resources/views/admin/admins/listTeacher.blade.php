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
            /* au‑dessus des autres modaux */
        }

        .multi-select-dropdown .dropdown-item input[type="checkbox"] {
            margin-right: 8px;
        }

        .multi-select-dropdown .selected-label {
            font-weight: 500;
        }

        .liste:hover {
            background-color: #2563eb;
            color: #fff;
        }

        .liste {
            border: 1px solid #2563eb;
            color: #2563eb;
        }

        .liste.btn.dropdown-toggle.show {
            border: 1px solid #2563eb;
            color: #2563eb;
        }

        .liste.btn.dropdown-toggle.show:hover {
            border: 1px solid #2563eb;
            color: #fff;
        }

        .btn.btn-sm.dropdown-toggle.px-0.show {
            border: none;
            color: #2563eb;
        }

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
            background-color: #fff;
            color: #2563eb;
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
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;">
                    <i class="fas fa-chalkbooard-teacher fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Liste des enseignants</h3>
                    <p class="text-muted mb-0 small">
                        Gérer le corps enseignant de votre département ({{ $teachersCount ?? count($teachers) }})
                    </p>
                </div>
            </div>

            <button type="button" id="add-teacher" class="btn btn-primary d-flex align-items-center gap-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
                <i class="fas fa-plus"></i>
                <span>Nouvel enseignant</span>
            </button>
        </div>

        <div class="card card-neo">
            {{-- Filtres --}}
            <div class="card-body border-bottom pb-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Filière</label>
                        <select name="filiere" class="form-select">
                            <option value="">Toutes les filières</option>
                            @foreach ($filieres as $f)
                            <option value="{{ $f->id }}" {{ request('filiere') == $f->id ? 'selected' : '' }}>
                                {{ $f->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Nom ou email..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">
                            Matière
                        </label>
                        <select name="matiere" class="form-select">
                            <option value="">Toutes les matières</option>
                            @foreach($matieres as $m)
                            <option value="{{ $m->id }}" {{ request('matiere') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-grow-1" data-loader-target="#globalLoader">
                                <i class="fas fa-filter me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary" data-loader-target="#globalLoader">
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
                            <th class="ps-4">Enseignant</th>
                            <th>Email</th>
                            <th>Spécialité</th>
                            <th>Matière</th>
                            <th>Matricule</th>
                            <th>Sexe</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($teachers as $teacher)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=2563EB&color=fff&size=40&rounded=true" class="rounded-circle me-3 avatar-ring" width="40" height="40" alt="Avatar">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $teacher->name }}</div>
                                        <small class="text-muted">Enseignant</small>
                                    </div>
                                </div>
                            </td>

                            <td class="text-muted small">
                                {{ $teacher->email }}
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm dropdown-toggle px-0" type="button" id="dropdownMenuButton{{ $teacher->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        Spécialités
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $teacher->id }}">
                                        {{-- Spécialités (filières) --}}
                                        @if($teacher->filieres->count())
                                        <li>
                                            <h6 class="dropdown-header">Spécialités</h6>
                                        </li>
                                        @foreach($teacher->filieres as $filiere)
                                        <li><span class="dropdown-item">{{ $filiere->name }}</span></li>
                                        @endforeach
                                        @else
                                        <li><span class="dropdown-item text-muted">Aucune spécialité</span></li>
                                        @endif
                                    </ul>
                                </div>
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm dropdown-toggle px-0" type="button" id="dropdownMenuButton{{ $teacher->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        Matières
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $teacher->id }}">
                                        {{-- Matières --}}
                                        @if($teacher->matieres->count())
                                        <li>
                                            <h6 class="dropdown-header">Matières</h6>
                                        </li>
                                        @foreach($teacher->matieres as $matiere)
                                        <li><span class="dropdown-item">{{ $matiere->name }}</span></li>
                                        @endforeach
                                        @else
                                        <li><span class="dropdown-item text-muted">Aucune matière</span></li>
                                        @endif
                                    </ul>
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark rounded-pill fw-semibold">
                                    {{ $teacher->matricule ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill px-3 py-2">
                                    {{ $teacher->sexe ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="btn btn-light border btn-icon" data-bs-toggle="modal" data-bs-target="#editModal{{ $teacher->id }}">
                                        <i class="fas fa-pen text-warning"></i>
                                    </button>

                                    <button type="button" class="btn btn-light border btn-icon" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $teacher->id }}">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modale d'édition -->
                        <div class="modal fade" id="editModal{{ $teacher->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">


                                    <form action="{{ route('admin.teachers.edit', $teacher) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Modifier {{ $teacher->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>


                                        <div class="modal-body">
                                            <label>Nom</label>
                                            <input type="text" name="name" value="{{ $teacher->name }}" class="form-control">


                                            <label>Email</label>
                                            <input type="email" name="email" value="{{ $teacher->email }}" class="form-control">


                                            <label>Matricule</label>
                                            <input type="text" name="matricule" value="{{ $teacher->matricule }}" class="form-control">


                                            {{-- Filières en dropdown à checkbox --}}
                                            <label class="mt-3" for="filieres">Spécialités (Filières)</label>
                                            <div class="multi-select-dropdown">
                                                <div class="dropdown">
                                                    <button class="liste btn  dropdown-toggle" type="button" id="filieresDropdown{{ $teacher->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="selected-label" id="filieresLabel{{ $teacher->id }}">
                                                            {{ $teacher->filieres->count() ? $teacher->filieres->count().(count($teacher->filieres) > 1 ? ' filières sélectionnées' : ' filière sélectionnée') : 'Aucune sélection' }}
                                                        </span>
                                                    </button>
                                                    <ul class="dropdown-menu w-100" aria-labelledby="filieresDropdown{{ $teacher->id }}">
                                                        @foreach($filieres as $filiere)
                                                        <li>
                                                            <label class="dropdown-item d-flex align-items-center">
                                                                <input type="checkbox" name="filieres[]" value="{{ $filiere->id }}" {{ $teacher->filieres->contains($filiere->id) ? 'checked' : '' }} class="me-2">
                                                                {{ $filiere->name }}
                                                            </label>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>


                                            {{-- Matières en dropdown à checkbox --}}
                                            <label class="mt-3" for="matieres">Matières</label>
                                            <div class="multi-select-dropdown">
                                                <div class="dropdown">
                                                    <button class="liste btn dropdown-toggle" type="button" id="matieresDropdown{{ $teacher->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="selected-label" id="matieresLabel{{ $teacher->id }}">
                                                            {{ $teacher->matieres->count() ? $teacher->matieres->count().(count($teacher->matieres) > 1 ? ' matières sélectionnées' : ' matière sélectionnée') : 'Aucune sélection' }}
                                                        </span>
                                                    </button>
                                                    <ul class="dropdown-menu w-100" aria-labelledby="matieresDropdown{{ $teacher->id }}">
                                                        @foreach($matieres as $matiere)
                                                        <li>
                                                            <label class="dropdown-item d-flex align-items-center">
                                                                <input type="checkbox" name="matieres[]" value="{{ $matiere->id }}" {{ $teacher->matieres->contains($matiere->id) ? 'checked' : '' }} class="me-2">
                                                                {{ $matiere->name }}
                                                            </label>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>


                                            <label>Sexe</label>
                                            <select name="sexe" class="form-select">
                                                <option value="Homme" @selected($teacher->sexe === 'Homme')>Homme</option>
                                                <option value="Femme" @selected($teacher->sexe === 'Femme')>Femme</option>
                                            </select>


                                            <label>Grade</label>
                                            <select name="grade" class="form-select">
                                                <option value="Assistant" @selected($teacher->grade == 'Assistant')>Assistant</option>
                                                <option value="Chargé de cours" @selected($teacher->grade == 'Chargé de cours')>Chargé de cours</option>
                                                <option value="Maître de conférences" @selected($teacher->grade == 'Maître de conférences')>Maître de conférences</option>
                                                <option value="Professeur" @selected($teacher->grade == 'Professeur')>Professeur</option>
                                            </select>
                                        </div>


                                        <div class="modal-footer justify-content-between p-3">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary" data-loader-target="#globalLoader">Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <!-- Modale suppression -->
                        <div class="modal fade" id="deleteModal{{ $teacher->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Supprimer {{ $teacher->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-danger" data-loader-target="#globalLoader">Supprimer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <div class="mb-3">
                                    <i class="fas fa-chalkboard-teacher fa-3x opacity-50 text-primary"></i>
                                </div>
                                Aucun enseignant trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            @if (method_exists($teachers, 'links'))
            <div class="card-footer bg-white border-0 pt-3 d-flex justify-content-end">
                {{ $teachers->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>


    {{-- MODAL CRÉATION ENSEIGNANT (inchangée) --}}
    <div class="modal fade" id="createTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-gradient-to-r from-primary to-info text-white rounded-top-4 border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Nouvel enseignant
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Ex: Jean DUPONT" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- EMAIL --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-2">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Ex: jean.dupont@univ.cm" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- MATRICULE --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted mb-2">
                                    Matricule <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="matricule" class="form-control @error('matricule') is-invalid @enderror" placeholder="Ex: ENS-2024-001" value="{{ old('matricule') }}" required>
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
                                <label class="mt-3" for="filieres">Spécialités (Filières)</label>
                                <div class="multi-select-dropdown">
                                    <div class="dropdown">
                                        <button class="liste btn dropdown-toggle" type="button" id="filieresDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="selected-label" id="filieresLabel">
                                                Aucune sélection
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="filieresDropdown">
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

                            {{-- Matières --}}
                            <div class="col-md-6">
                                <label class="mt-3" for="matieres">Matières</label>
                                <div class="multi-select-dropdown">
                                    <div class="dropdown">
                                        <button class="liste btn dropdown-toggle" type="button" id="matieresDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="selected-label" id="matieresLabel">
                                                Aucune sélection
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="matieresDropdown">
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
                                    <option value="Homme" {{ old('sexe') == 'Homme' ? 'selected' : '' }}>Homme</option>
                                    <option value="Femme" {{ old('sexe') == 'Femme' ? 'selected' : '' }}>Femme</option>
                                </select>
                                @error('sexe')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 alert alert-info mt-3 border-0 rounded-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Mot de passe par défaut :</strong> <code>password123</code><br>
                                <small>L'enseignant pourra le modifier dans son profil.</small>
                            </div>
                        </div>


                    </div>

                    <div class="modal-footer bg-light border-top rounded-bottom-4 px-4 py-3 justify-content-between">
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

    <script>
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        function updateLabel(dropdownId, labelId, type) {
            const dropdown = document.getElementById(dropdownId);
            const label = document.getElementById(labelId);
            const checked = dropdown.querySelectorAll('input[type="checkbox"]:checked').length;
            label.textContent = checked > 0 ?
                checked + ' ' + type + (checked > 1 ? 's sélectionnées' : ' sélectionnée') :
                'Aucune sélection';
        }

        document.querySelectorAll('#filieresDropdown{{ $teacher->id }} input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', () => {
                updateLabel('filieresDropdown{{ $teacher->id }}', 'filieresLabel{{ $teacher->id }}', 'filière');
            });
        });

        document.querySelectorAll('#matieresDropdown{{ $teacher->id }} input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', () => {
                updateLabel('matieresDropdown{{ $teacher->id }}', 'matieresLabel{{ $teacher->id }}', 'matière');
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.addEventListener('click', e => e.stopPropagation());
            });

            function updateLabelAll(dropdown, label, type) {
                const checked = dropdown.querySelectorAll('input[type="checkbox"]:checked').length;
                label.textContent = checked > 0 ?
                    checked + ' ' + type + (checked > 1 ? 's sélectionnées' : ' sélectionnée') :
                    'Aucune sélection';
            }

            document.querySelectorAll('.multi-select-dropdown').forEach(wrapper => {
                const dropdown = wrapper.querySelector('.dropdown');
                const label = wrapper.querySelector('.selected-label');
                const type = label.id.includes('filieres') ? 'filière' : 'matière';

                dropdown.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    cb.addEventListener('change', () => updateLabelAll(dropdown, label, type));
                });
            });
        });

        document.addEventListener('submit', function(e) {
            const form = e.target;
            console.log(form);
            if (!form.matches('form')) return;
            console.log(form);

            if (form.id === 'createTeacherForm' && !form.checkValidity()) {
                form.classList.add('was-validated');
                e.preventDefault();
                e.stopPropagation();
                return;
            }

        }, true);

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

    </script>
</x-app-layout>

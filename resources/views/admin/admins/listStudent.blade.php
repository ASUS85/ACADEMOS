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
            box-shadow: 0 0 0 3px rgba(34, 197, 94, .12);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

    </style>

    <div class="bg-light py-3 px-3 px-md-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;">
                    <i class="fas fa-user-graduate fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Liste des étudiants</h3>
                    <p class="text-muted mb-0 small">
                        Gérer les effectifs étudiants de votre département ({{ $studentsCount ?? count($students) }})
                    </p>
                </div>
            </div>
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
                        <input type="text" name="search" class="form-control" placeholder="Nom ou matricule..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success flex-grow-1" data-loader-target="#globalLoader">
                                <i class="fas fa-filter me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary" data-loader-target="#globalLoader">
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
                            <th class="ps-4">Étudiant</th>
                            <th>Email</th>
                            <th>Matricule</th>
                            <th>Filière</th>
                            <th>Niveau</th>
                            <th>Sexe</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=10B981&color=fff&size=40&rounded=true" class="rounded-circle me-3 avatar-ring" width="40" height="40" alt="Avatar">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $student->name }}</div>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </div>
                                </div>
                            </td>

                            <td class="text-muted small">{{ $student->email }}</td>


                            <td>
                                <span class="badge bg-light text-dark rounded-pill fw-semibold">
                                    {{ $student->matricule ?? 'N/A' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                    {{ $student->filiere->name ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                    {{ $student->niveau ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill px-3 py-2">
                                    {{ $student->sexe ?? '-' }}
                                </span>
                            </td>


                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="btn btn-light border btn-icon" data-bs-toggle="modal" data-bs-target="#editModal{{ $student->id }}">
                                        <i class="fas fa-pen text-warning"></i>
                                    </button>

                                    <button type="button" class="btn btn-light border btn-icon" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $student->id }}">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </div>
                            </td>


                        </tr>
                        <div class="modal fade" id="editModal{{ $student->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <form id="deleteStudentForm{{ $student->id }}" action="{{ url ('admin/students/'.$student->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Modifier {{ $student->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Nom</label>
                                            <input type="text" name="name" value="{{ $student->name }}" class="form-control">
                                            <label>Email</label>
                                            <input type="text" name="matricule" value="{{ $student->matricule }}" class="form-control">
                                            <label>Matricule</label>
                                            <input type="email" name="email" value="{{ $student->email }}" class="form-control">
                                            <label>Spécialité</label>
                                            <select name="filiere_id" class="form-select">
                                                <option value="{{ $student->filiere->id }}">{{ $student->filiere->name }}</option>
                                                @foreach ($filieres as $f)
                                                <option value="{{ $f->id }}" {{ request('filiere_id')==$f->id ? 'selected' : '' }}>
                                                    {{ $f->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <label>Sexe</label>
                                            <select name="sexe" class="form-select">
                                                <option value="{{ $student->sexe }}">{{ $student->sexe }}</option>
                                                <option value="Homme" @selected($student->sexe == 'Homme')>Homme</option>
                                                <option value="Femme" @selected($student->sexe == 'Femme')>Femme</option>
                                            </select>
                                            <label>Niveau</label>
                                            <select name="niveau" class="form-select">
                                                <option value="{{ $student->niveau }}">{{ $student->niveau }}</option>
                                                <option value="BTS" @selected($student->niveau == 'BTS')>BTS</option>
                                                <option value="Licence1" @selected($student->niveau == 'Licence1')>Licence 1</option>
                                                <option value="Licence2" @selected($student->niveau == 'Licence2')>Licence 2</option>
                                                <option value="Licence3" @selected($student->niveau == 'Licence3')>Licence 3</option>
                                                <option value="Master1" @selected($student->niveau == 'Master1')>Master 1</option>
                                                <option value="Master2" @selected($student->niveau == 'Master2')>Master 2</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary" data-loader-target="#globalLoader">Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div class="modal fade" id="deleteModal{{ $student->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.students.destroy', $student) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Supprimer {{ $student->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                                        </div>
                                        <div class="modal-footer justify-content-between p-3">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="button" class="btn btn-danger" data-confirm-title="Suppression de l'étudiant" data-confirm-message="Confirmez-vous la suppression de cet étudiant ?" data-confirm-submit-label="Oui, supprimer" data-confirm-form-id="deleteStudentForm{{ $student->id }}">
                                                Supprimer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <div class="mb-3">
                                    <i class="fas fa-user-graduate-slash fa-3x opacity-50 text-success"></i>
                                </div>
                                Aucun étudiant trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($students, 'links'))
            <div class="card-footer bg-white border-0 pt-3 d-flex justify-content-end">
                {{ $students->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>


    <script>
    </script>
</x-app-layout>

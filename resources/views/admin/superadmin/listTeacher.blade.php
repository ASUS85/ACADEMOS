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

    </style>

    <div class="bg-light py-3 px-3 px-md-4">
        {{-- Header SUPERADMIN --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;">
                    <i class="fas fa-chalkboard-teacher fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Tous les enseignants</h3>
                    <p class="text-muted mb-0 small">
                        Gestion globale du corps enseignant - Tous départements
                        <span class="badge bg-primary ms-2">{{ $teachers->total() }}</span>
                    </p>
                </div>
            </div>

            {{-- Boutons SuperAdmin --}}
            <div class="d-flex gap-2">
                <a href="{{ route('admin.teachers.create') }}" class="btn btn-success d-flex align-items-center gap-2 rounded-pill" data-loader-target="#globalLoader">
                    <i class="fas fa-plus"></i>
                    <span>Nouvel enseignant</span>
                </a>
                <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-primary" data-loader-target="#globalLoader">
                    <i class="fas fa-users me-1"></i>Utilisateurs
                </a>
            </div>
        </div>

        <div class="card card-neo">
            {{-- Filtres SUPERADMIN --}}
            <div class="card-body border-bottom pb-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Département</label>
                        <select name="department" class="form-select">
                            <option value="">Tous les départements</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->teachers_count }} enseignants)
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Filière / Spécialité</label>
                        <select name="filiere" class="form-select">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $f)
                            <option value="{{ $f->id }}" {{ request('filiere') == $f->id ? 'selected' : '' }}>
                                {{ $f->name }} ({{ $f->teachers_count }} enseignants)
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Nom, email, matricule..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted text-uppercase">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" data-loader-target="#globalLoader">
                                <i class="fas fa-filter me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-outline-secondary" data-loader-target="#globalLoader">
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
                            <th>Département</th>
                            <th>Spécialité / Filière</th>
                            <th>Matricule</th>
                            <th>Grade</th>
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
                                        <small class="text-muted">
                                            {{ $teacher->grade ?? 'Enseignant' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td class="text-muted small">
                                {{ $teacher->email }}
                            </td>

                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                                    {{ $teacher->department->name ?? 'Sans département' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                    {{ $teacher->filiere->name ?? ($teacher->specialite ?? '-') }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark rounded-pill fw-semibold">
                                    {{ $teacher->matricule ?? 'N/A' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1">
                                    {{ $teacher->grade ?? '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('superadmin.teachers.edit', $teacher) }}" class="btn btn-light border btn-icon" title="Modifier">
                                        <i class="fas fa-pen text-primary"></i>
                                    </a>

                                    <form id="deleteSuperTeacherForm{{ $teacher->id }}" action="{{ route('superadmin.users.destroy', $teacher) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-light border btn-icon" title="Supprimer" data-confirm-title="Suppression enseignant" data-confirm-message="Confirmez-vous la suppression de {{ $teacher->name }} ?" data-confirm-submit-label="Oui, supprimer" data-confirm-form-id="deleteSuperTeacherForm{{ $teacher->id }}">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
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

            @if(method_exists($teachers, 'links'))
            <div class="card-footer bg-white border-0 pt-3 d-flex justify-content-end">
                {{ $teachers->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

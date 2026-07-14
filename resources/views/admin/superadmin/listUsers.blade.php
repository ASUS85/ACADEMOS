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

        .chip-role-superadmin {
            background: #fee2e2;
            color: #dc2626;
        }

        .chip-role-admin {
            background: #fef3c7;
            color: #d97706;
        }

        .chip-role-teacher {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .chip-role-student {
            background: #dcfce7;
            color: #15803d;
        }

        .chip-role-jury {
            background: #f3e8ff;
            color: #7c3aed;
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
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;">
                    <i class="fas fa-users-cog fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Gestion globale des utilisateurs</h3>
                    <p class="text-muted mb-0 small">
                        SuperAdmin : Accès complet à tous les utilisateurs, départements et rôles
                    </p>
                </div>
            </div>
        </div>

        <div class="card card-neo">
            {{-- Bandeau filtres --}}
            <div class="card-body border-bottom pb-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Rôle</label>
                        <select name="role" class="form-select">
                            <option value="">Tous les rôles</option>
                            <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>SuperAdmin</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Enseignant</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Étudiant</option>
                            <option value="jury" {{ request('role') == 'jury' ? 'selected' : '' }}>Jury</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Département</label>
                        <select name="department" class="form-select">
                            <option value="">Tous les départements</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Nom, email..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted text-uppercase">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" data-loader-target="#globalLoader">
                                <i class="fas fa-filter me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary" data-loader-target="#globalLoader">
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
                            <th>Département</th>
                            <th>Matricule</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $user)
                        @php
                        $roles = $user->roles->pluck('name');
                        $primaryRole = $roles->first();
                        $roleLabel = match($primaryRole) {
                        'superadmin' => 'SuperAdmin',
                        'admin' => 'Administrateur',
                        'teacher' => 'Enseignant',
                        'student' => 'Étudiant',
                        'jury' => 'Jury',
                        default => 'Utilisateur'
                        };
                        $roleClass = match($primaryRole) {
                        'superadmin' => 'chip-role chip-role-superadmin',
                        'admin' => 'chip-role chip-role-admin',
                        'teacher' => 'chip-role chip-role-teacher',
                        'student' => 'chip-role chip-role-student',
                        'jury' => 'chip-role chip-role-jury',
                        default => 'chip-role bg-secondary text-white'
                        };
                        @endphp
                        <tr>
                            {{-- Col utilisateur avec avatar + rôle --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563EB&color=fff&size=40&rounded=true" class="rounded-circle me-3 avatar-ring" width="40" height="40" alt="Avatar">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                        <span class="{{ $roleClass }}">
                                            <i class="fas fa-tag me-1"></i>{{ $roleLabel }}
                                        </span>
                                        @if($roles->count() > 1)
                                        <div class="mt-1">
                                            @foreach($roles->slice(1) as $secondaryRole)
                                            <span class="badge bg-light text-dark text-xs me-1">
                                                {{ ucfirst($secondaryRole) }}
                                            </span>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="text-muted small">{{ $user->email }}</td>

                            <td>
                                @if($user->department)
                                <span class="badge bg-light text-dark rounded-pill px-3 py-1">
                                    {{ $user->department->name }}
                                </span>
                                @else
                                <span class="badge bg-secondary text-white rounded-pill px-3 py-1">
                                    Sans département
                                </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-light text-dark rounded-pill">
                                    {{ $user->matricule ?? '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    {{-- Actions selon le rôle --}}
                                    @if($user->hasRole('admin'))
                                    <a href="{{ route('superadmin.admins.edit', $user) }}" class="btn btn-light border btn-icon" title="Modifier Admin">
                                        <i class="fas fa-pen text-warning"></i>
                                    </a>
                                    @elseif($user->hasRole('teacher'))
                                    <a href="{{ route('superadmin.teachers.edit', $user) }}" class="btn btn-light border btn-icon" title="Modifier Enseignant">
                                        <i class="fas fa-pen text-primary"></i>
                                    </a>
                                    @elseif($user->hasRole('student'))
                                    <a href="{{ route('superadmin.students.edit', $user) }}" class="btn btn-light border btn-icon" title="Modifier Étudiant">
                                        <i class="fas fa-pen text-success"></i>
                                    </a>
                                    @endif

                                    {{-- Suppression (protection superadmin) --}}
                                    @if(auth()->id() !== $user->id)
                                    <form id="deleteSuperUserForm{{ $user->id }}" action="{{ route('superadmin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-light border btn-icon" title="Supprimer" data-confirm-title="Suppression utilisateur" data-confirm-message="Confirmez-vous la suppression de {{ $user->name }} ?" data-confirm-submit-label="Oui, supprimer" data-confirm-form-id="deleteSuperUserForm{{ $user->id }}">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                    @endif
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
    </div>
</x-app-layout>

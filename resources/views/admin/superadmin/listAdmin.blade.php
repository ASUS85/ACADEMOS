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
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;">
                    <i class="fas fa-user-shield fa-lg"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Tous les administrateurs</h3>
                    <p class="text-muted mb-0 small">
                        Gestion globale des administrateurs - Tous départements
                        <span class="badge bg-warning ms-2">{{ $admins->total() }}</span>
                    </p>
                </div>
            </div>

            {{-- Boutons SuperAdmin --}}
            <div class="d-flex gap-2">
                <a href="{{ route('superadmin.admins.create') }}" class="btn btn-success d-flex align-items-center gap-2 rounded-pill" data-loader-target="#globalLoader">
                    <i class="fas fa-plus"></i>
                    <span>Nouvel administrateur</span>
                </a>
                <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-primary" data-loader-target="#globalLoader">
                    <i class="fas fa-users me-1"></i>Tous utilisateurs
                </a>
            </div>
        </div>

        <div class="card card-neo">
            {{-- Filtres SUPERADMIN --}}
            <div class="card-body border-bottom pb-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Département</label>
                        <select name="department" class="form-select">
                            <option value="">Tous les départements</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->admins_count ?? 0 }} admin{{ $dept->admins_count > 1 ? 's' : '' }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Nom, email..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted text-uppercase">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" data-loader-target="#globalLoader">
                                <i class="fas fa-filter me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('superadmin.admins.index') }}" class="btn btn-outline-secondary" data-loader-target="#globalLoader">
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
                            <th class="ps-4">Administrateur</th>
                            <th>Email</th>
                            <th>Département</th>
                            <th>Date création</th>
                            <th>Dernière connexion</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($admins as $admin)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=6366F1&color=fff&size=40&rounded=true" class="rounded-circle me-3 avatar-ring" width="40" height="40" alt="Avatar">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $admin->name }}</div>
                                        <small class="text-muted">Administrateur</small>
                                    </div>
                                </div>
                            </td>

                            <td class="text-muted small">
                                {{ $admin->email }}
                            </td>

                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                    {{ $admin->department->name ?? 'Sans département' }}
                                </span>
                            </td>

                            <td>
                                <span class="text-muted small">
                                    {{ $admin->created_at?->format('d/m/Y') ?? '-' }}
                                </span>
                            </td>

                            <td>
                                @if($admin->last_login_at)
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">
                                    {{ $admin->last_login_at?->format('d/m H:i') }}
                                </span>
                                @else
                                <span class="text-muted small">Jamais</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('superadmin.admins.edit', $admin) }}" class="btn btn-light border btn-icon" title="Modifier">
                                        <i class="fas fa-pen text-primary"></i>
                                    </a>

                                    <form id="deleteSuperAdminForm{{ $admin->id }}" action="{{ route('superadmin.admins.destroy', $admin) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-light border btn-icon" title="Supprimer" data-confirm-title="Suppression administrateur" data-confirm-message="Confirmez-vous la suppression de {{ $admin->name }} ?" data-confirm-submit-label="Oui, supprimer" data-confirm-form-id="deleteSuperAdminForm{{ $admin->id }}">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <div class="mb-3">
                                    <i class="fas fa-user-shield fa-3x opacity-50 text-warning"></i>
                                </div>
                                Aucun administrateur trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($admins, 'links'))
            <div class="card-footer bg-white border-0 pt-3 d-flex justify-content-end">
                {{ $admins->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

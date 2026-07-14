<x-app-layout>
    @php
    $currentUser = auth()->user();
    $filiereValue = request('filiere');
    $levelValue = request('level');
    @endphp

    <style>
        .jury-teacher-hero {
            background: linear-gradient(135deg, #111827 0%, #1d4ed8 55%, #0f766e 100%);
            color: #fff;
        }

        .jury-teacher-panel {
            border: 0;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 10px 10px -5px rgba(15, 23, 42, 0.04);
        }

        .jury-teacher-table thead th {
            background: #f8fafc;
            border-bottom: 0;
            color: #475569;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .jury-teacher-badge {
            border-radius: 999px;
            padding: .35rem .75rem;
            font-size: .75rem;
            font-weight: 600;
        }

        .jury-teacher-badge-primary {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .jury-teacher-badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .jury-teacher-badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .jury-teacher-badge-muted {
            background: #e5e7eb;
            color: #374151;
        }

        .jury-teacher-preview-frame {
            width: 100%;
            height: 64vh;
            border: 0;
            background: #fff;
        }

    </style>

    <div class="container-fluid py-4">
        <div class="card jury-teacher-hero border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 mb-3">Admin département</span>
                        <h1 class="display-6 fw-bold mb-2">Gestion des jurys</h1>
                        <p class="lead mb-0 opacity-90">Même rendu que l’onglet jury enseignant, avec les actions de gestion propres à l’admin.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('admin.juries.index') }}" class="btn btn-light btn-lg px-4 py-3 shadow-sm">
                            <i class="fas fa-sync-alt me-2"></i>Actualiser
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-triangle-exclamation me-2"></i>{{ session('error') }}
        </div>
        @endif

        <div class="card jury-teacher-panel mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.juries.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label fw-semibold text-muted">Filière</label>
                        <select name="filiere" class="form-select form-select-lg">
                            <option value="">Toutes les filières</option>
                            @foreach(\App\Models\Filiere::all() as $filiere)
                            <option value="{{ $filiere->id }}" {{ (string) $filiereValue === (string) $filiere->id ? 'selected' : '' }}>{{ $filiere->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label fw-semibold text-muted">Niveau</label>
                        <select name="level" class="form-select form-select-lg">
                            <option value="">Tous les niveaux</option>
                            <option value="L1" {{ $levelValue === 'L1' ? 'selected' : '' }}>L1</option>
                            <option value="L2" {{ $levelValue === 'L2' ? 'selected' : '' }}>L2</option>
                            <option value="L3" {{ $levelValue === 'L3' ? 'selected' : '' }}>L3</option>
                        </select>
                    </div>

                    <div class="col-lg-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1" data-loader-target="#globalLoader">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('admin.juries.index') }}" class="btn btn-outline-secondary btn-lg" data-loader-target="#globalLoader">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if ($juryItems->isEmpty())
        <div class="card jury-teacher-panel">
            <div class="card-body py-5 text-center">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h3 class="h5 fw-bold mb-2">Aucun jury à afficher</h3>
                <p class="text-muted mb-0">Les jurys du département apparaîtront ici.</p>
            </div>
        </div>
        @else
        <div class="card jury-teacher-panel overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="h5 fw-bold mb-1">Jury département</h2>
                    <div class="text-muted small">{{ $juryItems->total() }} jury(ies) • page {{ $juryItems->currentPage() }} / {{ $juryItems->lastPage() }}</div>
                </div>
                <span class="jury-teacher-badge jury-teacher-badge-muted">{{ $currentUser->name }}</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0 jury-teacher-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Rapport</th>
                            <th>Filière</th>
                            <th>Jury</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($juryItems as $jury)
                        @php
                        $report = $jury->report;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $report?->student?->name ?? 'Rapport supprimé' }}</div>
                                <div class="text-muted small">{{ $report?->student?->matricule ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($report?->title ?? 'Rapport supprimé', 48) }}</div>
                                <div class="text-muted small">Déposé le {{ $report?->created_at?->format('d/m/Y') ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="jury-teacher-badge jury-teacher-badge-primary">{{ $report?->student?->filiere?->name ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    @forelse($jury->members as $member)
                                    <span class="jury-teacher-badge {{ $member->pivot->role === 'president' ? 'jury-teacher-badge-success' : 'jury-teacher-badge-warning' }}">
                                        {{ $member->name }} • {{ $member->pivot->role }}
                                    </span>
                                    @empty
                                    <span class="jury-teacher-badge jury-teacher-badge-muted">Aucun</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#editJury{{ $jury->id }}">
                                        <i class="fas fa-pen me-1"></i>Modifier
                                    </button>

                                    <form method="POST" action="{{ route('admin.juries.destroy', $jury) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm px-3" data-confirm-title="Suppression du jury" data-confirm-message="Confirmez-vous la suppression de ce jury ?">
                                            <i class="fas fa-trash me-1"></i>Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $juryItems->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    @foreach($juryItems as $jury)
    <div class="modal fade" id="editJury{{ $jury->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0">Modifier jury</h5>
                        <small class="text-white-50">Ajustez les membres et leurs rôles</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form method="POST" action="{{ route('admin.juries.update', $jury) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-lg-12">
                                <div class="small text-muted mb-2">Membres du jury</div>
                                <div id="members-container">
                                    @forelse($jury->members as $member)
                                    <div class="row g-2 mb-2 align-items-center">
                                        <div class="col-md-6">
                                            <select name="members[][user_id]" class="form-select">
                                                @foreach(\App\Models\User::role(['teacher','jury'])->get() as $u)
                                                <option value="{{ $u->id }}" {{ $u->id == $member->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <select name="members[][role]" class="form-select">
                                                <option value="president" {{ $member->pivot->role == 'president' ? 'selected' : '' }}>Président</option>
                                                <option value="encadreur" {{ $member->pivot->role == 'encadreur' ? 'selected' : '' }}>Encadreur</option>
                                                <option value="rapporteur" {{ $member->pivot->role == 'rapporteur' ? 'selected' : '' }}>Rapporteur</option>
                                                <option value="membre" {{ $member->pivot->role == 'membre' ? 'selected' : '' }}>Membre</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2 text-end">
                                            <button type="button" class="btn btn-outline-danger remove">X</button>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted mb-0">Aucun membre.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</x-app-layout>

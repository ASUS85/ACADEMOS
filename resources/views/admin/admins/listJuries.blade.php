<x-app-layout>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --purple-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .table-jury-container {
            background: linear-gradient(145deg, #f8fafc, #f1f5f9);
            border-radius: 24px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .table-jury {
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.95rem;
        }

        .table-jury th {
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            border: none;
            padding: 1.5rem 1rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-jury td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            transition: all 0.2s ease;
        }

        .table-jury tr {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 16px;
            overflow: hidden;
            margin: 0.5rem 0;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .table-jury tr:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-2xl);
            background: linear-gradient(145deg, #ffffff, #f8fafc);
        }

        .role-badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .president-badge {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: white;
        }

        .encadreur-badge {
            background: linear-gradient(135deg, #10b981, #34d399);
            color: white;
        }

        .rapporteur-badge {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: white;
        }

        .membre-badge {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            color: white;
        }

        .modal-xl-custom {
            max-width: 1000px !important;
            margin: 1.75rem auto !important;
        }

        .modal-card-preview {
            border-radius: 24px;
            box-shadow: var(--shadow-2xl);
            border: none;
        }

        .modal-content {
            border: none;
            border-radius: 18px;
        }

        .modal-header {
            border-bottom: 1px solid #edf2f7;
        }

        .modal-footer {
            border-top: 1px solid #edf2f7;
        }

        .modal-dialog {
            transition: all .25s ease;
        }

        .modal-backdrop.show {
            opacity: .55;
        }

        .member-row {
            background: #f8fafc;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px;
        }

        .member-row:hover {
            background: #f1f5f9;
        }

        .table-jury tr:hover {
            transform: none;
            background: #f8fafc;
        }

        .table-jury td {
            background: white;
        }

    </style>

    <div class="container-fluid">
        {{-- HEADER --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-gradient-to-br from-purple to-blue-main text-white rounded-3 p-3 shadow-lg me-4" style="width: 70px; height: 70px;">
                            <i class="fas fa-gavel fa-2x"></i>
                        </div>
                        <div>
                            <h1 class="h2 fw-bold mb-2 lh-sm">⚖️ Gestion des Jurys</h1>
                            <div class="d-flex align-items-center gap-3 text-muted small">
                                <span><i class="fas fa-list me-1"></i>{{ $juries->total() }} jury(s) constitué(s)</span>
                                <span class="vr mx-2"></span>
                                <span><i class="fas fa-clock me-1"></i>{{ now()->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
        <div class="alert alert-success border-0 shadow-lg rounded-3 mb-4 animate__animated animate__fadeIn" role="alert">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-20 text-success rounded-circle p-2 me-3">
                    <i class="fas fa-check fs-5"></i>
                </div>
                {{ session('success') }}
            </div>
        </div>
        @endif

        {{-- FILTRES --}}
        <div class="card shadow-xl rounded-4 mb-5 table-jury-container">
            <div class="card-body p-4">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold text-muted small mb-2">Filière</label>
                        <select name="filiere" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">📚 Toutes filières</option>
                            @foreach (\App\Models\Filiere::where('department_id', auth()->user()->department_id)->get() as $f)
                            <option value="{{ $f->id }}" {{ request('filiere') == $f->id ? 'selected' : '' }}>
                                {{ $f->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold text-muted small mb-2">Niveau</label>
                        <select name="level" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">🎓 Tous niveaux</option>
                            <option value="L1" {{ request('level') == 'L1' ? 'selected' : '' }}>L1</option>
                            <option value="L2" {{ request('level') == 'L2' ? 'selected' : '' }}>L2</option>
                            <option value="L3" {{ request('level') == 'L3' ? 'selected' : '' }}>L3</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <button type="submit" class="btn btn-primary btn-lg w-100 h-100">
                            <i class="fas fa-filter-circle-xmark me-2"></i>Filtrer
                        </button>
                    </div>
                    <div class="col-lg-5 col-md-6 text-end">
                        <a href="{{ route('admin.juries.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="fas fa-arrow-rotate-left me-2"></i>Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLEAU --}}
        @if ($juries->isEmpty())
        <div class="text-center py-12">
            <div class="card shadow-xl mx-auto" style="max-width: 500px;">
                <div class="card-body py-8">
                    <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-full p-6 mx-auto mb-4 shadow-lg d-inline-flex" style="width: 140px; height: 140px;">
                        <i class="fas fa-gavel fa-3x text-gray-400 mt-2"></i>
                    </div>
                    <h3 class="h3 fw-bold text-muted mb-3">Aucun jury constitué</h3>
                    <p class="text-muted mb-4 lh-lg lead">Tous les rapports validés sont en attente d'affectation.
                    </p>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-primary btn-lg shadow-lg px-5">
                        <i class="fas fa-file-signature me-2"></i>Rapports validés
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="table-jury-container shadow-2xl rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table-jury table mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user-graduate me-2"></i>Étudiant</th>
                            <th><i class="fas fa-graduation-cap me-2"></i>Filière</th>
                            <th><i class="fas fa-layer-group me-2"></i>Niveau</th>
                            <th><i class="fas fa-gavel me-2"></i>Groupe Jury</th>
                            <th style="width: 160px;"><i class="fas fa-cogs me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($juries as $index => $jury)
                        @php
                        $president = $jury->members->where('pivot.role', 'president')->first();
                        $encadreur = $jury->members->where('pivot.role', 'encadreur')->first();
                        $rapporteur = $jury->members->where('pivot.role', 'rapporteur')->first();
                        @endphp
                        <tr class="jury-row" data-jury-id="{{ $jury->id }}" data-bs-toggle="modal" data-bs-target="#juryDetail{{ $jury->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 45px; height: 45px; font-weight: 700; font-size: 0.85rem;">
                                        {{ substr($jury->report->student->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $jury->report->student->name }}</div>
                                        <small class="text-muted">{{ Str::limit($jury->report->title, 40) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-gradient-to-r from-info to-primary px-3 py-2 fw-semibold">
                                    {{ $jury->report->student->filiere->name ?? 'Non spécifié' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-20 text-success px-3 py-2 fw-semibold border border-success-subtle">
                                    {{ $jury->report->student->level ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if ($president)
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="president-badge role-badge">👑
                                        {{ $president->name }}</span>
                                    @if ($encadreur)
                                    <span class="encadreur-badge role-badge">👨‍🏫
                                        {{ $encadreur->name }}</span>
                                    @endif
                                    @if ($rapporteur)
                                    <span class="rapporteur-badge role-badge">📋
                                        {{ $rapporteur->name }}</span>
                                    @endif
                                </div>
                                @else
                                <span class="badge bg-secondary text-white px-3 py-1">En constitution</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-primary btn-sm jury-edit" data-bs-toggle="modal" data-bs-target="#editJury{{ $jury->id }}" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('admin.reports.index') }}#report-{{ $jury->report->id }}" class="btn btn-outline-info btn-sm" title="Voir rapport">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.juries.destroy', $jury) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ce jury ?')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL DÉTAIL CARD --}}
                        <div class="modal fade" id="juryDetail{{ $jury->id }}" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-xl-custom modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content shadow-lg">
                                    <div class="modal-header bg-gradient-to-r from-purple-gradient to-purple-600 text-white border-0 rounded-top-4 p-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-white bg-opacity-20 p-3 rounded-3 shadow-sm">
                                                <i class="fas fa-gavel fa-2x"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-1 fw-bold">Détail du jury</h4>
                                                <small>{{ $jury->report->student->name }} -
                                                    {{ $jury->report->title }}</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    {{-- CONTENU EXACT DE LA CARD --}}
                                    <div class="modal-body p-5">
                                        @php
                                        $autres = $jury->members->whereNotIn('pivot.role', [
                                        'president',
                                        'encadreur',
                                        'rapporteur',
                                        ]);
                                        @endphp

                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <h6 class="fw-bold mb-3 text-uppercase text-muted"><i class="fas fa-info-circle me-2"></i>Infos Rapport</h6>
                                                <div class="d-flex align-items-center text-muted small mb-3">
                                                    <i class="fas fa-graduation-cap text-primary me-2 fs-5"></i>
                                                    <span class="fw-semibold">{{ $jury->report->student->filiere->name ?? 'Non spécifié' }}</span>
                                                    <span class="mx-3">•</span>
                                                    <span class="fw-semibold">{{ $jury->report->student->level ?? 'N/A' }}</span>
                                                </div>
                                                <div class="bg-light p-3 rounded-3">
                                                    <small class="text-muted mb-1 d-block">Titre du
                                                        rapport</small>
                                                    <strong>{{ $jury->report->title }}</strong>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-4">
                                                <h6 class="fw-bold mb-3 text-uppercase text-muted"><i class="fas fa-users me-2"></i>Composition</h6>
                                                <div class="bg-light p-3 rounded-3 text-center">
                                                    <span class="badge bg-primary fs-6 px-4 py-2 mb-2 d-block">
                                                        {{ $jury->members->count() }}/4 membres
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-5">
                                            @if ($president)
                                            <div class="col-md-6 col-lg-3">
                                                <div class="text-center p-4 bg-gradient-to-br from-warning to-orange-500 text-white rounded-4 shadow-lg hover:scale-105 transition-all">
                                                    <div class="bg-white bg-opacity-20 p-3 rounded-circle mx-auto mb-3 d-inline-flex" style="width: 70px; height: 70px;">
                                                        <i class="fas fa-crown fa-2x"></i>
                                                    </div>
                                                    <h6 class="fw-bold mb-1">{{ $president->name }}</h6>
                                                    <div class="president-badge role-badge w-100">Président
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if ($encadreur)
                                            <div class="col-md-6 col-lg-3">
                                                <div class="text-center p-4 bg-gradient-to-br from-emerald-500 to-teal-500 text-white rounded-4 shadow-lg hover:scale-105 transition-all">
                                                    <div class="bg-white bg-opacity-20 p-3 rounded-circle mx-auto mb-3 d-inline-flex" style="width: 70px; height: 70px;">
                                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                                    </div>
                                                    <h6 class="fw-bold mb-1">{{ $encadreur->name }}</h6>
                                                    <div class="encadreur-badge role-badge w-100">Encadreur
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if ($rapporteur)
                                            <div class="col-md-6 col-lg-3">
                                                <div class="text-center p-4 bg-gradient-to-br from-blue-500 to-indigo-500 text-white rounded-4 shadow-lg hover:scale-105 transition-all">
                                                    <div class="bg-white bg-opacity-20 p-3 rounded-circle mx-auto mb-3 d-inline-flex" style="width: 70px; height: 70px;">
                                                        <i class="fas fa-file-signature fa-2x"></i>
                                                    </div>
                                                    <h6 class="fw-bold mb-1">{{ $rapporteur->name }}</h6>
                                                    <div class="rapporteur-badge role-badge w-100">
                                                        Rapporteur</div>
                                                </div>
                                            </div>
                                            @endif

                                            @if ($autres->isNotEmpty())
                                            @foreach ($autres->take(2) as $membre)
                                            <div class="col-md-6 col-lg-3">
                                                <div class="text-center p-4 bg-gradient-to-br from-slate-500 to-gray-600 text-white rounded-4 shadow-lg hover:scale-105 transition-all">
                                                    <div class="bg-white bg-opacity-20 p-3 rounded-circle mx-auto mb-3 d-inline-flex" style="width: 70px; height: 70px;">
                                                        <i class="fas fa-user fa-2x"></i>
                                                    </div>
                                                    <h6 class="fw-bold mb-1">{{ $membre->name }}</h6>
                                                    <div class="membre-badge role-badge w-100">
                                                        {{ $membre->pivot->role }}</div>
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>

                                        @if ($autres->count() > 2)
                                        <div class="alert alert-info border-0 rounded-4">
                                            <i class="fas fa-info-circle me-2"></i>
                                            {{ $autres->count() - 2 }} autre(s) membre(s)
                                        </div>
                                        @endif
                                    </div>

                                    <div class="modal-footer bg-light border-0 rounded-bottom-4 px-5 py-4">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-2"></i>Fermer
                                        </button>
                                        <div class="ms-auto">
                                            <button type="button" class="btn btn-warning jury-edit" data-jury="{{ $jury->id }}">
                                                <i class="fas fa-edit me-2"></i>Modifier
                                            </button>
                                            <a href="{{ route('admin.reports.index') }}#report-{{ $jury->report->id }}" class="btn btn-primary px-4">
                                                <i class="fas fa-eye me-2"></i>Voir rapport
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL EDIT (identique à avant) --}}
                        {{-- MODAL EDIT DIRECT --}}
                        <div class="modal fade" id="editJury{{ $jury->id }}" tabindex="-1">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content rounded-4 shadow-xl">
                                    <div class="modal-header bg-gradient-to-r from-warning to-orange-500 text-white border-0 rounded-top-4">
                                        <h5 class="modal-title mb-0">
                                            <i class="fas fa-edit me-2"></i>Modifier jury -
                                            {{ $jury->report->student->name }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.juries.update', $jury) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-body p-4">
                                            <div id="members-container-{{ $jury->id }}">
                                                @foreach ($jury->members as $member)
                                                <div class="member-row row align-items-end g-3 mb-3 p-3">
                                                    <div class="col-md-5">
                                                        <label class="form-label fw-semibold small mb-1">Membre</label>
                                                        <select name="members[][user_id]" class="form-select" required>
                                                            @foreach (\App\Models\User::role(['teacher', 'jury'])->where('department_id', auth()->user()->department_id)->get() as $u)
                                                            <option value="{{ $u->id }}" {{ $u->id == $member->id ? 'selected' : '' }}>
                                                                {{ $u->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold small mb-1">Rôle</label>
                                                        <select name="members[][role]" class="form-select" required>
                                                            <option value="president" {{ $member->pivot->role == 'president' ? 'selected' : '' }}>
                                                                👑 Président</option>
                                                            <option value="encadreur" {{ $member->pivot->role == 'encadreur' ? 'selected' : '' }}>
                                                                👨‍🏫 Encadreur</option>
                                                            <option value="rapporteur" {{ $member->pivot->role == 'rapporteur' ? 'selected' : '' }}>
                                                                📋 Rapporteur</option>
                                                            <option value="membre" {{ $member->pivot->role == 'membre' ? 'selected' : '' }}>
                                                                👤 Membre</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="button" class="btn btn-outline-danger w-100 remove-member h-100" onclick="removeMemberRow(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                                                <button type="button" class="btn btn-outline-success px-3" onclick="addMemberRow({{ $jury->id }})">
                                                    <i class="fas fa-plus me-2"></i>Ajouter membre
                                                </button>
                                                <div class="flex-grow-1"></div>
                                                <button type="submit" class="btn btn-success px-4">
                                                    <i class="fas fa-save me-2"></i>Sauvegarder
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $juries->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion boutons edit dans modals
            document.querySelectorAll('.jury-edit')
                .forEach(button => {

                    button.addEventListener('click', function(e) {

                        e.preventDefault();
                        e.stopPropagation();

                        const juryId = this.dataset.jury;

                        const detailModalEl =
                            document.getElementById(
                                `juryDetail${juryId}`
                            );

                        const detailModal =
                            bootstrap.Modal.getInstance(
                                detailModalEl
                            );

                        if (detailModal) {
                            detailModal.hide();
                        }

                        setTimeout(() => {

                            const editModal =
                                new bootstrap.Modal(
                                    document.getElementById(
                                        `editJury${juryId}`
                                    )
                                );

                            editModal.show();

                        }, 250);

                    });

                });

        });
        // ✅ FONCTIONS MODAL EDIT
        function addMemberRow(juryId) {
            const container = document.getElementById(`members-container-${juryId}`);
            const row = document.createElement('div');
            row.className = 'member-row row align-items-end g-2 mb-3 p-3 border rounded-3';
            row.innerHTML = `
        <div class="col-md-5">
            <label class="form-label fw-semibold small mb-1">Membre</label>
            <select name="members[][user_id]" class="form-select" required>
                ${getUsersOptions()}
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold small mb-1">Rôle</label>
            <select name="members[][role]" class="form-select" required>
                <option value="president">👑 Président</option>
                <option value="encadreur">👨‍🏫 Encadreur</option>
                <option value="rapporteur">📋 Rapporteur</option>
                <option value="membre">👤 Membre</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-outline-danger w-100 remove-member h-100" onclick="removeMemberRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
            container.appendChild(row);
        }

        function removeMemberRow(button) {
            button.closest('.member-row').remove();
        }

        function getUsersOptions() {
            // Simplifié : tu peux passer les users via data attribute ou fetch
            return Array.from(document.querySelectorAll('select[name="members[][user_id]"] option'))
                .map(opt => `<option value="${opt.value}">${opt.text}</option>`)
                .join('');
        }

    </script>
</x-app-layout>

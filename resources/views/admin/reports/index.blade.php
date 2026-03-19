<x-app-layout>
    <style>
        :root {
            --purple: #6f42c1;
            --blue-main: #3681B6;
            --gray-50: #f9fafb;
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* ✅ MODAL CORRECTION COMPLÈTE */
        .modal-backdrop {
            display: none
        }

        .modal-content {
            z-index: 1070 !important;
        }

        .modal-xl-custom {
            max-width: 1200px !important;
            margin: 1.75rem auto !important;
        }

        .modal-xl-custom .modal-content {
            border-radius: 24px !important;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.5) !important;
        }

        /* Toast au-dessus de tout */
        #liveToast-container {
            z-index: 1070 !important;
        }

        /* Styles identiques */
        .badge-soft {
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-soft-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-soft-info {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-soft-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-soft-muted {
            background: #e5e7eb;
            color: #374151;
        }

        .card-neo {
            border-radius: 1.5rem;
            border: 0;
            box-shadow: var(--shadow-xl);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-neo:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .avatar-ring {
            box-shadow: 0 0 0 4px rgba(59, 130, 246, .2);
            border: 3px solid white;
        }

        .btn-modern {
            border-radius: 12px;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            border: 2px solid;
            transition: all 0.2s ease;
        }

        .btn-purple {
            background: var(--purple);
            border-color: var(--purple);
            color: white;
        }

        .btn-purple:hover {
            background: #5d2ea7;
            border-color: #5d2ea7;
        }

        .comment-bubble {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 20px;
            border-left: 4px solid #3b82f6;
            padding: 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .comment-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .version-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>

    {{-- ✅ TOAST Z-INDEX CORRIGÉ --}}
    <div id="liveToast-container" class="position-fixed top-0 end-0 p-4" style="z-index: 1070 !important;">
        <div id="liveToast" class="toast border-0 shadow-lg" style="border-radius: 16px;">
            <div class="toast-body d-flex align-items-center p-3">
                <i class="fas fa-check-circle me-2 fs-5"></i><span id="toast-message"></span>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        {{-- Header --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-gradient-to-br from-purple to-blue-main text-success rounded-3 p-3 shadow-lg me-4"
                            style="width: 70px; height: 70px;">
                            <i class="fas fa-file-signature fa-2x"></i>
                        </div>
                        <div>
                            <h1 class="h2 fw-bold mb-2 lh-sm">Tous les rapports</h1>
                            <div class="d-flex align-items-center gap-3 text-muted small">
                                <span><i class="fas fa-database me-1"></i>{{ \App\Models\Report::count() }} total</span>
                                <span class="vr mx-2"></span>
                                <span>Dernière mise à jour : {{ now()->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert success --}}
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-lg rounded-3 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-20 text-success rounded-circle p-2 me-3">
                        <i class="fas fa-check fs-5"></i>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
            </div>
        @endif

        @if ($reports->isEmpty())
            {{-- Empty state --}}
            <div class="text-center py-8">
                <div class="card-neo mx-auto" style="max-width: 500px;">
                    <div class="card-body py-8">
                        <div class="bg-gray-50 rounded-full p-6 mx-auto mb-4 shadow-lg d-inline-flex"
                            style="width: 120px; height: 120px;">
                            <i class="fas fa-file-circle-plus fa-3x text-muted"></i>
                        </div>
                        <h3 class="h4 fw-bold text-muted mb-3">Aucun rapport pour le moment</h3>
                        <p class="text-muted mb-4 lh-lg">Les étudiants n'ont pas encore soumis leurs rapports de stage.
                        </p>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-primary btn-modern shadow-lg px-4">
                            <i class="fas fa-users me-2"></i>Gérer les étudiants
                        </a>
                    </div>
                </div>
            </div>
        @else
            {{-- Liste rapports --}}
            <div class="card-neo">
                <div class="card-header bg-white border-bottom border-0 rounded-top-4 px-5 py-4">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <span class="bg-primary bg-opacity-10 text-primary rounded-2 p-2">
                            <i class="fas fa-list-ul"></i>
                        </span>
                        Liste des rapports ({{ $reports->count() }})
                    </h5>
                </div>

                <div class="card-body p-0 bg-light">
                    <div class="row g-2 p-2 p-md-2">
                        @foreach ($reports as $report)
                            @php
                                $status = $report->status;
                                $badgeClass = match ($status) {
                                    'Soumis' => 'badge-soft-warning',
                                    'Affecté' => 'badge-soft-info',
                                    'Validé' => 'badge-soft-success',
                                    default => 'badge-soft-muted',
                                };
                                $statusIcon = match ($status) {
                                    'Soumis' => 'fas fa-inbox',
                                    'Affecté' => 'fas fa-user-check',
                                    'Validé' => 'fas fa-check-circle',
                                    default => 'far fa-clock',
                                };
                            @endphp

                            <div class="col-xl-4 col-lg-6">
                                <div class="report-card h-100 card border-0 shadow-lg rounded-3 overflow-hidden">
                                    <div class="card-body p-4">
                                        {{-- Étudiant + Titre --}}
                                        <div class="d-flex align-items-start mb-4">
                                            <img src="https://ui-avatars.com/api/?name={{ $report->student?->name ?? 'Student' }}&background=2563EB&color=fff&size=50&rounded=true"
                                                class="rounded-circle me-3 flex-shrink-0 avatar-ring shadow-sm"
                                                width="50" height="50">
                                            <div class="flex-grow-1 min-w-0">
                                                <h6 class="fw-bold mb-1 lh-sm">{{ $report->student?->name }}</h6>
                                                <small class="text-muted">{{ $report->student?->email }}</small>
                                                <div class="mt-2 fw-semibold text-truncate lh-sm"
                                                    style="font-size: 0.95rem;">
                                                    {{ Str::limit($report->title, 70) }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Badges --}}
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <span class="badge {{ $badgeClass }} px-3 py-2">
                                                <i class="{{ $statusIcon }} me-1"></i>{{ $status }}
                                            </span>
                                            @if ($report->file_path)
                                                <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank"
                                                    class="badge bg-danger bg-opacity-10 text-danger text-decoration-none px-3 py-2">
                                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                                </a>
                                            @endif
                                        </div>

                                        {{-- Métadonnées --}}
                                        <div class="d-flex flex-wrap gap-2 text-muted small mb-4">
                                            <span><i
                                                    class="fas fa-calendar me-1"></i>{{ $report->created_at->format('d MMM') }}</span>
                                            <span class="vr mx-2"></span>
                                            <span><i
                                                    class="fas fa-clock me-1"></i>{{ $report->created_at->diffForHumans() }}</span>
                                        </div>

                                        {{-- Actions principales --}}
                                        <div class="row g-2 mb-3">
                                            <div class="col-12">
                                                @php
                                                    $adminDeptId = auth()->user()->department_id;
                                                    $teachersInDept = \App\Models\User::role('teacher')
                                                        ->where('department_id', $adminDeptId)
                                                        ->get();
                                                @endphp
                                                <select id="teacher_{{ $report->id }}"
                                                    class="form-select form-select-sm">
                                                    <option value="">Enseignant</option>
                                                    @foreach ($teachersInDept as $teacher)
                                                        <option value="{{ $teacher->id }}"
                                                            {{ $report->teacher_id == $teacher->id ? 'selected' : '' }}>
                                                            {{ $teacher->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-success btn-sm w-100 mt-1"
                                                    onclick="assignTeacher({{ $report->id }})">
                                                    <i class="fas fa-user-plus"></i>
                                                    {{ $report->teacher_id ? 'Changer' : 'Affecter' }}
                                                </button>
                                            </div>
                                            <div class="col-12">
                                                <button
                                                    class="btn bg-success text-light btn-outline-purple btn-sm w-100"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#juryModal{{ $report->id }}"
                                                    title="Sélectionner le jury">
                                                    <i class="fas fa-gavel me-2"></i>
                                                    @if ($report->juryMembers->isNotEmpty())
                                                        <span class="badge bg-purple text-white rounded-pill ms-1">
                                                            {{ $report->juryMembers->count() }}/4
                                                        </span>
                                                        {{ $report->juryPresident?->name ?? 'Jury' }}
                                                    @else
                                                        Affecter jury
                                                    @endif
                                                </button>
                                                @if ($report->juryMembers->isNotEmpty())
                                                    <div class="mt-1 small text-muted">
                                                        <i
                                                            class="fas fa-users me-1"></i>{{ $report->juryMembers->count() }}
                                                        membre(s)
                                                        @if ($report->juryPresident?->name)
                                                            <br><small class="text-primary">👑
                                                                {{ $report->juryPresident->name }}</small>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- ✅ BOUTON COMMENTAIRES --}}
                                        <hr class="my-3">
                                        <div class="text-center">
                                            <button class="btn btn-outline-primary btn-modern w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#commentsModal{{ $report->id }}">
                                                <i class="fas fa-comments me-2"></i>
                                                Commentaires ({{ optional($report->comments)->count() ?? 0 }})
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- ✅ MODAL COMMENTAIRES (CORRIGÉE) --}}
                                <div class="modal fade" id="commentsModal{{ $report->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div
                                        class="modal-dialog modal-xl-custom modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div
                                                class="modal-header bg-gradient-to-r from-primary to-info border-0 text-white rounded-top-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-white bg-opacity-20 p-2 rounded-3">
                                                        <i class="fas fa-comments fa-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="modal-title mb-1 fw-bold">Commentaires du rapport
                                                        </h5>
                                                        <small>{{ Str::limit($report->title, 80) }}</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body p-0">
                                                {{-- Sidebar infos --}}
                                                <div class="bg-light border-bottom p-4">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <strong>{{ $report->student?->name }}</strong>
                                                            <div class="text-muted small">
                                                                {{ $report->student?->email }}</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span class="badge {{ $badgeClass }} px-3 py-2">
                                                                <i
                                                                    class="{{ $statusIcon }} me-1"></i>{{ $status }}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-3 text-end">
                                                            <span
                                                                class="version-tag">{{ $report->version ?? 'v1' }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Liste commentaires --}}
                                                <div class="p-4" style="max-height: 500px; overflow-y: auto;">
                                                    @forelse($report->comments ?? [] as $comment)
                                                        <div class="comment-bubble">
                                                            <div class="d-flex align-items-start gap-3 mb-3">
                                                                <img src="https://ui-avatars.com/api/?name={{ $comment->user?->name ?? 'User' }}&background=3b82f6&color=fff&size=42"
                                                                    class="comment-avatar flex-shrink-0"
                                                                    alt="Avatar">
                                                                <div class="flex-grow-1">
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-between mb-1">
                                                                        <strong
                                                                            class="text-dark">{{ $comment->user?->name ?? 'Utilisateur' }}</strong>
                                                                        <small
                                                                            class="text-muted">{{ $comment->created_at->format('d/m H:i') }}</small>
                                                                    </div>
                                                                    <div class="text-muted small lh-sm">
                                                                        {{ $comment->comment }}</div>
                                                                </div>
                                                            </div>
                                                            @if ($comment->file_path)
                                                                <div class="ms-5 p-2 bg-white rounded-2 shadow-sm">
                                                                    <a href="{{ asset('storage/' . $comment->file_path) }}"
                                                                        target="_blank" class="text-decoration-none">
                                                                        <i
                                                                            class="fas fa-paperclip me-1 text-muted"></i>Fichier
                                                                        joint
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <div class="text-center py-6 text-muted">
                                                            <i class="fas fa-comment-slash fa-2x mb-3 opacity-50"></i>
                                                            <p>Aucun commentaire pour ce rapport</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light border-0 rounded-bottom-4 px-4 py-3">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i>Fermer
                                                </button>
                                                <button type="button" class="btn btn-primary"
                                                    onclick="addComment({{ $report->id }})">
                                                    <i class="fas fa-reply me-1"></i>Ajouter commentaire
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ⭐ MODAL SÉLECTION JURY --}}
                                <div class="modal fade" id="juryModal{{ $report->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header bg-gradient-purple border-0 text-white">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-white bg-opacity-20 p-3 rounded-3">
                                                        <i class="fas fa-gavel fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="modal-title mb-1 fw-bold">Sélectionner le jury</h5>
                                                        <small>{{ Str::limit($report->title, 60) }}</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body p-0">
                                                {{-- Filtres --}}
                                                <div class="p-4 border-bottom bg-light">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <select id="jury_dept_{{ $report->id }}"
                                                                class="form-select form-select-sm">
                                                                <option value="">Tous les départements</option>
                                                                @foreach (\App\Models\Department::all() as $dept)
                                                                    <option value="{{ $dept->id }}">
                                                                        {{ $dept->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <select id="jury_filiere_{{ $report->id }}"
                                                                class="form-select form-select-sm">
                                                                <option value="">Toutes spécialités</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text"
                                                                    id="jury_search_{{ $report->id }}"
                                                                    class="form-control" placeholder="Rechercher...">
                                                                <button class="btn btn-outline-secondary"
                                                                    onclick="filterJury({{ $report->id }})">
                                                                    <i class="fas fa-search"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="jury_show_all_{{ $report->id }}" checked>
                                                            <label class="form-check-label small"
                                                                for="jury_show_all_{{ $report->id }}">
                                                                Inclure tous les enseignants (pas seulement jury)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Tableau --}}
                                                <div class="table-responsive" style="max-height: 500px;">
                                                    <table class="table table-hover mb-0"
                                                        id="juryTable{{ $report->id }}">
                                                        <thead class="table-light sticky-top">
                                                            <tr>
                                                                <th><input type="checkbox"
                                                                        id="selectAll_{{ $report->id }}"
                                                                        onchange="toggleSelectAll({{ $report->id }})">
                                                                </th>
                                                                <th>Nom</th>
                                                                <th>Département</th>
                                                                <th>Spécialité</th>
                                                                <th>Sexe</th>
                                                                <th>Rôles</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach (\App\Models\User::role(['teacher', 'jury'])->where('department_id', auth()->user()->department_id)->get() as $member)
                                                                <tr data-name="{{ strtolower($member->name) }}"
                                                                    data-dept="{{ $member->department_id }}"
                                                                    data-filiere="{{ $member->filiere_id ?? 0 }}">
                                                                    <td>
                                                                        <input type="checkbox" class="jury-checkbox"
                                                                            value="{{ $member->id }}"
                                                                            data-name="{{ $member->name }}"
                                                                            {{ $report->juryMembers->contains($member->id) ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <img src="https://ui-avatars.com/api/?name={{ $member->name }}&background=6f42c1&color=fff&size=32"
                                                                                class="rounded-circle me-2"
                                                                                width="32" height="32">
                                                                            <strong>{{ $member->name }}</strong>
                                                                        </div>
                                                                    </td>
                                                                    <td>{{ $member->department?->name ?? '-' }}</td>
                                                                    <td>{{ $member->filiere?->name ?? ($member->specialite ?? '-') }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge bg-{{ $member->sexe == 'M' ? 'primary' : 'pink' }} text-white">
                                                                            {{ $member->sexe == 'M' ? 'H' : 'F' }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        @if ($member->hasRole('teacher'))
                                                                            <span
                                                                                class="badge bg-success text-white">Teacher</span>
                                                                        @endif
                                                                        @if ($member->hasRole('jury'))
                                                                            <span
                                                                                class="badge bg-info text-white">Jury</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Compteur sélection --}}
                                                <div
                                                    class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                                                    <span id="juryCount_{{ $report->id }}"
                                                        class="fw-bold text-purple">
                                                        <span id="selectedCount_{{ $report->id }}">0</span>/4
                                                        membres sélectionnés
                                                    </span>
                                                    <button class="btn btn-purple px-4"
                                                        onclick="confirmJury({{ $report->id }})"
                                                        id="confirmBtn_{{ $report->id }}" disabled>
                                                        <i class="fas fa-check me-2"></i>Affecter jury
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            @if (method_exists($reports, 'links'))
                <div class="mt-5">{{ $reports->links('pagination::bootstrap-5') }}</div>
            @endif
        @endif
    </div>

    {{-- ✅ JS COMPLÈT --}}
    <script>
        function showToast(message, type = 'success') {
            const toast = new bootstrap.Toast(document.getElementById('liveToast'));
            document.getElementById('toast-message').innerHTML =
                `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}`;
            document.getElementById('liveToast').className =
                `toast border-0 shadow-lg bg-${type === 'success' ? 'success' : 'danger'} text-white`;
            toast.show();
        }

        function assignTeacher(reportId) {
            const teacherId = document.getElementById(`teacher_${reportId}`).value;
            if (!teacherId) return showToast('Veuillez sélectionner un enseignant', 'danger');

            fetch(`{{ url('/reports') }}/${reportId}/assign`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        teacher_id: teacherId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Erreur', 'danger');
                    }
                })
                .catch(() => showToast('Erreur réseau', 'danger'));
        }

        function assignJury(reportId) {
            const juryIds = Array.from(document.querySelector(`#jury_${reportId}`).selectedOptions).map(opt => opt.value);

            if (juryIds.length === 0) return showToast('Veuillez sélectionner au moins 1 juré', 'danger');
            if (juryIds.length > 4) return showToast('Maximum 4 membres du jury', 'danger');

            fetch(`{{ url('/reports') }}/${reportId}/assign-jury`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        jury_ids: juryIds
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Erreur', 'danger');
                    }
                })
                .catch(() => showToast('Erreur réseau', 'danger'));
        }


        function addComment(reportId) {
            showToast('Fonctionnalité en développement', 'info');
        }

        // Limite select multiple à 4
        document.querySelectorAll('select[multiple][data-max]').forEach(select => {
            select.addEventListener('change', function() {
                if (this.selectedOptions.length > this.dataset.max) {
                    Array.from(this.selectedOptions)
                        .slice(this.dataset.max)
                        .forEach(option => option.selected = false);
                    showToast(`Maximum ${this.dataset.max} membres`, 'warning');
                }
            });
        });

        // ⭐ JURY MODAL FUNCTIONS
        let juryData_{{ $report->id }} = @json(\App\Models\User::role(['teacher', 'jury'])->where('department_id', auth()->user()->department_id)->get());

        function filterJury(reportId) {
            const dept = document.getElementById(`jury_dept_${reportId}`).value;
            const filiere = document.getElementById(`jury_filiere_${reportId}`).value;
            const search = document.getElementById(`jury_search_${reportId}`).value.toLowerCase();
            const showAll = document.getElementById(`jury_show_all_${reportId}`).checked;

            document.querySelectorAll(`#juryTable${reportId} tbody tr`).forEach(row => {
                const name = row.dataset.name;
                const deptId = row.dataset.dept;
                const filiereId = row.dataset.filiere;

                const matchesDept = !dept || deptId == dept;
                const matchesFiliere = !filiere || filiereId == filiere;
                const matchesSearch = !search || name.includes(search);
                const matchesRole = showAll || row.querySelector('.badge.bg-info');

                row.style.display = (matchesDept && matchesFiliere && matchesSearch && matchesRole) ? '' : 'none';
            });
            updateJuryCount(reportId);
        }

        function toggleSelectAll(reportId) {
            const selectAll = document.getElementById(`selectAll_${reportId}`).checked;
            document.querySelectorAll(`#juryModal${reportId} .jury-checkbox`).forEach(cb => {
                if (selectAll && document.querySelectorAll(`#juryModal${reportId} .jury-checkbox:checked`).length <
                    4) {
                    cb.checked = true;
                } else {
                    cb.checked = false;
                }
            });
            updateJuryCount(reportId);
        }

        function updateJuryCount(reportId) {
            const count = document.querySelectorAll(`#juryModal${reportId} .jury-checkbox:checked`).length;
            document.getElementById(`selectedCount_${reportId}`).textContent = count;
            document.getElementById(`confirmBtn_${reportId}`).disabled = count === 0 || count > 4;
        }

        function confirmJury(reportId) {
            const juryIds = Array.from(document.querySelectorAll(`#juryModal${reportId} .jury-checkbox:checked`)).map(cb =>
                cb.value);

            if (juryIds.length > 4) {
                showToast('Maximum 4 membres du jury', 'danger');
                return;
            }

            fetch(`{{ url('/reports') }}/${reportId}/assign-jury`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        jury_ids: juryIds
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        bootstrap.Modal.getInstance(document.getElementById(`juryModal${reportId}`)).hide();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Erreur', 'danger');
                    }
                })
                .catch(() => showToast('Erreur réseau', 'danger'));
        }

        // Event listeners pour chaque modal jury
        document.querySelectorAll('[id^="juryModal"]').forEach(modal => {
            const reportId = modal.id.match(/juryModal(\d+)/)[1];

            // Filtres
            document.getElementById(`jury_dept_${reportId}`)?.addEventListener('change', () => filterJury(
            reportId));
            document.getElementById(`jury_search_${reportId}`)?.addEventListener('keyup', () => filterJury(
                reportId));
            document.getElementById(`jury_show_all_${reportId}`)?.addEventListener('change', () => filterJury(
                reportId));

            // Checkboxes
            modal.querySelectorAll('.jury-checkbox').forEach(cb => {
                cb.addEventListener('change', () => {
                    if (document.querySelectorAll(`#juryModal${reportId} .jury-checkbox:checked`)
                        .length > 4) {
                        cb.checked = false;
                        showToast('Maximum 4 membres', 'warning');
                    }
                    updateJuryCount(reportId);
                });
            });
        });
    </script>
</x-app-layout>

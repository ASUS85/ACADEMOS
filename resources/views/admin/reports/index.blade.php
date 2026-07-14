<x-app-layout>
    @php
    $currentUser = auth()->user();
    $isAdmin = $currentUser->hasAnyRole(['admin', 'superadmin']);
    $pageTitle = $currentUser->hasRole('superadmin') ? 'Rapports globaux' : 'Rapports du département';
    $searchValue = request('search');
    @endphp

    <style>
        .reports-hero {
            background: linear-gradient(135deg, #0f172a 0%, #0f766e 55%, #2563eb 100%);
            color: #fff;
        }

        .reports-panel {
            border: 0;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 10px 10px -5px rgba(15, 23, 42, 0.04);
        }

        .table thead th {
            background: #f8fafc;
            border-bottom: 0;
            color: #475569;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .report-preview-frame {
            width: 100%;
            height: 68vh;
            border: 0;
            background: #fff;
        }

        .report-badge {
            border-radius: 999px;
            padding: .38rem .75rem;
            font-size: .75rem;
            font-weight: 600;
        }

        .report-badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .report-badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .report-badge-info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .report-badge-muted {
            background: #e5e7eb;
            color: #374151;
        }

        .jury-stack-modal {
            z-index: 1065;
        }

        .jury-stack-backdrop {
            z-index: 1060;
        }

    </style>

    <div class="container-fluid py-4">
        <div class="card reports-hero border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 mb-3">{{ $pageTitle }}</span>
                        <h1 class="display-6 fw-bold mb-2">Tableau des rapports</h1>
                        <p class="lead mb-0 opacity-90">Recherche, prévisualisation embarquée, téléchargement et
                            suppression depuis un seul écran.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('reports.index') }}" class="btn btn-light btn-lg px-4 py-3 shadow-sm">
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

        <div class="card reports-panel mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-8">
                        <label class="form-label fw-semibold text-muted">Recherche</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="search" name="search" value="{{ $searchValue }}" class="form-control" placeholder="Titre, étudiant, matricule, enseignant...">
                        </div>
                    </div>
                    <div class="col-lg-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1" data-loader-target="#globalLoader">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-lg" data-loader-target="#globalLoader">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if ($reports->isEmpty())
        <div class="card reports-panel">
            <div class="card-body py-5 text-center">
                <i class="fas fa-file-circle-question fa-3x text-muted mb-3"></i>
                <h3 class="h5 fw-bold mb-2">Aucun rapport trouvé</h3>
                <p class="text-muted mb-0">Ajustez votre recherche ou attendez les prochaines soumissions.</p>
            </div>
        </div>
        @else
        <div class="card reports-panel overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="h5 fw-bold mb-1">Rapports</h2>
                    <div class="text-muted small">{{ $reports->total() }} résultat(s) • page {{ $reports->currentPage()
                            }} / {{ $reports->lastPage() }}</div>
                </div>
                <span class="report-badge report-badge-muted">Dernière mise à jour {{ now()->format('d/m/Y H:i')
                        }}</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Étudiant</th>
                            <th>Titre</th>
                            <th>Statut</th>
                            <th>Encadreur</th>
                            <th>Jury</th>
                            <th>Date</th>
                            <th class="text-end" style="width: 190px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        @php
                        $status = $report->status;
                        $statusClass = match ($status) {
                        'Validé final' => 'report-badge-success',
                        'Validé', 'En attente jury' => 'report-badge-info',
                        'Soumis', 'Affecté', 'commenté' => 'report-badge-warning',
                        default => 'report-badge-muted',
                        };
                        $reportPayload = $report->loadMissing([
                        'student.filiere',
                        'teacher',
                        'latestVersion',
                        'comments.user',
                        'juryGroup.members'
                        ]);
                        $reportPayloadData = base64_encode(json_encode($reportPayload));
                        $reportPreviewUrl = route('reports.preview', $report);
                        $reportDownloadUrl = route('reports.download', $report);
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $report->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $report->student?->name ?? '-' }}</div>
                                <div class="text-muted small">{{ $report->student?->matricule ??
                                $report->student?->email ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($report->title, 50) }}</div>
                                <div class="text-muted small">{{ $report->comments_count ?? $report->comments->count()
                                                        }} commentaire(s)</div>
                            </td>
                            <td><span class="report-badge {{ $statusClass }}">{{ $status }}</span></td>
                            <td>{{ $report->teacher?->name ?? 'Non affecté' }}</td>
                            <td>
                                @if ($report->juryGroup?->members?->count())
                                <span class="report-badge report-badge-info">{{ $report->juryGroup->members->count() }}
                                    membre(s)</span>
                                @else
                                <span class="report-badge report-badge-muted">Aucun</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $report->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $report->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm px-3" data-report="{{ $reportPayloadData }}" data-preview-url="{{ $reportPreviewUrl }}" data-download-url="{{ $reportDownloadUrl }}" onclick="openReportDetails(this)">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm px-3" data-confirm-title="Suppression du rapport" data-confirm-message="Confirmez-vous la suppression de ce rapport ?" data-confirm-submit-label="Oui, supprimer" data-confirm-form-id="deleteReportForm{{ $report->id }}">
                                        <i class="fas fa-trash me-1"></i>Suppr.
                                    </button>
                                </div>

                                <form id="deleteReportForm{{ $report->id }}" method="POST" action="{{ route('reports.destroy', $report) }}" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $reports->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    <div class="modal fade" id="reportDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0" id="reportDetailsTitle">Détails du rapport</h5>
                        <small class="text-white-50">Prévisualisation et téléchargement dans l'application</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-8 border-end">
                            <div class="p-4 border-bottom bg-light">
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                    <span id="reportDetailsStatus" class="report-badge report-badge-muted">Statut</span>
                                    <span id="reportDetailsVersion" class="report-badge report-badge-info">v1</span>
                                </div>
                                <h5 class="fw-bold mb-2" id="reportDetailsTitleText">Rapport</h5>
                                <div class="text-muted small" id="reportDetailsMeta">Chargement...</div>
                            </div>
                            <div id="reportDetailsPreviewWrap" class="p-3">
                                <iframe id="reportDetailsPreview" class="report-preview-frame" src=""></iframe>
                                <div id="reportDetailsPreviewFallback" class="alert alert-light border d-none mb-0">
                                    L'aperçu direct est disponible pour les PDF uniquement. Utilisez le téléchargement
                                    si le fichier est dans un autre format.
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="p-4">
                                <div class="d-grid gap-2 mb-4">
                                    <a id="reportDetailsDownload" href="#" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="fas fa-download me-2"></i>Télécharger
                                    </a>
                                    <button type="button" class="btn btn-success btn-lg" onclick="openJuryAssignmentFromDetails()">
                                        <i class="fas fa-gavel me-2"></i>Affecter jury
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-lg" data-bs-dismiss="modal">
                                        Fermer
                                    </button>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">Informations</h6>
                                    <div class="small text-muted mb-2">Étudiant</div>
                                    <div class="fw-semibold mb-3" id="reportDetailsStudent">-</div>

                                    <div class="small text-muted mb-2">Matricule / Email</div>
                                    <div class="fw-semibold mb-3" id="reportDetailsStudentMeta">-</div>

                                    <div class="small text-muted mb-2">Filière</div>
                                    <div class="fw-semibold mb-3" id="reportDetailsFiliere">-</div>

                                    <div class="small text-muted mb-2">Encadreur</div>
                                    <div class="fw-semibold mb-3" id="reportDetailsTeacher">-</div>

                                    <div class="small text-muted mb-2">Jury</div>
                                    <div class="fw-semibold mb-3" id="reportDetailsJury">-</div>
                                    <div id="reportDetailsJuryMembers" class="d-flex flex-wrap gap-2"></div>
                                </div>

                                @if ($isAdmin)
                                <div class="border-top pt-4">
                                    <h6 class="fw-bold mb-3">Affecter / modifier l'encadreur</h6>
                                    <select id="reportTeacherSelect" class="form-select mb-2">
                                        <option value="">Choisir un enseignant</option>
                                        @foreach ($availableTeachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-success w-100" onclick="assignTeacherFromDetails()">
                                        <i class="fas fa-user-check me-2"></i>Affecter
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($isAdmin)
    <div class="modal fade" id="juryAssignmentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0">Affectation du jury</h5>
                        <small class="text-white-50">Président, rapporteur et membres du département</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info border-0 mb-4">
                        <strong>Encadreur courant :</strong> <span id="juryAssignmentSupervisor">-</span>
                    </div>

                    <form id="juryAssignmentForm" method="POST" action="{{ route('reports.assign-jury', ['report' => 0]) }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Président</label>
                            <select id="juryPresidentSelect" name="president_id" class="form-select" required>
                                <option value="">Choisir un enseignant</option>
                                @foreach ($availableTeachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rapporteur</label>
                            <select id="juryRapporteurSelect" name="rapporteur_id" class="form-select" required>
                                <option value="">Choisir un enseignant</option>
                                @foreach ($availableTeachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Membre supplémentaire 1</label>
                            <select id="juryMemberSelect1" class="form-select">
                                <option value="">Aucun</option>
                                @foreach ($availableTeachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Membre supplémentaire 2</label>
                            <select id="juryMemberSelect2" class="form-select">
                                <option value="">Aucun</option>
                                @foreach ($availableTeachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-warning border-0 mb-0">
                                L'encadreur du rapport sera automatiquement ajouté au jury avec le poste d'encadreur.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-success" onclick="submitJuryAssignment()">
                        <i class="fas fa-save me-2"></i>Sauvegarder le jury
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        let currentReportId = null;
        let currentReportData = null;

        window.openReportDetails = function(button) {
            let report;
            const rawPayload = button.dataset.report || '';

            try {
                const decodedPayload = rawPayload.startsWith('ey') ? atob(rawPayload) : rawPayload;
                report = JSON.parse(decodedPayload);
            } catch (error) {
                console.error('Impossible de parser les données du rapport :', error);
                return;
            }

            currentReportId = report.id;
            currentReportData = report;

            const latestVersion = report.latest_version || {};
            const student = report.student || {};
            const filiere = student.filiere || {};
            const teacher = report.teacher || {};
            const juryMembers = report.juryGroup && Array.isArray(report.juryGroup.members) ? report.juryGroup.members : [];

            const previewFile = latestVersion.file_path || report.file_path || '';
            const previewUrl = button.dataset.previewUrl || '';
            const downloadUrl = button.dataset.downloadUrl || previewUrl;
            const previewable = previewFile && previewFile.toLowerCase().endsWith('.pdf');

            const status = report.status || 'N/A';
            let statusClass = 'report-badge-muted';

            if (status === 'Validé final') {
                statusClass = 'report-badge-success';
            } else if (['Validé', 'En attente jury'].includes(status)) {
                statusClass = 'report-badge-info';
            } else if (['Soumis', 'Affecté', 'commenté'].includes(status)) {
                statusClass = 'report-badge-warning';
            }

            document.getElementById('reportDetailsTitle').textContent = `Rapport #${report.id}`;
            document.getElementById('reportDetailsTitleText').textContent = report.title || 'Rapport';

            const statusBadge = document.getElementById('reportDetailsStatus');
            statusBadge.className = `report-badge ${statusClass}`;
            statusBadge.textContent = status;

            document.getElementById('reportDetailsVersion').textContent = latestVersion.version || 'v1';
            document.getElementById('reportDetailsMeta').textContent = `Créé le ${new Date(report.created_at).toLocaleString()} • Mis à jour le ${new Date(report.updated_at).toLocaleString()}`;
            document.getElementById('reportDetailsStudent').textContent = student.name || '-';
            document.getElementById('reportDetailsStudentMeta').textContent = student.matricule || student.email || '-';
            document.getElementById('reportDetailsFiliere').textContent = filiere.name || '-';
            document.getElementById('reportDetailsTeacher').textContent = teacher.name || 'Non affecté';
            document.getElementById('reportDetailsJury').textContent = juryMembers.length ? `${juryMembers.length} membre(s)` : 'Aucun';

            const juryMembersWrap = document.getElementById('reportDetailsJuryMembers');
            if (juryMembersWrap) {
                juryMembersWrap.innerHTML = juryMembers.length ?
                    juryMembers.map((member) => {
                        const role = member.pivot.role || 'membre';
                        return `<span class="badge bg-light text-dark border">${member.name} • ${role}</span>`;
                    }).join('') :
                    '<span class="text-muted small">Aucun membre affecté</span>';
            }

            const previewFrame = document.getElementById('reportDetailsPreview');
            const previewFallback = document.getElementById('reportDetailsPreviewFallback');
            const downloadLink = document.getElementById('reportDetailsDownload');

            if (previewable && previewUrl) {
                previewFrame.classList.remove('d-none');
                previewFallback.classList.add('d-none');
                previewFrame.src = previewUrl;
            } else {
                previewFrame.src = '';
                previewFrame.classList.add('d-none');
                previewFallback.classList.remove('d-none');
            }

            downloadLink.href = downloadUrl || '#';

            const teacherSelect = document.getElementById('reportTeacherSelect');
            if (teacherSelect) {
                teacherSelect.value = teacher.id || '';
            }

            const modalEl = document.getElementById('reportDetailsModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();

            modalEl.addEventListener('hidden.bs.modal', () => {
                previewFrame.src = '';
                currentReportId = null;
            }, {
                once: true
            });
        };

        window.assignTeacherFromDetails = function() {
            const teacherSelect = document.getElementById('reportTeacherSelect');
            const teacherId = teacherSelect ? teacherSelect.value : '';

            if (!currentReportId || !teacherId) {
                return;
            }

            fetch(`{{ url('/reports') }}/${currentReportId}/assign`, {
                    method: 'POST'
                    , headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        , 'Content-Type': 'application/json'
                        , 'Accept': 'application/json'
                    }
                    , body: JSON.stringify({
                        teacher_id: teacherId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        };

        window.openJuryAssignmentFromDetails = function() {
            if (!currentReportData) {
                return;
            }

            const supervisor = document.getElementById('juryAssignmentSupervisor');
            const form = document.getElementById('juryAssignmentForm');
            const presidentSelect = document.getElementById('juryPresidentSelect');
            const rapporteurSelect = document.getElementById('juryRapporteurSelect');
            const memberSelect1 = document.getElementById('juryMemberSelect1');
            const memberSelect2 = document.getElementById('juryMemberSelect2');
            const reportTeacher = currentReportData.teacher || {};
            const juryMembers = currentReportData.juryGroup && Array.isArray(currentReportData.juryGroup.members) ? currentReportData.juryGroup.members : [];

            supervisor.textContent = reportTeacher.name || 'Non affecté';
            form.action = `{{ url('/reports') }}/${currentReportData.id}/assign-jury`;

            const selectedPresidents = juryMembers.find(member => (member.pivot && member.pivot.role) === 'president');
            const selectedRapporteurs = juryMembers.find(member => (member.pivot && member.pivot.role) === 'rapporteur');
            const extraMembers = juryMembers.filter(member => {
                const role = member.pivot ? member.pivot.role : '';
                return !['president', 'rapporteur', 'encadreur'].includes(role);
            });

            presidentSelect.value = selectedPresidents ? selectedPresidents.id : '';
            rapporteurSelect.value = selectedRapporteurs ? selectedRapporteurs.id : '';
            memberSelect1.value = extraMembers[0] ? extraMembers[0].id : '';
            memberSelect2.value = extraMembers[1] ? extraMembers[1].id : '';

            bootstrap.Modal.getOrCreateInstance(document.getElementById('juryAssignmentModal')).show();
        };

        window.submitJuryAssignment = function() {
            if (!currentReportData) {
                return;
            }

            const form = document.getElementById('juryAssignmentForm');
            const memberSelect1 = document.getElementById('juryMemberSelect1');
            const memberSelect2 = document.getElementById('juryMemberSelect2');
            const presidentSelect = document.getElementById('juryPresidentSelect');
            const rapporteurSelect = document.getElementById('juryRapporteurSelect');

            const memberIds = [memberSelect1 ? memberSelect1.value : '', memberSelect2 ? memberSelect2.value : ''].filter(Boolean);

            fetch(`{{ url('/reports') }}/${currentReportData.id}/assign-jury`, {
                    method: 'POST'
                    , headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        , 'Content-Type': 'application/json'
                        , 'Accept': 'application/json'
                    }
                    , body: JSON.stringify({
                        president_id: presidentSelect ? presidentSelect.value : ''
                        , rapporteur_id: rapporteurSelect ? rapporteurSelect.value : ''
                        , member_ids: memberIds
                    })
                })
                .then(response => response.json().then(data => ({
                    ok: response.ok
                    , data
                })))
                .then(({
                    ok
                    , data
                }) => {
                    if (ok && data.success) {
                        location.reload();
                        return;
                    }

                    alert(data.message || 'Impossible de sauvegarder le jury.');
                })
                .catch(() => {
                    alert('Une erreur est survenue lors de la sauvegarde du jury.');
                });
        };

        const juryAssignmentModal = document.getElementById('juryAssignmentModal');
        if (juryAssignmentModal) {
            juryAssignmentModal.addEventListener('show.bs.modal', () => {
                juryAssignmentModal.classList.add('jury-stack-modal');
                setTimeout(() => {
                    const backdrop = document.querySelector('.modal-backdrop:last-of-type');
                    if (backdrop) {
                        backdrop.classList.add('jury-stack-backdrop');
                    }
                }, 0);
            });

            juryAssignmentModal.addEventListener('hidden.bs.modal', () => {
                juryAssignmentModal.classList.remove('jury-stack-modal');
            });
        }

    </script>
</x-app-layout>

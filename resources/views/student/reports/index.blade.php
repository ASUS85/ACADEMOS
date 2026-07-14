<x-app-layout>
    @php
    $currentUser = auth()->user();
    $searchValue = request('search');
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
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 mb-3">Étudiant</span>
                        <h1 class="display-6 fw-bold mb-2">Mes rapports</h1>
                        <p class="lead mb-0 opacity-90">Même rendu que l’onglet jury enseignant: tableau, badges, aperçu et téléchargement dans un modal.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('student.reports.create') }}" class="btn btn-light btn-lg px-4 py-3 shadow-sm">
                            <i class="fas fa-upload me-2"></i>Soumettre un rapport
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

        <div class="card jury-teacher-panel mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('student.reports.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-8">
                        <label class="form-label fw-semibold text-muted">Recherche</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="search" name="search" value="{{ $searchValue }}" class="form-control" placeholder="Titre, encadreur, statut...">
                        </div>
                    </div>
                    <div class="col-lg-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1" data-loader-target="#globalLoader">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('student.reports.index') }}" class="btn btn-outline-secondary btn-lg" data-loader-target="#globalLoader">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if ($reports->isEmpty())
        <div class="card jury-teacher-panel">
            <div class="card-body py-5 text-center">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h3 class="h5 fw-bold mb-2">Aucun rapport trouvé</h3>
                <p class="text-muted mb-0">Soumettez un rapport pour le voir apparaître ici.</p>
            </div>
        </div>
        @else
        <div class="card jury-teacher-panel overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="h5 fw-bold mb-1">Mes rapports</h2>
                    <div class="text-muted small">{{ $reports->total() }} rapport(s) • page {{ $reports->currentPage() }} / {{ $reports->lastPage() }}</div>
                </div>
                <span class="jury-teacher-badge jury-teacher-badge-muted">{{ $currentUser->name }}</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0 jury-teacher-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Titre</th>
                            <th>Statut</th>
                            <th>Encadreur</th>
                            <th>Jury</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        @php
                        $juryTotal = $report->juryGroup?->members?->count() ?? 0;
                        $reportPayloadData = base64_encode(json_encode($report->loadMissing(['student.filiere', 'teacher', 'latestVersion', 'comments.user', 'juryGroup.members'])));
                        $previewUrl = route('reports.preview', $report);
                        $downloadUrl = route('reports.download', $report);
                        $previewFile = $report->latestVersion?->file_path ?? $report->file_path;
                        $canPreview = $previewFile && str_ends_with(strtolower($previewFile), '.pdf');
                        $statusClass = match ($report->status) {
                        'Validé final', 'Validé' => 'jury-teacher-badge-success',
                        'En attente jury' => 'jury-teacher-badge-primary',
                        'Soumis', 'Affecté', 'commenté' => 'jury-teacher-badge-warning',
                        default => 'jury-teacher-badge-muted',
                        };
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $report->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($report->title, 48) }}</div>
                                <div class="text-muted small">Version {{ $report->latestVersion?->version ?? 'active' }}</div>
                            </td>
                            <td>
                                <span class="jury-teacher-badge {{ $statusClass }}">{{ $report->status }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $report->teacher?->name ?? 'Non affecté' }}</div>
                                <div class="text-muted small">Encadreur</div>
                            </td>
                            <td>
                                @if ($juryTotal > 0)
                                <span class="jury-teacher-badge jury-teacher-badge-primary">{{ $juryTotal }} membre(s)</span>
                                @else
                                <span class="jury-teacher-badge jury-teacher-badge-muted">Aucun</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $report->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $report->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm px-3" data-report="{{ $reportPayloadData }}" data-preview-url="{{ $previewUrl }}" data-download-url="{{ $downloadUrl }}" data-can-preview="{{ $canPreview ? 1 : 0 }}" onclick="openStudentReportDetails(this)">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </button>
                                    <a href="{{ $downloadUrl }}" target="_blank" class="btn btn-primary btn-sm px-3">
                                        <i class="fas fa-download me-1"></i>Télécharger
                                    </a>
                                </div>
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

    <div class="modal fade" id="studentReportDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0" id="studentReportDetailsTitle">Détails du rapport</h5>
                        <small class="text-white-50">Prévisualisation et téléchargement dans l'application</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-8 border-end">
                            <div class="p-4 border-bottom bg-light">
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                    <span id="studentReportDetailsStatus" class="jury-teacher-badge jury-teacher-badge-muted">Statut</span>
                                    <span id="studentReportDetailsVersion" class="jury-teacher-badge jury-teacher-badge-primary">v1</span>
                                </div>
                                <h5 class="fw-bold mb-2" id="studentReportDetailsTitleText">Rapport</h5>
                                <div class="text-muted small" id="studentReportDetailsMeta">Chargement...</div>
                            </div>
                            <div id="studentReportDetailsPreviewWrap" class="p-3">
                                <iframe id="studentReportDetailsPreview" class="jury-teacher-preview-frame" src=""></iframe>
                                <div id="studentReportDetailsPreviewFallback" class="alert alert-light border d-none mb-0">
                                    L'aperçu direct est disponible pour les PDF uniquement. Utilisez le téléchargement si le fichier est dans un autre format.
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="p-4">
                                <div class="d-grid gap-2 mb-4">
                                    <a id="studentReportDetailsDownload" href="#" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="fas fa-download me-2"></i>Télécharger
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-lg" data-bs-dismiss="modal">Fermer</button>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">Informations</h6>
                                    <div class="small text-muted mb-2">Encadreur</div>
                                    <div class="fw-semibold mb-3" id="studentReportDetailsTeacher">-</div>

                                    <div class="small text-muted mb-2">Jury affecté</div>
                                    <div class="fw-semibold mb-3" id="studentReportDetailsJury">-</div>
                                    <div id="studentReportDetailsJuryMembers" class="d-flex flex-wrap gap-2"></div>
                                </div>

                                <div class="small text-muted mb-2">Commentaire enseignant</div>
                                <div class="fw-semibold mb-3" id="studentReportDetailsTeacherComment">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openStudentReportDetails(button) {
            let report;
            const rawPayload = button.dataset.report || '';

            try {
                report = JSON.parse(atob(rawPayload));
            } catch (error) {
                console.error('Impossible de lire les données du rapport.', error);
                return;
            }

            const title = report.title || 'Détails du rapport';
            const student = report.student || {};
            const filiere = student.filiere || {};
            const teacher = report.teacher || {};
            const juryGroup = report.juryGroup || report.jury_group || null;
            const juryMembers = juryGroup && Array.isArray(juryGroup.members) ? juryGroup.members : [];
            const teacherComment = report.teacher_comment || 'Aucun commentaire disponible.';
            const previewUrl = button.dataset.previewUrl;
            const downloadUrl = button.dataset.downloadUrl;
            const canPreview = button.dataset.canPreview === '1';
            const latestVersionValue = report.latest_version && report.latest_version.version ? report.latest_version.version : '';
            const fallbackLatestVersionValue = report.latestVersion && report.latestVersion.version ? report.latestVersion.version : '';
            const latestVersion = latestVersionValue || fallbackLatestVersionValue || 'v1';
            const status = report.status || 'Statut';

            document.getElementById('studentReportDetailsTitle').textContent = title;
            document.getElementById('studentReportDetailsTitleText').textContent = title;
            document.getElementById('studentReportDetailsMeta').textContent = `${student.name || '-'} • ${filiere.name || '-'}`;
            document.getElementById('studentReportDetailsTeacher').textContent = teacher.name || 'Non affecté';
            document.getElementById('studentReportDetailsTeacherComment').textContent = teacherComment;
            document.getElementById('studentReportDetailsStatus').textContent = status;
            document.getElementById('studentReportDetailsVersion').textContent = latestVersion;
            document.getElementById('studentReportDetailsDownload').href = downloadUrl;

            const previewFrame = document.getElementById('studentReportDetailsPreview');
            const previewFallback = document.getElementById('studentReportDetailsPreviewFallback');

            if (canPreview) {
                previewFrame.classList.remove('d-none');
                previewFallback.classList.add('d-none');
                previewFrame.src = previewUrl;
            } else {
                previewFrame.src = '';
                previewFrame.classList.add('d-none');
                previewFallback.classList.remove('d-none');
            }

            const juryContainer = document.getElementById('studentReportDetailsJuryMembers');
            juryContainer.innerHTML = '';

            if (juryMembers.length > 0) {
                document.getElementById('studentReportDetailsJury').textContent = `${juryMembers.length} membre(s)`;
                juryMembers.forEach(member => {
                    const memberRole = member.pivot && member.pivot.role ? member.pivot.role : 'membre';
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-light text-dark border px-3 py-2';
                    badge.textContent = `${member.name} • ${memberRole}`;
                    juryContainer.appendChild(badge);
                });
            } else {
                document.getElementById('studentReportDetailsJury').textContent = 'Aucun';
                juryContainer.innerHTML = '<span class="text-muted small">Aucun membre affecté</span>';
            }

            bootstrap.Modal.getOrCreateInstance(document.getElementById('studentReportDetailsModal')).show();
        }

    </script>
</x-app-layout>

<x-app-layout>
    @php
    $currentUser = auth()->user();
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

    </style>

    <div class="container-fluid py-4">
        <div class="card reports-hero border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 mb-3">Étudiant</span>
                        <h1 class="display-6 fw-bold mb-2">Mes rapports</h1>
                        <p class="lead mb-0 opacity-90">Même logique d’affichage que le superadmin: tableau, recherche, aperçu et téléchargement.</p>
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

        <div class="card reports-panel mb-4">
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
        <div class="card reports-panel">
            <div class="card-body py-5 text-center">
                <i class="fas fa-file-circle-question fa-3x text-muted mb-3"></i>
                <h3 class="h5 fw-bold mb-2">Aucun rapport trouvé</h3>
                <p class="text-muted mb-0">Soumettez un rapport pour le voir apparaître ici.</p>
            </div>
        </div>
        @else
        <div class="card reports-panel overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="h5 fw-bold mb-1">Rapports</h2>
                    <div class="text-muted small">{{ $reports->total() }} résultat(s) • page {{ $reports->currentPage() }} / {{ $reports->lastPage() }}</div>
                </div>
                <span class="report-badge report-badge-muted">Dernière mise à jour {{ now()->format('d/m/Y H:i') }}</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
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
                        $reportPayload = $report->loadMissing(['student.filiere', 'teacher', 'latestVersion', 'comments.user', 'juryGroup.members']);
                        $reportPayloadData = base64_encode(json_encode($reportPayload));
                        $previewUrl = route('reports.preview', $report);
                        $downloadUrl = route('reports.download', $report);
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $report->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($report->title, 50) }}</div>
                                <div class="text-muted small">{{ $report->latestVersion?->version ? 'Version ' . $report->latestVersion->version : 'Version active' }}</div>
                            </td>
                            <td><span class="report-badge {{ $statusClass }}">{{ $status }}</span></td>
                            <td>{{ $report->teacher?->name ?? 'Non affecté' }}</td>
                            <td>
                                @if ($report->juryGroup?->members?->count())
                                <span class="report-badge report-badge-info">{{ $report->juryGroup->members->count() }} membre(s)</span>
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
                                    <button type="button" class="btn btn-outline-primary btn-sm px-3" data-report="{{ $reportPayloadData }}" data-preview-url="{{ $previewUrl }}" data-download-url="{{ $downloadUrl }}" onclick="openStudentReportDetails(this)">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </button>
                                    <a href="{{ $downloadUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3">
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
                                    <span id="studentReportDetailsStatus" class="report-badge report-badge-muted">Statut</span>
                                    <span id="studentReportDetailsVersion" class="report-badge report-badge-info">v1</span>
                                </div>
                                <h5 class="fw-bold mb-2" id="studentReportDetailsTitleText">Rapport</h5>
                                <div class="text-muted small" id="studentReportDetailsMeta">Chargement...</div>
                            </div>
                            <div id="studentReportDetailsPreviewWrap" class="p-3">
                                <iframe id="studentReportDetailsPreview" class="report-preview-frame" src=""></iframe>
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
                const decodedPayload = rawPayload.startsWith('ey') ? atob(rawPayload) : rawPayload;
                report = JSON.parse(decodedPayload);
            } catch (error) {
                console.error('Impossible de parser les données du rapport :', error);
                return;
            }

            const latestVersion = report.latestVersion || {};
            const teacher = report.teacher || {};
            const juryMembers = report.juryGroup && Array.isArray(report.juryGroup.members) ? report.juryGroup.members : [];
            const previewUrl = button.dataset.previewUrl || '';
            const downloadUrl = button.dataset.downloadUrl || previewUrl;
            const canPreview = latestVersion.file_path ? latestVersion.file_path.toLowerCase().endsWith('.pdf') : false;

            const modalEl = document.getElementById('studentReportDetailsModal');
            const title = document.getElementById('studentReportDetailsTitle');
            const status = document.getElementById('studentReportDetailsStatus');
            const version = document.getElementById('studentReportDetailsVersion');
            const titleText = document.getElementById('studentReportDetailsTitleText');
            const meta = document.getElementById('studentReportDetailsMeta');
            const preview = document.getElementById('studentReportDetailsPreview');
            const previewFallback = document.getElementById('studentReportDetailsPreviewFallback');
            const download = document.getElementById('studentReportDetailsDownload');
            const jury = document.getElementById('studentReportDetailsJury');
            const juryMembersWrap = document.getElementById('studentReportDetailsJuryMembers');
            const teacherComment = document.getElementById('studentReportDetailsTeacherComment');
            const teacherLabel = document.getElementById('studentReportDetailsTeacher');

            title.textContent = `Rapport #${report.id}`;
            status.textContent = report.status || 'Statut';
            version.textContent = latestVersion.version || 'v1';
            titleText.textContent = report.title || 'Rapport';
            meta.textContent = `Créé le ${new Date(report.created_at).toLocaleString()} • Mis à jour le ${new Date(report.updated_at).toLocaleString()}`;
            teacherLabel.textContent = teacher.name || 'Non affecté';
            jury.textContent = juryMembers.length ? `${juryMembers.length} membre(s)` : 'Aucun';
            teacherComment.textContent = report.teacher_comment || 'Aucun commentaire';
            download.href = downloadUrl || '#';

            if (juryMembersWrap) {
                juryMembersWrap.innerHTML = juryMembers.length ?
                    juryMembers.map((member) => `<span class="badge bg-light text-dark border">${member.name} • ${(member.pivot && member.pivot.role) ? member.pivot.role : 'membre'}</span>`).join('') :
                    '<span class="text-muted small">Aucun membre affecté</span>';
            }

            if (canPreview && previewUrl) {
                preview.classList.remove('d-none');
                previewFallback.classList.add('d-none');
                preview.src = previewUrl;
            } else {
                preview.src = '';
                preview.classList.add('d-none');
                previewFallback.classList.remove('d-none');
            }

            bootstrap.Modal.getOrCreateInstance(modalEl).show();

            modalEl.addEventListener('hidden.bs.modal', () => {
                preview.src = '';
            }, {
                once: true
            });
        }

    </script>
</x-app-layout>

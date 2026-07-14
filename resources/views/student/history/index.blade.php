<x-app-layout>
    @php
    $workflowLabels = [
    'Soumis' => ['label' => 'Soumis', 'step' => 1, 'color' => 'secondary'],
    'Affecté' => ['label' => 'Affecté à un enseignant', 'step' => 2, 'color' => 'info'],
    'commenté' => ['label' => 'En correction', 'step' => 2, 'color' => 'warning'],
    'Validé' => ['label' => 'Validé par l’enseignant', 'step' => 3, 'color' => 'success'],
    'En attente jury' => ['label' => 'En attente du jury', 'step' => 4, 'color' => 'primary'],
    'Validé final' => ['label' => 'Validé final', 'step' => 5, 'color' => 'success'],
    'Rejeté' => ['label' => 'Rejeté', 'step' => 5, 'color' => 'danger'],
    ];
    @endphp

    <div class="container-fluid py-4">
        <div class="row align-items-center mb-4 g-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="fas fa-clock-rotate-left fa-2x"></i>
                    </div>
                    <div>
                        <h1 class="h3 fw-bold mb-1">Historique des versions</h1>
                        <p class="text-muted mb-0">Toutes vos versions de rapport sont listées ici, dans un tableau de suivi.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('student.reports.create') }}" class="btn btn-primary px-4 py-3 shadow-sm me-2" data-loader-target="#globalLoader">
                    <i class="fas fa-upload me-2"></i>Nouvelle soumission
                </a>
                <a href="{{ route('student.history.index') }}" class="btn btn-outline-secondary px-4 py-3" data-loader-target="#globalLoader">
                    <i class="fas fa-rotate me-2"></i>Actualiser
                </a>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
        </div>
        @endif

        @if ($historyVersions->count() === 0)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h3 class="h5 fw-bold mb-2">Aucune version soumise</h3>
                <p class="text-muted mb-4">Déposez votre premier rapport pour remplir l’historique.</p>
                <a href="{{ route('student.reports.create') }}" class="btn btn-primary px-4" data-loader-target="#globalLoader">Soumettre maintenant</a>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Rapport</th>
                            <th>Version</th>
                            <th>Statut</th>
                            <th>Déposé le</th>
                            <th class="text-end" style="width: 220px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historyVersions as $version)
                        @php
                        $report = $version->report;
                        $workflow = $workflowLabels[$report->status] ?? ['label' => $report->status, 'step' => 1, 'color' => 'secondary'];
                        $isLatestVersion = optional($report->latestVersion)->id === $version->id;
                        $isLocked = $report->teacher_id || ($report->juryGroup?->members?->count() ?? 0) > 0 || in_array($report->status, ['Affecté', 'commenté', 'Validé', 'En attente jury', 'Validé final', 'Rejeté'], true);
                        $canDelete = !$isLatestVersion || !$isLocked;
                        $previewUrl = route('report-versions.preview', $version);
                        $downloadUrl = route('report-versions.download', $version);
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $report->title }}</div>
                                <div class="text-muted small">Encadreur: {{ $report->teacher?->name ?? 'En attente' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $version->version }}</span>
                                @if ($isLatestVersion)
                                <span class="badge bg-success-subtle text-success ms-2 px-3 py-2">Active</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $workflow['color'] }}-subtle text-{{ $workflow['color'] }} px-3 py-2">{{ $workflow['label'] }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $version->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $version->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                    <button type="button" class="btn btn-outline-primary btn-sm px-3" data-preview-url="{{ $previewUrl }}" data-download-url="{{ $downloadUrl }}" data-preview-title="{{ $report->title }} - {{ $version->version }}" onclick="openVersionPreview(this)">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </button>
                                    <a href="{{ $downloadUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3">
                                        <i class="fas fa-download me-1"></i>Télécharger
                                    </a>
                                    @if ($canDelete)
                                    <button type="button" class="btn btn-outline-danger btn-sm px-3" data-confirm-title="Suppression de la version" data-confirm-message="Confirmez-vous la suppression de cette version ?" data-confirm-submit-label="Oui, supprimer" data-confirm-form-id="deleteVersionForm{{ $version->id }}">
                                        <i class="fas fa-trash me-1"></i>Suppr.
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-outline-danger btn-sm px-3" disabled title="Cette version est en prise en charge">
                                        <i class="fas fa-trash me-1"></i>Protégé
                                    </button>
                                    @endif
                                </div>

                                @if ($canDelete)
                                <form id="deleteVersionForm{{ $version->id }}" method="POST" action="{{ route('report-versions.destroy', $version) }}" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $historyVersions->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    <div class="modal fade" id="versionPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0" id="versionPreviewTitle">Prévisualisation</h5>
                        <small class="text-white-50">Aperçu direct de la version sélectionnée</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="versionPreviewFrame" src="" style="width: 100%; height: 75vh; border: 0;"></iframe>
                </div>
                <div class="modal-footer bg-light border-0">
                    <a id="versionPreviewDownload" href="#" target="_blank" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Télécharger
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openVersionPreview(button) {
            const url = button.getAttribute('data-preview-url');
            const title = button.getAttribute('data-preview-title') || 'Prévisualisation';
            const downloadUrl = button.getAttribute('data-download-url') || url;
            const modalEl = document.getElementById('versionPreviewModal');
            const frame = document.getElementById('versionPreviewFrame');
            const download = document.getElementById('versionPreviewDownload');
            const modalTitle = document.getElementById('versionPreviewTitle');

            if (!url || !frame || !download || !modalTitle) {
                return;
            }

            modalTitle.textContent = title;
            frame.src = url;
            download.href = downloadUrl;

            bootstrap.Modal.getOrCreateInstance(modalEl).show();

            modalEl.addEventListener('hidden.bs.modal', () => {
                frame.src = '';
            }, {
                once: true
            });
        }

    </script>
</x-app-layout>

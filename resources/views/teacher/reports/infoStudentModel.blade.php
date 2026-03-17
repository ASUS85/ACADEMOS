<div class="modal fade" id="reportModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Détails du Rapport - {{ $report->student->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6 class="text-primary fw-bold mb-3">Informations Étudiant</h6>
                        <p class="mb-1"><strong>Matricule:</strong> {{ $report->student->matricule ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Filière:</strong> {{ $report->student->filiere->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Statut Actuel:</strong>
                            <span class="badge bg-warning text-dark">{{ $report->status }}</span>
                        </p>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <h6 class="text-primary fw-bold mb-3">Dernière Version</h6>
                        <p class="small text-muted mb-3">{{ $report->title }}</p>
                        <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank" class="btn btn-primary w-100 mb-2">
                            <i class="fa fa-file-pdf me-2"></i> Télécharger le rapport
                        </a>
                    </div>
                </div>

                <hr class="my-4">

                <form action="{{ route('reports.teacher-comment', $report->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Commentaires & Instructions</label>
                        <textarea name="comment" class="form-control bg-light" rows="3" placeholder="Laissez vos remarques ici...">{{ $report->teacher_comment }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="commenter" class="btn btn-warning flex-grow-1">Demander correction</button>
                        <button type="submit" name="action" value="valider" class="btn btn-success flex-grow-1">Valider le rapport</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

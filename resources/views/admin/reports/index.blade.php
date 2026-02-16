<x-app-layout>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <i class="fas fa-file-alt fa-2x text-primary me-3"></i>
                <div>
                    <h1 class="h3 mb-1 fw-bold">📊 Tous les rapports (Admin)</h1>
                    <p class="text-muted mb-0">{{ \App\Models\Report::count() }} rapport(s) au total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Success/Error -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-start border-success border-5" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>✅ {{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-start border-danger border-5" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>❌ {{ session('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tableau ou message vide -->
    @if (\App\Models\Report::count() === 0)
        <div class="text-center py-12">
            <i class="fas fa-file-circle-question fa-5x text-muted mb-4 opacity-50"></i>
            <h3 class="h4 text-muted mb-3">Aucun rapport soumis</h3>
            <p class="text-muted">Les étudiants n'ont pas encore soumis de rapports de stage.</p>
        </div>
    @else
        <div class="card shadow border-0">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2 text-primary"></i>
                    Liste des rapports
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="border-0 py-3">
                                    <i class="fas fa-user-graduate me-1"></i>Étudiant
                                </th>
                                <th class="border-0 py-3">
                                    <i class="fas fa-file-signature me-1"></i>Titre
                                </th>
                                <th class="border-0 py-3 text-center">
                                    <i class="fas fa-info-circle me-1"></i>Statut
                                </th>
                                <th class="border-0 py-3">
                                    <i class="fas fa-calendar me-1"></i>Date
                                </th>
                                <th class="border-0 py-3">Commentaire Enseignant</th>
                                <th class="border-0 py-3 text-center">Action Jury</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr class="align-middle">
                                    <!-- Étudiant -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ $report->student->name }}&background=28a745&color=fff&size=40&rounded=true"
                                                 class="rounded-circle me-3" width="40" height="40">
                                            <div>
                                                <div class="fw-bold">{{ $report->student->name }}</div>
                                                <small class="text-muted">{{ $report->student->email }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Titre -->
                                    <td>
                                        <strong class="text-dark">{{ Str::limit($report->title, 60) }}</strong>
                                        @if($report->file_path)
                                            <br><small>
                                                <i class="fas fa-file-pdf text-danger me-1"></i>
                                                <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank" class="text-decoration-none">PDF</a>
                                            </small>
                                        @endif
                                    </td>

                                    <!-- Statut -->
                                    <td class="text-center">
                                        <span class="badge fs-6 px-3 py-2 rounded-pill
                                            @if ($report->status === 'Soumis') bg-warning text-dark
                                            @elseif($report->status === 'Affecté') bg-info text-white
                                            @elseif($report->status === 'Validé') bg-success text-white
                                            @else bg-secondary text-white @endif">
                                            {{ $report->status }}
                                        </span>
                                    </td>

                                    <!-- Date -->
                                    <td>
                                        <span class="badge bg-light text-dark px-2 py-1">
                                            {{ $report->created_at->format('d/m/Y') }}
                                        </span>
                                        <br><small class="text-muted">
                                            {{ $report->created_at->diffForHumans() }}
                                        </small>
                                    </td>

                                    <!-- Commentaire Enseignant -->
                                    <td>
                                        @if ($report->teacher_status === 'Validé par enseignant')
                                            <div class="bg-info bg-opacity-10 p-3 rounded border-start border-info border-3">
                                                <div class="small text-dark mb-1">
                                                    "{{ Str::limit($report->teacher_comment, 80) }}"
                                                </div>
                                                <div class="badge bg-info text-dark">
                                                    {{ $report->teacher->name ?? 'Enseignant' }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary text-white">⏳ En attente enseignant</span>
                                        @endif
                                    </td>

                                    <!-- Action Jury -->
                                    <td class="text-center">
                                        @if ($report->teacher_status === 'Validé par enseignant' && !$report->jury_id)
                                            <form method="POST" action="{{ route('reports.assign-jury', $report) }}" class="d-inline">
                                                @csrf
                                                <div class="input-group input-group-sm mb-2">
                                                    <select name="jury_id" class="form-select form-select-sm" required>
                                                        @foreach (\App\Models\User::role('jury')->get() as $jury)
                                                            <option value="{{ $jury->id }}">
                                                                {{ $jury->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="btn btn-outline-purple btn-sm px-3">
                                                        <i class="fas fa-user-plus"></i> Affecter
                                                    </button>
                                                </div>
                                            </form>
                                        @elseif($report->jury_id)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Jury assigné
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                ⏳ En attente enseignant
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
</x-app-layout>
<style>
.btn-outline-purple {
    color: #6f42c1;
    border-color: #6f42c1;
}
.btn-outline-purple:hover {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
}
</style>

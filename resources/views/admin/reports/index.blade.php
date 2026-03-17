<x-app-layout>
    <style>
        .badge-soft {
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
            font-size: 0.75rem;
        }
        .badge-soft-warning { background: #fef3c7; color: #92400e; }
        .badge-soft-info    { background: #e0f2fe; color: #0369a1; }
        .badge-soft-success { background: #dcfce7; color: #166534; }
        .badge-soft-muted   { background: #e5e7eb; color: #374151; }

        .card-neo {
            border-radius: 1rem;
            border: 0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }
        .avatar-ring {
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }
        .btn-outline-purple {
            color: #6f42c1;
            border-color: #6f42c1;
        }
        .btn-outline-purple:hover {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: #fff;
        }
        .report-card:hover {
            box-shadow: 0 14px 30px rgba(15,23,42,0.12);
            transform: translateY(-2px);
        }
        .report-card {
            transition: all .2s ease;
        }
        .chip {
            border-radius: 999px;
            padding: 0.15rem 0.5rem;
            font-size: 0.7rem;
        }
    </style>

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width:52px;height:52px;">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-1 fw-bold">Tous les rapports (Admin)</h1>
                        <p class="text-muted mb-0 small">
                            <i class="fas fa-database me-1 text-primary"></i>
                            {{ \App\Models\Report::count() }} rapport(s) au total
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm rounded-3 mb-3" role="alert">
                <span class="me-2 d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25" style="width:34px;height:34px;">
                    <i class="fas fa-check"></i>
                </span>
                <div class="flex-grow-1">
                    <strong>{{ session('success') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm rounded-3 mb-3" role="alert">
                <span class="me-2 d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25" style="width:34px;height:34px;">
                    <i class="fas fa-exclamation"></i>
                </span>
                <div class="flex-grow-1">
                    <strong>{{ session('error') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Empty state --}}
        @if (\App\Models\Report::count() === 0)
            <div class="card border-0 shadow-sm card-neo">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-muted" style="width:84px;height:84px;">
                            <i class="fas fa-file-circle-question fa-2x"></i>
                        </span>
                    </div>
                    <h3 class="h4 text-muted mb-2">Aucun rapport soumis</h3>
                    <p class="text-muted mb-0">Les étudiants n'ont pas encore soumis de rapports de stage.</p>
                </div>
            </div>
        @else
            {{-- Liste en cartes pleine largeur --}}
            <div class="card card-neo mb-3">
                <div class="card-header bg-white border-0 px-4 pt-3 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill me-2">
                            <i class="fas fa-list me-1"></i> Liste des rapports
                        </span>
                    </div>
                    {{-- zone pour filtres / recherche plus tard si besoin --}}
                </div>

                <div class="card-body px-3 px-md-4 pt-2 pb-3">
                    <div class="d-flex flex-column gap-3">
                        @foreach ($reports as $report)
                            @php
                                $status = $report->status;
                                $badgeClass = 'badge-soft-muted';
                                $statusIcon = 'far fa-clock';
                                if ($status === 'Soumis')  { $badgeClass = 'badge-soft-warning'; $statusIcon = 'fas fa-inbox'; }
                                if ($status === 'Affecté') { $badgeClass = 'badge-soft-info';    $statusIcon = 'fas fa-user-check'; }
                                if ($status === 'Validé')  { $badgeClass = 'badge-soft-success'; $statusIcon = 'fas fa-check-circle'; }
                            @endphp

                            <div class="report-card card border-0 shadow-sm rounded-3">
                                <div class="card-body">
                                    <div class="row g-3 align-items-start">
                                        {{-- Col Étudiant + titre --}}
                                        <div class="col-lg-4 d-flex">
                                            <img src="https://ui-avatars.com/api/?name={{ $report->student?->name ?? 'Student' }}&background=2563EB&color=fff&size=48&rounded=true"
                                                 class="rounded-circle me-3 avatar-ring"
                                                 width="48" height="48" alt="Avatar">
                                            <div>
                                                <div class="fw-semibold text-dark">
                                                    {{ $report?->student?->name }}
                                                </div>
                                                <div class="small text-muted mb-1">
                                                    {{ $report?->student?->email }}
                                                </div>
                                                <div class="fw-semibold text-dark">
                                                    {{ Str::limit($report->title, 80) }}
                                                </div>
                                                @if ($report->file_path)
                                                    <small>
                                                        <i class="fas fa-file-pdf text-danger me-1"></i>
                                                        <a href="{{ asset('storage/' . $report?->file_path) }}"
                                                           target="_blank"
                                                           class="link-danger text-decoration-none">
                                                            Voir le PDF
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Col Statut + date --}}
                                        <div class="col-lg-2">
                                            <div class="mb-2">
                                                <span class="badge badge-soft {{ $badgeClass }}">
                                                    <i class="{{ $statusIcon }} me-1"></i>
                                                    {{ $status }}
                                                </span>
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $report?->created_at->format('d/m/Y') }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $report?->created_at->diffForHumans() }}
                                            </div>
                                        </div>

                                        {{-- Col Commentaire enseignant --}}
                                        <div class="col-lg-3">
                                            @if ($report?->teacher_status === 'Validé par enseignant')
                                                <div class="p-3 rounded-3 border-start border-3 border-info bg-info bg-opacity-10 h-100">
                                                    <div class="small text-dark mb-2 fst-italic">
                                                        “{{ Str::limit($report?->teacher_comment, 80) }}”
                                                    </div>
                                                    <span class="chip bg-info bg-opacity-25 text-info">
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        {{ $report?->teacher?->name ?? 'Enseignant' }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="badge badge-soft badge-soft-muted">
                                                    ⏳ En attente enseignant
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Col Affectation enseignant --}}
                                        <div class="col-lg-1 d-flex flex-column align-items-center">
                                            @if ($report->status === 'Soumis' && !$report?->teacher_id)
                                                <form method="POST"
                                                      action="{{ url('/reports/' . $report?->id . '/assign') }}"
                                                      class="w-100">
                                                    @csrf
                                                    <div class="input-group input-group-sm">
                                                        <select name="teacher_id"
                                                                class="form-select form-select-sm"
                                                                required>
                                                            @php $teachers = \App\Models\User::role('teacher')->get(); @endphp
                                                            @if ($teachers->isEmpty())
                                                                <option value="">Aucun enseignant</option>
                                                            @else
                                                                @foreach ($teachers as $teacher)
                                                                    <option value="{{ $teacher->id }}">
                                                                        {{ $teacher->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <button type="submit"
                                                            class="btn btn-outline-success btn-sm mt-2 w-100">
                                                        <i class="fas fa-user-plus me-1"></i> OK
                                                    </button>
                                                </form>
                                            @elseif($report->teacher_id)
                                                <span class="badge badge-soft badge-soft-success text-center">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    {{ $report->teacher->name ?? 'Enseignant' }}
                                                </span>
                                            @else
                                                <span class="badge badge-soft badge-soft-warning">
                                                    ⏳ {{ ucfirst($report->status) }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Col Jury --}}
                                        <div class="col-lg-2 d-flex flex-column align-items-center">
                                            @if ($report->teacher_status === 'Validé par enseignant' && !$report->jury_id)
                                                <form method="POST"
                                                      action="{{ route('reports.assign-jury', $report) }}"
                                                      class="w-100">
                                                    @csrf
                                                    <div class="input-group input-group-sm">
                                                        <select name="jury_id"
                                                                class="form-select form-select-sm"
                                                                required>
                                                            @foreach (\App\Models\User::role('jury')->get() as $jury)
                                                                <option value="{{ $jury->id }}">
                                                                    {{ $jury->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit"
                                                            class="btn btn-outline-purple btn-sm mt-2 w-100">
                                                        <i class="fas fa-user-plus me-1"></i> Jury
                                                    </button>
                                                </form>
                                            @elseif($report->jury_id)
                                                <span class="badge badge-soft badge-soft-success">
                                                    <i class="fas fa-check-circle me-1"></i> Jury assigné
                                                </span>
                                            @else
                                                <span class="badge badge-soft badge-soft-warning">
                                                    ⏳ En attente enseignant
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination si tu en as besoin ici --}}
                    {{-- <div class="mt-3 d-flex justify-content-end">
                        {{ $reports->links('pagination::bootstrap-5') }}
                    </div> --}}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

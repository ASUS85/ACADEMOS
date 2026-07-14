<x-app-layout>
    @php
    $totalReports = (int) ($stats['total_reports'] ?? 0);
    $totalUsers = (int) ($stats['total_users'] ?? 0);
    $students = (int) ($stats['students'] ?? 0);
    $teachers = (int) ($stats['teachers'] ?? 0);
    $juries = (int) ($stats['juries'] ?? 0);
    $educationStaff = $teachers + $juries;
    $reportDensity = $totalUsers > 0 ? round(($totalReports / $totalUsers) * 100) : 0;
    $studentShare = $totalUsers > 0 ? round(($students / $totalUsers) * 100) : 0;
    $staffShare = $totalUsers > 0 ? round(($educationStaff / $totalUsers) * 100) : 0;
    $backRoute = auth()->user()->hasRole('superadmin') ? route('superadmin.reports') : route('admin.reports.index');
    @endphp

    <div class="container-fluid py-4">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #0f766e 55%, #2563eb 100%);">
            <div class="card-body p-4 p-lg-5 text-white">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 mb-3">Tableau de bord analytique</span>
                        <h1 class="display-6 fw-bold mb-2">Statistiques ACADEMOS</h1>
                        <p class="lead mb-0 opacity-90">Vue synthétique des rapports, des comptes et de la répartition des rôles pour piloter l’activité de la plateforme.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ $backRoute }}" class="btn btn-light btn-lg px-4 py-3 shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Retour aux rapports
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="fas fa-file-alt fa-lg"></i>
                            </div>
                            <span class="badge bg-primary-subtle text-primary">Rapports</span>
                        </div>
                        <h2 class="display-6 fw-bold mb-1">{{ $totalReports }}</h2>
                        <p class="text-muted mb-0">Volume global de dossiers suivis.</p>
                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ min(100, $reportDensity) }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success">Comptes</span>
                        </div>
                        <h2 class="display-6 fw-bold mb-1">{{ $totalUsers }}</h2>
                        <p class="text-muted mb-0">Utilisateurs actifs sur la plateforme.</p>
                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="fas fa-user-graduate fa-lg"></i>
                            </div>
                            <span class="badge bg-warning-subtle text-warning">Étudiants</span>
                        </div>
                        <h2 class="display-6 fw-bold mb-1">{{ $students }}</h2>
                        <p class="text-muted mb-0">Population étudiante suivie.</p>
                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ min(100, $studentShare) }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-circle bg-info-subtle text-info d-inline-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="fas fa-chalkboard-teacher fa-lg"></i>
                            </div>
                            <span class="badge bg-info-subtle text-info">Encadrants</span>
                        </div>
                        <h2 class="display-6 fw-bold mb-1">{{ $educationStaff }}</h2>
                        <p class="text-muted mb-0">Enseignants et jurys disponibles.</p>
                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ min(100, $staffShare) }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                            <div>
                                <h3 class="h5 fw-bold mb-1">Répartition générale</h3>
                                <p class="text-muted mb-0">Lecture rapide de l’activité globale.</p>
                            </div>
                            <span class="badge bg-dark-subtle text-dark px-3 py-2">Données en temps réel</span>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="p-4 rounded-4 bg-light h-100">
                                    <small class="text-muted d-block mb-2">Part des étudiants</small>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <h4 class="fw-bold mb-0">{{ $studentShare }}%</h4>
                                        <i class="fas fa-chart-pie text-warning"></i>
                                    </div>
                                    <div class="progress mt-3" style="height: 10px;">
                                        <div class="progress-bar bg-warning" style="width: {{ min(100, $studentShare) }}%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 rounded-4 bg-light h-100">
                                    <small class="text-muted d-block mb-2">Encadrants</small>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <h4 class="fw-bold mb-0">{{ $staffShare }}%</h4>
                                        <i class="fas fa-user-tie text-info"></i>
                                    </div>
                                    <div class="progress mt-3" style="height: 10px;">
                                        <div class="progress-bar bg-info" style="width: {{ min(100, $staffShare) }}%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 rounded-4 bg-light h-100">
                                    <small class="text-muted d-block mb-2">Densité des rapports</small>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <h4 class="fw-bold mb-0">{{ $reportDensity }}%</h4>
                                        <i class="fas fa-wave-square text-primary"></i>
                                    </div>
                                    <div class="progress mt-3" style="height: 10px;">
                                        <div class="progress-bar bg-primary" style="width: {{ min(100, $reportDensity) }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="h5 fw-bold mb-3">Actions rapides</h3>
                        <div class="d-grid gap-3">
                            <a href="{{ route('superadmin.reports') }}" class="btn btn-outline-primary btn-lg py-3">Voir les rapports</a>
                            <a href="{{ route('superadmin.users.index') ?? '#' }}" class="btn btn-outline-secondary btn-lg py-3">Gérer les utilisateurs</a>
                            <button class="btn btn-primary btn-lg py-3" onclick="location.reload()">Rafraîchir les données</button>
                        </div>
                        <hr class="my-4">
                        <div class="small text-muted">
                            Cette vue est pensée comme un tableau de bord de pilotage, avec lecture rapide et accès direct aux zones d’action.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

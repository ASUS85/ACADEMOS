@php
    // SUPERADMIN : Statistiques GLOBALES (TOUS les départements)
    $superadminStats = [
        'totalUsers' => \App\Models\User::count(),
        'totalStudents' => \App\Models\User::role('student')->count(),
        'totalTeachers' => \App\Models\User::role('teacher')->count(),
        'totalAdmins' => \App\Models\User::role('admin')->count(),
        'totalSuperadmins' => \App\Models\User::role('superadmin')->count(),
        'totalDepartments' => \App\Models\Department::count(),
    ];

    $reportsQuery = \App\Models\Report::with(['student']);
    $totalReports = $reportsQuery->count();
    $submittedCount = $reportsQuery->where('status', 'Soumis')->count();
    $assignedCount = $reportsQuery->where('status', 'Affecté')->count();
    $evaluatedCount = $reportsQuery->where('status', 'Évalué')->count();
    $validatedCount = $reportsQuery->where('status', 'Validé final')->count();

    // Top 10 étudiants actifs (avec rapports) TOUS départements
    $studentsQuery = \App\Models\User::role('student')
        ->whereHas('reports')
        ->with(['filiere', 'department', 'reports.teacher'])
        ->orderBy('updated_at', 'desc');

    if (request()->filled('search')) {
        $search = request('search');
        $studentsQuery->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('matricule', 'like', "%$search%");
        });
    }

    $students = $studentsQuery->take(10)->get(); // Top 10 sans pagination pour aperçu
@endphp

<style>
    :root {
        --purple: #6f42c1;
        --blue-main: #3681B6;
    }
    .stat-hover:hover { transform: translateY(-3px); }
    .legend-dot { width: 22px; height: 22px; border-radius: 50%; }
    .legend-pill { border-radius: 999px; }
    .scroll-thin::-webkit-scrollbar { width: 5px; }
    .scroll-thin::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(50px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .animate__animated { animation: fadeInRight 0.4s ease-out; }
    .modal-wide { min-height: 800px; min-width: 900px; }
</style>

<div class="bg-light py-3 px-3 px-md-4">
    <div class="mb-4">
        <h4 class="fw-bold text-uppercase text-dark">🎯 Tableau de bord Super Administrateur</h4>
        <p class="text-muted mb-0">Vue globale de l'ensemble du système académique</p>
    </div>

    {{-- Stat cards GLOBALES --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Total Utilisateurs</small>
                        <h2 class="fw-bold mb-0">{{ $superadminStats['totalUsers'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background-color:#3EA84C;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Étudiants</small>
                        <h2 class="fw-bold mb-0">{{ $superadminStats['totalStudents'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background-color:#3681B6;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="fas fa-chalkboard-teacher fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Enseignants</small>
                        <h2 class="fw-bold mb-0">{{ $superadminStats['totalTeachers'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="fas fa-user-shield fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Admins</small>
                        <h2 class="fw-bold mb-0">{{ $superadminStats['totalAdmins'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="fas fa-building-columns fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Départements</small>
                        <h2 class="fw-bold mb-0">{{ $superadminStats['totalDepartments'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background-color:#3EA84C;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="fas fa-file-contract fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Rapports</small>
                        <h2 class="fw-bold mb-0">{{ $totalReports }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat + actions SUPERADMIN --}}
    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 p-3 pt-0 text-center shadow-sm bg-white rounded-3">
                        📊 Statistique globale des rapports
                    </h5>
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div style="height:220px;">
                                <canvas id="reportChart"></canvas>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ps-sm-3 mt-3 mt-sm-0">
                                <div class="d-flex align-items-center mb-2 p-2 border rounded-pill">
                                    <span class="legend-dot me-2" style="background:#f6a54c;"></span>
                                    <span>Soumis: <strong>{{ $submittedCount }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center mb-2 p-2 border rounded-pill">
                                    <span class="legend-dot me-2" style="background:#ff7d7d;"></span>
                                    <span>Affectés: <strong>{{ $assignedCount }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center mb-2 p-2 border rounded-pill">
                                    <span class="legend-dot me-2" style="background:#6da8e3;"></span>
                                    <span>Évalués: <strong>{{ $evaluatedCount }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center mb-2 p-2 border rounded-pill">
                                    <span class="legend-dot me-2" style="background:#4cc38a;"></span>
                                    <span>Validés: <strong>{{ $validatedCount }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions rapides SUPERADMIN --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 p-3 pt-0 text-center shadow-sm bg-white rounded-3">🚀 Actions rapides</h5>

                    <a href="{{ url('/superadmin/users') }}" class="d-flex align-items-center justify-content-between p-3 border rounded-3 text-decoration-none text-secondary mb-2 bg-white shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3 me-3" style="width:38px;height:38px;">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Gestion utilisateurs</div>
                                <small class="text-muted">Tous rôles & départements</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>

                    <a href="{{ route('superadmin.students.index') }}" class="d-flex align-items-center justify-content-between p-3 border rounded-3 text-decoration-none text-secondary mb-2 bg-white shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-3 me-3" style="width:38px;height:38px;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Étudiants</div>
                                <small class="text-muted">Tous départements</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>

                    <a href="{{ route('superadmin.teachers.index') }}" class="d-flex align-items-center justify-content-between p-3 border rounded-3 text-decoration-none text-secondary mb-2 bg-white shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center bg-info-subtle text-info rounded-3 me-3" style="width:38px;height:38px;">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Enseignants</div>
                                <small class="text-muted">Tous départements</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>

                    <a href="{{ route('superadmin.admins.index') }}" class="d-flex align-items-center justify-content-between p-3 border rounded-3 text-decoration-none text-secondary mb-2 bg-white shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-3 me-3" style="width:38px;height:38px;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Administrateurs</div>
                                <small class="text-muted">Gestion admins départements</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Aperçu Top 10 étudiants actifs (tous départements) --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-fire text-warning me-2"></i>
                Top 10 Étudiants actifs récemment
            </h5>
            <form method="GET" action="{{ url()->current() }}" class="d-flex" style="max-width:300px;">
                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-light border-0 shadow-none"
                           placeholder="Rechercher étudiant..." value="{{ request('search') }}">
                </div>
            </form>
        </div>

        <div class="table-responsive scroll-thin">
            <table class="table mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" width="60">N°</th>
                        <th>Étudiant</th>
                        <th>Département / Filière</th>
                        <th>Matricule</th>
                        <th>Dernière activité</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr class="bg-white">
                            <td class="ps-4 text-muted fw-medium">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $student->name }}</div>
                                <small class="text-muted">{{ $student->email }}</small>
                            </td>
                            <td>
                                <div class="mb-1">
                                    <span class="badge bg-primary-subtle text-primary px-2 py-1 small">
                                        {{ $student->department->name ?? 'N/A' }}
                                    </span>
                                </div>
                                <small class="text-secondary">
                                    {{ $student->filiere->name ?? 'N/A' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary-subtle fw-bold">
                                    {{ $student->matricule ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $student->updated_at->diffForHumans() }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-2x opacity-25 mb-3 d-block"></i>
                                Aucun étudiant actif récemment
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["Soumis", "Affecté", "Évalué", "Validé"],
            datasets: [{
                data: [{{ $submittedCount }}, {{ $assignedCount }}, {{ $evaluatedCount }}, {{ $validatedCount }}],
                backgroundColor: ["#f6ad55", "#feb2b2", "#90cdf4", "#9ae6b4"],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display:false } },
            cutout: '0%'
        }
    });
});
</script>

<style>
    /* Global Content Area */
    .content-area {
        background-color: #f4f2f7;
        min-height: 100vh;
        padding: 20px;
    }

    /* Cartes Statistiques */
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Actions Rapides */
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 15px;
        border: 1px solid #edf2f7;
        border-radius: 10px;
        text-decoration: none;
        color: #4a5568;
        margin-bottom: 12px;
        transition: all 0.2s;
        background: #ffffff;
    }

    .action-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        color: #2d3748;
    }

    .action-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 12px;
    }

    /* Tableau */
    .table-container {
        border-radius: 15px;
        overflow: hidden;
        background: white;
    }

    .custom-table thead {
        background-color: #edf2f7;
    }

    .custom-table th {
        border: none;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        color: #718096;
        padding: 15px;
    }

    .custom-table td {
        margin: 11px;
        padding: 4px;
        vertical-align: middle;
        border: 2px solid #f3f5f7;
    }

    /* Légende Graphique */
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding: 10px ;
        border: 1px solid #e4e6e5;
        font-size: 0.9rem;
        border-radius: 20px;
    }

    .dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .table-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-top: 20px;
        border: none;
    }

    .custom-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    /* Style des en-têtes avec les couleurs alternées de l'image */
    .custom-table thead th {
        background-color: #E2E8F0; /* Bleu très clair / Gris */
        color: #4A5568;
        font-weight: 700;
        text-transform: none;
        padding: 15px;
        border: 2px solid rgb(248, 248, 248);
        vertical-align: middle;
    }
</style>

@php
    // Logique de récupération des données
    $studentsCount = \App\Models\User::role('student')->count();
    $teachersCount = \App\Models\User::role('teacher')->count();
    $reportsCount = \App\Models\Report::count();
    $validatedCount = \App\Models\Report::where('status', 'Validé final')->count();

    // Pour les stats du graphique (exemples de statuts à adapter)
    $modifiedCount = \App\Models\Report::where('status', 'modifié')->count();
    $commentedCount = \App\Models\Report::where('status', 'commenté')->count();

    $latestReports = \App\Models\Report::with(['student', 'teacher'])
        ->latest()
        ->limit(5)
        ->get();
@endphp

<div class="content-area">
    <div class="mb-4">
        <h4 class="fw-bold text-uppercase" style="color: #2d3748;">Tableau de bord administrateur</h4>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 shadow-sm">
            <div class="card stat-card text-white shadow-sm" style="background-color: #3EA84C;">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle me-3"><i class="fas fa-users fa-lg"></i></div>
                    <div>
                        <small class="opacity-75">Total Etudiants</small>
                        <h2 class="fw-bold mb-0">{{ $studentsCount }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 shadow-sm">
            <div class="card stat-card shadow-sm text-white" style="background-color: #3681B6;">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle me-3"><i class="fas fa-user-tie fa-lg"></i></div>
                    <div>
                        <small class="opacity-75">Total Enseignants</small>
                        <h2 class="fw-bold mb-0">{{ $teachersCount }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 shadow-sm">
            <div class="card stat-card shadow-sm text-white" style="background-color: #3EA84C;">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle me-3"><i class="fas fa-file-upload fa-lg"></i></div>
                    <div>
                        <small class="opacity-75">Rapports soumis</small>
                        <h2 class="fw-bold mb-0">{{ $reportsCount }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm text-white" style="background-color: #3681B6;">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle me-3"><i class="fas fa-check-circle fa-lg"></i></div>
                    <div>
                        <small class="opacity-75">Rapports validés</small>
                        <h2 class="fw-bold mb-0">{{ $validatedCount }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 p-3 pt-0 text-center shadow">Statistique des rapports</h5>
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div style="height: 220px;">
                                <canvas id="reportChart"></canvas>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ps-md-4">
                                <div class="legend-item">
                                    <span class="dot" style="background: #f6a54c;"></span>
                                    <span>Soumis: <strong>{{ $reportsCount }}</strong></span>
                                </div>
                                <div class="legend-item">
                                    <span class="dot" style="background: #ff7d7d;"></span>
                                    <span>Affecter: <strong>{{ $modifiedCount }}</strong></span>
                                </div>
                                <div class="legend-item">
                                    <span class="dot" style="background: #6da8e3;"></span>
                                    <span>Evaluer: <strong>{{ $commentedCount }}</strong></span>
                                </div>
                                <div class="legend-item">
                                    <span class="dot" style="background: #4cc38a;"></span>
                                    <span>Valider: <strong>{{ $validatedCount }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 p-3 pt-0 text-center shadow">Action rapide</h5>

                    <a href="{{ url('/admin/reports') }}" class="action-btn shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="action-icon bg-primary-subtle text-primary"><i class="fa fa-file-invoice"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Rapports soumis</div>
                                <small class="text-muted">Gérer les documents</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>

                    <a href="{{ url('/students') }}" class="action-btn shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="action-icon bg-success-subtle text-success"><i class="fa fa-user-graduate"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Liste des étudiants</div>
                                <small class="text-muted">Gérer les effectifs</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>

                    <a href="{{ url('/teachers') }}" class="action-btn shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="action-icon bg-info-subtle text-info"><i class="fa fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Liste des enseignants</div>
                                <small class="text-muted">Gérer le corps professoral</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="color: #2d3748;">📋 Liste des Étudiants</h5>

            <form method="GET" action="{{ url()->current() }}" class="d-flex" style="width: 300px;">
                <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 shadow-none"
                        placeholder="Rechercher un étudiant..." value="{{ request('search') }}">
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table custom-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" width="60">N°</th>
                        <th>Étudiant</th>
                        <th>Filière & Niveau</th>
                        <th>Matricule</th>
                        <th>Encadreur</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr class="align-middle bg-white">
                            <td class="ps-4 text-muted fw-medium">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <div class="fw-bold text-dark">{{ $student->name }}</div>
                                <small class="text-muted">{{ $student->email }}</small>
                            </td>

                            <td>
                                <span class="badge text-dark px-3 mb-1">
                                    {{ $student->filiere->name ?? 'N/A' }}
                                </span>
                                <div class="small text-secondary ps-1">
                                    <i class="fas fa-layer-group me-1 small"></i>{{ $student->niveau ?? 'N/A' }}
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-light text-primary border border-primary-subtle fw-bold">
                                    {{ $student->matricule ?? '-' }}
                                </span>
                            </td>

                            <td>
                                @php
                                    // On récupère l'encadreur lié au rapport de l'étudiant
                                    $lastReport = $student->reports->last();
                                @endphp
                                @if ($lastReport && $lastReport->teacher)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center"
                                            style="width: 28px; height: 28px; font-size: 0.65rem; font-weight: bold;">
                                            {{ strtoupper(substr($lastReport->teacher->name, 0, 2)) }}
                                        </div>
                                        <span class="small fw-bold text-dark">{{ $lastReport->teacher->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small italic">Pas d'encadreur</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <a href="{{ route('admin.teachers.edit', $student) }}"
                                        class="btn btn-sm btn-white border-end" title="Modifier / Gérer">
                                        <i class="fas fa-user-edit text-primary"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-white" title="Voir les rapports">
                                        <i class="fas fa-folder-open text-success"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-search fa-2x opacity-25"></i></div>
                                Aucun étudiant trouvé pour ce département.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ["Soumis", "Affecté", "Evalué", "Validé"],
                datasets: [{
                    data: [{{ $reportsCount }}, {{ $modifiedCount }}, {{ $commentedCount }},
                        {{ $validatedCount }}
                    ],
                    backgroundColor: ["#f6ad55", "#feb2b2", "#90cdf4", "#9ae6b4"],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    } // Légende gérée en HTML pour le style
                },
                cutout: '0%' // Change à '50%' si tu veux un format Donut
            }
        });
    });
</script>

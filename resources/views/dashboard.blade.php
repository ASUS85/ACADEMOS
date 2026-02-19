<x-app-layout>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-start border-success border-5" role="alert">
                    <i class="fas fa-check-circle me-2"></i><strong>✅ {{ session('success') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-start border-danger border-5" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><strong>❌ {{ session('error') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Titre -->
            <div class="d-flex align-items-center mb-5">
                <i class="fas fa-graduation-cap fs-1 text-primary me-3"></i>
                <div>
                    <h1 class="h2 fw-bold mb-1">ACADEMOS Dashboard</h1>
                    <p class="text-muted mb-0">Système de gestion des rapports de stage</p>
                </div>
            </div>

            <!-- Info Utilisateur -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="h4 mb-1">👤 Bonjour {{ auth()->user()->name }} !</h3>
                            <div class="badge bg-primary fs-6 px-3 py-2">
                                <i class="fas fa-mask me-1"></i>
                                Rôle : {{ auth()->user()->roles->first()->name ?? 'Aucun rôle' }}
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="text-muted">Connecté le {{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ÉTUDIANT : Aperçu + Historique -->
            @if (auth()->user()->hasRole('student'))
                @php $myReports = auth()->user()->reports()->with('latestVersion')->latest()->limit(3)->get(); @endphp

                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100 text-decoration-none">
                            <div class="card-body p-5 text-center">
                                <i class="fas fa-file-upload fa-4x text-warning mb-4"></i>
                                <h3 class="h4 fw-bold mb-3">📄 Soumettre Rapport</h3>
                                <p class="text-muted mb-4">Nouveau rapport de stage</p>
                                <a href="{{ url('/student/reports/create') }}" class="btn btn-warning btn-lg px-5">
                                    <i class="fas fa-plus me-2"></i>Nouveau
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-header bg-light border-0">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-history me-2 text-primary"></i>
                                    Mes Derniers Rapports ({{ $myReports->count() }})
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                @forelse($myReports as $report)
                                <div class="border-bottom p-3 hover-bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-truncate" style="max-width: 200px;">
                                                {{ Str::limit($report->title, 40) }}
                                            </div>
                                            <small class="text-muted">{{ $report->status }}</small>
                                        </div>
                                        <span class="badge bg-{{ $report->status === 'Validé final' ? 'success' : 'warning' }}">
                                            {{ $report->latestVersion->version ?? 'v1' }}
                                        </span>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4 text-muted">
                                    Aucun rapport
                                </div>
                                @endforelse
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ url('/student/dashboard') }}" class="btn btn-outline-primary w-100">
                                    📋 Voir Tous Mes Rapports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- ENSEIGNANT : Aperçu + Historique -->
            @elseif(auth()->user()->hasRole('teacher'))
                @php $myReports = auth()->user()->assignedReports()->with('latestVersion', 'student')->latest()->limit(3)->get(); @endphp

                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-header bg-light border-0">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-chalkboard-teacher me-2 text-success"></i>
                                    Mes Rapports à Corriger
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                @forelse($myReports as $report)
                                <div class="border-bottom p-3 hover-bg-light">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="fw-bold text-truncate">{{ Str::limit($report->title, 30) }}</div>
                                            <small class="text-muted">👨‍🎓 {{ $report->student->name ?? 'Étudiant' }}</small>
                                        </div>
                                        <span class="badge bg-info">{{ $report->latestVersion->version ?? 'v1' }}</span>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4 text-muted">
                                    Aucun rapport affecté
                                </div>
                                @endforelse
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="{{ url('/teacher/reports') }}" class="btn btn-success w-100">
                                    <i class="fas fa-edit me-2"></i>Corriger Rapports
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100 bg-gradient-warning bg-opacity-10">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-lightbulb fa-3x text-warning mb-3"></i>
                                <h4 class="fw-bold mb-2">💡 Prêt à corriger ?</h4>
                                <p class="text-muted mb-4">Consultez l'historique complet des versions et ajoutez vos commentaires</p>
                                <a href="{{ url('/teacher/reports') }}" class="btn btn-warning btn-lg px-4">
                                    👀 Voir Historique Complet
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- ADMIN -->
            @elseif (auth()->user()->hasRole('admin'))
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <a href="{{ url('/admin/reports') }}" class="card border-0 shadow h-100 text-decoration-none">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-file-alt fa-3x text-success mb-3"></i>
                                <h5 class="card-title fw-bold">📋 Tous les rapports</h5>
                                <p class="text-muted">Gérer / Affecter</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/admin/users') }}" class="card border-0 shadow h-100 text-decoration-none">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h5 class="card-title fw-bold">👥 Gérer utilisateurs</h5>
                                <div class="text-muted small">Créer enseignants</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/admin/stats') }}" class="card border-0 shadow h-100 text-decoration-none">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                                <h5 class="card-title fw-bold">📊 Statistiques</h5>
                            </div>
                        </a>
                    </div>
                </div>

            <!-- JURY -->
            @elseif(auth()->user()->hasRole('jury'))
                @php $juryCount = auth()->user()->juryReports()->count(); @endphp
                <div class="row g-4 mb-5">
                    <div class="col-12">
                        <div class="card border-0 shadow-lg">
                            <div class="card-body p-5">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="h4 mb-3">
                                            <i class="fas fa-gavel text-danger me-2"></i>
                                            Mes Rapports à évaluer
                                        </h3>
                                        <h2 class="display-5 fw-bold text-primary">
                                            {{ $juryCount }} rapport(s)
                                        </h2>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <a href="{{ url('/jury/reports') }}" class="btn btn-outline-primary btn-lg px-5">
                                            <i class="fas fa-eye me-2"></i>Évaluer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4 opacity-50"></i>
                    <h2 class="h3 mb-4">⚠️ Aucun rôle assigné</h2>
                    <p class="lead text-muted">Contacte l'administrateur pour assigner un rôle</p>
                </div>
            @endif

            <!-- Navigation Rapide (TOUS RÔLES) -->
            <div class="row g-3 mt-5">
                @if(auth()->user()->hasRole('student'))
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ url('/student/dashboard') }}" class="btn btn-outline-primary w-100 p-4 rounded-3 border-0 shadow-sm hover-shadow-lg">
                            <i class="fas fa-list fa-2x mb-2 d-block text-primary"></i>
                            <span class="fw-bold">📋 Historique Complet</span><br>
                            <small class="text-muted">Versions + Commentaires</small>
                        </a>
                    </div>
                @endif
                @if(auth()->user()->hasRole('teacher'))
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ url('/teacher/reports') }}" class="btn btn-outline-success w-100 p-4 rounded-3 border-0 shadow-sm hover-shadow-lg">
                            <i class="fas fa-history fa-2x mb-2 d-block text-success"></i>
                            <span class="fw-bold">📜 Tous mes Rapports</span><br>
                            <small class="text-muted">Avec historique versions</small>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>

<style>
.hover-bg-light:hover { background-color: #f8f9fa !important; }
.hover-shadow-lg:hover { box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important; }
.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
.bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
</style>

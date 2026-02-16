<x-app-layout>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><strong>✅ {{ session('success') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

            <!-- Boutons selon rôle -->
            @if (auth()->user()->hasRole('admin'))
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <a href="{{ url('/admin/reports') }}" class="card border-0 shadow h-100 text-decoration-none">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-file-alt fa-3x text-success mb-3"></i>
                                <h5 class="card-title fw-bold">📋 Tous les rapports</h5>
                                <p class="card-text text-muted">Gérer / Affecter</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('/admin/users') }}" class="card border-0 shadow h-100 text-decoration-none">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h5 class="card-title fw-bold">👥 Gérer utilisateurs</h5>
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

            @elseif(auth()->user()->hasRole('jury'))
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow">
                            <div class="card-body p-5">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="h4 mb-3">
                                            <i class="fas fa-gavel text-danger me-2"></i>
                                            Mes Rapports à évaluer
                                        </h3>
                                        <h2 class="display-5 fw-bold text-primary">
                                            {{ auth()->user()->juryReports()->count() }} rapport(s)
                                        </h2>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <a href="/jury/reports" class="btn btn-outline-primary btn-lg px-5">
                                            <i class="fas fa-eye me-2"></i>Évaluer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->hasRole('student'))
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body p-5 text-center">
                                <i class="fas fa-file-upload fa-4x text-warning mb-4 opacity-75"></i>
                                <h3 class="h4 fw-bold mb-3">📄 Soumettre Rapport</h3>
                                <p class="text-muted mb-4">Nouveau rapport de stage</p>
                                <a href="/student/reports/create" class="btn btn-warning btn-lg px-5">
                                    <i class="fas fa-plus me-2"></i>Nouveau
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->hasRole('teacher'))
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow">
                            <div class="card-body p-5">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="h4 mb-3">
                                            <i class="fas fa-chalkboard-teacher text-success me-2"></i>
                                            Mes Rapports à corriger
                                        </h3>
                                        <h2 class="display-5 fw-bold text-success">
                                            {{ auth()->user()->assignedReports()->count() }} rapport(s)
                                        </h2>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <a href="/teacher/reports" class="btn btn-success btn-lg px-5">
                                            <i class="fas fa-edit me-2"></i>Corriger
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
        </div>
    </div>
</div>
</x-app-layout>

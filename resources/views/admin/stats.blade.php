<x-app-layout>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <i class="fas fa-chart-bar fa-2x text-primary me-3"></i>
                <h1 class="h3 mb-1 fw-bold">📊 Statistiques ACADEMOS</h1>
            </div>
        </div>
    </div>

    @php
        // Détecte si tous les compteurs sont à zéro
        $keys = ['total_reports', 'total_users', 'students', 'teachers', 'juries'];
        $allZero = true;
        foreach ($keys as $k) {
            if (!empty($stats[$k]) && (int) $stats[$k] > 0) {
                $allZero = false;
                break;
            }
        }
    @endphp

    @if($allZero)
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                        <h3 class="h4 fw-bold mb-2">Aucune donnée pour le moment</h3>
                        <p class="text-muted mb-4">Il n'y a encore aucun rapport ou utilisateur enregistré. Lorsque des étudiants soumettront des rapports ou que des comptes seront créés, les statistiques s'afficheront ici.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('superadmin.reports') }}" class="btn btn-outline-primary">Voir les rapports</a>
                            <a href="{{ route('superadmin.users.index') ?? '#' }}" class="btn btn-outline-secondary">Gérer les utilisateurs</a>
                            <button class="btn btn-primary" onclick="location.reload()">Rafraîchir</button>
                        </div>
                        <small class="d-block text-muted mt-3">Suggestion : créer des données de test via un seeder si vous configurez l'environnement local.</small>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                        <h2 class="display-4 fw-bold">{{ $stats['total_reports'] }}</h2>
                        <p class="text-primary-emphasis fs-5">Rapports</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x text-success mb-3"></i>
                        <h2 class="display-4 fw-bold">{{ $stats['total_users'] }}</h2>
                        <p class="text-success-emphasis fs-5">Utilisateurs</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-user-graduate fa-3x text-warning mb-3"></i>
                        <h2 class="display-4 fw-bold">{{ $stats['students'] }}</h2>
                        <p class="text-warning-emphasis fs-5">Étudiants</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-chalkboard-teacher fa-3x text-info mb-3"></i>
                        <h2 class="display-4 fw-bold">{{ $stats['teachers'] + $stats['juries'] }}</h2>
                        <p class="text-info-emphasis fs-5">Encadrants</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            @php
                $backRoute = auth()->user()->hasRole('superadmin')
                    ? route('superadmin.reports')
                    : route('admin.reports.index');
            @endphp
            <a href="{{ $backRoute }}" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Retour Rapports
            </a>
        </div>
    </div>
</div>
</x-app-layout>

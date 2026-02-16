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

    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.reports') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Retour Rapports
            </a>
        </div>
    </div>
</div>
</x-app-layout>

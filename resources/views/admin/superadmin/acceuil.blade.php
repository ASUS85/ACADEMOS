 @php
                $usersCount = \App\Models\User::count();
                $reportsCount = \App\Models\Report::count();
                $adminsCount = \App\Models\User::role('admin')->count();
                $teachersCount = \App\Models\User::role('teacher')->count();
            @endphp

            <!-- Stats Globales -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card shadow border-0 text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h4 class="fw-bold">{{ $usersCount }}</h4>
                            <small class="text-muted">Utilisateurs</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow border-0 text-center">
                        <div class="card-body">
                            <i class="fas fa-user-shield fa-2x text-danger mb-2"></i>
                            <h4 class="fw-bold">{{ $adminsCount }}</h4>
                            <small class="text-muted">Administrateurs</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow border-0 text-center">
                        <div class="card-body">
                            <i class="fas fa-chalkboard-teacher fa-2x text-success mb-2"></i>
                            <h4 class="fw-bold">{{ $teachersCount }}</h4>
                            <small class="text-muted">Enseignants</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow border-0 text-center">
                        <div class="card-body">
                            <i class="fas fa-file-alt fa-2x text-warning mb-2"></i>
                            <h4 class="fw-bold">{{ $reportsCount }}</h4>
                            <small class="text-muted">Rapports</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Super Admin -->
            <div class="row g-4 mb-5">

                <div class="col-md-4">
                    <a href="{{ url('/superadmin/users') }}" class="card border-0 shadow h-100 text-decoration-none">
                        <div class="card-body text-center">
                            <i class="fas fa-users-cog fa-3x text-primary mb-3"></i>
                            <h5 class="fw-bold">👥 Tous Utilisateurs</h5>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ url('/superadmin/reports') }}"
                        class="card border-0 shadow h-100 text-decoration-none">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-3x text-success mb-3"></i>
                            <h5 class="fw-bold">📄 Tous Rapports</h5>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ url('/superadmin/stats') }}" class="card border-0 shadow h-100 text-decoration-none">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                            <h5 class="fw-bold">📊 Statistiques Globales</h5>
                        </div>
                    </a>
                </div>

            </div>

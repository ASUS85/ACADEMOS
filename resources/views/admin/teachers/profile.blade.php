<x-app-layout>
    <div class="bg-light py-4 px-3 px-md-4">
        <div class="container-fluid">
            {{-- Header --}}
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <div class="mb-4 position-relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2dd4bf&color=fff&size=120&rounded=true"
                             class="rounded-circle shadow-lg"
                             style="width: 120px; height: 120px; border: 4px solid white;"
                             alt="Avatar">
                        <div class="position-absolute top-100 start-50 translate-middle d-none" id="avatar-loader">
                            <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2">{{ auth()->user()->name }}</h2>
                    <p class="text-success mb-0 fs-5">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        Enseignant
                    </p>
                    <p class="text-muted fs-6">
                        {{ auth()->user()->department->name ?? 'Département' }}
                    </p>
                    @if(auth()->user()->grade)
                        <p class="badge bg-success bg-opacity-75 fs-6 px-3 py-2 mt-1">
                            {{ auth()->user()->grade }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                {{-- Informations personnelles --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow rounded-4 h-100">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-info-circle text-success me-2"></i>
                                Informations personnelles
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-success fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Nom complet</label>
                                            <span class="fw-semibold">{{ auth()->user()->name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-success fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Email</label>
                                            <span class="fw-semibold">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building text-success fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Département</label>
                                            <span class="fw-semibold badge bg-light text-dark border rounded-pill px-3 py-2">
                                                {{ auth()->user()->department->name ?? 'Non assigné' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if(auth()->user()->filiere)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-graduation-cap text-success fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Spécialité</label>
                                            <span class="fw-semibold badge bg-success bg-opacity-20 border border-success px-3 py-2">
                                                {{ auth()->user()->filiere->name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar text-success fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Membre depuis</label>
                                            <span class="text-muted">
                                                {{ auth()->user()->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if(auth()->user()->matricule)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-badge text-success fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Matricule</label>
                                            <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-semibold">
                                                {{ auth()->user()->matricule }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users text-success fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Rapports assignés</label>
                                            <span class="badge bg-info bg-opacity-20 border border-info px-4 py-2 fs-6">
                                                {{ auth()->user()->reports()->count() }} rapport{{ auth()->user()->reports()->count() > 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions rapides --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow rounded-4 h-100">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-cogs text-info me-2"></i>
                                Actions rapides
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="d-grid gap-3">
                                <button class="btn btn-success rounded-3 border-0 shadow-sm py-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="fas fa-pen me-2"></i>
                                    Modifier le profil
                                </button>

                                <button class="btn btn-outline-success rounded-3 border-0 shadow-sm py-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key me-2"></i>
                                    Changer mot de passe
                                </button>

                                <a href="{{ route('teacher.reports.index') }}" class="btn btn-outline-primary rounded-3 shadow-sm py-3 text-start">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Mes rapports ({{ auth()->user()->reports()->count() }})
                                </a>

                                <a href="{{ url('/dashboard') }}" class="btn btn-outline-info rounded-3 shadow-sm py-3 text-start">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Tableau de bord
                                </a>

                                <button class="btn btn-outline-danger rounded-3 shadow-sm py-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Déconnexion
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Modifier Profil --}}
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 shadow-lg">
                <form method="POST" action="{{ route('teacher.profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header bg-success text-white rounded-top-4">
                        <h5 class="modal-title">
                            <i class="fas fa-user-edit me-2"></i>Modifier profil enseignant
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nom complet</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @if(auth()->user()->matricule)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Matricule</label>
                                <input type="text" name="matricule" class="form-control @error('matricule') is-invalid @enderror"
                                       value="{{ old('matricule', auth()->user()->matricule) }}">
                                @error('matricule') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @endif
                            @if(auth()->user()->filiere)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Spécialité</label>
                                <input type="text" name="specialite" class="form-control @error('specialite') is-invalid @enderror"
                                       value="{{ old('specialite', auth()->user()->specialite ?? auth()->user()->filiere->name) }}">
                                @error('specialite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Changer mot de passe --}}
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 shadow-lg">
                <form method="POST" action="{{ route('teacher.profile.password') }}">
                    @csrf
                    <div class="modal-header bg-info text-white rounded-top-4">
                        <h5 class="modal-title">
                            <i class="fas fa-key me-2"></i>Changer mot de passe
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirmer</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-info text-white">Changer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</x-app-layout>

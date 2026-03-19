<x-app-layout>
    <div class="bg-light py-4 px-3 px-md-4">
        <div class="container-fluid">
            {{-- Header --}}
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <div class="mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=1c8ac4&color=fff&size=120&rounded=true"
                             class="rounded-circle shadow-lg"
                             style="width: 120px; height: 120px; border: 4px solid white;"
                             alt="Avatar">
                    </div>
                    <h2 class="fw-bold mb-2">{{ auth()->user()->name }}</h2>
                    <p class="text-success mb-0 fs-5">
                        <i class="fas fa-user-shield me-2"></i>
                        Administrateur Département
                    </p>
                    <p class="text-muted">
                        {{ auth()->user()->department->name ?? 'Département' }}
                    </p>
                </div>
            </div>

            <div class="row g-4">
                {{-- Informations personnelles --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow rounded-4 h-100">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Informations personnelles
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-primary fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Nom complet</label>
                                            <span class="fw-semibold">{{ auth()->user()->name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-primary fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Email</label>
                                            <span class="fw-semibold">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building text-primary fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Département</label>
                                            <span class="fw-semibold badge bg-light text-dark border rounded-pill px-3 py-2">
                                                {{ auth()->user()->department->name ?? 'Non assigné' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar text-primary fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Membre depuis</label>
                                            <span class="text-muted">
                                                {{ auth()->user()->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-badge text-primary fs-4 me-3"></i>
                                        <div>
                                            <label class="fw-bold text-muted small mb-1 d-block">Rôle</label>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-4 py-2 fs-6">
                                                Administrateur
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
                                <button class="btn btn-primary rounded-3 border-0 shadow-sm py-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="fas fa-pen me-2"></i>
                                    Modifier le profil
                                </button>

                                <button class="btn btn-outline-primary rounded-3 border-0 shadow-sm py-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key me-2"></i>
                                    Changer mot de passe
                                </button>

                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-success rounded-3 shadow-sm py-3 text-start">
                                    <i class="fas fa-users me-2"></i>
                                    Gérer les utilisateurs
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
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title">
                            <i class="fas fa-user-edit me-2"></i>Modifier profil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nom complet</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Changer mot de passe --}}
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 shadow-lg">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <div class="modal-header bg-warning text-dark rounded-top-4">
                        <h5 class="modal-title">
                            <i class="fas fa-key me-2"></i>Changer mot de passe
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirmer</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">Changer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</x-app-layout>

<x-app-layout>
    <div class="bg-light py-4 px-3 px-md-4">
        <div class="container-fluid">
            <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1d4ed8, #0f766e);">
                <div class="card-body p-4 p-lg-5 text-white">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8 d-flex align-items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ffffff&color=1d4ed8&size=120&rounded=true" class="rounded-circle shadow-lg border border-4 border-white" style="width: 120px; height: 120px;" alt="Avatar">
                            <div>
                                <span class="badge bg-white bg-opacity-20 text-white mb-2">Profil administrateur</span>
                                <h2 class="fw-bold mb-1">{{ auth()->user()->name }}</h2>
                                <p class="mb-1 fs-5 opacity-90">
                                    <i class="fas fa-user-shield me-2"></i>Administrateur Département
                                </p>
                                <p class="mb-0 opacity-75">{{ auth()->user()->department->name ?? 'Département' }}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-inline-flex flex-wrap gap-2">
                                <span class="badge bg-white text-primary px-3 py-2">{{ auth()->user()->email }}</span>
                                <span class="badge bg-white text-primary px-3 py-2">Membre depuis {{ auth()->user()->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Informations personnelles
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 bg-light h-100">
                                        <label class="fw-bold text-muted small mb-1 d-block">Nom complet</label>
                                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 bg-light h-100">
                                        <label class="fw-bold text-muted small mb-1 d-block">Email</label>
                                        <div class="fw-semibold">{{ auth()->user()->email }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 bg-light h-100">
                                        <label class="fw-bold text-muted small mb-1 d-block">Département</label>
                                        <div class="fw-semibold">{{ auth()->user()->department->name ?? 'Non assigné' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 bg-light h-100">
                                        <label class="fw-bold text-muted small mb-1 d-block">Rôle</label>
                                        <div class="fw-semibold text-success">Administrateur</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-cogs text-info me-2"></i>
                                Actions rapides
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-grid gap-3">
                                <button class="btn btn-primary rounded-3 border-0 shadow-sm py-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="fas fa-pen me-2"></i>Modifier le profil
                                </button>
                                <button class="btn btn-outline-primary rounded-3 border-0 shadow-sm py-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key me-2"></i>Changer mot de passe
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-success rounded-3 shadow-sm py-3 text-start" data-loader-target="#globalLoader">
                                    <i class="fas fa-users me-2"></i>Gérer les utilisateurs
                                </a>
                                <button class="btn btn-outline-danger rounded-3 shadow-sm py-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
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
                <form method="POST" action="{{ route('admin.update') }}">
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
                        <button type="submit" class="btn btn-primary" data-loader-target="#globalLoader">Enregistrer</button>
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
                        <button type="submit" class="btn btn-warning" data-loader-target="#globalLoader">Changer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</x-app-layout>

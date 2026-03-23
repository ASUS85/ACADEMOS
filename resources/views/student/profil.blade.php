<x-app-layout>
    <div class="bg-light py-4 px-3 px-md-4">
        <div class="container-fluid">

            {{-- HEADER --}}
            <div class="text-center mb-5">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0d6efd&color=fff&size=120&rounded=true"
                    class="rounded-circle shadow" style="width: 120px; height: 120px; border: 4px solid white;">

                <h2 class="fw-bold mt-3">{{ auth()->user()->name }}</h2>

                <span class="badge bg-primary px-3 py-2">
                    🎓 Étudiant
                </span>

                <p class="text-muted mt-2">
                    {{ auth()->user()->filiere->name ?? 'Filière non définie' }} |
                    Niveau : {{ auth()->user()->niveau ?? '-' }}
                </p>

                <div class="mt-3">
                    <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#editProfileModal">
                        <i class="fas fa-pen me-2"></i>
                        Modifier mon profil
                    </button>
                </div>
            </div>

            <div class="row g-4">

                {{-- INFOS ETUDIANT --}}
                <div class="col-lg-8">
                    <div class="card shadow border-0 rounded-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="fw-bold">
                                <i class="fas fa-user text-primary me-2"></i>
                                Informations académiques
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row g-4">

                                <div class="col-md-6">
                                    <strong>Matricule :</strong><br>
                                    <span class="text-muted">{{ auth()->user()->matricule ?? '-' }}</span>
                                </div>

                                <div class="col-md-6">
                                    <strong>Email :</strong><br>
                                    <span class="text-muted">{{ auth()->user()->email }}</span>
                                </div>

                                <div class="col-md-6">
                                    <strong>Département :</strong><br>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        {{ auth()->user()->department->name ?? '-' }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <strong>Spécialité :</strong><br>
                                    <span class="text-muted">
                                        {{  auth()->user()->filiere->name ?? '-' }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <strong>Sexe :</strong><br>
                                    <span class="badge bg-{{ auth()->user()->sexe == 'M' ? 'primary' : 'pink' }}">
                                        {{ auth()->user()->sexe == 'M' ? 'Homme' : 'Femme' }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <strong>Membre depuis :</strong><br>
                                    <span class="text-muted">
                                        {{ auth()->user()->created_at->format('d/m/Y') }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- STATISTIQUES --}}
                <div class="col-lg-4">
                    <div class="card shadow border-0 rounded-4">
                        <div class="card-header bg-white border-0">
                            <h6 class="fw-bold">
                                <i class="fas fa-chart-bar text-success me-2"></i>
                                Statistiques
                            </h6>
                        </div>

                        <div class="card-body">

                            <div class="d-flex justify-content-between mb-3">
                                <span>Total rapports</span>
                                <strong>{{ auth()->user()->reports->count() }}</strong>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span>Validés</span>
                                <strong class="text-success">
                                    {{ auth()->user()->reports->where('status', 'Validé final')->count() }}
                                </strong>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span>En attente</span>
                                <strong class="text-warning">
                                    {{ auth()->user()->reports->where('status', 'Soumis')->count() }}
                                </strong>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- LISTE DES RAPPORTS --}}
            <div class="mt-5">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-header bg-white border-0">
                        <h5 class="fw-bold">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            Mes rapports
                        </h5>
                    </div>

                    <div class="card-body">

                        @forelse(auth()->user()->reports as $report)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                                <div>
                                    <strong>{{ $report->title }}</strong><br>
                                    <small class="text-muted">
                                        {{ $report->created_at->format('d/m/Y') }}
                                    </small>
                                </div>

                                <span
                                    class="badge bg-{{ $report->status == 'Validé final' ? 'success' : ($report->status == 'Rejeté' ? 'danger' : 'warning') }}">
                                    {{ $report->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-muted text-center">Aucun rapport soumis</p>
                        @endforelse

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL MODIFIER PROFIL --}}
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 shadow-lg">

                <form method="POST" action="{{ url('student/update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title">
                            <i class="fas fa-user-edit me-2"></i>
                            Modifier mon profil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nom complet</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ auth()->user()->name }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ auth()->user()->email }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Spécialité</label>
                                <input type="text" name="specialite" class="form-control"
                                    value="{{  auth()->user()->filiere->name }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Niveau</label>
                                <input type="text" name="niveau" class="form-control"
                                    value="{{ auth()->user()->niveau }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sexe</label>
                                <select name="sexe" class="form-select">
                                    <option value="M" {{ auth()->user()->sexe == 'M' ? 'selected' : '' }}>Homme
                                    </option>
                                    <option value="F" {{ auth()->user()->sexe == 'F' ? 'selected' : '' }}>Femme
                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Enregistrer
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>

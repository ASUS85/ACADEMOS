<x-app-layout>
    <style>
        .status-icon {
            width: 40px;
            height: 40px;
            background: #19a55a;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .drop-zone {
            border: 2px dashed #2b7ca3;
            border-radius: 10px;
            transition: 0.3s;
            cursor: pointer;
        }

        .drop-zone:hover {
            background: #f1f7fb;
        }

        .bg-wait {
            background: #ecd3ad;
            color: #856404;
        }
        .welcome-gradient {
        background: linear-gradient(to left, #1083ee, #0660d4); /* Dégradé bleu de gauche à droite */
        color: white; /* Texte en blanc pour le contraste */
    }
    </style>

    <div class="container-fluid  p-4">
        <div class="mb-4 welcome-gradient p-4 rounded-3 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    @php
                        $heure = date('H');
                        $salutation = $heure >= 6 && $heure < 18 ? 'Bonjour' : 'Bonsoir';
                    @endphp
                    <h2 class="h5 fw-bold text-uppercase mb-1" style="letter-spacing: 1px; color: white !important;">
                        {{ $salutation }}, <span class="fw-normal">{{ Auth::user()->name }}</span> !
                    </h2>
                    <p class="mb-0 opacity-75 small">
                        Ravi de vous revoir sur <strong>Academo</strong>. Voici l'état d'avancement de vos rapports.
                    </p>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="status-icon shadow-sm">
                        <i class="fa fa-check"></i>
                    </div>
                    <div>
                        <strong class="d-block">Statut :
                            @if ($latestReport)
                                {{ $latestReport->status }}
                            @else
                                Aucun rapport soumis
                            @endif
                        </strong>
                        <span class="badge rounded-pill bg-warning text-dark px-3 mt-1">
                            {{ $latestReport->teacher->name ?? 'En attente d\'encadreur' }}
                        </span>
                    </div>
                </div>

                @if ($latestReport && $latestReport->teacher_comment)
                    <button class="btn btn-outline-secondary mt-3 mt-md-0 rounded-3 px-4" data-bs-toggle="collapse"
                        data-bs-target="#collapseComment">
                        <i class="fa fa-comment me-2"></i> Voir les commentaires
                    </button>
                @endif
            </div>
        </div>

        @if ($latestReport && $latestReport->teacher_comment)
            <div class="collapse mb-4" id="collapseComment">
                <div class="alert alert-info border-0 shadow-sm mb-0">
                    <strong>Dernier commentaire de l'encadreur :</strong><br>
                    "{{ $latestReport->teacher_comment }}"
                </div>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold m-2 p-3 shadow-sm rounded-3">Soumettre mon rapport</h3>

                        <form action="{{ route('student.reports.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Titre du rapport</label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="Ex: Rapport de stage - Fin d'étude" required>
                            </div>

                            <div class="d-flex align-items-center justify-content-center drop-zone p-5 text-center mb-3"
                                id="dropZone"
                                style="
    height: 300px;>

                                <p class="text-muted mb-0"
                                id="fileNameDisplay"> <i class="fa fa-file-arrow-up fa-2x text-primary mb-2"></i>
                                Glisser un fichier ou cliquer pour
                                sélectionner</p>
                                <input type="file" name="file" id="fileInput" hidden accept=".pdf">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"
                                style="background: #1e7ca6;">
                                SOUMETTRE MON RAPPORT
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body py-4 px-0">
                        <h3 class="h6 fw-bold m-2 p-3 shadow-sm rounded-3">Rapports récents</h3>

                        @forelse($reports->take(3) as $report)
                            <div class="d-flex align-items-center justify-content-between p-3 mb-3 m-4 rounded-3 shadow"
                                style="background: #f7f9fb;">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa fa-file text-primary fs-4"></i>
                                    <div>
                                        <span class="d-block fw-bold small text-truncate"
                                            style="max-width: 150px;">{{ $report->title }}</span>
                                        <small class="text-muted">Déposé le
                                            {{ $report->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>

                                @php
                                    $btnClass = match ($report->status) {
                                        'Validé final' => 'btn-success',
                                        'À corriger' => 'btn-danger',
                                        'En attente' => 'bg-wait',
                                        default => 'btn-primary',
                                    };
                                @endphp
                                <span
                                    class="badge {{ str_contains($btnClass, 'bg') ? $btnClass : 'btn ' . $btnClass }} px-3 py-2 small">
                                    {{ $report->status }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <svg width="180" height="180" viewBox="0 0 200 200" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="100" cy="100" r="90" fill="#f1f7fb" />
                                        <path d="M70 60H130V140H70V60Z" fill="white" stroke="#1e7ca6" stroke-width="2"
                                            stroke-linejoin="round" />
                                        <path d="M80 80H120" stroke="#cbd5e0" stroke-width="2" stroke-linecap="round" />
                                        <path d="M80 100H120" stroke="#cbd5e0" stroke-width="2"
                                            stroke-linecap="round" />
                                        <path d="M80 120H100" stroke="#cbd5e0" stroke-width="2"
                                            stroke-linecap="round" />
                                        <circle cx="140" cy="140" r="30" fill="#1e7ca6" />
                                        <path d="M132 140H148M140 132V148" stroke="white" stroke-width="3"
                                            stroke-linecap="round" />
                                    </svg>
                                </div>
                                <h5 class="fw-bold text-dark">Prêt à commencer ?</h5>
                                <p class="text-muted px-4">
                                    Vous n'avez pas encore soumis de rapport. Utilisez le formulaire à gauche pour
                                    envoyer votre première version à votre encadreur.
                                </p>
                                <i class="fa fa-arrow-left text-primary d-none d-lg-inline animate-bounce"></i>
                            </div>
                        @endforelse

                        @if ($reports->count() > 3)
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-link text-decoration-none fw-bold"
                                    style="color: #1e7ca6;">VOIR PLUS</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dropZone = document.getElementById("dropZone");
        const fileInput = document.getElementById("fileInput");
        const fileNameDisplay = document.getElementById("fileNameDisplay");

        dropZone.addEventListener("click", () => fileInput.click());
        fileInput.addEventListener("change", (e) => {
            if (e.target.files.length) {
                fileNameDisplay.innerHTML = `<strong>Fichier sélectionné :</strong> ${e.target.files[0].name}`;
            }
        });
    </script>
</x-app-layout>

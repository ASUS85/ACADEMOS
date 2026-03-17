<x-app-layout>
<style>
    .card-custom {
        position: relative;
        border: none;
        border-radius: 12px;
        color: white;
        transition: transform 0.2s;
        cursor: pointer;
    }
    .card-custom:hover { transform: translateY(-5px); }

    /* Effet d'épaisseur derrière les cartes */
    .card-custom::after {
        content: "";
        position: absolute;
        right: -10px;
        top: 10px;
        width: 100%;
        height: 100%;
        border-radius: 12px;
        z-index: -1;
        opacity: 0.4;
    }

    .bg-attente { background: #ead3a7; color: #333 !important; }
    .bg-attente::after { background: #e6cfa1; }

    .bg-valider { background: #0fa958; }
    .bg-valider::after { background: #0c8d4a; }

    .bg-terminer { background: #157ea3; }
    .bg-terminer::after { background: #116c8c; }

    .avatar-circle {
        width: 35px; height: 35px;
        background: #167da4; color: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold;
    }
</style>

<div class="container-fluid py-4 px-5">
    <div class="mb-4 p-4 rounded-3 shadow-sm text-white" style="background: linear-gradient(to right, #1b75eb, #1255af);">
        <h2 class="h5 fw-bold text-uppercase mb-1">BIENVENUE SUR ACADEMO, {{ Auth::user()->name }}</h2>
        <p class="mb-0 opacity-75 small">Gestion de vos étudiants et correction des rapports.</p>
    </div>

    <div class="row g-5 mb-5 mt-2">
        <div class="col-md-4">
            <div class="card card-custom bg-attente p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Rapports en attente</span>
                    <span class="display-6 fw-bold">{{ $stats['pending'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom bg-valider p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-white">Rapports validés</span>
                    <span class="display-6 fw-bold text-white">{{ $stats['validated'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom bg-terminer p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-white">Rapports terminés</span>
                    <span class="display-6 fw-bold text-white">{{ $stats['finished'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-3 shadow-sm">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h3 class="h5 fw-bold mb-0">Liste de mes étudiants</h3>

            <div class="d-flex gap-2 align-items-center">
                <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2">
                    <select name="filiere" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()">
                        <option value="">Toutes les Filières</option>
                        @foreach($filieres as $filiere)
                            <option value="{{ $filiere->id }}" {{ request('filiere') == $filiere->id ? 'selected' : '' }}>
                                {{ $filiere->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <div class="input-group input-group-sm bg-light rounded-2 px-2">
                    <span class="input-group-text bg-transparent border-0">🔍</span>
                    <input type="text" id="teacherSearch" class="form-control bg-transparent border-0" placeholder="Rechercher...">
                </div>
            </div>
        </div>

        @if($assignedReports->isEmpty())
            <div class="text-center py-5">
                <svg width="150" height="150" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0" stroke-width="1">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <p class="text-muted mt-3">Aucun étudiant ne vous a encore été affecté.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle" id="teacherTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nom de l'étudiant</th>
                            <th>Date d'envoi</th>
                            <th>Filière</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignedReports as $report)
                        <tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#reportModal{{ $report->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">{{ strtoupper(substr($report->student->name, 0, 1)) }}</div>
                                    <span class="fw-bold">{{ $report->student->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $report->updated_at->format('d/m/Y') }}</td>
                            <td><span class="badge bg-light text-dark">{{ $report->student->filiere->name ?? 'N/A' }}</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Détails</button>
                            </td>
                        </tr>

                        @include('teacher.reports.infoStudentModel', ['report' => $report])

                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
    document.getElementById("teacherSearch").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#teacherTable tbody tr");
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>
</x-app-layout>

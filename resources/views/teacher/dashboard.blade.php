<x-app-layout>
    <style>
        .card-custom {
            border-radius: 16px;
            color: white;
            transition: 0.3s;
            cursor: pointer;
        }

        .card-custom:hover {
            transform: translateY(-5px) scale(1.01);
        }

        .bg-attente {
            background: #3681B6;
        }

        .bg-valider {
            background: #3EA84C;
        }

        .bg-terminer {
            background: #3681B6;
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            background: #0d6efd;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .table-hover tbody tr:hover {
            background: #f8f9fa;
        }
    </style>

    <div class="container-fluid py-4 px-4">

        {{-- HEADER --}}
        <div class="mb-4 p-4 rounded-4 shadow text-white" style="background: linear-gradient(135deg, #1b75eb, #1255af);">
            <h2 class="fw-bold mb-1">
                👨‍🏫 Bonjour {{ Auth::user()->name }}
            </h2>
            <p class="mb-0 opacity-75">
                Suivi des rapports et encadrement des étudiants
            </p>
        </div>

        {{-- STATS --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card card-custom bg-attente p-4 shadow">
                    <div class="d-flex justify-content-between">
                        <span>En attente</span>
                        <h3>{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-custom bg-valider p-4 shadow">
                    <div class="d-flex justify-content-between">
                        <span>Validés</span>
                        <h3>{{ $stats['validated'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-custom bg-terminer p-4 shadow">
                    <div class="d-flex justify-content-between">
                        <span>Terminés</span>
                        <h3>{{ $stats['finished'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card shadow border-0 rounded-4">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold">📋 Mes étudiants</h5>

                    <div class="d-flex gap-2">

                        {{-- FILTRE --}}
                        <form method="GET">
                            <select name="filiere" class="form-select" onchange="this.form.submit()">
                                <option value="">Toutes les filières</option>
                                @foreach ($filieres as $filiere)
                                    <option value="{{ $filiere->id }}"
                                        {{ request('filiere') == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        {{-- SEARCH --}}
                        <input type="text" id="teacherSearch" class="form-control" placeholder="🔍 Rechercher...">
                    </div>
                </div>

                @if ($assignedReports->isEmpty())
                    <div class="text-center py-5 text-muted">
                        Aucun étudiant assigné 😔
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="teacherTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Date</th>
                                    <th>Filière</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($assignedReports as $report)
                                    @php
                                        $statusClass = match ($report->status) {
                                            \App\Models\Report::STATUS_FINAL => 'success',
                                            \App\Models\Report::STATUS_VALIDATED => 'primary',
                                            \App\Models\Report::STATUS_REJECTED => 'danger',
                                            \App\Models\Report::STATUS_COMMENTED => 'info',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <tr data-bs-toggle="modal" data-bs-target="#reportModal{{ $report->id }}"
                                        style="cursor:pointer">

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-2">
                                                    {{ strtoupper(substr($report->student->name, 0, 1)) }}
                                                </div>
                                                {{ $report->student->name }}
                                            </div>
                                        </td>

                                        <td>{{ $report->updated_at->format('d/m/Y') }}</td>

                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $report->student->filiere->name ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                    </tr>

                                    @include('teacher.reports.infoStudentModel', ['report' => $report])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-3">
                        {{ $assignedReports->links() }}
                    </div>
                @endif

            </div>
        </div>

    </div>

    <script>
        document.getElementById("teacherSearch").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll("#teacherTable tbody tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
            });
        });
    </script>

</x-app-layout>

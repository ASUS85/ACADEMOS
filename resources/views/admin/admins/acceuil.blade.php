<style>
    :root {
        --purple: #6f42c1;
        --blue-main: #3681B6;
    }

    .stat-hover:hover {
        transform: translateY(-3px);
    }

    .legend-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
    }

    .legend-pill {
        border-radius: 999px;
    }

    .scroll-thin::-webkit-scrollbar {
        width: 5px;
    }

    .scroll-thin::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate__animated {
        animation: fadeInRight 0.4s ease-out;
    }

    .modal-wide {
        min-height: 800px;
        min-width: 900px;
    }
</style>

@php
    $user = auth()->user();
    $adminDepartment = $user->department_id;

    $studentsCount = \App\Models\User::role('student')->where('department_id', $adminDepartment)->count();

    $teachersCount = \App\Models\User::role('teacher')->where('department_id', $adminDepartment)->count();

    $reportsQuery = \App\Models\Report::whereHas('student', function ($q) use ($adminDepartment) {
        $q->where('department_id', $adminDepartment);
    });

    $totalReports = (clone $reportsQuery)->count();
    $submittedCount = (clone $reportsQuery)->where('status', 'Soumis')->count();
    $assignedCount = (clone $reportsQuery)->where('status', 'Affecté')->count();
    $evaluatedCount = (clone $reportsQuery)->where('status', 'Évalué')->count();
    $validatedCount = (clone $reportsQuery)->where('status', 'Validé final')->count();

    $query = \App\Models\User::role('student')
        ->where('department_id', $adminDepartment)
        ->whereHas('reports')
        ->with(['filiere', 'reports.teacher']);

    if (request()->filled('search')) {
        $search = request('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('matricule', 'like', "%$search%");
        });
    }

    $students = $query->latest()->paginate(10)->withQueryString();
@endphp





<div class="bg-light py-3 px-3 px-md-4">
    <div class="mb-4">
        <h4 class="fw-bold text-uppercase text-dark">Tableau de bord administrateur</h4>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background-color:#3EA84C;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3"
                        style="width:48px;height:48px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Total Étudiants</small>
                        <h2 class="fw-bold mb-0">{{ $studentsCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background-color:#3681B6;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3"
                        style="width:48px;height:48px;">
                        <i class="fas fa-user-tie fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Total Enseignants</small>
                        <h2 class="fw-bold mb-0">{{ $teachersCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background-color:#3EA84C;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3"
                        style="width:48px;height:48px;">
                        <i class="fas fa-file-upload fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Rapports soumis</small>
                        <h2 class="fw-bold mb-0">{{ $reportsCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 rounded-3 shadow-sm text-white stat-hover" style="background-color:#3681B6;">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3"
                        style="width:48px;height:48px;">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <small class="opacity-75">Rapports validés</small>
                        <h2 class="fw-bold mb-0">{{ $validatedCount }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat + actions --}}
    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 p-3 pt-0 text-center shadow-sm bg-white rounded-3">Statistique des rapports
                    </h5>
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div style="height:220px;">
                                <canvas id="reportChart"></canvas>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ps-sm-3 mt-3 mt-sm-0">
                                <div class="d-flex align-items-center mb-2 p-2 border rounded-pill">
                                    <span class="legend-dot me-2" style="background:#f6a54c;"></span>
                                    <span>Soumis: <strong>{{ $submittedCount }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center mb-2 p-2 border rounded-pill">
                                    <span class="legend-dot me-2" style="background:#ff7d7d;"></span>
                                    <span>Affectés: <strong>{{ $assignedCount }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center mb-2 p-2 border rounded-pill">
                                    <span class="legend-dot me-2" style="background:#6da8e3;"></span>
                                    <span>Évalués: <strong>{{ $evaluatedCount }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center mb-2 p-2 border rounded-pill">
                                    <span class="legend-dot me-2" style="background:#4cc38a;"></span>
                                    <span>Validés: <strong>{{ $validatedCount }}</strong></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions rapides --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 p-3 pt-0 text-center shadow-sm bg-white rounded-3">Action rapide</h5>

                    <a href="{{ url('/admin/reports') }}"
                        class="d-flex align-items-center justify-content-between p-3 border rounded-3 text-decoration-none text-secondary mb-2 bg-white shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3 me-3"
                                style="width:38px;height:38px;">
                                <i class="fa fa-file-invoice"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Rapports soumis</div>
                                <small class="text-muted">Gérer les documents</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>

                    <a href="{{ url('admin/students') }}"
                        class="d-flex align-items-center justify-content-between p-3 border rounded-3 text-decoration-none text-secondary mb-2 bg-white shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-3 me-3"
                                style="width:38px;height:38px;">
                                <i class="fa fa-user-graduate"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Liste des étudiants</div>
                                <small class="text-muted">Gérer les effectifs</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>

                    <a href="{{ url('admin/teachers') }}"
                        class="d-flex align-items-center justify-content-between p-3 border rounded-3 text-decoration-none text-secondary mb-2 bg-white shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center bg-info-subtle text-info rounded-3 me-3"
                                style="width:38px;height:38px;">
                                <i class="fa fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Liste des enseignants</div>
                                <small class="text-muted">Gérer le corps professoral</small>
                            </div>
                        </div>
                        <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Table étudiants --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">📋 Liste des Étudiants</h5>

            <form method="GET" action="{{ url()->current() }}" class="d-flex" style="max-width:300px;">
                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-light border-0 shadow-none"
                        placeholder="Rechercher un étudiant..." value="{{ request('search') }}">
                </div>
            </form>
        </div>

        <div id="table-data">
            <div class="table-responsive scroll-thin">
                <table class="table mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" width="60">N°</th>
                            <th>Étudiant</th>
                            <th>Filière & Niveau</th>
                            <th>Matricule</th>
                            <th>Encadreur</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            <tr class="bg-white">
                                <td class="ps-4 text-muted fw-medium">
                                    {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                                </td>

                                <td>
                                    <div class="fw-bold text-dark">{{ $student->name }}</div>
                                    <small class="text-muted">{{ $student->email }}</small>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark px-3 mb-1">
                                        {{ $student->filiere->name ?? 'N/A' }}
                                    </span>
                                    <div class="small text-secondary">
                                        <i class="fas fa-layer-group me-1 small"></i>{{ $student->niveau ?? 'N/A' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-light text-primary border border-primary-subtle fw-bold">
                                        {{ $student->matricule ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    @php $lastReport = $student->reports->last(); @endphp
                                    @if ($lastReport && $lastReport->teacher)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width:28px;height:28px;font-size:0.65rem;font-weight:bold;">
                                                {{ strtoupper(substr($lastReport->teacher->name, 0, 2)) }}
                                            </div>
                                            <span
                                                class="small fw-bold text-dark">{{ $lastReport->teacher->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Pas d'encadreur</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                        <button type="button" onclick='openEditModal(@json($student->load('filiere')))'
                                            class="btn btn-sm btn-light border-end" title="Modifier">
                                            <i class="fas fa-user-edit text-primary"></i>
                                        </button>
                                        <button type="button"
                                            onclick="openReportModal({{ json_encode($student) }}, {{ json_encode($student->reports) }})"
                                            class="btn btn-sm btn-light text-success" title="Dossier & Affectation">
                                            <i class="fas fa-folder-open"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-2">
                                        <i class="fas fa-search fa-2x opacity-25"></i>
                                    </div>
                                    Aucun étudiant trouvé pour ce département.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-end align-items-center">
                <div class="pagination-custom pagination-ajax">
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal édition étudiant --}}
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-wide rounded-4 shadow-lg">
            <div class="modal-header bg-gradient text-white"
                style="background:linear-gradient(135deg,#3681B6,#6f42c1);">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-user-graduate me-2"></i>
                        Dossier Étudiant
                    </h5>
                    <small class="opacity-75">Modifier les informations académiques</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="text-center my-3">
                <img id="student_avatar" src="https://ui-avatars.com/api/?name=Student&size=80"
                    class="rounded-circle shadow mb-2" alt="avatar">
                <h6 id="student_name_display_modal" class="fw-bold mb-0"></h6>
                <small class="text-muted">Profil étudiant</small>
            </div>

            <div class="modal-body bg-light">
                <div class="card border-0 shadow-sm mb-3 rounded-3">
                    <div class="card-body">
                        <form id="editStudentForm">
                            <input type="hidden" id="edit_id">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Nom
                                        Complet</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-user text-primary"></i>
                                        </span>
                                        <input type="text" id="edit_name" class="form-control border-start-0 ps-0"
                                            placeholder="Nom de l'étudiant">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">
                                        Email Institutionnel
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </span>
                                        <input type="email" id="edit_email"
                                            class="form-control border-start-0 ps-0" placeholder="email@univ.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">
                                        Matricule / Identifiant
                                    </label>
                                    <input type="text" id="edit_matricule" class="form-control bg-white"
                                        placeholder="Ex: 22G00123">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">
                                        Filière / Niveau
                                    </label>
                                    <select id="edit_level" class="form-select bg-white">
                                        <option value="L3">Licence 3</option>
                                        <option value="M1">Master 1</option>
                                        <option value="M2">Master 2</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button type="button" onclick="confirmDelete()" class="btn btn-outline-danger">
                        <i class="fas fa-trash-alt me-2"></i>Supprimer l'étudiant
                    </button>
                    <div>
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" onclick="submitEdit()" class="btn btn-primary fw-bold px-4">
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0"></div>
        </div>
    </div>
</div>

{{-- Modal rapports --}}
<div class="modal fade" id="reportDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-wide rounded-4 shadow-lg">
            <div class="modal-header bg-gradient text-white"
                style="background:linear-gradient(135deg,#3681B6,#6f42c1);">
                <div>
                    <h5 class="modal-title">📁 Gestion des Rapports</h5>
                    <small class="opacity-75">Affectation & suivi académique</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 pt-2">
                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="card h-100 border-0 bg-light rounded-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-4 text-dark">
                                    <i class="fas fa-file-pdf me-2 text-danger"></i>Documents Soumis
                                </h6>
                                <div id="reports_list"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm rounded-4 mb-3">
                            <div class="card-header bg-white border-0 pt-3">
                                <h6 class="fw-bold m-0">
                                    <i class="fas fa-user-tie me-2 text-success"></i>
                                    Affectation Enseignant (Encadreur)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="teacher_assignment_section" class="table-responsive scroll-thin"
                                    style="max-height:200px;">
                                    <table class="table table-hover align-middle">
                                        <tbody id="teachers_table_body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 border-start border-4"
                            style="border-color:var(--purple) !important;">
                            <div class="card-header bg-white border-0 pt-3">
                                <h6 class="fw-bold m-0" style="color:var(--purple);">
                                    <i class="fas fa-gavel me-2"></i>Affectation Jury (Soutenance)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="jury_assignment_section" class="table-responsive scroll-thin"
                                    style="max-height:200px;">
                                    <table class="table table-hover align-middle">
                                        <tbody id="juries_table_body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
   document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["Soumis", "Affecté", "Évalué", "Validé"],
            datasets: [{
                data: [
                    {{ $submittedCount }},
                    {{ $assignedCount }},
                    {{ $evaluatedCount }},
                    {{ $validatedCount }}
                ],
                backgroundColor: ["#f6ad55", "#feb2b2", "#90cdf4", "#9ae6b4"],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display:false } },
            cutout: '0%'
        }
    });
});


    // Pagination AJAX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination-ajax a')) {
            e.preventDefault();
            let url = e.target.closest('a').href;
            fetchTable(url);
        }
    });

    function fetchTable(url) {
        const table = document.getElementById('table-data');
        table.style.opacity = '0.5';

        fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('table-data').innerHTML;
                table.innerHTML = newTable;
                table.style.opacity = '1';
                window.history.pushState({}, '', url);
            })
            .catch(error => console.warn('Erreur AJAX:', error));
    }

    const allTeachers = @json(\App\Models\User::role('teacher')->where('department_id', auth()->user()->department_id)->get());
    const allJuries = @json(\App\Models\User::role('jury')->get());

    function openEditModal(student) {
        document.getElementById('edit_id').value = student.id;
        document.getElementById('edit_name').value = student.name ?? '';
        document.getElementById('edit_email').value = student.email ?? '';
        document.getElementById('edit_matricule').value = student.matricule ?? '';
        document.getElementById('edit_level').value = student.niveau ?? 'L3';

        document.getElementById('student_name_display_modal').innerText = student.name;
        document.getElementById('student_avatar').src =
            `https://ui-avatars.com/api/?name=${student.name}&size=80`;

        new bootstrap.Modal(document.getElementById('editStudentModal')).show();
    }

    function openReportModal(student, reports) {
        document.getElementById('student_name_display_modal').innerText = student.name;

        // Liste des rapports
        let reportHtml = '';
        reports.forEach(report => {
            reportHtml += `
                <div class="p-3 bg-white rounded-4 shadow-sm border mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fw-bold mb-1">${report.title ?? 'Rapport'}</p>
                            <small class="text-muted">${report.status ?? ''}</small>
                        </div>
                        <a href="/storage/${report.file_path}" target="_blank"
                           class="btn btn-sm btn-danger rounded-pill px-3">
                            PDF
                        </a>
                    </div>
                </div>`;
        });
        document.getElementById('reports_list').innerHTML = reportHtml || '<p class="text-muted">Aucun rapport.</p>';

        // Enseignants
        let tBody = '';
        allTeachers.forEach(t => {
            tBody += `
                <tr>
                    <td>
                        <img src="https://ui-avatars.com/api/?name=${t.name}&size=30"
                             class="rounded-circle me-2">
                        <b>${t.name}</b>
                    </td>
                    <td class="text-end">
                        <button onclick="processAssignment(${reports[0]?.id ?? 0}, ${t.id}, 'teacher')"
                                class="btn btn-sm btn-outline-success rounded-pill px-3">
                            Affecter
                        </button>
                    </td>
                </tr>`;
        });
        document.getElementById('teachers_table_body').innerHTML = tBody;

        // Juries
        let jBody = '';
        allJuries.forEach(j => {
            jBody += `
                <tr>
                    <td>
                        <i class="fas fa-user-shield me-2" style="color:var(--purple);"></i>
                        <b>${j.name}</b>
                    </td>
                    <td class="text-end">
                        <button onclick="processAssignment(${reports[0]?.id ?? 0}, ${j.id}, 'jury')"
                                class="btn btn-sm border rounded-pill px-3"
                                style="border-color:var(--purple);color:var(--purple);">
                            Désigner
                        </button>
                    </td>
                </tr>`;
        });
        document.getElementById('juries_table_body').innerHTML = jBody;

        new bootstrap.Modal(document.getElementById('reportDetailsModal')).show();
    }

    function processAssignment(reportId, userId, type) {
        if (!reportId) return;
        let url = type === 'teacher' ? `/reports/${reportId}/assign` : `/reports/${reportId}/assign-jury`;
        let data = type === 'teacher' ? {
            teacher_id: userId
        } : {
            jury_id: userId
        };

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(() => {
            showToast(`Affectation ${type === 'teacher' ? "de l'encadreur" : 'du jury'} réussie !`);
            setTimeout(() => location.reload(), 1200);
        });
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className =
            'position-fixed top-0 end-0 m-4 p-3 bg-dark text-white rounded-3 shadow-lg animate__animated';
        toast.style.zIndex = "9999";
        toast.innerHTML = `<i class="fas fa-check-circle text-success me-2"></i> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>

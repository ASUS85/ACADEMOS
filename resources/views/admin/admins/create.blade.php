<x-app-layout>

    <div class="container py-5">

        <div class="card shadow border-0">
            <div class="card-body p-5">

                <h3 class="mb-4">👨‍🏫 Ajouter Enseignant</h3>

                <form action="{{ route('admin.teachers.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Nom</label>
                            <input name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input name="email" type="email" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Matricule</label>
                            <input name="matricule" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Grade</label>
                            <input name="grade" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Sexe</label>
                            <select name="sexe" class="form-control">
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Spécialité / Filière</label>
                            <select id="filiere" name="specialite" class="form-control">
                                <option value="">Sélectionner spécialité</option>
                            </select>
                        </div>

                    </div>

                    <button class="btn btn-primary">
                        Enregistrer
                    </button>

                </form>

            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let departmentId = "{{ auth()->user()->department_id }}";

            fetch('/api/filieres/' + departmentId)
                .then(res => res.json())
                .then(data => {

                    let select = document.getElementById('filiere');

                    data.forEach(f => {
                        select.innerHTML += `<option value="${f.id}">${f.name}</option>`;
                    });

                });

        });
    </script>

</x-app-layout>

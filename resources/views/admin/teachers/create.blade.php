<x-app-layout>
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-body p-5">
                <h3 class="mb-4">Ajouter Enseignant</h3>
                <form action="{{ route('admin.teachers.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Nom</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Email</label>
                            <input name="email" type="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Matricule</label>
                            <input name="matricule" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Grade</label>
                            <input name="grade" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Sexe</label>
                            <select name="sexe" class="form-control">
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Département</label>
                           <input type="text" class="form-control" disabled value="{{ $department->name }}">
                        <input type="hidden" name="department_id" value="{{ $department->id }}">
                        </div>
                        <div class="col-md-12">
                            <label>Spécialité / Filière</label>
                            <select name="specialite" class="form-control" required>
                                <option value="">Sélectionner spécialité</option>
                                @foreach ($specialites as $f)
                                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-4">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

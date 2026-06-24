<x-app-layout>
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Gestion des jurys</h2>
    </div>

    {{-- FILTRES --}}
    <form class="row mb-3">
        <div class="col-md-3">
            <select name="filiere" class="form-select">
                <option value="">Filière</option>
                @foreach(\App\Models\Filiere::all() as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="level" class="form-select">
                <option value="">Niveau</option>
                <option>L1</option>
                <option>L2</option>
                <option>L3</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary">Filtrer</button>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="card shadow-lg rounded-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Rapport</th>
                        <th>Filière</th>
                        <th>Jury</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($juries as $jury)
                        <tr>
                            <td>{{ $jury->report->student->name }}</td>
                            <td>{{ $jury->report->title }}</td>
                            <td>{{ $jury->report->student->filiere->name ?? '-' }}</td>

                            <td>
                                @foreach($jury->members as $member)
                                    <span class="badge bg-info">
                                        {{ $member->name }} ({{ $member->pivot->role }})
                                    </span>
                                @endforeach
                            </td>

                            <td>
                                <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editJury{{ $jury->id }}">
                                    Modifier
                                </button>

                                <form method="POST" action="{{ route('juries.destroy',$jury) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>

                        {{-- MODAL EDIT --}}
                        <div class="modal fade" id="editJury{{ $jury->id }}">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content p-4">

                                    <h5>Modifier jury</h5>

                                    <form method="POST" action="{{ route('juries.update',$jury) }}">
                                        @csrf @method('PUT')

                                        <div id="members-container">
                                            @foreach($jury->members as $member)
                                                <div class="row mb-2">
                                                    <div class="col-md-6">
                                                        <select name="members[][user_id]" class="form-select">
                                                            @foreach(\App\Models\User::role(['teacher','jury'])->get() as $u)
                                                                <option value="{{ $u->id }}"
                                                                    {{ $u->id == $member->id ? 'selected':'' }}>
                                                                    {{ $u->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <select name="members[][role]" class="form-select">
                                                            <option value="president" {{ $member->pivot->role=='president'?'selected':'' }}>Président</option>
                                                            <option value="encadreur" {{ $member->pivot->role=='encadreur'?'selected':'' }}>Encadreur</option>
                                                            <option value="membre" {{ $member->pivot->role=='membre'?'selected':'' }}>Membre</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove">X</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="submit" class="btn btn-success mt-3">
                                            Sauvegarder
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ $juries->links() }}

</div>
</x-app-layout>

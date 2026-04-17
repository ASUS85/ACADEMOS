<x-app-layout>
<h2>Éditer étudiant : {{ $student->name }}</h2>

<form action="{{ url('admin/students/' . $student->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Nom</label>
    <input type="text" name="name" value="{{ old('name', $student->name) }}">

    <label>Email</label>
    <input type="email" name="email" value="{{ old('email', $student->email) }}">

    <label>Niveau</label>
    <select name="niveau">
        <option value="">-- Choisir un niveau --</option>
        @foreach($niveaux as $niv)
        <option value="{{ $niv }}" @selected($student->niveau == $niv)>
            {{ $niv }}
        </option>
        @endforeach
    </select>


    <label>Filière</label>
    <select name="filiere_id">
        @foreach($filieres as $filiere)
        <option value="{{ $filiere->id }}" @selected($student->filiere_id == $filiere->id)>
            {{ $filiere->name }}
        </option>
        @endforeach
    </select>

    <button type="submit">Mettre à jour</button>
</form>
</x-app-layout>
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom Complet')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Matricule Étudiant (NOUVEAU) -->
        <div class="mt-4">
            <x-input-label for="matricule" :value="__('Matricule')" />
            <x-text-input id="matricule" class="block mt-1 w-full" type="text" name="matricule" :value="old('matricule')"
                required autocomplete="matricule" />
            <x-input-error :messages="$errors->get('matricule')" class="mt-2" />
            <small class="text-muted">Ex: ISPCB-INF-2026-001</small>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmer Mot de passe')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Département -->
        <div class="mt-4">
            <x-input-label for="department_id" :value="__('Département')" />

            <select id="department" name="department_id" class="block mt-1 w-full border-gray-300 rounded">
                <option value="">Choisir département</option>

                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">
                        {{ $department->name }}
                    </option>
                @endforeach

            </select>
        </div>


        <!-- Filière -->
        <div class="mt-4">
            <x-input-label for="filiere_id" :value="__('Filière')" />

            <select id="filiere" name="filiere_id" class="block mt-1 w-full border-gray-300 rounded">

                <option value="">Choisir filière</option>

            </select>
        </div>

        <!-- RÔLE CACHÉ : Étudiant UNIQUEMENT -->
        <input type="hidden" name="role" value="student">

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Déjà inscrit?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('S\'inscrire') }}
            </x-primary-button>
        </div>
    </form>


    <script>
        document.getElementById('department').addEventListener('change', function() {

            let departmentId = this.value;

            fetch('/ACADEMOS/public/filieres/' + departmentId)

                .then(response => response.json())

                .then(data => {
                    //console.log("Filière :", data);
                    let filiereSelect = document.getElementById('filiere');

                    filiereSelect.innerHTML = '<option value="">Choisir filière</option>';

                    data.forEach(filiere => {

                        filiereSelect.innerHTML +=
                            `<option value="${filiere.id}">${filiere.name}</option>`;

                    });

                });

        });
    </script>
</x-guest-layout>

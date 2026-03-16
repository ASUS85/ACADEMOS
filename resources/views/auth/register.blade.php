<x-guest-layout>
    <style>
        :root {
            --primary-blue: #2c7da0;
            --secondary-green: #40916c;
            --error-red: #e63946;
        }

        .logo-icon {
            position: relative;
            width: 32px;
            height: 38px;
        }

        .file-shape {
            width: 22px;
            height: 30px;
            border-radius: 2px;
            position: absolute;
        }

        .file-green {
            background-color: var(--secondary-green);
            z-index: 2;
            top: 0;
            left: 0;
        }

        .file-blue {
            background-color: var(--primary-blue);
            z-index: 1;
            top: 6px;
            left: 12px;
        }

        .logo-text {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .logo-text span {
            color: var(--primary-blue) !important;
        }

        .is-invalid-front {
            border: 2px solid var(--error-red) !important;
            background: #fff1f0;
            box-shadow: 0 0 0 0.15rem rgba(230, 57, 70, 0.15);
        }

        .error-message {
            color: var(--error-red);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            font-weight: 600;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .logo-bg {
            position: absolute;
            width: 420px;
            top: -280px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 0;
            pointer-events: none;
        }

        /* Bouton connexion */
        .btn-login {
            border-radius: 15px;
            background: #2c86cc;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #1f6fb2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 134, 204, 0.3);
        }

        /* Bouton inscription */
        .btn-register {
            border-radius: 15px;
            border: 1px solid #2c86cc;
            color: #2c86cc;
            background: transparent;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: #2c86cc;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 134, 204, 0.25);
        }

        #slogan {
            transition: opacity 0.8s ease-in-out;
            min-height: 2.5rem;
            /* pour éviter le saut de hauteur */
        }
    </style>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar gauche -->
            <div class="col-lg-5">
                <div class="d-none d-md-block p-0  position-relative">
                    <img src="{{ asset('images/register.jpg') }}" class="w-100  object-fit-cover" alt="Consent"
                        style="object-fit: cover;">
                    <div class="position-absolute top-50 start-50 translate-middle text-white text-center">
                        <h1 class="display-5 fw-bold mb-4">Bienvenue sur academo !</h1>
                        <p id="slogan" class="lead mb-3 opacity-90 fst-italic"></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-1"></div>

            <!-- Formulaire droite -->
            <div class="col-lg-5 col-12 bg-white d-flex align-items-start justify-content-start p-4">
                <div class="w-100 p-4 shadow-lg" style="border-radius: 15px;">
                    <!-- Logo -->
                    <div class="text-center mb-4 position-relative">
                        <!-- logo arrière plan -->
                        <img src="{{ asset('images/academo.svg') }}" class="logo-bg" alt="AcaDemo logo">
                    </div>

                    <h2 class="h3 fw-bold text-center mb-2" style="padding-top: 35px;z-index:2;">Inscrivez-vous</h2>
                    <p class="text-muted text-center mb-4 small">Cela ne prend que quelques instants !</p>

                    <!-- Formulaire -->
                    <form id="registerForm" method="POST" action="{{ route('register') }}" novalidate
                        style="z-index: 2;">
                        @csrf
                        <div class="row g-2">
                            <!-- Nom complet -->
                            <div class="col-md-12 mb-2">
                                <label for="name" class="form-label fw-semibold small text-dark mb-2">Nom
                                    complet</label>
                                <input id="name" type="text" name="name"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required autofocus autocomplete="name"
                                    placeholder="Votre nom complet">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="error-message" id="nameError">Veuillez renseigner votre nom complet.</div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-12 mb-2">
                                <label for="email" class="form-label fw-semibold small text-dark mb-2">Email</label>
                                <input id="email" type="email" name="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required autocomplete="username"
                                    placeholder="votre@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="error-message" id="emailError">Veuillez saisir un email valide.</div>
                            </div>

                            <!-- Matricule -->
                            <div class="col-md-12 mb-2">
                                <label for="matricule"
                                    class="form-label fw-semibold small text-dark mb-2">Matricule</label>
                                <input id="matricule" type="text" name="matricule"
                                    class="form-control form-control-lg @error('matricule') is-invalid @enderror"
                                    value="{{ old('matricule') }}" required autocomplete="matricule"
                                    placeholder="Entrer votre matricule">
                                @error('matricule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="error-message" id="matriculeError">Veuillez saisir votre matricule.</div>
                            </div>

                            <!-- Mot de passe + Confirmation -->
                            <div class="col-md-6 mb-2">
                                <label for="password" class="form-label fw-semibold small text-dark mb-2">Mot de
                                    passe</label>
                                <div class="position-relative">
                                    <input id="password" type="password" name="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        required autocomplete="new-password" placeholder="Entrer votre mot de passe">
                                    <span id="togglePassword"
                                        class="position-absolute top-50 end-0 translate-middle-y me-3"
                                        style="cursor:pointer;z-index:3;">
                                        <i class="fa-solid fa-eye"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="error-message" id="passwordError">Le mot de passe est requis (min. 8
                                    caractères).</div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="password_confirmation"
                                    class="form-label fw-semibold small text-dark mb-2">Confirmer mot de passe</label>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                    required autocomplete="new-password" placeholder="Confirmer le mot de passe">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="error-message" id="confirmError">Les mots de passe ne correspondent pas.
                                </div>
                            </div>

                            <!-- Département -->
                            <div class="col-md-12 mb-2">
                                <label for="department"
                                    class="form-label fw-semibold small text-dark mb-2">Département</label>
                                <select id="department" name="department_id"
                                    class="form-select form-select-lg @error('department_id') is-invalid @enderror"
                                    required>
                                    <option value="">Choisir département</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="error-message" id="departmentError">Veuillez sélectionner un département.
                                </div>
                            </div>

                            <!-- Filière -->
                            <div class="col-md-12 mb-2">
                                <label for="filiere"
                                    class="form-label fw-semibold small text-dark mb-2">Filière</label>
                                <select id="filiere" name="filiere_id"
                                    class="form-select form-select-lg @error('filiere_id') is-invalid @enderror"
                                    required>
                                    <option value="">Choisir filière</option>
                                </select>
                                @error('filiere_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="error-message" id="filiereError">Veuillez sélectionner une filière.</div>
                            </div>

                            <!-- Rôle caché -->
                            <input type="hidden" name="role" value="student">

                            <!-- Boutons -->
                            <div class="row g-3 mb-2">
                                <div class="col-5">
                                    <a href="{{ route('login') }}"
                                        class="btn btn-login w-100 py-3 fw-semibold fs-6 btn-lg">
                                        Se connecter
                                    </a>
                                </div>
                                <div class="col-2"></div>
                                <div class="col-5">
                                    <button type="submit"
                                        class="btn btn-register w-100 py-3 fw-semibold fs-6 btn-lg">
                                        S'inscrire
                                    </button>
                                </div>
                            </div>

                            <div class="text-center">
                                <small class="text-muted">
                                    Vous avez déjà un compte ?
                                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Se
                                        connecter</a>
                                </small>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const fields = {
            name: document.getElementById('name'),
            email: document.getElementById('email'),
            matricule: document.getElementById('matricule'),
            password: document.getElementById('password'),
            confirm: document.getElementById('password_confirmation'),
            department: document.getElementById('department'),
            filiere: document.getElementById('filiere')
        };
        const errors = {
            name: document.getElementById('nameError'),
            email: document.getElementById('emailError'),
            matricule: document.getElementById('matriculeError'),
            password: document.getElementById('passwordError'),
            confirm: document.getElementById('confirmError'),
            department: document.getElementById('departmentError'),
            filiere: document.getElementById('filiereError')
        };

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        togglePassword.addEventListener('click', function() {
            const type = fields.password.getAttribute('type') === 'password' ? 'text' : 'password';
            fields.password.setAttribute('type', type);
            this.innerHTML = type === 'password' ?
                '<i class="fa-solid fa-eye"></i>' :
                '<i class="fa-solid fa-eye-slash"></i>';
        });

        // Clear errors on input
        Object.keys(fields).forEach(key => {
            fields[key].addEventListener('input', function() {
                this.classList.remove('is-invalid-front');
                if (errors[key]) errors[key].classList.remove('show');
            });
        });

        // Chargement dynamique des filières
        document.getElementById('department').addEventListener('change', function() {
            const departmentId = this.value;
            const filiereSelect = document.getElementById('filiere');

            filiereSelect.innerHTML = '<option value="">Chargement...</option>';

            if (!departmentId) {
                filiereSelect.innerHTML = '<option value="">Choisir filière</option>';
                return;
            }

            fetch('/ACADEMOS/public/filieres/' + departmentId)
                .then(response => response.json())
                .then(data => {
                    filiereSelect.innerHTML = '<option value="">Choisir filière</option>';
                    data.forEach(filiere => {
                        filiereSelect.innerHTML +=
                            `<option value="${filiere.id}">${filiere.name}</option>`;
                    });
                })
                .catch(() => {
                    filiereSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        });

        // Validation frontend
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Reset erreurs
            Object.values(fields).forEach(field => {
                field.classList.remove('is-invalid-front');
            });
            Object.values(errors).forEach(error => {
                error.classList.remove('show');
            });

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!fields.name.value.trim()) {
                fields.name.classList.add('is-invalid-front');
                errors.name.classList.add('show');
                isValid = false;
            }

            if (!fields.email.value.trim() || !emailRegex.test(fields.email.value)) {
                fields.email.classList.add('is-invalid-front');
                errors.email.classList.add('show');
                isValid = false;
            }

            if (!fields.matricule.value.trim()) {
                fields.matricule.classList.add('is-invalid-front');
                errors.matricule.classList.add('show');
                isValid = false;
            }

            if (fields.password.value.length < 8) {
                fields.password.classList.add('is-invalid-front');
                errors.password.classList.add('show');
                isValid = false;
            }

            if (fields.confirm.value !== fields.password.value) {
                fields.confirm.classList.add('is-invalid-front');
                errors.confirm.classList.add('show');
                isValid = false;
            }

            if (!fields.department.value) {
                fields.department.classList.add('is-invalid-front');
                errors.department.classList.add('show');
                isValid = false;
            }

            if (!fields.filiere.value) {
                fields.filiere.classList.add('is-invalid-front');
                errors.filiere.classList.add('show');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
                fields.name.focus();
            }
        });


        // 🔹 Slogans / citations
        const slogans = [
            "Apprendre, c'est grandir chaque jour.",
            "Le savoir ouvre toutes les portes.",
            "Étudier aujourd'hui pour réussir demain.",
            "Chaque effort compte dans le parcours académique.",
            "La curiosité est le début de la connaissance.",
            "Travaille dur, rêve plus grand.", 
            "L'éducation est le passeport vers l'avenir.",
            "Persévérance et discipline mènent au succès.",
            "Cultive ton esprit, enrichis ton futur.",
            "Chaque rapport est une victoire sur toi-même."
        ];

        let sloganIndex = 0;
        const sloganElement = document.getElementById('slogan');

        // Fonction pour changer le slogan
        function changeSlogan() {
            // Effet disparition
            sloganElement.style.opacity = 0;

            setTimeout(() => {
                sloganElement.textContent = slogans[sloganIndex];
                sloganElement.style.opacity = 1;
                sloganIndex = (sloganIndex + 1) % slogans.length;
            }, 800);
        }

        changeSlogan();
        setInterval(changeSlogan, 8000);
    </script>
</x-guest-layout>

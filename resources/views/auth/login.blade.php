<x-guest-layout>
    {{-- Assure‑toi que Bootstrap est bien chargé dans ton layout principal --}}
    <style>
        :root {
            --primary-blue: #2c7da0;
            --secondary-green: #40916c;
            --error-red: #e63946;
        }

        .is-invalid-front {
            border: 2px solid #e63946 !important;
            background: #fff1f0;
        }

        .error-message {
            color: #e63946;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            font-weight: 600;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .logo-bg {
            position: absolute !important;
            z-index: 1;
        }

        .is-invalid-front {
            border: 2px solid #e63946 !important;
            background: #fff1f0;
            box-shadow: 0 0 0 0.15rem rgba(230, 57, 70, 0.15);
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
    </style>

    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

    <div class="row g-0 w-100 h-100 m-0 shadow overflow-hidden bg-white position-relative">
        <!-- Colonne GAUCHE : Image -->
        <div class="col-md-5 d-none d-md-block p-0 h-100 position-relative overflow-hidden">
            <img src="{{ asset('images/Consent-pana.png') }}" class="w-100 h-100 object-fit-cover" alt="Consent"
                style="object-fit: cover;">

            <!-- Overlay texte optionnel -->
            <div class="position-absolute top-50 start-50 translate-middle text-white text-center px-4">
                <i class="fas fa-clipboard-list fa-4x mb-3 d-block"></i>
                <h2 class="display-5 fw-bold mb-3">CONSENT</h2>
                <p class="lead mb-0">Système académique</p>
            </div>
        </div>

        <!-- Colonne DROITE : Formulaire -->
        <div class="col-md-6 col-12 p-5 d-flex flex-column justify-content-center h-100">
            <!-- Logo AcaDemo -->

            <div class="row text-center mb-5 pb-4 position-relative overflow" style="height: 120px;">
                <!-- Lien + Titre au-dessus -->
                <a href="{{ route('login') }}" class="col-md-6 position-relative d-inline-block z-index-2"
                    style="z-index: 2;">
                    <img src="{{ asset('images/academo.svg') }}" alt="Logo Academos"
                        style="width: 600px; height: auto; margin-top: -500px; margin-left: -120px;">
                    <!-- Petite version clickable -->
                </a>
                <h2 class="col-md-6 fw-semibold text-dark mb-0 position-relative z-index-2"
                    style="z-index: 2;margin-left: -450px;">Se connecter à AcaDemo</h2>
            </div>

            <!-- Formulaire -->
            <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate style="z-index: 2;">
                @csrf
                <div class="row g-2 border-bottom border-top pt-3 mb-3">
                    <!-- Email field corrigée -->
                    <div class="col-6 mb-4">
                        <label for="email" class="form-label fw-semibold small text-dark mb-2">Adresse mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus
                            class="form-control form-control-lg @error('email') is-invalid @enderror"
                            placeholder="votre@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <!-- SUPPRIME text-danger d'ici -->
                        <div class="error-message" id="emailError">Veuillez entrer une adresse mail valide.</div>
                    </div>

                    <!-- Password field corrigée -->
                    <div class="col-6 mb-4">
                        <label for="password" class="form-label fw-semibold small text-dark mb-2">Mot de passe</label>

                        <div class="position-relative">
                            <input id="password" type="password" name="password"
                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                placeholder="Entrer votre mot de passe">

                            <span id="togglePassword" class="position-absolute top-50 end-0 translate-middle-y me-3"
                                style="cursor:pointer;">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="error-message" id="passError">
                            Le mot de passe est obligatoire.
                        </div>

                        <div class="text-end mt-2">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="small text-muted text-decoration-none">
                                    <i class="fas fa-key me-1"></i>Mot de passe oublié ?
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="mb-4">
                    <div class="form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label for="remember_me" class="form-check-label small text-muted">
                            Se souvenir de moi
                        </label>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="row g-3">
                    <div class="col-5">
                        <button type="button" class="btn btn-register w-100 py-3 fw-semibold border-3 fs-6"
                            onclick="window.location.href='{{ route('register') }}'">
                            S'inscrire
                        </button>
                    </div>
                    <div class="col-2"></div>
                    <div class="col-5">
                        <button type="submit" class="btn btn-login w-100 py-3 fw-semibold fs-6">
                            Se connecter
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const emailErr = document.getElementById('emailError');
        const passErr = document.getElementById('passError');

        form.addEventListener('submit', function(e) {

            let isValid = true;

            email.classList.remove('is-invalid-front');
            password.classList.remove('is-invalid-front');
            emailErr.classList.remove('show');
            passErr.classList.remove('show');

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email.value.trim() || !emailRegex.test(email.value)) {
                email.classList.add('is-invalid-front');
                emailErr.classList.add('show');
                isValid = false;
            }

            if (!password.value.trim()) {
                password.classList.add('is-invalid-front');
                passErr.classList.add('show');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();

                if (email.classList.contains('is-invalid-front')) {
                    email.focus();
                } else {
                    password.focus();
                }
            }

        });

        email.addEventListener('input', function() {
            email.classList.remove('is-invalid-front');
            emailErr.classList.remove('show');
        });

        password.addEventListener('input', function() {
            password.classList.remove('is-invalid-front');
            passErr.classList.remove('show');
        });

        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', function() {

            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            this.innerHTML = type === 'password' ?
                '<i class="fa-solid fa-eye"></i>' :
                '<i class="fa-solid fa-eye-slash"></i>';

        });
    </script>



</x-guest-layout>

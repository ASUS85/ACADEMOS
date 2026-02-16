<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ACADEMOS - Connexion</title>

    <!-- Bootstrap 5 + FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .login-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>

<body class="min-vh-100 d-flex align-items-center login-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <!-- Logo + Titre -->
                <div class="text-center mb-5">
                    <div class="bg-white rounded-circle p-4 shadow-lg mb-4 mx-auto" style="width: 120px; height: 120px;">
                        <i class="fas fa-graduation-cap fa-4x text-primary"></i>
                    </div>
                    <h1 class="h2 fw-bold text-white mb-2">ACADEMOS</h1>
                    <p class="text-white-50 mb-0">Système de gestion des rapports de stage</p>
                </div>

                <!-- Formulaire -->
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <h3 class="h4 fw-bold text-center text-dark mb-4">
                            <i class="fas fa-sign-in-alt me-2 text-primary"></i>
                            Connexion
                        </h3>

                        {{ $slot }}
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-white-50 mb-0">
                        <small>© {{ date('Y') }} ACADEMOS - Système universitaire Cameroun</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

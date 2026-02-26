# Projet Academique de creation d'une application web de suivie des rapports de stage  "Academos"

# Description du projet 

 Le département d’informatique de l’Institut Supérieur Professionnel Catholique
de Bertoua (ISPCB) souhaite moderniser le processus d’évaluation des rapports
de fin d’études (rapports de stage, projets tutorés, mémoires, thèses).
Actuellement manuel, ce processus est long, peu traçable et sujet à des erreurs.
Le présent projet vise à concevoir un système web permettant d’automatiser,
sécuriser et suivre efficacement l’ensemble du cycle d’évaluation.
# Plus d'info dans le Cahier de charge Final 

# Approche à suivre pour lancer le projet

-- Installer git sur votre pc

-- Ouvrir le terminal git CMD

-- cd X:\chemin\du dossier de travail 

-- taper git clone https://github.com/ASUS85/ACADEMOS.git (ça creer un Dossier ACADEMOS avec tous les fichiers et code)

# Routine de travail

lorsque vous faite des modifs et voulez mettre en ligne

-- Ouvrir le terminal git CMD

-- cd X:\chemin\du dossier de travail\ACADEMOS

-- taper git pull origin master (Avant de travailler recupérer les nouveautés des autres !important)

--taper 
- git add . (ajoute tous les fichier sur git)
- git commit -m "modification apporté de manière bref"  (description)
- git push origin master


# Respecter ses etapes pour une bon suivi du travail svp

#Archtecture du Projet 
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminController.php
│   │   │   │   └── TeacherController.php
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── ConfirmablePasswordController.php
│   │   │   │   ├── EmailVerificationNotificationController.php
│   │   │   │   ├── EmailVerificationPromptController.php
│   │   │   │   ├── NewPasswordController.php
│   │   │   │   ├── PasswordController.php
│   │   │   │   ├── PasswordResetLinkController.php
│   │   │   │   ├── RegisteredUserController.php
│   │   │   │   └── VerifyEmailController.php
│   │   │   ├── Controller.php
│   │   │   ├── ProfileController.php
│   │   │   └── ReportController.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   └── LoginRequest.php
│   │   │   └── ProfileUpdateRequest.php
│   │   └── Middleware
│   ├── Models/
│   │   ├── Report.php
│   │   ├── ReportVersion.php
│   │   └── User.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── View/
│       └── Components/
│           ├── AppLayout.php
│           └── GuestLayout.php
├── bootstrap/
│   ├── cache/
│   │   └── .gitignore
│   ├── app.php
│   └── providers.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── permission.php
│   ├── queue.php
│   ├── services.php
│   ├── session.php
│   └── tinker.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_02_15_173718_create_permission_tables.php
│   │   ├── 2026_02_15_194421_create_reports_table.php
│   │   ├── 2026_02_15_212104_add_comments_to_reports_table.php
│   │   ├── 2026_02_15_214851_add_jury_evaluation_to_reports_table.php
│   │   ├── 2026_02_17_140555_create_report_versions_table.php
│   │   └── 2026_02_17_143752_add_user_fields_for_roles.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   └── RoleSeeder.php
│   └── .gitignore
├── docker/
│   ├── 8.0/
│   │   ├── Dockerfile
│   │   ├── php.ini
│   │   ├── start-container
│   │   └── supervisord.conf
│   ├── 8.1/
│   │   ├── Dockerfile
│   │   ├── php.ini
│   │   ├── start-container
│   │   └── supervisord.conf
│   ├── 8.2/
│   │   ├── Dockerfile
│   │   ├── php.ini
│   │   ├── start-container
│   │   └── supervisord.conf
│   ├── 8.3/
│   │   ├── Dockerfile
│   │   ├── php.ini
│   │   ├── start-container
│   │   └── supervisord.conf
│   ├── 8.4/
│   │   ├── Dockerfile
│   │   ├── php.ini
│   │   ├── start-container
│   │   └── supervisord.conf
│   ├── 8.5/
│   │   ├── Dockerfile
│   │   ├── php.ini
│   │   ├── start-container
│   │   └── supervisord.conf
│   ├── mariadb/
│   │   └── create-testing-database.sh
│   ├── mysql/
│   │   └── create-testing-database.sh
│   └── pgsql/
│       └── create-testing-database.sql
├── public/
│   ├── .htaccess
│   ├── favicon.ico
│   ├── index.php
│   └── robots.txt
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       ├── admin/
│       │   ├── admins/
│       │   │   └── index.blade.php
│       │   ├── reports/
│       │   │   └── index.blade.php
│       │   ├── superadmin/
│       │   │   └── users.blade.php
│       │   ├── teachers/
│       │   │   └── create.blade.php
│       │   ├── stats.blade.php
│       │   └── users.blade.php
│       ├── auth/
│       │   ├── confirm-password.blade.php
│       │   ├── forgot-password.blade.php
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   ├── reset-password.blade.php
│       │   └── verify-email.blade.php
│       ├── components/
│       │   ├── application-logo.blade.php
│       │   ├── auth-session-status.blade.php
│       │   ├── danger-button.blade.php
│       │   ├── dropdown-link.blade.php
│       │   ├── dropdown.blade.php
│       │   ├── input-error.blade.php
│       │   ├── input-label.blade.php
│       │   ├── modal.blade.php
│       │   ├── nav-link.blade.php
│       │   ├── primary-button.blade.php
│       │   ├── responsive-nav-link.blade.php
│       │   ├── secondary-button.blade.php
│       │   └── text-input.blade.php
│       ├── errors/
│       │   ├── 401.blade.php
│       │   ├── 402.blade.php
│       │   ├── 403.blade.php
│       │   ├── 404.blade.php
│       │   ├── 419.blade.php
│       │   ├── 429.blade.php
│       │   ├── 500.blade.php
│       │   ├── 503.blade.php
│       │   ├── layout.blade.php
│       │   └── minimal.blade.php
│       ├── jury/
│       │   └── reports/
│       │       └── index.blade.php
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── guest.blade.php
│       │   └── navigation.blade.php
│       ├── profile/
│       │   ├── partials/
│       │   │   ├── delete-user-form.blade.php
│       │   │   ├── update-password-form.blade.php
│       │   │   └── update-profile-information-form.blade.php
│       │   └── edit.blade.php
│       ├── student/
│       │   ├── reports/
│       │   │   └── create.blade.php
│       │   └── dashboard.blade.php
│       ├── teacher/
│       │   └── reports/
│       │       └── index.blade.php
│       ├── vendor/
│       │   ├── mail/
│       │   │   ├── html/
│       │   │   │   ├── themes/
│       │   │   │   │   └── default.css
│       │   │   │   ├── button.blade.php
│       │   │   │   ├── footer.blade.php
│       │   │   │   ├── header.blade.php
│       │   │   │   ├── layout.blade.php
│       │   │   │   ├── message.blade.php
│       │   │   │   ├── panel.blade.php
│       │   │   │   ├── subcopy.blade.php
│       │   │   │   └── table.blade.php
│       │   │   └── text/
│       │   │       ├── button.blade.php
│       │   │       ├── footer.blade.php
│       │   │       ├── header.blade.php
│       │   │       ├── layout.blade.php
│       │   │       ├── message.blade.php
│       │   │       ├── panel.blade.php
│       │   │       ├── subcopy.blade.php
│       │   │       └── table.blade.php
│       │   ├── notifications/
│       │   │   └── email.blade.php
│       │   └── pagination/
│       │       ├── bootstrap-4.blade.php
│       │       ├── bootstrap-5.blade.php
│       │       ├── default.blade.php
│       │       ├── semantic-ui.blade.php
│       │       ├── simple-bootstrap-4.blade.php
│       │       ├── simple-bootstrap-5.blade.php
│       │       ├── simple-default.blade.php
│       │       ├── simple-tailwind.blade.php
│       │       └── tailwind.blade.php
│       ├── dashboard.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── auth.php
│   ├── console.php
│   └── web.php
├── storage/
│   ├── app/
│   │   ├── private/
│   │   │   └── .gitignore
│   │   ├── public/
│   │   │   └── .gitignore
│   │   └── .gitignore
│   ├── framework/
│   │   ├── cache/
│   │   │   ├── data/
│   │   │   │   └── .gitignore
│   │   │   └── .gitignore
│   │   ├── sessions/
│   │   │   └── .gitignore
│   │   ├── testing/
│   │   │   └── .gitignore
│   │   ├── views/
│   │   │   └── .gitignore
│   │   └── .gitignore
│   └── logs/
│       └── .gitignore
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   │   ├── AuthenticationTest.php
│   │   │   ├── EmailVerificationTest.php
│   │   │   ├── PasswordConfirmationTest.php
│   │   │   ├── PasswordResetTest.php
│   │   │   ├── PasswordUpdateTest.php
│   │   │   └── RegistrationTest.php
│   │   ├── ExampleTest.php
│   │   └── ProfileTest.php
│   ├── Unit/
│   │   └── ExampleTest.php
│   └── TestCase.php
├── .editorconfig
├── .env.example
├── .gitattributes
├── .gitignore
├── artisan
├── assignRole('student')
├── composer.json
├── composer.lock
├── first()
├── fresh(['student'
├── index.php
├── package.json
├── phpunit.xml
├── postcss.config.js
├── README_.md
├── README.md
├── sail
├── tailwind.config.js
└── vite.config.js



<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

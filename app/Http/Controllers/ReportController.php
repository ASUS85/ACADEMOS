<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportJuryEvaluation;
use App\Models\ReportVersion;
use App\Notifications\ReportWorkflowNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Jury;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'superadmin'])) {
            return $user->hasRole('superadmin') ? $this->superadminReports($request) : $this->adminIndex($request);
        }

        if ($user->hasRole('teacher')) {
            return $this->teacherIndex();
        }

        if ($user->hasRole('student')) {
            return $this->studentReportsIndex();
        }

        if ($user->hasRole('jury')) {
            return $this->juryIndex();
        }

        abort(403);
    }

    private function notifyUsers(iterable $users, string $title, string $message, ?string $url = null, array $meta = []): void
    {
        collect($users)
            ->filter(fn ($user) => $user instanceof User)
            ->unique('id')
            ->each(function (User $user) use ($title, $message, $url, $meta) {
                $targetUrl = $url ?? ($user->hasRole('student') ? route('student.reports.index') : route('reports.index'));
                $user->notify(new ReportWorkflowNotification($title, $message, $targetUrl, $meta));
            });
    }

    private function adminRecipients(?int $departmentId = null)
    {
        $admins = User::role('admin')
            ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
            ->get();

        $superadmins = User::role('superadmin')->get();

        return $admins->concat($superadmins)->unique('id')->values();
    }

    /**
     * Afficher le formulaire de soumission
     */
    public function create()
    {
        return view('student.reports.create');
    }

    /**
     * Nettoyer un nom pour le fichier
     */
    private function cleanFileName(string $string): string
    {
        // 1. Convertir les accents en lettres simples
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        // 2. Remplacer tout ce qui n'est pas lettre/chiffre par un tiret
        $string = preg_replace('/[^A-Za-z0-9]/', '-', $string);

        // 3. Supprimer les tirets multiples
        $string = preg_replace('/-+/', '-', $string);

        // 4. Supprimer les tirets au début et à la fin
        return trim($string, '-');
    }

    /**
     * Stocker un nouveau rapport
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240'
        ]);

        $extension = $request->file('file')->getClientOriginalExtension();

        // Nettoyage complet
        $cleanTitle = $this->cleanFileName($request->title);
        $studentNameClean = $this->cleanFileName(Auth::user()->name);

        $report = Report::where('student_id', Auth::id())
            ->latest('created_at')
            ->first();

        if (!$report) {
            $fileName = "{$cleanTitle}-{$studentNameClean}-" . now()->format('Y-m-d') . ".{$extension}";
            $filePath = $request->file('file')->storeAs('reports', $fileName, 'public');

            $report = Report::create([
                'student_id' => Auth::id(),
                'title' => $request->title,
                'file_path' => $filePath,
                'status' => Report::STATUS_SUBMITTED,
            ]);

            ReportVersion::create([
                'report_id' => $report->id,
                'user_id' => Auth::id(),
                'version' => 'v1',
                'file_path' => $filePath,
                'action' => 'soumis',
            ]);

            $this->notifyUsers(
                $this->adminRecipients(Auth::user()->department_id),
                'Nouveau rapport soumis',
                Auth::user()->name . ' a soumis son premier rapport : ' . $report->title . '.',
                route('reports.index'),
                ['report_id' => $report->id, 'event' => 'report.submitted']
            );

            return redirect('/dashboard')->with('success', '✅ Rapport soumis !');
        }

        $versionNumber = $report->versions()->count() + 1;
        $version = 'v' . $versionNumber;
        $fileName = "rapport-{$report->id}-{$version}-" . now()->format('Ymd') . '.' . $extension;
        $filePath = $request->file('file')->storeAs('reports', $fileName, 'public');

        ReportVersion::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'version' => $version,
            'file_path' => $filePath,
            'action' => 'modifié',
        ]);

        $report->update([
            'title' => $request->title ?: $report->title,
            'file_path' => $filePath,
            'status' => Report::STATUS_SUBMITTED,
        ]);

        if ($report->teacher) {
            $this->notifyUsers(
                [$report->teacher],
                'Rapport resoumis',
                Auth::user()->name . ' a resoumis le rapport : ' . $report->title . '.',
                route('reports.index'),
                ['report_id' => $report->id, 'event' => 'report.resubmitted']
            );
        }

        return redirect('/dashboard')->with('success', '✅ Rapport soumis !');
    }

    /**
     * Dashboard étudiant
     */
    public function studentDashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        $reports = $user->reports()
            ->with(['versions.user', 'teacher', 'juryGroup.members'])
            ->latest()
            ->get();

        $latestReport = $reports->first();

        return view('student.dashboard', compact('reports', 'latestReport'));
    }

    public function studentHistoryIndex()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasRole('student')) {
            abort(403);
        }

        $historyVersions = ReportVersion::with(['report.student.filiere', 'report.teacher', 'report.latestVersion', 'report.juryGroup.members'])
            ->whereHas('report', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('student.history.index', compact('historyVersions'));
    }

    public function studentReportsIndex()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasRole('student')) {
            abort(403);
        }

        $reports = $user->reports()
            ->with(['teacher', 'latestVersion', 'versions.user', 'juryGroup.members', 'comments.user'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('student.reports.index', compact('reports'));
    }

    /**
     * Ré-soumettre un rapport
     */
    public function resubmit(Request $request, Report $report)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->id !== $report->student_id) abort(403);

        $request->validate(['file' => 'required|file|mimes:pdf,doc,docx|max:10240']);

        $versionNumber = $report->versions()->count() + 1;
        $version = 'v' . $versionNumber;

        $extension = $request->file('file')->getClientOriginalExtension();
        $fileName = "rapport-{$report->id}-{$version}-" . now()->format('Ymd') . '.' . $extension;
        $filePath = $request->file('file')->storeAs('reports', $fileName, 'public');

        \App\Models\ReportVersion::create([
            'report_id' => $report->id,
            'user_id' => $user->id,
            'version' => $version,
            'file_path' => $filePath,
            'action' => 'modifié'
        ]);

        $report->update(['status' => 'Soumis']);
        return back()->with('success', "✅ Version {$version} envoyée !");
    }

    public function previewVersion(ReportVersion $version)
    {
        $this->authorizeReportVersionFileAccess($version);

        abort_if(!$version->file_path || !Storage::disk('public')->exists($version->file_path), 404);

        return response()->file(Storage::disk('public')->path($version->file_path), [
            'Content-Disposition' => 'inline',
        ]);
    }

    public function downloadVersion(ReportVersion $version)
    {
        $this->authorizeReportVersionFileAccess($version);

        abort_if(!$version->file_path || !Storage::disk('public')->exists($version->file_path), 404);

        return response()->download(Storage::disk('public')->path($version->file_path));
    }

    public function destroyVersion(ReportVersion $version)
    {
        /** @var User $user */
        $user = Auth::user();

        $report = $version->report()->with(['versions', 'juryGroup.members'])->firstOrFail();

        if (!$user->hasRole('student') || $report->student_id !== $user->id) {
            abort(403);
        }

        $isLocked = $report->teacher_id || ($report->juryGroup?->members?->count() ?? 0) > 0 || in_array($report->status, [
            Report::STATUS_ASSIGNED,
            Report::STATUS_COMMENTED,
            Report::STATUS_VALIDATED,
            Report::STATUS_JURY_PENDING,
            Report::STATUS_FINAL,
            Report::STATUS_REJECTED,
        ], true);

        $latestVersionId = $report->latestVersion?->id;
        $isLatestVersion = (int) $latestVersionId === (int) $version->id;

        if ($isLatestVersion && $isLocked) {
            return back()->withErrors('Ce rapport est déjà pris en charge et sa dernière version ne peut pas être supprimée.');
        }

        if ($version->file_path) {
            Storage::disk('public')->delete($version->file_path);
        }

        $version->delete();

        if ($isLatestVersion) {
            $previousVersion = $report->versions()->latest('created_at')->first();

            if ($previousVersion) {
                $report->update(['file_path' => $previousVersion->file_path]);
            } else {
                $report->delete();
            }
        }

        return back()->with('success', 'Version supprimée avec succès.');
    }

    /**
     * Dashboard enseignant
     */
    public function teacherIndex()
    {
        /** @var User $user */
        $user = Auth::user();

        $reports = $user->assignedReports()
            ->with(['student', 'latestVersion', 'versions.user', 'comments.user', 'juryGroup.members'])
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('teacher.reports.index', compact('reports'));
    }

    /**
     * Tableau enseignant pour les rapports où il est membre du jury
     */
    public function teacherJuryIndex()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasRole('teacher')) {
            abort(403);
        }

        $reports = $user->juryReports()
            ->with(['student.filiere', 'teacher', 'latestVersion', 'versions.user', 'juryGroup.members', 'juryEvaluations.user'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('teacher.jury.index', compact('reports'));
    }

    /**
     * Ajouter un commentaire et valider le rapport
     */
    public function teacherComment(Request $request, Report $report)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasRole('teacher') || $report->teacher_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
            'action' => 'required|in:commenter,valider,rejeter'
        ]);

        // Enregistrer le commentaire
        \App\Models\Comment::create([
            'report_id' => $report->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        $updateData = [
            'teacher_comment' => $request->comment,
            'teacher_status' => match ($request->action) {
                'valider' => 'Validé par enseignant',
                'rejeter' => 'Rejeté par enseignant',
                default => 'Commenté',
            },
        ];

        if ($request->action === 'valider') {
            $updateData['status'] = Report::STATUS_VALIDATED;

            $report->update($updateData);

            $this->notifyUsers(
                array_filter([$report->student, ...$this->adminRecipients($report->student?->department_id)->all()]),
                'Rapport validé par l’enseignant',
                $user->name . ' a validé le rapport : ' . $report->title . '.',
                route('reports.index'),
                ['report_id' => $report->id, 'event' => 'teacher.validated']
            );

            return back()->with('success', '✅ Rapport validé avec succès');
        }

        if ($request->action === 'rejeter') {
            $updateData['status'] = Report::STATUS_REJECTED;

            $report->update($updateData);

            $this->notifyUsers(
                array_filter([$report->student, ...$this->adminRecipients($report->student?->department_id)->all()]),
                'Rapport rejeté par l’enseignant',
                $user->name . ' a rejeté le rapport : ' . $report->title . '.',
                route('reports.index'),
                ['report_id' => $report->id, 'event' => 'teacher.rejected']
            );

            return back()->with('success', '✅ Rapport rejeté avec commentaire');
        }

        $updateData['status'] = Report::STATUS_COMMENTED;

        $report->update($updateData);

        $this->notifyUsers(
            [$report->student],
            'Nouveau commentaire de l’enseignant',
            $user->name . ' a ajouté une appréciation sur votre rapport : ' . $report->title . '.',
            route('student.reports.index'),
            ['report_id' => $report->id, 'event' => 'teacher.commented']
        );

        return back()->with('success', '✅ Commentaire enregistré avec succès');
    }

    /**
     * Affecter un rapport à un enseignant
     */
    public function assign(Request $request, Report $report)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        $request->validate(['teacher_id' => 'required|exists:users,id']);

        $teacher = User::role('teacher')
            ->where('id', $request->teacher_id)
            ->when($user->hasRole('admin'), function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })
            ->firstOrFail();

        $report->update([
            'teacher_id' => $teacher->id,
            'status' => 'Affecté'
        ]);

        $this->notifyUsers(
            [$teacher],
            'Rapport affecté',
            'Le rapport "' . $report->title . '" vous a été affecté.',
            route('reports.index'),
            ['report_id' => $report->id, 'event' => 'teacher.assigned']
        );

        return response()->json([
            'success' => true,
            'message' => '✅ Rapport affecté à l\'enseignant'
        ]);
    }

    /**
     * Retirer l'enseignant affecté
     */
    public function removeTeacher(Report $report)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        $report->teacher_id = null;
        $report->status = 'Soumis';
        $report->save();

        return response()->json(['success' => true]);
    }

    /**
     * Affecter un rapport à un jury
     */
    /**
     * Affecter un jury (1-4 membres) à un rapport
     */
    public function assignJury(Request $request, Report $report)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        // 1) Vérifier que le rapport est validé
        if ($report->status !== Report::STATUS_VALIDATED) {
            return response()->json([
                'success' => false,
                'message' => "❌ Le rapport doit être validé avant d'assigner un jury"
            ], 422);
        }

        // 2) Valider les champs (on passe à president_id / rapporteur_id)
        $validated = $request->validate([
            'president_id'  => ['required', 'integer', 'different:rapporteur_id', 'exists:users,id'],
            'rapporteur_id' => ['required', 'integer', 'exists:users,id'],
            'member_ids' => ['nullable', 'array', 'max:2'],
            'member_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $memberIds = collect($validated['member_ids'] ?? [])
            ->push((int) $validated['president_id'])
            ->push((int) $validated['rapporteur_id'])
            ->when($report->teacher_id, fn($ids) => $ids->push((int) $report->teacher_id))
            ->unique()
            ->values();

        $submittedIds = collect($validated['member_ids'] ?? [])
            ->push((int) $validated['president_id'])
            ->push((int) $validated['rapporteur_id'])
            ->when($report->teacher_id, fn($ids) => $ids->push((int) $report->teacher_id));

        if ($submittedIds->count() !== $submittedIds->unique()->count()) {
            return response()->json([
                'success' => false,
                'message' => '❌ Un enseignant ne peut pas occuper deux postes dans le même jury'
            ], 422);
        }

        if ($memberIds->count() > 4) {
            return response()->json([
                'success' => false,
                'message' => '❌ Le jury ne peut pas dépasser 4 enseignants'
            ], 422);
        }

        $teachersCount = User::role('teacher')
            ->whereIn('id', $memberIds)
            ->when($user->hasRole('admin'), function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })
            ->count();

        if ($teachersCount !== $memberIds->count()) {
            return response()->json([
                'success' => false,
                'message' => '❌ Le jury doit être constitué uniquement d’enseignants du département'
            ], 422);
        }

        // 3) Créer / récupérer le jury pour ce rapport
        $jury = Jury::updateOrCreate(
            ['report_id' => $report->id],
            ['department_id' => $report->student?->department_id ?? $user->department_id]
        );

        // 4) Réinitialiser les membres
        $jury->members()->detach();

        $extraMemberIds = collect($validated['member_ids'] ?? [])->unique();

        if ($report->teacher_id) {
            $jury->members()->attach($report->teacher_id, ['role' => 'encadreur']);
        }

        $jury->members()->syncWithoutDetaching([
            $validated['president_id'] => ['role' => 'president'],
            $validated['rapporteur_id'] => ['role' => 'rapporteur'],
        ]);

        foreach ($extraMemberIds as $memberId) {
            if (!$jury->members()->where('users.id', $memberId)->exists()) {
                $jury->members()->attach($memberId, ['role' => 'membre']);
            }
        }

        $jury->load('members');

        $this->notifyUsers(
            $jury->members->all(),
            'Jury affecté',
            'Vous avez été affecté au jury du rapport : ' . $report->title . '.',
            route('reports.index'),
            ['report_id' => $report->id, 'event' => 'jury.assigned']
        );

        // 5) Mettre à jour le statut du rapport
        $report->update([
            'status'  => Report::STATUS_JURY_PENDING,
            'jury_id' => $validated['president_id'], // compatibilité : président du jury
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Jury constitué avec succès'
        ]);
    }





    /**
     * Récupérer les membres du jury pour un rapport (AJAX)
     */
    public function getJuryMembers(Report $report)
    {
        $members = $report->juryGroup?->members ?? collect();

        return response()->json($members->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'role' => $member->pivot->role,
                'is_president' => $member->pivot->role === 'president',
                'roles' => $member->getRoleNames()->toArray()
            ];
        }));
    }



    /**
     * Dashboard jury
     */
    public function juryIndex()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['teacher', 'jury'])) {
            abort(403);
        }

        $reports = $user->juryReports()
            ->with(['student.filiere', 'teacher', 'latestVersion', 'versions.user', 'juryGroup.members', 'juryEvaluations.user'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('jury.reports.index', compact('reports'));
    }

    /**
     * Evaluation par le jury
     */
    public function juryEvaluate(Request $request, Report $report)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['teacher', 'jury']) || !$report->juryGroup?->members->contains('id', $user->id)) {
            abort(403);
        }

        $request->validate([
            'jury_technical_note' => 'required|numeric|min:0|max:20',
            'jury_presentation_note' => 'required|numeric|min:0|max:20',
            'jury_content_note' => 'required|numeric|min:0|max:20',
            'jury_comment' => 'nullable|string|max:1000',
            'jury_decision' => 'required|in:Validé,Rejeté,À revoir'
        ]);

        $evaluationScore = round((
            $request->jury_technical_note +
            $request->jury_presentation_note +
            $request->jury_content_note
        ) / 3, 2);

        ReportJuryEvaluation::updateOrCreate(
            [
                'report_id' => $report->id,
                'user_id' => $user->id,
            ],
            [
                'technical_note' => $request->jury_technical_note,
                'presentation_note' => $request->jury_presentation_note,
                'content_note' => $request->jury_content_note,
                'final_score' => $evaluationScore,
                'decision' => $request->jury_decision,
                'comment' => $request->jury_comment,
            ]
        );

        $this->notifyUsers(
            $this->adminRecipients($report->student?->department_id),
            'Nouvelle note de jury',
            $user->name . ' a noté le rapport : ' . $report->title . '.',
            route('reports.index'),
            ['report_id' => $report->id, 'event' => 'jury.scored']
        );

        $summary = $this->refreshJuryFinalState($report);

        $message = $summary['completed']
            ? "✅ Toutes les notes sont enregistrées. Note finale: {$summary['final_score']}/20"
            : "✅ Votre note a été enregistrée. {$summary['submitted']}/{$summary['total']} membre(s) ont déjà noté ce rapport.";

        return redirect()->back()->with('success', $message);
    }

    private function refreshJuryFinalState(Report $report): array
    {
        $report->loadMissing(['juryGroup.members', 'juryEvaluations']);

        $totalMembers = $report->juryGroup?->members?->count() ?? 0;
        $submittedEvaluations = $report->juryEvaluations->count();

        if ($totalMembers === 0) {
            return [
                'completed' => false,
                'submitted' => 0,
                'total' => 0,
                'final_score' => null,
            ];
        }

        if ($submittedEvaluations < $totalMembers) {
            $report->update([
                'status' => Report::STATUS_JURY_PENDING,
            ]);

            return [
                'completed' => false,
                'submitted' => $submittedEvaluations,
                'total' => $totalMembers,
                'final_score' => null,
            ];
        }

        $averageTechnical = round((float) $report->juryEvaluations->avg('technical_note'), 2);
        $averagePresentation = round((float) $report->juryEvaluations->avg('presentation_note'), 2);
        $averageContent = round((float) $report->juryEvaluations->avg('content_note'), 2);
        $averageFinal = round((float) $report->juryEvaluations->avg('final_score'), 2);

        $decisionVotes = $report->juryEvaluations->countBy('decision');
        $topVotes = $decisionVotes->max();
        $dominantDecisions = $decisionVotes->filter(fn ($count) => $count === $topVotes)->keys();

        $finalDecision = $dominantDecisions->count() === 1
            ? $dominantDecisions->first()
            : match (true) {
                $averageFinal >= 14 => 'Validé',
                $averageFinal >= 10 => 'À revoir',
                default => 'Rejeté',
            };

        $wasAlreadyFinal = $report->status === Report::STATUS_FINAL && $report->jury_final_score !== null;

        $report->update([
            'jury_technical_note' => $averageTechnical,
            'jury_presentation_note' => $averagePresentation,
            'jury_content_note' => $averageContent,
            'jury_final_score' => $averageFinal,
            'jury_decision' => $finalDecision,
            'jury_comment' => "Évaluation collégiale enregistrée par {$submittedEvaluations}/{$totalMembers} membre(s) du jury.",
            'status' => $finalDecision === 'Validé' ? Report::STATUS_FINAL : $finalDecision,
        ]);

        if (!$wasAlreadyFinal) {
            $recipients = collect([$report->student, $report->teacher])
                ->merge($report->juryGroup?->members ?? collect())
                ->merge($this->adminRecipients($report->student?->department_id))
                ->filter(fn ($user) => $user instanceof User)
                ->unique('id')
                ->values();

            $this->notifyUsers(
                $recipients,
                'Note finale calculée',
                'La note finale du rapport "' . $report->title . '" a été calculée.',
                route('reports.index'),
                ['report_id' => $report->id, 'event' => 'jury.finalized', 'final_score' => $averageFinal]
            );
        }

        return [
            'completed' => true,
            'submitted' => $submittedEvaluations,
            'total' => $totalMembers,
            'final_score' => $averageFinal,
        ];
    }

    public function addJuryMember(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
        ]);

        $departmentId = $user->department_id;

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? Str::slug($validated['name']) . '+teacher-' . now()->timestamp . '@academos.local',
            'password' => bcrypt('password123'),
            'department_id' => $departmentId,
            'role_name' => 'teacher',
        ]);

        $teacher->assignRole('teacher');

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $teacher->id,
                'name' => $teacher->name,
            ],
        ]);
    }

    /**
     * Index administrateur
     */
    public function adminIndex(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Report::with(['student.filiere', 'teacher', 'latestVersion', 'comments.user', 'juryGroup.members'])
            ->whereHas('student', function ($studentQuery) use ($user) {
                $studentQuery->where('department_id', $user->department_id);
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('title', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('matricule', 'like', "%{$search}%");
                    })
                    ->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                        $teacherQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtre filière
        if ($request->filled('filiere')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere);
            });
        }

        // Filtre niveau
        if ($request->filled('level')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('niveau', $request->level);
            });
        }

        $reports = $query->latest()->withCount('comments')->paginate(10)->withQueryString();

        // récupérer filières du département
        $filieres = \App\Models\Filiere::where('department_id', $user->department_id)->get();

        //récuperer les teachers
        $availableTeachers = User::role('teacher')->where('department_id', $user->department_id)->get();

        return view('admin.reports.index', compact('reports', 'filieres', 'availableTeachers'));
    }

    public function superadminReports(Request $request)
    {
        $query = Report::with(['student.filiere', 'teacher', 'latestVersion', 'comments.user', 'juryGroup.members'])
            ->latest()
            ->withCount('comments');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('title', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('matricule', 'like', "%{$search}%");
                    })
                    ->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                        $teacherQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $reports = $query->paginate(10)->withQueryString();
        $availableTeachers = User::role('teacher')->get();

        return view('admin.reports.index', compact('reports', 'availableTeachers'));
    }

    public function destroy(Report $report)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        foreach ($report->versions as $version) {
            if ($version->file_path) {
                Storage::disk('public')->delete($version->file_path);
            }
        }

        if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }

        $report->delete();

        return back()->with('success', 'Rapport supprimé avec succès.');
    }

    public function preview(Report $report)
    {
        $this->authorizeReportFileAccess($report);

        $filePath = $this->resolveReportFilePath($report);

        abort_if(!$filePath || !Storage::disk('public')->exists($filePath), 404);

        return response()->file(Storage::disk('public')->path($filePath), [
            'Content-Disposition' => 'inline',
        ]);
    }

    public function download(Report $report)
    {
        $this->authorizeReportFileAccess($report);

        $filePath = $this->resolveReportFilePath($report);

        abort_if(!$filePath || !Storage::disk('public')->exists($filePath), 404);

        return response()->download(Storage::disk('public')->path($filePath));
    }

    private function resolveReportFilePath(Report $report): ?string
    {
        return $report->latestVersion?->file_path ?? $report->file_path;
    }

    private function authorizeReportFileAccess(Report $report): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'superadmin'])) {
            return;
        }

        if ($user->hasRole('student') && $report->student_id === $user->id) {
            return;
        }

        if ($user->hasRole('teacher') && $report->teacher_id === $user->id) {
            return;
        }

        if ($report->juryGroup?->members?->contains('id', $user->id)) {
            return;
        }

        abort(403);
    }

    private function authorizeReportVersionFileAccess(ReportVersion $version): void
    {
        $this->authorizeReportFileAccess($version->report()->with('juryGroup.members')->firstOrFail());
    }

    /**
     * Statistiques admin
     */
    public function adminStats()
    {
        $stats = [
            'total_reports' => Report::count(),
            'total_users' => \App\Models\User::count(),
            'students' => \App\Models\User::role('student')->count(),
            'teachers' => \App\Models\User::role('teacher')->count(),
            'juries' => \App\Models\User::role('jury')->count(),
        ];
        return view('admin.stats', compact('stats'));
    }

    public function superadminStats()
    {
        return $this->adminStats();
    }
}
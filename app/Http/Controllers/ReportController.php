<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ReportController extends Controller
{
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

        $fileName = "{$cleanTitle}-{$studentNameClean}-" . now()->format('Y-m-d') . ".{$extension}";

        $filePath = $request->file('file')->storeAs('reports', $fileName, 'public');

        Report::create([
            'student_id' => Auth::id(),
            'title' => $request->title,
            'file_path' => $filePath,
            'status' => 'Soumis'
        ]);

        return redirect('/dashboard')->with('success', '✅ Rapport soumis !');
    }

    /**
     * Dashboard étudiant
     */
    public function studentDashboard()
    {
        $reports = auth()->user()->reports()
            ->with(['versions.user', 'teacher', 'jury'])
            ->latest()
            ->get();
        $latestReport = $reports->first();
        return view('student.dashboard', compact('reports', 'latestReport'));
    }

    /**
     * Ré-soumettre un rapport
     */
    public function resubmit(Request $request, Report $report)
    {
        if (auth()->id() !== $report->student_id) abort(403);

        $request->validate(['file' => 'required|file|mimes:pdf|max:10240']);

        $versionNumber = $report->versions()->count() + 1;
        $version = 'v' . $versionNumber;

        $fileName = "rapport-{$report->id}-{$version}-" . now()->format('Ymd') . '.pdf';
        $filePath = $request->file('file')->storeAs('reports', $fileName, 'public');

        \App\Models\ReportVersion::create([
            'report_id' => $report->id,
            'user_id' => auth()->id(),
            'version' => $version,
            'file_path' => $filePath,
            'action' => 'modifié'
        ]);

        $report->update(['status' => 'Soumis']);
        return back()->with('success', "✅ Version {$version} envoyée !");
    }

    /**
     * Dashboard enseignant
     */
    public function teacherIndex()
    {
        $reports = auth()->user()->assignedReports()->with('student')->latest()->get();
        return view('teacher.reports.index', compact('reports'));
    }

    /**
     * Ajouter un commentaire et valider le rapport
     */
    public function teacherComment(Request $request, Report $report)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'action' => 'required|in:commenter,valider'
        ]);

        // Enregistrer le commentaire
        \App\Models\Comment::create([
            'report_id' => $report->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        // Gestion action
        if ($request->action === 'valider') {
            $report->update([
                'status' => Report::STATUS_VALIDATED // ✅ mieux avec constante
            ]);

            return back()->with('success', '✅ Rapport validé avec succès');
        }

        $report->update([
            'status' => 'commenté'
        ]);

        return back()->with('success', '✅ Demande de correction envoyée');
    }

    /**
     * Affecter un rapport à un enseignant
     */
    public function assign(Request $request, Report $report)
    {
        $request->validate(['teacher_id' => 'required|exists:users,id']);

        $report->update([
            'teacher_id' => $request->teacher_id,
            'status' => 'Affecté'
        ]);

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
        $request->validate([
            'jury_ids' => ['required', 'array', 'min:1', 'max:4'],
            'jury_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $juryIds = array_filter($request->jury_ids);

        // Vérifier encadreur
        if ($report->teacher_id && in_array($report->teacher_id, $juryIds)) {
            return response()->json([
                'success' => false,
                'message' => "❌ L'encadreur ne peut pas faire partie du jury."
            ], 422);
        }

        // Vérifier rôles
        $members = User::whereIn('id', $juryIds)
            ->where(function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'teacher');
                })->orWhereHas('roles', function ($q) {
                    $q->where('name', 'jury');
                });
            })
            ->get();

        if ($members->count() !== count($juryIds)) {
            return response()->json([
                'success' => false,
                'message' => "❌ Certains membres n'ont pas le rôle 'teacher' ou 'jury'."
            ], 422);
        }

        // ✅ ÇA MARCHE MAINTENANT !
        $report->juryMembers()->detach();

        $presidentId = $juryIds[0];
        foreach ($juryIds as $index => $userId) {
            $report->juryMembers()->attach((int)$userId, [
                'is_president' => ($index === 0)
            ]);
        }

        $report->update([
            'jury_id' => $presidentId,
            'status' => 'En attente jury'
        ]);

        return response()->json([
            'success' => true,
            'message' => "✅ Jury affecté ! (" . count($juryIds) . " membre(s))"
        ]);
    }





    /**
     * Récupérer les membres du jury pour un rapport (AJAX)
     */
    public function getJuryMembers(Report $report)
    {
        $jury = $report->juryMembers()->get();
        return response()->json($jury->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'is_president' => $member->pivot->is_president,
                'roles' => $member->getRoleNames()->toArray()
            ];
        }));
    }



    /**
     * Dashboard jury
     */
    public function juryIndex()
    {
        $reports = auth()->user()->juryReports()->with(['student', 'teacher'])->latest()->get();
        return view('jury.reports.index', compact('reports'));
    }

    /**
     * Evaluation par le jury
     */
    public function juryEvaluate(Request $request, Report $report)
    {
        $request->validate([
            'jury_technical_note' => 'required|numeric|min:0|max:20',
            'jury_presentation_note' => 'required|numeric|min:0|max:20',
            'jury_content_note' => 'required|numeric|min:0|max:20',
            'jury_comment' => 'nullable|string|max:1000',
            'jury_decision' => 'required|in:Validé,Rejeté,À revoir'
        ]);

        $moyenne = round(($request->jury_technical_note + $request->jury_presentation_note + $request->jury_content_note) / 3, 2);

        $appreciation = match (true) {
            $moyenne >= 18 => 'Très Honorable',
            $moyenne >= 16 => 'Très Bien',
            $moyenne >= 14 => 'Bien',
            $moyenne >= 12 => 'Assez Bien',
            $moyenne >= 10 => 'Passable',
            default => 'Échec'
        };

        $report->update([
            'jury_technical_note' => $request->jury_technical_note,
            'jury_presentation_note' => $request->jury_presentation_note,
            'jury_content_note' => $request->jury_content_note,
            'jury_final_score' => $moyenne,
            'jury_decision' => $request->jury_decision,
            'jury_comment' => $request->jury_comment,
            'status' => $request->jury_decision === 'Validé' ? 'Validé final' : $request->jury_decision
        ]);

        return redirect()->back()->with('success', "✅ Évaluation terminée ! Moyenne: {$moyenne}/20 ({$appreciation})");
    }

    /**
     * Index administrateur
     */
    public function adminIndex(Request $request)
    {
        $user = auth()->user();

        $query = Report::with(['student.filiere', 'teacher', 'comments.user', 'juryGroup.members', 'juryMembers']);

        // Filtre département admin
        $query->whereHas('student', function ($q) use ($user) {
            $q->where('department_id', $user->department_id);
        });

        // Filtre filière
        if ($request->filled('filiere')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere);
            });
        }

        // Filtre niveau
        if ($request->filled('level')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('level', $request->level);
            });
        }

        $reports = $query->latest()->withCount('comments')->paginate(10)->withQueryString();

        // récupérer filières du département
        $filieres = \App\Models\Filiere::where('department_id', $user->department_id)->get();

        return view('admin.reports.index', compact('reports', 'filieres'));
    }

    public function superadminReports()
    {
        $reports = Report::with('student')->latest()->get();
        return view('admin.reports.index', compact('reports'));
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

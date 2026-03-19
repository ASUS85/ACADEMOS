<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $request->validate(['comment' => 'required|string|max:1000']);

        $report->update([
            'teacher_comment' => $request->comment,
            'teacher_status' => 'Validé par enseignant'
        ]);

        return redirect()->back()->with('success', '✅ Commentaire ajouté et rapport validé !');
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
    public function assignJury(Request $request, Report $report)
    {
        $request->validate(['jury_id' => 'required|exists:users,id']);

        $report->update([
            'jury_id' => $request->jury_id,
            'status' => 'En attente jury'
        ]);

        return redirect()->back()->with('success', '✅ Rapport affecté au jury !');
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
    public function adminIndex()
    {
        $reports = Report::with('student')->latest()->get();
        return view('admin.reports.index', compact('reports'));
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

<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function create()
    {
        return view('student.reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240'
        ]);

        $studentName = Auth::user()->name;
        $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '-', $request->title); // Nettoyer
        $fileName = "{$cleanTitle}-{$studentName}-" . now()->format('Y-m-d') . ".pdf";

        $filePath = $request->file('file')->storeAs('reports', $fileName, 'public');

        Report::create([
            'student_id' => Auth::id(),
            'title' => $request->title,
            'file_path' => $filePath,
            'status' => 'Soumis'
        ]);

        return redirect('/dashboard')->with('success', '✅ Rapport soumis !');
    }


    public function adminIndex()
    {
        $reports = Report::with('student')->latest()->get();
        return view('admin.reports.index', compact('reports'));
    }

    public function assign(Request $request, Report $report)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id'
        ]);

        $report->update([
            'teacher_id' => $request->teacher_id,
            'status' => 'Affecté'
        ]);

        return redirect()->back()->with('success', '✅ Rapport affecté à l\'enseignant !');
    }

    public function teacherIndex()
    {
        $reports = auth()->user()->assignedReports()->with('student')->latest()->get();
        return view('teacher.reports.index', compact('reports'));
    }

    public function teacherComment(Request $request, Report $report)
    {
        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        $report->update([
            'teacher_comment' => $request->comment,
            'teacher_status' => 'Validé par enseignant'
        ]);

        return redirect()->back()->with('success', '✅ Commentaire ajouté et rapport validé !');
    }

    public function assignJury(Request $request, Report $report)
    {
        $request->validate([
            'jury_id' => 'required|exists:users,id'
        ]);

        $report->update([
            'jury_id' => $request->jury_id,
            'status' => 'En attente jury'
        ]);

        return redirect()->back()->with('success', '✅ Rapport affecté au jury !');
    }


    public function juryIndex()
    {
        $reports = auth()->user()->juryReports()->with(['student', 'teacher'])->latest()->get();
        return view('jury.reports.index', compact('reports'));
    }

    public function adminUsers()
    {
        $users = \App\Models\User::with('roles')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function adminStats()
    {
        $stats = [
            'total_reports' => \App\Models\Report::count(),
            'total_users' => \App\Models\User::count(),
            'students' => \App\Models\User::role('student')->count(),
            'teachers' => \App\Models\User::role('teacher')->count(),
            'juries' => \App\Models\User::role('jury')->count(),
        ];
        return view('admin.stats', compact('stats'));
    }

    public function juryEvaluate(Request $request, Report $report)
    {
        $request->validate([
            'jury_note_forme' => 'required|numeric|min:0|max:20',
            'jury_note_fond' => 'required|numeric|min:0|max:20',
            'jury_note_langage' => 'required|numeric|min:0|max:20',
            'jury_commentaire' => 'nullable|string|max:1000',
            'jury_decision' => 'required|in:Validé,Rejeté,À revoir'
        ]);

        // Calcul moyenne automatique (grille Cameroun)
        $moyenne = round(($request->jury_note_forme + $request->jury_note_fond + $request->jury_note_langage) / 3, 2);

        // Déterminer appréciation automatique
        $appreciation = match (true) {
            $moyenne >= 18 => 'Très Honorable',
            $moyenne >= 16 => 'Très Bien',
            $moyenne >= 14 => 'Bien',
            $moyenne >= 12 => 'Assez Bien',
            $moyenne >= 10 => 'Passable',
            default => 'Échec'
        };

        $report->update([
            'jury_note_forme' => $request->jury_note_forme,
            'jury_note_fond' => $request->jury_note_fond,
            'jury_note_langage' => $request->jury_note_langage,
            'jury_moyenne_finale' => $moyenne,
            'jury_appreciation' => $appreciation,
            'jury_commentaire' => $request->jury_commentaire,
            'jury_decision' => $request->jury_decision,
            'status' => $request->jury_decision === 'Validé' ? 'Validé final' : $request->jury_decision
        ]);

        return redirect()->back()->with('success', "✅ Évaluation terminée ! Moyenne: {$moyenne}/20 ({$appreciation})");
    }
}

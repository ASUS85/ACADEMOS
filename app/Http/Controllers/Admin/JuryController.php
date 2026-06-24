<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jury;
use App\Models\Report;
use App\Models\User;

class JuryController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Jury::with(['report.student.filiere', 'members'])
            ->where('department_id', $user->department_id);

        if ($request->filiere) {
            $query->whereHas('report.student', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere);
            });
        }

        if ($request->level) {
            $query->whereHas('report.student', function ($q) use ($request) {
                $q->where('niveau', $request->level);
            });
        }

        $juries = $query->latest()->paginate(10);

        return view('admin.admins.listJuries', compact('juries'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'members' => 'required|array|min:1|max:4',
            'members.*.user_id' => 'required|exists:users,id',
            'members.*.role' => 'required|in:president,encadreur,rapporteur,membre',
        ]);

        $report = Report::findOrFail($request->report_id);

        // Vérifier que le rapport est validé
        if ($report->status !== 'Validé') {  // ou Report::STATUS_VALIDATED
            return back()->withErrors('Le rapport doit être validé.');
        }

        // Créer ou récupérer le jury
        $jury = Jury::updateOrCreate(
            ['report_id' => $report->id],
            ['department_id' => auth()->user()->department_id]
        );

        $members = collect($request->members);
        $memberIds = $members->pluck('user_id')->map(fn($id) => (int) $id);

        if ($memberIds->duplicates()->isNotEmpty()) {
            return back()->withErrors('Un enseignant ne peut pas occuper deux postes dans le même jury.');
        }

        if ($members->where('role', 'president')->count() !== 1) {
            return back()->withErrors('Le jury doit avoir exactement un président.');
        }

        if ($members->where('role', 'rapporteur')->count() !== 1) {
            return back()->withErrors('Le jury doit avoir exactement un rapporteur.');
        }

        $validTeachers = User::role('teacher')
            ->whereIn('id', $memberIds)
            ->where('department_id', auth()->user()->department_id)
            ->count();

        if ($validTeachers !== $memberIds->count()) {
            return back()->withErrors('Tous les membres du jury doivent être des enseignants du département.');
        }

        $jury->members()->detach();

        foreach ($members as $member) {
            if ($member['role'] === 'encadreur' && $report->teacher_id != $member['user_id']) {
                return back()->withErrors('Encadreur invalide');
            }

            $jury->members()->attach($member['user_id'], ['role' => $member['role']]);
        }

        // Mettre à jour le statut du rapport
        $report->update(['status' => 'En attente jury']);

        return back()->with('success', 'Jury créé avec succès !');
    }

    public function update(Request $request, Jury $jury)
    {
        $request->validate([
            'members' => 'required|array|min:1|max:4',
            'members.*.user_id' => 'required|exists:users,id',
            'members.*.role' => 'required|in:president,encadreur,rapporteur,membre',
        ]);

        $report = $jury->report;

        $members = collect($request->members);
        $memberIds = $members->pluck('user_id')->map(fn($id) => (int) $id);

        if ($memberIds->duplicates()->isNotEmpty()) {
            return back()->withErrors('Un enseignant ne peut pas occuper deux postes dans le même jury.');
        }

        if ($members->where('role', 'president')->count() !== 1) {
            return back()->withErrors('Le jury doit avoir exactement un président.');
        }

        if ($members->where('role', 'rapporteur')->count() !== 1) {
            return back()->withErrors('Le jury doit avoir exactement un rapporteur.');
        }

        $validTeachers = User::role('teacher')
            ->whereIn('id', $memberIds)
            ->where('department_id', auth()->user()->department_id)
            ->count();

        if ($validTeachers !== $memberIds->count()) {
            return back()->withErrors('Tous les membres du jury doivent être des enseignants du département.');
        }

        foreach ($members as $member) {
            if ($member['role'] === 'encadreur' && $report->teacher_id != $member['user_id']) {
                return back()->withErrors('Encadreur invalide');
            }
        }

        // Vider et recréer les membres
        $jury->members()->detach();
        foreach ($members as $member) {
            $jury->members()->attach($member['user_id'], ['role' => $member['role']]);
        }

        return back()->with('success', 'Jury mis à jour avec succès !');
    }

    public function destroy(Jury $jury)
    {
        $report = $jury->report;
        $jury->delete();

        // Remettre le rapport en "Validé" après suppression du jury
        $report->update(['status' => 'Validé']);

        return back()->with('success', 'Jury supprimé avec succès !');
    }

    public function addJuryMember(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'role'  => 'nullable|in:president,rapporteur,membre',
            'email' => 'nullable|email|unique:users,email', // si tu ajoutes l’email
        ]);

        $admin = auth()->user();

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email ?? \Str::slug($request->name) . '+jury@example.com',
            'password'      => bcrypt('password123'), // à voir pour la gestion réelle
            'department_id' => $admin->department_id,
            'role_name'     => 'teacher',
        ]);

        $user->assignRole('teacher');

        return response()->json([
            'success' => true,
            'user'    => [
                'id'   => $user->id,
                'name' => $user->name,
            ],
        ]);
    }
}

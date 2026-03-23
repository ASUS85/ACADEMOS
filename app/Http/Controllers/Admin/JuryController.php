<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jury;
use App\Models\Report;

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
                $q->where('level', $request->level);
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
        ]);

        $jury = Jury::create([
            'report_id' => $request->report_id,
            'department_id' => auth()->user()->department_id
        ]);

        foreach ($request->members as $member) {
            if ($member['role'] === 'encadreur') {
                $report = Report::find($request->report_id);

                if ($report && $report->teacher_id != $member['user_id']) {
                    return back()->withErrors('Encadreur invalide');
                }
            }
        }

        return back()->with('success', 'Jury créé');
    }

    public function update(Request $request, Jury $jury)
    {
        $request->validate([
            'members' => 'required|array|min:1|max:4',
        ]);
        $jury->members()->detach();

        foreach ($request->members as $member) {
            if ($member['role'] === 'encadreur') {
                $report = Report::find($request->report_id);

                if ($report && $report->teacher_id != $member['user_id']) {
                    return back()->withErrors('Encadreur invalide');
                }
            }
        }

        return back()->with('success', 'Jury mis à jour');
    }

    public function destroy(Jury $jury)
    {
        $jury->delete();
        return back()->with('success', 'Jury supprimé');
    }
}

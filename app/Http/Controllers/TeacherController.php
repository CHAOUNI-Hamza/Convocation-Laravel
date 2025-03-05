<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Session;
use App\Models\Exam;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;

class TeacherController extends Controller
{
    public function getTeachersDisponibles(Request $request)
{
    $date = $request->input('date');
    $creneau = $request->input('creneau'); // 9h00, 11h00, 14h00, 16h30

    // Liste des créneaux en ordre chronologique
    $creneaux = ['09:00', '11:00', '14:00', '16:30'];
    $indexCreneau = array_search($creneau, $creneaux);

    // Récupérer tous les professeurs avec le nombre d'examens surveillés
    $professeurs = Teacher::withCount(['exams as total_exams'])
        ->having('total_exams', '<', 8)
        ->get();

    // Récupérer les professeurs qui ont surveillé les examens précédents du même jour
    $professeurs_ayant_surveille_avant = Exam::where('date', $date)
        ->whereIn('creneau_horaire', array_slice($creneaux, 0, $indexCreneau))
        ->join('exam_teacher', 'exams.id', '=', 'exam_teacher.exam_id')
        ->pluck('exam_teacher.teacher_id');

    if ($creneau === '09:00') {
        // Exclure ceux qui ont déjà surveillé à 09:00 le même jour
        $professeurs_ayant_surveille_09h = Exam::where('date', $date)
            ->where('creneau_horaire', '09:00')
            ->join('exam_teacher', 'exams.id', '=', 'exam_teacher.exam_id')
            ->pluck('exam_teacher.teacher_id');

        $professeurs = $professeurs->reject(function ($prof) use ($professeurs_ayant_surveille_09h) {
            return $professeurs_ayant_surveille_09h->contains($prof->id);
        });
    }

    if ($creneau === '11:00') {
        // Prioriser ceux qui ont surveillé à 9h00
        $professeurs = $professeurs->sortByDesc(function ($prof) use ($professeurs_ayant_surveille_avant) {
            return $professeurs_ayant_surveille_avant->contains($prof->id);
        });
    } elseif ($creneau === '14:00') {
        // Exclure ceux qui ont surveillé 9h00 et 11h00
        $professeurs = $professeurs->reject(function ($prof) use ($professeurs_ayant_surveille_avant) {
            return $professeurs_ayant_surveille_avant->contains($prof->id);
        });
    } elseif ($creneau === '16:30') {
        // Récupérer les professeurs ayant surveillé à 09:00 et 11:00
        $professeurs_ayant_surveille_09h_11h = Exam::where('date', $date)
            ->whereIn('creneau_horaire', ['09:00', '11:00'])
            ->join('exam_teacher', 'exams.id', '=', 'exam_teacher.exam_id')
            ->pluck('exam_teacher.teacher_id');

        // Exclure les professeurs ayant surveillé à 09:00 et 11:00
        $professeurs = $professeurs->reject(function ($prof) use ($professeurs_ayant_surveille_09h_11h) {
            return $professeurs_ayant_surveille_09h_11h->contains($prof->id);
        });

        // Récupérer les professeurs ayant surveillé à 14:00
        $professeurs_ayant_surveille_14h = Exam::where('date', $date)
            ->where('creneau_horaire', '14:00')
            ->join('exam_teacher', 'exams.id', '=', 'exam_teacher.exam_id')
            ->pluck('exam_teacher.teacher_id');

        // Prioriser ceux qui ont surveillé à 14h00
        $professeurs = $professeurs->sortByDesc(function ($prof) use ($professeurs_ayant_surveille_14h) {
            return $professeurs_ayant_surveille_14h->contains($prof->id);
        });
    }

    return response()->json($professeurs);
}



    public function all()
    {
        $teachers = Teacher::all();
        return TeacherResource::collection($teachers);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers = Teacher::paginate(15);
        return TeacherResource::collection($teachers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTeacherRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTeacherRequest $request)
    {
        $teacher = new Teacher();

        $teacher->sum_number = $request->input('sum_number');
        $teacher->name = $request->input('name');
        $teacher->first_name = $request->input('first_name');
        $teacher->name_ar = $request->input('name_ar');
        $teacher->first_name_ar = $request->input('first_name_ar');
        $teacher->email = $request->input('email');

        $teacher->save();
        return new TeacherResource($teacher);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(Teacher $teacher)
    {
        return new TeacherResource($teacher);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTeacherRequest  $request
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $teacher->sum_number = $request->input('sum_number');
        $teacher->name = $request->input('name');
        $teacher->first_name = $request->input('first_name');
        $teacher->name_ar = $request->input('name_ar');
        $teacher->first_name_ar = $request->input('first_name_ar');
        $teacher->email = $request->input('email');

        $teacher->save();
        return new TeacherResource($teacher);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return response()->noContent();
    }
}

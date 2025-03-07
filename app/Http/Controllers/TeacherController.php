<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Session;
use App\Models\Exam;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function getTeachersDisponibles(Request $request)
    {
        // Récupérer la date et le créneau horaire depuis la requête
        $date = $request->input('date');  // Format : 'Y-m-d'
        $creneauHoraire = $request->input('creneau_horaire');  // Format : 'H:i'

        // Logique pour le créneau à 09:00
        if ($creneauHoraire == '09:00') {
            $teachersWithoutCreneauAt09 = Teacher::whereDoesntHave('exams', function ($query) use ($date, $creneauHoraire) {
                $query->where('date', $date)
                    ->where('creneau_horaire', '09:00');
            })->get();

            $teachersWithOtherCreneaux = Teacher::whereHas('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->whereIn('creneau_horaire', ['11:00', '14:00', '16:30']);
            })->get();

            $teachers = $teachersWithOtherCreneaux->merge($teachersWithoutCreneauAt09);
        } 
        // Logique pour le créneau à 11:00
        elseif ($creneauHoraire == '11:00') {
            $teachersWithoutCreneauAt11 = Teacher::whereDoesntHave('exams', function ($query) use ($date, $creneauHoraire) {
                $query->where('date', $date)
                    ->where('creneau_horaire', '11:00');
            })->get();

            $teachersWithOtherCreneaux = Teacher::whereHas('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->whereIn('creneau_horaire', ['09:00', '14:00', '16:30']);
            })->get();

            $teachers = $teachersWithOtherCreneaux->merge($teachersWithoutCreneauAt11);
        } 
        // Logique pour le créneau à 14:00
        elseif ($creneauHoraire == '14:00') {
            $teachersWithoutCreneauAt14 = Teacher::whereDoesntHave('exams', function ($query) use ($date, $creneauHoraire) {
                $query->where('date', $date)
                    ->where('creneau_horaire', '14:00');
            })->get();

            $teachersWithOtherCreneaux = Teacher::whereHas('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->whereIn('creneau_horaire', ['09:00', '11:00', '16:30']);
            })->get();

            $teachers = $teachersWithOtherCreneaux->merge($teachersWithoutCreneauAt14);
        } 
        // Logique pour le créneau à 16:30
        elseif ($creneauHoraire == '16:30') {
            $teachersWithoutCreneauAt16_30 = Teacher::whereDoesntHave('exams', function ($query) use ($date, $creneauHoraire) {
                $query->where('date', $date)
                    ->where('creneau_horaire', '16:30');
            })->get();

            $teachersWithOtherCreneaux = Teacher::whereHas('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->whereIn('creneau_horaire', ['09:00', '11:00', '14:00']);
            })->get();

            $teachers = $teachersWithOtherCreneaux->merge($teachersWithoutCreneauAt16_30);
        } 
        // Si le créneau n'est pas 09:00, 11:00, 14:00 ou 16:30
        else {
            $teachers = Teacher::all();
        }

        // Trier les enseignants pour mettre en bas ceux qui sont déjà occupés à une autre date
        $teachers = $teachers->sortBy(function($teacher) use ($date) {
            return $teacher->exams()->where('date', '!=', $date)->exists();
        });

        return $teachers;
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

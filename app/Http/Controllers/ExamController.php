<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Teacher;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;

class ExamController extends Controller
{

    public function all()
    {
        $exams = Exam::all();
        return ExamResource::collection($exams);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $exams = Exam::paginate(15);
        $exams->getCollection()->transform(function ($exam) {
            $teacherIds = !empty($exam->teacher_ids) ? (is_array($exam->teacher_ids) ? $exam->teacher_ids : json_decode($exam->teacher_ids, true)) : [];
            $teachers = !empty($teacherIds) && is_array($teacherIds) ? Teacher::whereIn('id', $teacherIds)->get() : [];
            $exam->teachers = $teachers;
            return $exam;
        });

        return ExamResource::collection($exams);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreExamRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExamRequest $request)
    {
        $exam = new Exam();

        $exam->date = \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d');
        $exam->creneau_horaire = \Carbon\Carbon::parse($request->input('creneau_horaire'))->format('H:i');
        $exam->module = $request->input('module');
        $exam->salle = $request->input('salle');
        $exam->filiere = $request->input('filiere');
        $exam->semestre = $request->input('semestre');
        $exam->groupe = $request->input('groupe');
        $exam->lib_mod = $request->input('lib_mod');

        /*if ($request->has('teacher_ids')) {
            $exam->teacher_ids = $request->input('teacher_ids');
        }*/
        if ($request->has('teacher_ids')) {
            $exam->teacher_ids = is_array($request->input('teacher_ids')) 
                ? $request->input('teacher_ids') 
                : json_decode($request->input('teacher_ids'), true);
        }
        

        $exam->save();

        return new ExamResource($exam);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function show(Exam $exam)
    {
        return new ExamResource($exam);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExamRequest  $request
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $exam->date = \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d');
        $exam->creneau_horaire = \Carbon\Carbon::parse($request->input('creneau_horaire'))->format('H:i');
        $exam->module = $request->input('module');
        $exam->salle = $request->input('salle');
        $exam->filiere = $request->input('filiere');
        $exam->semestre = $request->input('semestre');
        $exam->groupe = $request->input('groupe');
        $exam->lib_mod = $request->input('lib_mod');

        if ($request->has('teacher_ids')) {
            $exam->teacher_ids = is_array($request->input('teacher_ids')) 
                ? $request->input('teacher_ids') 
                : json_decode($request->input('teacher_ids'), true);
        }

        $exam->save();

        return new ExamResource($exam);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();
        return response()->noContent();
    }
}

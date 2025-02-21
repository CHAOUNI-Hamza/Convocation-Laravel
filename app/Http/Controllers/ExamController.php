<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $exams = Exam::all();
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

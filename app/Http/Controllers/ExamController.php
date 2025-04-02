<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Teacher;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;

class ExamController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function all()
    {
        $exams = Exam::with('teachers')->get();
        return ExamResource::collection($exams);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = $request->input('date');
        $module = $request->input('module');
        $salle = $request->input('salle');
        $filiere = $request->input('filiere');
        $semestre = $request->input('semestre');
        $groupe = $request->input('groupe');

        $query = Exam::with('teachers')->orderBy('created_at', 'desc');
        
        if ($date) {
            $query->whereDate('date', $date); 
        }
        if ($module) {
            $query->where('module', 'like', '%' . $module . '%');
        }
        if ($salle) {
            $query->where('salle', 'like', '%' . $salle . '%');
        }
        if ($filiere) {
            $query->where('filiere', 'like', '%' . $filiere . '%');
        }
        if ($semestre) {
            $query->where('semestre', 'like', '%' . $semestre . '%');
        }
        if ($groupe) {
            $query->where('groupe', 'like', '%' . $groupe . '%');
        }
        $exams = $query->paginate(15);
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
        // Créer un nouvel examen
        $exam = Exam::create([
            'date' => \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d'),
            'creneau_horaire' => \Carbon\Carbon::parse($request->input('creneau_horaire'))->format('H:i'),
            'module' => $request->input('module'),
            'salle' => $request->input('salle'),
            'filiere' => $request->input('filiere'),
            'semestre' => $request->input('semestre'),
            'groupe' => $request->input('groupe'),
            'lib_mod' => $request->input('lib_mod'),
            'prof_mod' => $request->input('prof_mod'),
        ]);

        // Associer les enseignants si fournis
        if ($request->has('teacher_ids')) {
            $teacherIds = is_array($request->teacher_ids) ? $request->teacher_ids : json_decode($request->teacher_ids, true);

            // Vérifier si les IDs sont valides
            $validTeachers = Teacher::whereIn('id', $teacherIds)->pluck('id')->toArray();
            
            // Attacher les enseignants à l'examen
            $exam->teachers()->attach($validTeachers);
        }

        return new ExamResource($exam->load('teachers'));
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
        $exam->update([
            'date' => \Carbon\Carbon::parse($request->date)->format('Y-m-d'),
            'creneau_horaire' => \Carbon\Carbon::parse($request->creneau_horaire)->format('H:i'),
            'module' => $request->module,
            'salle' => $request->salle,
            'filiere' => $request->filiere,
            'semestre' => $request->semestre,
            'groupe' => $request->groupe,
            'lib_mod' => $request->lib_mod,
            'prof_mod' => $request->input('prof_mod'),
        ]);
    
        // Vérifier et mettre à jour les enseignants
        if ($request->has('teacher_ids')) {
            $teacherIds = is_array($request->teacher_ids) ? $request->teacher_ids : json_decode($request->teacher_ids, true);
    
            // Vérifier si les enseignants existent bien
            $validTeachers = Teacher::whereIn('id', $teacherIds)->pluck('id')->toArray();
    
            // Synchroniser les enseignants avec la table pivot
            $exam->teachers()->sync($validTeachers);
        }
    
        return new ExamResource($exam->load('teachers'));
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

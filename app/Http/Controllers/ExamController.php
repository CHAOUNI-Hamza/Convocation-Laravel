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
    public function index(Request $request)
    {
        // Récupérer le critère de filtrage par date
        $date = $request->input('date');
        $module = $request->input('module');
        $salle = $request->input('salle');
        $filiere = $request->input('filiere');
        $semestre = $request->input('semestre');
        $groupe = $request->input('groupe');

        // Construction de la requête de base
        $query = Exam::with('teachers')->orderBy('created_at', 'desc');
        
        // Filtrage par date
        if ($date) {
            $query->whereDate('date', $date); // Filtrage exact par date
        }

        // Filtrage par module
        if ($module) {
            $query->where('module', 'like', '%' . $module . '%');
        }

        // Filtrage par salle
        if ($salle) {
            $query->where('salle', 'like', '%' . $salle . '%');
        }

        // Filtrage par filière
        if ($filiere) {
            $query->where('filiere', 'like', '%' . $filiere . '%');
        }

        // Filtrage par semestre
        if ($semestre) {
            $query->where('semestre', 'like', '%' . $semestre . '%');
        }

        // Filtrage par groupe
        if ($groupe) {
            $query->where('groupe', 'like', '%' . $groupe . '%');
        }

        // Appliquer la pagination après filtrage
        $exams = $query->paginate(15);

        // Retourner les résultats formatés avec ExamResource
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

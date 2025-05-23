<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Teacher;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use Illuminate\Support\Facades\DB;

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

    public function getExamTeachersDetails()
    {
        $results = DB::table('exam_teacher')
            ->join('exams', 'exam_teacher.exam_id', '=', 'exams.id')
            ->join('teachers', 'exam_teacher.teacher_id', '=', 'teachers.id')
            ->select(
                'exams.filiere',
                'exams.semestre',
                'exams.date',
                'exams.salle',
                'exams.creneau_horaire',
                'exams.prof_mod',
                'teachers.name',
                'teachers.first_name'
            )
            ->get();

        return response()->json($results);
    }

    public function assignProfModulesRandomly()
    {
        // Ne pas refaire si déjà affecté
        if (Exam::where('assigned', true)->exists()) {
            return response()->json(['message' => 'Les professeurs ont déjà été affectés.'], 403);
        }

        $groupedExams = Exam::select('module', 'date', 'creneau_horaire', 'prof_mod')
            ->groupBy('module', 'date', 'creneau_horaire', 'prof_mod')
            ->get();

        foreach ($groupedExams as $group) {
            $exams = Exam::where('module', $group->module)
                ->where('date', $group->date)
                ->where('creneau_horaire', $group->creneau_horaire)
                ->where('prof_mod', $group->prof_mod)
                ->get();

            if ($exams->isEmpty()) {
                continue;
            }

            $randomExam = $exams->random();
            $teacher = Teacher::where(DB::raw("CONCAT(name, ' ', first_name)"), $group->prof_mod)->first();

            if ($teacher && !$randomExam->teachers()->where('teacher_id', $teacher->id)->exists()) {
                $randomExam->teachers()->attach($teacher->id);
                $randomExam->assigned = true;
                $randomExam->save();
            }
        }

        return response()->json(['message' => 'Professeurs affectés avec succès.']);
    }


    public function removeProfAssignments()
    {
        $exams = Exam::where('assigned', true)->get();

        foreach ($exams as $exam) {
            $exam->teachers()->detach(); // Détache tous les professeurs liés
            $exam->assigned = false;
            $exam->save();
        }

        return response()->json(['message' => 'Affectations supprimées avec succès.']);
    }


    public function all()
    {
        $exams = Exam::with('teachers')
        ->orderBy('date', 'asc')  // Trier par date en premier
        ->orderBy('creneau_horaire', 'asc') // Trier par créneau horaire ensuite
        ->get();

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
        $profMod = $request->input('prof_mod');
        $libMod = $request->input('lib_mod');

        $query = Exam::with('teachers')
        ->orderBy('module', 'asc')
        ->orderBy('date', 'asc')  // Trier par date en premier
        ->orderBy('creneau_horaire', 'asc'); // Trier par créneau horaire ensuite
        
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
        if ($profMod) {
            $query->where('prof_mod', 'like', '%' . $profMod . '%');
        }
        if ($libMod) {
            $query->where('lib_mod', 'like', '%' . $libMod . '%');
        }
        $exams = $query->paginate(600);
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
        //return $request;
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

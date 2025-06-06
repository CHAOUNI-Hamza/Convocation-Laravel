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
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function getBySom($som)
    {
        $prof = Teacher::where('sum_number', $som)->first();

        if (!$prof) {
            return response()->json(['message' => 'prof not found'], 404);
        }

        return response()->json($prof);
    }

    // Récupérer tous les examens d’un professeur spécifique
    public function getExamDunProf($id) {
        $teacher = Teacher::with(['exams' => function($query) {
            $query->orderBy('date')->orderBy('creneau_horaire');  // Trie par date
        }])
        ->select('id', 'name', 'first_name')
        ->find($id);
    
        if (!$teacher) {
            return response()->json(['message' => 'Professeur non trouvé'], 404);
        }
    
        return response()->json([
            'name' => $teacher->name,
            'first_name' => $teacher->first_name,
            'exams' => $teacher->exams
        ]);
    }

    public function getExamDunProfParSumNumber($sum_number)
    {
        $teacher = Teacher::with(['exams' => function($query) {
                $query->orderBy('date');  // Trie par date
            }])
            ->select('id', 'name', 'first_name')
            ->where('sum_number', $sum_number)
            ->first();

        if (!$teacher) {
            return response()->json(['message' => 'Professeur non trouvé'], 404);
        }

        return response()->json([
            'name' => $teacher->name,
            'first_name' => $teacher->first_name,
            'exams' => $teacher->exams
        ]);
    }
    

    public function getTeachersDisponibles(Request $request)
    {
        $date = $request->input('date');  // Format : 'Y-m-d'
        $creneauHoraire = $request->input('creneau_horaire');  // Format : 'H:i'

        if ($creneauHoraire == '09:00') {
            $teachersWithoutCreneau = Teacher::whereDoesntHave('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->where('creneau_horaire', '09:00');
            })->get();

            $teachersWithOtherCreneaux = Teacher::whereHas('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->whereIn('creneau_horaire', ['11:30', '14:00', '14:30', '16:30', '17:00']);
            })->get();
        } 
        elseif ($creneauHoraire == '11:30') {
            $teachersWithoutCreneau = Teacher::whereDoesntHave('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->where('creneau_horaire', '11:30');
            })->get();

            $teachersWithOtherCreneaux = Teacher::whereHas('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->whereIn('creneau_horaire', ['09:00', '14:00', '14:30', '16:30', '17:00']);
            })->get();
        } 
        elseif ($creneauHoraire == '14:00' || $creneauHoraire == '14:30') {
            $teachersWithoutCreneau = Teacher::whereDoesntHave('exams', function ($query) use ($date, $creneauHoraire) {
                $query->where('date', $date)
                    ->where('creneau_horaire', $creneauHoraire);
            })->get();

            $teachersWithOtherCreneaux = Teacher::whereHas('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->whereIn('creneau_horaire', ['09:00', '11:30', '16:30', '17:00']);
            })->get();
        } 
        elseif ($creneauHoraire == '16:30' || $creneauHoraire == '17:00') {
            $teachersWithoutCreneau = Teacher::whereDoesntHave('exams', function ($query) use ($date, $creneauHoraire) {
                $query->where('date', $date)
                    ->where('creneau_horaire', $creneauHoraire);
            })->get();

            $teachersWithOtherCreneaux = Teacher::whereHas('exams', function ($query) use ($date) {
                $query->where('date', $date)
                    ->whereIn('creneau_horaire', ['09:00', '11:30', '14:00', '14:30']);
            })->get();
        } 
        else {
            $teachersWithoutCreneau = Teacher::all();
            $teachersWithOtherCreneaux = collect();
        }

        // Fusionner les résultats
        $teachers = $teachersWithOtherCreneaux->merge($teachersWithoutCreneau);

        // Charger le total des examens pour chaque professeur
        $teachers->loadCount('exams');

        // Trier les enseignants pour mettre en bas ceux qui sont déjà occupés à une autre date
        $teachers = $teachers->sortBy(function($teacher) use ($date) {
            return $teacher->exams()->where('date', '!=', $date)->exists();
        });

        return TeacherResource::collection($teachers);
    }


    public function all()
    {
        $teachers = Teacher::with('exams')
        ->orderBy('first_name', 'asc') // Tri alphabétique croissant
        ->get();

        return TeacherResource::collection($teachers);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->input('name');

        $teachers = Teacher::when($name, function ($query, $name) {
            return $query->where('name', 'like', '%' . $name . '%');
        })->orderBy('created_at', 'desc')->get();

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
        $teacher->city = $request->input('city');
        $teacher->status = $request->input('status');
        $teacher->limit = $request->input('limit');
        $teacher->grad = $request->input('grad');
        $teacher->day_time = $request->input('day_time');

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
        $teacher->city = $request->input('city');
        $teacher->status = $request->input('status');
        $teacher->limit = $request->input('limit');
        $teacher->grad = $request->input('grad');
        $teacher->day_time = $request->input('day_time');

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

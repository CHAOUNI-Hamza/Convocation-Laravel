<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Session;
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
        

        // Récupérer tous les professeurs avec le nombre de séances surveillées
        $professeurs = Teacher::withCount(['sessions as total_seances'])
            ->having('total_seances', '<', 8)
            ->get();
        
        // Récupérer les professeurs qui ont surveillé les séances précédentes du même jour
        $professeurs_ayant_surveille_avant = Session::where('date', $date)
            ->whereIn('creneau_horaire', array_slice($creneaux, 0, $indexCreneau))
            ->pluck('professeur_id');
        
            if ($creneau === '09:00') {
                // Exclure ceux qui ont déjà surveillé à 09:00 le même jour
                $professeurs_ayant_surveille_09h = Session::where('date', $date)
                    ->where('creneau_horaire', '09:00')
                    ->pluck('professeur_id');
            
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
            $professeurs_ayant_surveille_09h_11h = Session::where('date', $date)
                ->whereIn('creneau_horaire', ['09:00', '11:00'])
                ->pluck('professeur_id');
        
            // Exclure les professeurs ayant surveillé à 09:00 et 11:00
            $professeurs = $professeurs->reject(function ($prof) use ($professeurs_ayant_surveille_09h_11h) {
                return $professeurs_ayant_surveille_09h_11h->contains($prof->id);
            });
        
            // Récupérer les professeurs ayant surveillé à 14:00
            $professeurs_ayant_surveille_14h = Session::where('date', $date)
                ->where('creneau_horaire', '14:00')
                ->pluck('professeur_id');
        
            // Prioriser ceux qui ont surveillé à 14h00
            $professeurs = $professeurs->sortByDesc(function ($prof) use ($professeurs_ayant_surveille_14h) {
                return $professeurs_ayant_surveille_14h->contains($prof->id);
            });
        }
        

        return response()->json($professeurs);
    }

    public function ajouterProfesseur(Request $request)
    {
        // Validation des données
        $request->validate([
            'professeur_id' => 'required|exists:teachers,id',
            'examen_id' => 'required|exists:exams,id',
            'date' => 'required|date',
            'creneau_horaire' => 'required|in:09:00,11:00,14:00,16:30',
        ]);

        // Vérifier si le professeur a déjà atteint 8 séances de surveillance
        $nbSeances = Session::where('professeur_id', $request->professeur_id)->count();
        if ($nbSeances >= 8) {
            return response()->json(['message' => 'Ce professeur a déjà atteint la limite de 8 séances.'], 400);
        }

        // Vérifier si le professeur est déjà assigné à un créneau de surveillance pour ce jour-là
        $professeurDejaAssigne = Session::where('professeur_id', $request->professeur_id)
            ->where('date', $request->date)
            ->where('creneau_horaire', $request->creneau_horaire)
            ->exists();

        if ($professeurDejaAssigne) {
            return response()->json(['message' => 'Ce professeur surveille déjà une autre séance à cette heure.'], 400);
        }

        // Définition des créneaux matin et après-midi
        $matin = ['09:00', '11:00'];
        $apresMidi = ['14:00', '16:30'];

        // Vérifier la compatibilité des créneaux dans la même journée
        if (in_array($request->creneau_horaire, $matin)) {
            $existeApresMidi = Session::where('professeur_id', $request->professeur_id)
                ->where('date', $request->date)
                ->whereIn('creneau_horaire', $apresMidi)
                ->exists();
            if ($existeApresMidi) {
                return response()->json(['message' => 'Ce professeur a déjà une surveillance dans l\'après-midi. Il ne peut pas être assigné le matin.'], 400);
            }
        }

        if (in_array($request->creneau_horaire, $apresMidi)) {
            $existeMatin = Session::where('professeur_id', $request->professeur_id)
                ->where('date', $request->date)
                ->whereIn('creneau_horaire', $matin)
                ->exists();
            if ($existeMatin) {
                return response()->json(['message' => 'Ce professeur a déjà une surveillance dans la matinée. Il ne peut pas être assigné l\'après-midi.'], 400);
            }
        }

        // Ajouter la séance de surveillance
        $seance = Session::create([
            'professeur_id' => $request->professeur_id,
            'examen_id' => $request->examen_id,
            'date' => $request->date,
            'creneau_horaire' => $request->creneau_horaire,
        ]);

        return response()->json(['message' => 'Professeur ajouté à la surveillance avec succès.', 'seance' => $seance], 201);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers = Teacher::all();
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

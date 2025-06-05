<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Student;
use App\Models\Teacher;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;

class ReservationController extends Controller
{
    public function getReservationsByApogee($apogee)
    {
        $student = Student::where('apogee', $apogee)->first();

        if (!$student) {
            return response()->json(['message' => 'Étudiant non trouvé'], 404);
        }

        $reservations = Reservation::with('timeslot')
            ->where('student_id', $student->id)
            ->get();

        return response()->json([
            'studentres' => $student,
            'reservations' => $reservations
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservations = Reservation::with(['student', 'timeslot'])->get();
        return ReservationResource::collection($reservations);
    }

    public function indexRes(Request $request)
    {
        $apogee = $request->input('apogee');
        $cne = $request->input('cne');
        $last_name = $request->input('last_name');
        $date = $request->input('date');
        $time_range = $request->input('time_range');

        $query = Reservation::with(['student', 'timeslot']);

        // Filtres sur les champs de la relation student
        if ($apogee) {
            $query->whereHas('student', function ($q) use ($apogee) {
                $q->where('apogee', 'like', '%' . $apogee . '%');
            });
        }

        if ($cne) {
            $query->whereHas('student', function ($q) use ($cne) {
                $q->where('cne', 'like', '%' . $cne . '%');
            });
        }

        if ($last_name) {
            $query->whereHas('student', function ($q) use ($last_name) {
                $q->where('last_name', 'like', '%' . $last_name . '%');
            });
        }

        // Filtres sur les champs de la relation timeslot
        if ($date) {
            $query->whereHas('timeslot', function ($q) use ($date) {
                $q->whereDate('date', $date);
            });
        }

        if ($time_range) {
            $query->whereHas('timeslot', function ($q) use ($time_range) {
                $q->where('time_range', 'like', $time_range . '%');
            });
        }    

        $reservations = $query->orderBy('id', 'desc')->get();

        return ReservationResource::collection($reservations);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreReservationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_string($request->timeslots)) {
            $request->merge([
                'timeslots' => json_decode($request->timeslots, true),
            ]);
        }

        $request->validate([
            'apogee' => 'required|exists:students,apogee',
            'timeslots' => 'required|array',
            'timeslots.*' => 'exists:timeslots,id',
        ]);

        $student = Student::where('apogee', $request->apogee)->first();

        // Vérifier si l'étudiant a déjà une réservation
        $hasReservation = Reservation::where('student_id', $student->id)->exists();

        if ($hasReservation) {
            return response()->json(['message' => "لقد قام الطالب بالفعل بإجراء حجز"], 400);
        }

        foreach ($request->timeslots as $timeslot_id) {
            Reservation::create([
                'student_id' => $student->id,
                'timeslot_id' => $timeslot_id,
            ]);
        }

        return response()->json(['message' => 'تم تسجيل الحجز بنجاح']);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateReservationRequest  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}

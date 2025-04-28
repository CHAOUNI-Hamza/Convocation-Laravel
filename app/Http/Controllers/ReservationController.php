<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Student;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

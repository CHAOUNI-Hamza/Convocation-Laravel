<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;

class StudentController extends Controller
{
    public function getByApogee($apogee)
    {
        $student = Student::where('apogee', $apogee)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return response()->json($student);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::orderBy('created_at', 'desc')->paginate(15);
        return StudentResource::collection($students);
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
     * @param  \App\Http\Requests\StoreStudentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStudentRequest $request)
    {
        $student = new Student();
        $student->apogee = $request->input('apogee');
        $student->cne = $request->input('cne');
        $student->first_name = $request->input('first_name');
        $student->last_name = $request->input('last_name');
        $student->last_name_ar = $request->input('last_name_ar');
        $student->first_name_ar = $request->input('first_name_ar');
        $student->cnie = $request->input('cnie');
        $student->birth_date = $request->input('birth_date');
        $student->lab = $request->input('lab');
        $student->save();

        return new StudentResource($student);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        return new StudentResource($student);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStudentRequest  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->apogee = $request->input('apogee');
        $student->cne = $request->input('cne');
        $student->first_name = $request->input('first_name');
        $student->last_name = $request->input('last_name');
        $student->last_name_ar = $request->input('last_name_ar');
        $student->first_name_ar = $request->input('first_name_ar');
        $student->cnie = $request->input('cnie');
        $student->birth_date = $request->input('birth_date');
        $student->lab = $request->input('lab');
        $student->save();

        return new StudentResource($student);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return response()->noContent();
    }
}

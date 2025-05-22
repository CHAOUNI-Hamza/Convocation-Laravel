<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Requests\UpdateCycleRequest;
use Illuminate\Http\Request;

class CycleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cycle = Cycle::first();
        return response()->json($cycle);
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
     * @param  \App\Http\Requests\StoreCycleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCycleRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function show(Cycle $cycle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function edit(Cycle $cycle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCycleRequest  $request
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $cycle = Cycle::first();

        if (!$cycle) {
            return response()->json(['message' => 'Cycle introuvable'], 404);
        }

        $cycle->update($request->all());

        return response()->json(['message' => 'Cycle mis à jour avec succès', 'data' => $cycle]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cycle $cycle)
    {
        //
    }
}

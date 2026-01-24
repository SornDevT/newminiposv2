<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->get('page') === '-1') {
            return Unit::all();
        }
        return Unit::paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:units,name',
                'abbreviation' => 'required|string|max:10',
                'description' => 'nullable|string',
            ]);

            $unit = Unit::create($request->all());
            return response()->json($unit, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return $unit;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
                'abbreviation' => 'required|string|max:10',
                'description' => 'nullable|string',
            ]);

            $unit->update($request->all());
            return response()->json($unit, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return response()->json(null, 204);
    }
}
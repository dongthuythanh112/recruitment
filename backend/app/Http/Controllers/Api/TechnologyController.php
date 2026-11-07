<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Http\Request;

class TechnologyController extends Controller
{
    public function index()
    {
        return response()->json(
            Technology::latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $technology = Technology::create($validated);

        return response()->json($technology, 201);
    }

    public function show(Technology $technology)
    {
        return response()->json($technology);
    }

    public function update(
        Request $request,
        Technology $technology
    ) {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $technology->update($validated);

        return response()->json($technology);
    }

    public function destroy(Technology $technology)
    {
        $technology->delete();

        return response()->noContent();
    }
}
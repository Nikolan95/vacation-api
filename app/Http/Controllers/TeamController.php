<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:administrator');
    }

    public function index()
    {
        $teams = Team::with('manager')->get();
        return response()->json($teams);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $team = Team::create($validated);
        return response()->json($team, 201);
    }

    public function show($id)
    {
        $team = Team::with('manager', 'users')->findOrFail($id);
        return response()->json($team);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $team = Team::findOrFail($id);
        $team->update($validated);

        return response()->json($team);
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return response()->json(['message' => 'Team deleted successfully']);
    }
}

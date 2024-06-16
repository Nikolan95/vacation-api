<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:administrator');
    }

    public function index()
    {
        $users = User::with('team')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:administrator,manager,user',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'team_id' => $validated['team_id'],
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::with('team')->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|in:administrator,manager,user',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        $user->update($validated);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}


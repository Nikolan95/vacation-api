<?php

namespace App\Http\Controllers;

use App\Models\VacationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VacationRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = auth()->user();
        $existingRequests = VacationRequest::where('user_id', $user->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($query) use ($validated) {
                        $query->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })->exists();

        if ($existingRequests) {
            return response()->json(['error' => 'Vacation dates overlap with an existing request.'], 422);
        }

        $vacationRequest = new VacationRequest([
            'user_id' => $user->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'pending',
        ]);
        $vacationRequest->save();

        return response()->json($vacationRequest, 201);
    }

    public function update(Request $request, $id)
    {
        $vacationRequest = VacationRequest::findOrFail($id);

        // Ensure the user owns the request and it's still pending
        if (Auth::id() !== $vacationRequest->user_id || $vacationRequest->status !== 'pending') {
            return response()->json(['error' => 'Unauthorized or request is not pending'], 403);
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Ensure the new dates don't overlap with any other approved or pending requests
        $overlappingRequests = VacationRequest::where('user_id', Auth::id())
            ->where('id', '!=', $vacationRequest->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($query) use ($validated) {
                        $query->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })->count();

        if ($overlappingRequests > 0) {
            return response()->json(['error' => 'The requested dates overlap with another request'], 422);
        }

        $vacationRequest->start_date = $validated['start_date'];
        $vacationRequest->end_date = $validated['end_date'];
        $vacationRequest->save();

        return response()->json(['message' => 'Vacation request updated successfully']);
    }

    public function cancel($id)
    {
        $vacationRequest = VacationRequest::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $vacationRequest->status = 'cancelled';
        $vacationRequest->save();

        return response()->json(['message' => 'Vacation request cancelled.']);
    }

    public function approve($id)
    {
        $vacationRequest = VacationRequest::findOrFail($id);
        $this->authorize('approve', $vacationRequest);

        $vacationRequest->status = 'approved';
        $vacationRequest->save();

        return response()->json(['message' => 'Vacation request approved.']);
    }

    public function reject($id)
    {
        $vacationRequest = VacationRequest::findOrFail($id);
        $this->authorize('approve', $vacationRequest);

        $vacationRequest->status = 'rejected';
        $vacationRequest->save();

        return response()->json(['message' => 'Vacation request rejected.']);
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role == 'manager') {
            $vacationRequests = VacationRequest::whereHas('user', function ($query) use ($user) {
                $query->where('team_id', $user->team_id);
            })->get();
        } else {
            $vacationRequests = $user->vacationRequests;
        }

        return response()->json($vacationRequests);
    }

}

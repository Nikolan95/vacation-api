<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VacationRequest;

class VacationRequestPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function approve(User $user, VacationRequest $vacationRequest)
    {
        return $user->role == 'manager' && $user->team_id == $vacationRequest->user->team_id;
    }
}

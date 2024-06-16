<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $authUser, User $user)
    {
        return $authUser->role === 'administrator';
    }

    public function delete(User $authUser, User $user)
    {
        return $authUser->role === 'administrator';
    }
}

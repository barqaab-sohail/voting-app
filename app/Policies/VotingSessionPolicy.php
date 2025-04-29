<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VotingSession;
use Illuminate\Auth\Access\HandlesAuthorization;

class VotingSessionPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->is_admin;
    }

    public function update(User $user, VotingSession $votingSession)
    {
        return $user->is_admin && $user->id === $votingSession->created_by;
    }

    public function delete(User $user, VotingSession $votingSession)
    {
        return $user->is_admin && $user->id === $votingSession->created_by;
    }
}

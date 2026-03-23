<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
    public function view(User $user, Meeting $meeting): bool
    {
        return $user->id === $meeting->created_by ||
            $meeting->participants()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return $user->id === $meeting->created_by;
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return $user->id === $meeting->created_by;
    }
}

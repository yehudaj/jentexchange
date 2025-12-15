<?php

namespace App\Policies;

use App\Models\Entertainer;
use App\Models\User;

class EntertainerPolicy
{
    public function update(User $user, Entertainer $entertainer)
    {
        if ($user->isAdmin()) return true;
        return $entertainer->user_id === $user->id;
    }
}

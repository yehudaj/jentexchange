<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function update(User $user, Customer $customer)
    {
        if ($user->isAdmin()) return true;
        return $customer->user_id === $user->id;
    }
}

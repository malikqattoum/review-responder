<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;
use App\Models\TeamMember;

class BusinessPolicy
{
    public function view(User $user, Business $business): bool
    {
        // Owner or team member
        if ($user->id === $business->user_id) {
            return true;
        }

        return TeamMember::where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->accepted()
            ->exists();
    }

    public function update(User $user, Business $business): bool
    {
        // Only owner or admin
        if ($user->id === $business->user_id) {
            return true;
        }

        return TeamMember::where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->accepted()
            ->where('role', 'admin')
            ->exists();
    }

    public function delete(User $user, Business $business): bool
    {
        // Only owner
        return $user->id === $business->user_id;
    }

    public function manageTeam(User $user, Business $business): bool
    {
        // Only owner or admin
        if ($user->id === $business->user_id) {
            return true;
        }

        return TeamMember::where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->accepted()
            ->where('role', 'admin')
            ->exists();
    }
}

<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isModerator();
    }

    public function view(User $user, Report $report)
    {
        return $user->isModerator() || $user->id === $report->reporter_id;
    }

    public function create(User $user)
    {
        return !$user->isBanned();
    }

    public function update(User $user, Report $report)
    {
        return $user->isModerator();
    }

    public function delete(User $user, Report $report)
    {
        return $user->isAdmin();
    }

    public function resolve(User $user, Report $report)
    {
        return $user->isModerator() && $report->status === 'pending';
    }

    public function reject(User $user, Report $report)
    {
        return $user->isModerator() && $report->status === 'pending';
    }

    public function manageAll(User $user)
    {
        return $user->isModerator();
    }
} 
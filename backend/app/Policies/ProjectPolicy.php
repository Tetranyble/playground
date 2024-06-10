<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {

        return $user->hasRoles('manager');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // The ordering is to preempt the top level role
        return $user->hasRoles('manager') ||
            $user->hasPermissions('project_show') ||
            $project->isOwner($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->hasRoles('manager') ||
            $user->hasPermissions('project_update') ||
            $project->isOwner($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->hasRoles('manager') ||
            $user->hasPermissions('project_delete') ||
            $project->isOwner($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return $user->hasRoles('manager') ||
            $user->hasPermissions('project_delete') ||
            $project->isOwner($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRoles('manager') ||
            $user->hasPermissions('project_delete') ||
            $project->isOwner($user);
    }
}

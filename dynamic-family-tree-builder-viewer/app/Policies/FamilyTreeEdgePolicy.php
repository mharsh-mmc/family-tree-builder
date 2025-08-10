<?php

namespace App\Policies;

use App\Models\FamilyTreeEdge;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FamilyTreeEdgePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view edges in their own family trees
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FamilyTreeEdge $familyTreeEdge): bool
    {
        // Users can only view edges in their own family tree
        return $user->id === $familyTreeEdge->sourceNode->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Authenticated users can create family tree edges
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FamilyTreeEdge $familyTreeEdge): bool
    {
        // Users can only update edges in their own family tree
        return $user->id === $familyTreeEdge->sourceNode->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FamilyTreeEdge $familyTreeEdge): bool
    {
        // Users can only delete edges in their own family tree
        return $user->id === $familyTreeEdge->sourceNode->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FamilyTreeEdge $familyTreeEdge): bool
    {
        // Users can only restore edges in their own family tree
        return $user->id === $familyTreeEdge->sourceNode->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FamilyTreeEdge $familyTreeEdge): bool
    {
        // Users can only permanently delete edges in their own family tree
        return $user->id === $familyTreeEdge->sourceNode->user_id;
    }
}
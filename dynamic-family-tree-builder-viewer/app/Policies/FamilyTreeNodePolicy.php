<?php

namespace App\Policies;

use App\Models\FamilyTreeNode;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FamilyTreeNodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own family trees
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FamilyTreeNode $familyTreeNode): bool
    {
        // Users can only view nodes in their own family tree
        return $user->id === $familyTreeNode->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Authenticated users can create family tree nodes
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FamilyTreeNode $familyTreeNode): bool
    {
        // Users can only update nodes in their own family tree
        return $user->id === $familyTreeNode->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FamilyTreeNode $familyTreeNode): bool
    {
        // Users can only delete nodes in their own family tree
        return $user->id === $familyTreeNode->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FamilyTreeNode $familyTreeNode): bool
    {
        // Users can only restore nodes in their own family tree
        return $user->id === $familyTreeNode->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FamilyTreeNode $familyTreeNode): bool
    {
        // Users can only permanently delete nodes in their own family tree
        return $user->id === $familyTreeNode->user_id;
    }

    /**
     * Determine whether the user can view family tree data for a specific user.
     */
    public function viewUserTree(User $user, int $targetUserId): bool
    {
        // Users can view public family trees (for the viewer functionality)
        // In the future, this could be enhanced with sharing permissions
        return true;
    }

    /**
     * Determine whether the user can manage family tree edges.
     */
    public function manageEdges(User $user, FamilyTreeNode $sourceNode, FamilyTreeNode $targetNode): bool
    {
        // Users can only manage edges between nodes in their own family tree
        return $user->id === $sourceNode->user_id && $user->id === $targetNode->user_id;
    }
}
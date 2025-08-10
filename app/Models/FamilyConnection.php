<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_node_id',
        'target_node_id',
        'relation_type',
        'description',
    ];

    /**
     * Get the source family member.
     */
    public function sourceMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class, 'source_node_id');
    }

    /**
     * Get the target family member.
     */
    public function targetMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class, 'target_node_id');
    }

    /**
     * Get the relationship type in a human-readable format.
     */
    public function getRelationTypeLabelAttribute(): string
    {
        $types = [
            'parent' => 'Parent',
            'child' => 'Child',
            'spouse' => 'Spouse',
            'sibling' => 'Sibling',
            'grandparent' => 'Grandparent',
            'grandchild' => 'Grandchild',
            'aunt' => 'Aunt',
            'uncle' => 'Uncle',
            'cousin' => 'Cousin',
            'niece' => 'Niece',
            'nephew' => 'Nephew',
            'in-law' => 'In-Law',
            'step' => 'Step',
            'adopted' => 'Adopted',
            'foster' => 'Foster',
        ];

        return $types[$this->relation_type] ?? $this->relation_type ?? 'Unknown';
    }

    /**
     * Check if this connection is bidirectional.
     */
    public function isBidirectional(): bool
    {
        return in_array($this->relation_type, ['spouse', 'sibling']);
    }

    /**
     * Get the reverse relationship type.
     */
    public function getReverseRelationTypeAttribute(): string
    {
        $reverseTypes = [
            'parent' => 'child',
            'child' => 'parent',
            'grandparent' => 'grandchild',
            'grandchild' => 'grandparent',
            'aunt' => 'niece/nephew',
            'uncle' => 'niece/nephew',
            'niece' => 'aunt/uncle',
            'nephew' => 'aunt/uncle',
        ];

        return $reverseTypes[$this->relation_type] ?? $this->relation_type;
    }
}
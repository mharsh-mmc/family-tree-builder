<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyTreeEdge extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_node_id',
        'target_node_id',
        'relation_type',
    ];

    public function sourceNode(): BelongsTo
    {
        return $this->belongsTo(FamilyTreeNode::class, 'source_node_id');
    }

    public function targetNode(): BelongsTo
    {
        return $this->belongsTo(FamilyTreeNode::class, 'target_node_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamilyTreeNode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'relation',
        'profile_pic',
        'dob',
        'is_alive',
        'dod',
        'biodata',
        'position_x',
        'position_y',
    ];

    protected $casts = [
        'dob' => 'date',
        'dod' => 'date',
        'is_alive' => 'boolean',
        'position_x' => 'decimal:2',
        'position_y' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceEdges(): HasMany
    {
        return $this->hasMany(FamilyTreeEdge::class, 'source_node_id');
    }

    public function targetEdges(): HasMany
    {
        return $this->hasMany(FamilyTreeEdge::class, 'target_node_id');
    }

    public function getProfilePicUrlAttribute(): string
    {
        if ($this->profile_pic) {
            return asset('storage/' . $this->profile_pic);
        }
        return asset('images/default-avatar.png');
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->dob) {
            return null;
        }

        if ($this->is_alive) {
            return now()->diffInYears($this->dob);
        }

        if ($this->dod) {
            return $this->dod->diffInYears($this->dob);
        }

        return null;
    }
}

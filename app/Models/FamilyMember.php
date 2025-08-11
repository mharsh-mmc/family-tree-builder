<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'relation',
        'dob',
        'dod',
        'is_alive',
        'biodata',
        'profile_pic',
        'position_x',
        'position_y',
    ];

    protected $casts = [
        'dob' => 'date',
        'dod' => 'date',
        'is_alive' => 'boolean',
        'position_x' => 'float',
        'position_y' => 'float',
    ];

    /**
     * Get the connections where this member is the source.
     */
    public function sourceConnections(): HasMany
    {
        return $this->hasMany(FamilyConnection::class, 'source_node_id');
    }

    /**
     * Get the connections where this member is the target.
     */
    public function targetConnections(): HasMany
    {
        return $this->hasMany(FamilyConnection::class, 'target_node_id');
    }

    /**
     * Get all connections for this member.
     */
    public function connections()
    {
        return $this->sourceConnections->merge($this->targetConnections);
    }

    /**
     * Get the age of the family member.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->dob) {
            return null;
        }

        $endDate = $this->is_alive ? now() : $this->dod;
        
        if (!$endDate) {
            return null;
        }

        return $endDate->diffInYears($this->dob);
    }

    /**
     * Get the display name with age.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        
        if ($this->age !== null) {
            $name .= " ({$this->age})";
        }
        
        if (!$this->is_alive) {
            $name .= " †";
        }
        
        return $name;
    }

    /**
     * Scope to get only living members.
     */
    public function scopeLiving($query)
    {
        return $query->where('is_alive', true);
    }

    /**
     * Scope to get only deceased members.
     */
    public function scopeDeceased($query)
    {
        return $query->where('is_alive', false);
    }
}
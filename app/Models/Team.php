<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'org_id',
        'name',
        'description',
        'settings',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'name' => 'string',
        'description' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the organization that the team belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Get the users in the team.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot('role', 'added_at')
            ->withTimestamps();
    }

    /**
     * Get the workflows associated with the team.
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class, 'team_id'); // If workflows can be assigned to teams
    }
}

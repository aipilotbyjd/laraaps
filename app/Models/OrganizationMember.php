<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'org_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'user_id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'joined_at' => 'datetime',
        'role' => 'string',
    ];

    public $timestamps = false;

    protected $table = 'organization_members';

    /**
     * Get the user that belongs to the organization.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the organization that the user belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }
}

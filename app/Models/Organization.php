<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'plan',
        'limits',
        'created_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'limits' => 'array',
        'created_at' => 'datetime',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * Get the workflows for the organization.
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class, 'org_id');
    }

    /**
     * Get the users in the organization.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get the teams in the organization.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'org_id');
    }

    /**
     * Get the credentials for the organization.
     */
    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class, 'org_id');
    }

    /**
     * Get the webhooks for the organization.
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class, 'org_id');
    }

    /**
     * Get the rate limits for the organization.
     */
    public function rateLimits(): HasOne
    {
        return $this->hasOne(RateLimit::class, 'org_id');
    }

    /**
     * Get the audit logs for the organization.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'org_id');
    }

    /**
     * Get the notifications for the organization.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'org_id');
    }
}

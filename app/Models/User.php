<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'avatar',
        'timezone',
        'language',
        'email_verified_at',
        'last_login_at',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'two_factor_recovery_codes' => 'encrypted',
        'two_factor_secret' => 'encrypted',
    ];

    /**
     * Get the user's API keys.
     */
    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    /**
     * Get the user's sessions.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Get the organizations the user belongs to.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get the teams the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot('role', 'added_at')
            ->withTimestamps();
    }

    /**
     * Get the user's workflows.
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class, 'user_id');
    }

    /**
     * Get the user's credentials.
     */
    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class, 'user_id');
    }

    /**
     * Get the user's notifications.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_id');
    }

    /**
     * Get the user's webhook logs.
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class, 'user_id');
    }
}

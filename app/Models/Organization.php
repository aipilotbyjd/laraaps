<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'owner_id',
        'email',
        'website',
        'logo',
        'timezone',
        'currency',
        'country',
        'settings',
        'limits',
        'plan',
        'trial_ends_at',
        'is_active',
        'suspended_at',
        'suspension_reason',
    ];

    protected $casts = [
        'id' => 'string',
        'owner_id' => 'integer',
        'settings' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $attributes = [
        'settings' => '{}',
        'limits' => '{"workflows": 10, "executions_per_month": 1000, "team_members": 5}',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
            if (! $model->slug) {
                $model->slug = Str::slug($model->name).'-'.Str::random(6);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'org_id');
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class, 'org_id');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(WorkflowExecution::class, 'org_id');
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class, 'org_id');
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class, 'org_id');
    }

    public function variables(): HasMany
    {
        return $this->hasMany(Variable::class, 'org_id');
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class, 'org_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'org_id');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active && ! $this->suspended_at;
    }

    public function canAddWorkflow(): bool
    {
        $limit = $this->limits['workflows'] ?? 10;

        return $this->workflows()->count() < $limit;
    }

    public function canExecute(): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $limit = $this->limits['executions_per_month'] ?? 1000;
        $currentMonth = now()->format('Y-m');
        $executionsThisMonth = $this->executions()
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->count();

        return $executionsThisMonth < $limit;
    }

    public function canAddMember(): bool
    {
        $limit = $this->limits['team_members'] ?? 5;

        return $this->users()->count() < $limit;
    }
}

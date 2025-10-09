<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'org_id',
        'user_id',
        'name',
        'description',
        'version',
        'active',
        'nodes',
        'connections',
        'settings',
        'tags',
        'folder_id',
        'trigger_config',
        'cron_expression',
        'timezone',
        'avg_execution_time_ms',
        'success_rate',
        'last_execution_at',
        'parent_version_id',
        'is_latest',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'user_id' => 'string', // UUID
        'nodes' => 'array',
        'connections' => 'array',
        'settings' => 'array',
        'tags' => 'array',
        'folder_id' => 'string', // UUID
        'parent_version_id' => 'string', // UUID
        'trigger_config' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'last_execution_at' => 'datetime',
        'active' => 'boolean',
        'is_latest' => 'boolean',
        'version' => 'integer',
        'avg_execution_time_ms' => 'integer',
        'success_rate' => 'decimal:2',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the organization that owns the workflow.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Get the user that owns the workflow.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the executions for the workflow.
     */
    public function executions(): HasMany
    {
        return $this->hasMany(WorkflowExecution::class, 'workflow_id');
    }

    /**
     * Get the versions of the workflow.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(WorkflowVersion::class, 'workflow_id');
    }

    /**
     * Get the webhooks associated with the workflow.
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class, 'workflow_id');
    }

    /**
     * Get the schedules for the workflow.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'workflow_id');
    }

    /**
     * Get the tags associated with the workflow.
     */
    public function tagsRelation(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'workflow_tag', 'workflow_id', 'tag_id');
    }

    /**
     * Get the comments for the workflow.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'workflow_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'workflow_id',
        'org_id',
        'user_id',
        'mode',
        'status',
        'queued_at',
        'started_at',
        'finished_at',
        'wait_until',
        'execution_time_ms',
        'node_executions_count',
        'trigger_data',
        'execution_data_id',
        'error_message',
        'error_stack',
        'memory_used_mb',
        'cpu_time_ms',
        'retry_count',
        'parent_execution_id',
        'credits_used',
        'created_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'workflow_id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'user_id' => 'string', // UUID
        'trigger_data' => 'array',
        'execution_data_id' => 'string', // UUID
        'parent_execution_id' => 'string', // UUID
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'wait_until' => 'datetime',
        'created_at' => 'datetime',
        'execution_time_ms' => 'integer',
        'node_executions_count' => 'integer',
        'memory_used_mb' => 'integer',
        'cpu_time_ms' => 'integer',
        'retry_count' => 'integer',
        'credits_used' => 'integer',
        'mode' => 'string',
        'status' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * Get the workflow that the execution belongs to.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    /**
     * Get the organization that the execution belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Get the user that initiated the execution.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the node executions for this workflow execution.
     */
    public function nodeExecutions(): HasMany
    {
        return $this->hasMany(NodeExecution::class, 'execution_id');
    }

    /**
     * Get the parent execution if this is a retry.
     */
    public function parentExecution(): BelongsTo
    {
        return $this->belongsTo(WorkflowExecution::class, 'parent_execution_id');
    }
}

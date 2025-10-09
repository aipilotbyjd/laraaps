<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NodeExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'execution_id',
        'workflow_id',
        'node_id',
        'node_type',
        'status',
        'started_at',
        'finished_at',
        'execution_time_ms',
        'input_data_id',
        'output_data_id',
        'error',
        'http_requests_count',
        'db_queries_count',
        'created_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'execution_id' => 'string', // UUID
        'workflow_id' => 'string', // UUID
        'input_data_id' => 'string', // UUID
        'output_data_id' => 'string', // UUID
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'created_at' => 'datetime',
        'execution_time_ms' => 'integer',
        'http_requests_count' => 'integer',
        'db_queries_count' => 'integer',
        'status' => 'string',
        'node_type' => 'string',
        'node_id' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * Get the workflow execution that the node execution belongs to.
     */
    public function workflowExecution(): BelongsTo
    {
        return $this->belongsTo(WorkflowExecution::class, 'execution_id');
    }

    /**
     * Get the workflow that the node execution belongs to.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }
}

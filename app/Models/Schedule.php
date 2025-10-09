<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'workflow_id',
        'name',
        'description',
        'cron_expression',
        'timezone',
        'active',
        'last_execution_at',
        'next_execution_at',
        'execution_data',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'workflow_id' => 'string', // UUID
        'created_by' => 'string', // UUID
        'execution_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_execution_at' => 'datetime',
        'next_execution_at' => 'datetime',
        'active' => 'boolean',
        'name' => 'string',
        'description' => 'string',
        'cron_expression' => 'string',
        'timezone' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the workflow that the schedule belongs to.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    /**
     * Get the user that created the schedule.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

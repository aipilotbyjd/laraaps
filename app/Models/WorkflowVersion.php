<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'workflow_id',
        'version_number',
        'nodes',
        'connections',
        'settings',
        'changelog',
        'created_by',
        'active',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'workflow_id' => 'string', // UUID
        'created_by' => 'string', // UUID
        'nodes' => 'array',
        'connections' => 'array',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'version_number' => 'integer',
        'active' => 'boolean',
        'changelog' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the workflow that the version belongs to.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    /**
     * Get the user that created the version.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'workflow_id',
        'node_id',
        'org_id',
        'method',
        'path',
        'auth_type',
        'auth_config',
        'ip_whitelist',
        'last_triggered_at',
        'trigger_count',
        'active',
        'created_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'workflow_id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'auth_config' => 'array',
        'ip_whitelist' => 'array',
        'created_at' => 'datetime',
        'last_triggered_at' => 'datetime',
        'trigger_count' => 'integer',
        'active' => 'boolean',
        'method' => 'string',
        'path' => 'string',
        'auth_type' => 'string',
        'node_id' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * Get the workflow that the webhook belongs to.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    /**
     * Get the organization that the webhook belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }
}

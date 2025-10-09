<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'org_id',
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'changes',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'user_id' => 'string', // UUID
        'resource_id' => 'string', // UUID
        'changes' => 'array',
        'created_at' => 'datetime',
        'action' => 'string',
        'resource_type' => 'string',
        'ip_address' => 'string',
        'user_agent' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * Get the organization that the audit log belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Get the user that triggered the audit log event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

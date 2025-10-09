<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credential extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'org_id',
        'user_id',
        'name',
        'type',
        'encrypted_data',
        'encryption_key_id',
        'last_tested_at',
        'test_status',
        'shared_with_users',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'user_id' => 'string', // UUID
        'shared_with_users' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_tested_at' => 'datetime',
        'test_status' => 'string',
        'type' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the organization that owns the credential.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Get the user that owns the credential.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the workflows that use this credential.
     */
    public function workflows(): HasMany
    {
        // This relationship would require an intermediate table to track which workflows use which credentials
        // Implementation would depend on how credentials are referenced in workflows
        return $this->hasMany(Workflow::class, 'credential_id'); // Placeholder
    }
}

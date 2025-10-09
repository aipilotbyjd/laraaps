<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variable extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'org_id',
        'user_id',
        'name',
        'value',
        'encrypted_value',
        'is_secret',
        'scope',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'user_id' => 'string', // UUID
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_secret' => 'boolean',
        'name' => 'string',
        'scope' => 'string', // 'global', 'environment', 'workflow', etc.
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the organization that the variable belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Get the user that created the variable.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

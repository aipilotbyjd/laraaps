<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'key',
        'permissions',
        'last_used_at',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'user_id' => 'string', // UUID
        'permissions' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'name' => 'string',
        'key' => 'string', // This should be hashed
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the user that owns the API key.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

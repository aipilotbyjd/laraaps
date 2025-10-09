<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'recipient_id',
        'type',
        'title',
        'message',
        'data',
        'read',
        'read_at',
        'related_entity_id',
        'related_entity_type',
        'created_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'recipient_id' => 'string', // UUID
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
        'related_entity_id' => 'string', // UUID
        'created_at' => 'datetime',
        'type' => 'string',
        'title' => 'string',
        'message' => 'string',
        'related_entity_type' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * Get the user that receives the notification.
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}

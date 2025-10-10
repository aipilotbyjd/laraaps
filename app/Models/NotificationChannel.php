<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'type',
        'config',
        'is_active',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}

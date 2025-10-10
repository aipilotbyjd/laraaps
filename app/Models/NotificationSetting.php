<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'key',
        'value',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'file_path',
        'user_id',
        'shared_by',
        'permissions',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'shared_by' => 'string',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}

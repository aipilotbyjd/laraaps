<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutionData extends Model
{
    use HasFactory;

    protected $table = 'execution_data';

    protected $fillable = [
        'id',
        'data',
    ];

    protected $casts = [
        'id' => 'string',
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $keyType = 'string';

    public $incrementing = false;
}

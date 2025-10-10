<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'workflow_id',
        'user_id',
        'permissions',
    ];

    protected $casts = [
        'id' => 'string',
        'workflow_id' => 'string',
        'user_id' => 'string',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}

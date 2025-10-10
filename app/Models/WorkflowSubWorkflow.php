<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowSubWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'workflow_id',
        'sub_workflow_id',
    ];

    protected $casts = [
        'id' => 'string',
        'workflow_id' => 'string',
        'sub_workflow_id' => 'string',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}

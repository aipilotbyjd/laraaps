<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'workflow_definition',
        'tags',
        'category',
        'author_id',
        'org_id',
        'is_public',
        'downloads',
        'stars',
    ];

    protected $casts = [
        'id' => 'string',
        'workflow_definition' => 'array',
        'tags' => 'array',
        'is_public' => 'boolean',
        'author_id' => 'string',
        'org_id' => 'string',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}

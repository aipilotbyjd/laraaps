<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'type',
        'description',
        'properties',
        'is_trigger',
        'is_custom',
        'org_id',
        'user_id',
    ];

    protected $casts = [
        'id' => 'string',
        'properties' => 'array',
        'is_trigger' => 'boolean',
        'is_custom' => 'boolean',
        'org_id' => 'string',
        'user_id' => 'string',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}

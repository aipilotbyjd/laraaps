<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'owner_id',
    ];

    protected $casts = [
        'id' => 'string',
        'owner_id' => 'string',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_member')->withPivot('role')->withTimestamps();
    }
}

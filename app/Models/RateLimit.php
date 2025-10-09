<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'org_id',
        'resource_type',
        'limit_per_minute',
        'limit_per_hour',
        'limit_per_day',
        'current_usage_minute',
        'current_usage_hour',
        'current_usage_day',
        'window_start',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'resource_type' => 'string',
        'limit_per_minute' => 'integer',
        'limit_per_hour' => 'integer',
        'limit_per_day' => 'integer',
        'current_usage_minute' => 'integer',
        'current_usage_hour' => 'integer',
        'current_usage_day' => 'integer',
        'window_start' => 'datetime',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * Get the organization that the rate limit belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }
}

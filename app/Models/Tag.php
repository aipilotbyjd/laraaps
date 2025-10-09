<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'org_id',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string', // UUID
        'org_id' => 'string', // UUID
        'created_by' => 'string', // UUID
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'name' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the organization that the tag belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Get the user that created the tag.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the workflows that have this tag.
     */
    public function workflows(): BelongsToMany
    {
        return $this->belongsToMany(Workflow::class, 'workflow_tag', 'tag_id', 'workflow_id');
    }
}

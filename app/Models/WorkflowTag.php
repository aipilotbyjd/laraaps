<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'tag_id',
    ];

    protected $casts = [
        'workflow_id' => 'string', // UUID
        'tag_id' => 'string', // UUID
    ];

    public $timestamps = false;

    protected $table = 'workflow_tag';

    /**
     * Get the workflow for the tag association.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    /**
     * Get the tag for the workflow association.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
}

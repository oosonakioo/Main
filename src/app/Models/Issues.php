<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issues extends Model
{
    use SoftDeletes;

    public function issueTopic(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Categories::class, 'issue_topic_id');
    }
}

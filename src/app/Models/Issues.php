<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issues extends Model
{
    use SoftDeletes;

    public function issueTopic()
    {
        return $this->belongsTo(\App\Models\Categories::class, 'issue_topic_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Maillists extends Model
{
    protected $fillable = ['docuno, templates_id'];

    public function getdocuno(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Paymentmasters::class);
    }

    public function gettemplates(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Templates::class);
    }
}

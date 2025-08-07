<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Regions extends Model
{
    use Translatable;

    public $translatedAttributes = ['title', 'detail'];

    public function Lists(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lists::class);
    }
}

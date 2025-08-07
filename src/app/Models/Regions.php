<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    use Translatable;

    public $translatedAttributes = ['title', 'detail'];

    public function Lists(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lists::class);
    }
}

<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Products extends Model
{
    use Translatable;

    public $translatedAttributes = ['title', 'detail'];

    public function categories(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Categories::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use Translatable;

    public $translatedAttributes = ['title', 'detail'];

    public function categories(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Categories::class);
    }
}

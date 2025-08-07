<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medias extends Model
{
    use Translatable;

    protected $fillable = ['menu', 'active'];

    public $translatedAttributes = ['title'];

    public function gallerys(): HasMany
    {
        return $this->hasMany('App\Model\MediasGallery');
    }
}

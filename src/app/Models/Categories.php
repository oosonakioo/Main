<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use Translatable;

    public $translatedAttributes = ['title', 'detail'];
}

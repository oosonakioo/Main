<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Lists extends Model
{
    use Translatable;

    const SUBMENU = 'menu';

    protected $fillable = [Lists::SUBMENU, 'active'];

    public $translatedAttributes = ['title', 'detail'];
}

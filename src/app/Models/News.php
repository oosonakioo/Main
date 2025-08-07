<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Helper;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use Translatable;

    protected $fillable = ['active', 'pin_home_page'];

    public $translatedAttributes = ['title', 'detail'];

    public function getDateTimeFormat()
    {
        return Helper::datetime($this->news_date);
    }
}

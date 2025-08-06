<?php
namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Helper;

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

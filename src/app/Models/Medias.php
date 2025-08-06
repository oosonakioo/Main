<?php
namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Medias extends Model
{
    use Translatable;

    protected $fillable = ['menu', 'active'];
    public $translatedAttributes = ['title'];

    public function gallerys()
    {
    	return $this->hasMany('App\Model\MediasGallery');
    }
}

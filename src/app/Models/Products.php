<?php
namespace App\Models;

use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
	use Translatable;

	public $translatedAttributes = ['title', 'detail'];

	public function categories()
	{
		return $this->belongsTo('App\Models\Categories');
	}
}

<?php
namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
	use Translatable;

	public $translatedAttributes = ['title', 'detail'];

	public function Lists()
	{
		return $this->belongsTo(\App\Models\Lists::class);
	}
}

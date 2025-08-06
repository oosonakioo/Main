<?php
namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Contents extends Model
{
	use Translatable;

	const MENU = "menu";

	protected $fillable = [Contents::MENU];
	public $translatedAttributes = ['title', 'detail'];
}

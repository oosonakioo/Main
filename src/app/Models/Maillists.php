<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maillists extends Model
{

	protected $fillable = ['docuno, templates_id'];

	public function getdocuno()
	{
		return $this->belongsTo(\App\Models\Paymentmasters::class);
	}
	public function gettemplates()
	{
		return $this->belongsTo(\App\Models\Templates::class);
	}
}

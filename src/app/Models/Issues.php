<?php
namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issues extends Model
{
	use SoftDeletes;

    protected $dates = ['deleted_at'];

	public function issueTopic()
	{
    	return $this->belongsTo(\App\Models\Categories::class, 'issue_topic_id');
	}
}

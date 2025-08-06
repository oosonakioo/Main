<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paymentdetails extends Model
{
	protected $fillable = ['custcode_id', 'listno', 'rematotalamnt'];
}

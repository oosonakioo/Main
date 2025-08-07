<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paymentmasters extends Model
{
    protected $fillable = ['docuno', 'paymentstatus'];

    public function price(): HasMany
    {
        return $this->hasMany(\App\Models\Paymentdetails::class, 'docuno_id', 'docuno');
    }
}

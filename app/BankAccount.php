<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class BankAccount extends Model
{
    protected $hidden = ['id', 'user_id', 'created_at', 'updated_at'];

    protected $with = ['payments'];

    public function payment_requests()
    {
        return $this->hasMany('App\PaymentRequest');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    public function getIban()
    {
        return Crypt::decryptString($this->IBAN);
    }
}

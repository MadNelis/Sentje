<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    public function bank_account()
    {
        return $this->belongsTo('App\BankAccount');
    }

    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    public function pay_plans()
    {
        return $this->hasMany('App\PayPlan');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayPlan extends Model
{
    public function payment_request()
    {
        return $this->belongsTo('App\PaymentRequest');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scheduled_payments()
    {
        return $this->hasMany('App\ScheduledPayment');
    }

    public function payments()
    {
        return $this->belongsToMany('App\Payment', 'scheduled_payments');
    }
}

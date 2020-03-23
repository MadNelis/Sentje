<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduledPayment extends Model
{
    public function payment()
    {
        return $this->belongsTo('App\Payment');
    }

    public function pay_plan()
    {
        return $this->belongsTo('App\PayPlan');
    }
}

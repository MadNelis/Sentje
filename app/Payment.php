<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $hidden = ['id', 'image_path', 'payment_id', 'bank_account_id', 'user_id', 'payment_request_id', 'created_at', 'updated_at'];

    public function payment_request()
    {
        return $this->belongsTo('App\PaymentRequest');
    }
}

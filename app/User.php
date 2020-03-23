<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relations
    public function payment_requests()
    {
        return $this->hasManyThrough('App\PaymentRequest', 'App\BankAccount');
    }

    public function received_payment_requests()
    {
        return $this->belongsToMany('App\PaymentRequest', 'received_payment_requests')->withTimestamps();
    }

    public function pay_plans()
    {
        return $this->hasMany('App\PayPlan');
    }

    public function bank_accounts()
    {
        return $this->hasMany('App\BankAccount');
    }

    public function groups()
    {
        return $this->hasMany('App\FavoritesGroup');
    }

    public function contacts()
    {
        return $this->belongsToMany('App\User', 'user_contacts', 'user_id', 'contact_id');
    }
}

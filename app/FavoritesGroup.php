<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoritesGroup extends Model
{
    public function members()
    {
        return $this->belongsToMany('App\User', 'favorites_group_members');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}

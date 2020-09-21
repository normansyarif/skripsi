<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nodes extends Model
{
    protected $table = 'nodes';

    public function sensors()
    {
        return $this->hasMany('App\Sensors', 'node_id')->orderBy('name');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}

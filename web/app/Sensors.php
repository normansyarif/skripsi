<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sensors extends Model
{
    protected $table = 'sensors';
    public $timestamps = false;

    public function values()
    {
        return $this->hasMany('App\SensorValues', 'sensor_id')->orderBy('added_at');
    }

    public function annotations()
    {
        return $this->hasMany('App\Annotation', 'sensor_id');
    }

    public function node()
    {
        return $this->belongsTo('App\Nodes', 'node_id');
    }
}

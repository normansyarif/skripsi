<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SensorValues extends Model
{
	public $timestamps = false;
    protected $table = 'sensor_values';

    public function sensor()
    {
        return $this->belongsTo('App\Sensors', 'sensor_id');
    }
}

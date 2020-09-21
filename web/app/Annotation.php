<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Annotation extends Model
{
    protected $table = 'annotation';
    public $timestamps = false;

    public function sensor()
    {
        return $this->belongsTo('App\Sensors', 'sensor_id');
    }
}

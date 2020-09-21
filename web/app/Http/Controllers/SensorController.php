<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sensors;
use App\SensorValues;

class SensorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update(Request $req, $id) {
    	$sensor = Sensors::find($id);
    	$sensor->name = $req->input('name');
    	$sensor->unit = $req->input('unit');
        $sensor->is_notif = $req->input('is-notif');
        $sensor->last_notif = time();

        $stateArr = [];
        for($i = 1; $i <= 5; $i++) {
            $arr = [];
            $arr["name"] = $req->input('status-' . $i);
            $arr["limit"] = $req->input('limit-' . $i);
            $arr["color"] = $req->input('color-' . $i);
            array_push($stateArr, $arr);
        }

        $sensor->limit_values = json_encode($stateArr);

    	if($sensor->save()){
    		return redirect(route('node.view', $sensor->node_id))->with('success', 'Berhasil meng-update sensor');
    	}else{
    		return redirect(route('node.view', $sensor->node_id))->with('error', 'Gagal meng-update sensor');
    	}
    }

    public function post(Request $req) {
        $sensor = new Sensors;
        $sensor->name = $req->input('name');
        $sensor->unit = $req->input('unit');
        $sensor->is_notif = 0;
        $sensor->last_notif = time();
        $sensor->node_id = $req->input('node_id');

        $sensor->save();
        return redirect(route('node.view',$req->input('node_id')))->with('success', 'Berhasil menambah sensor');
    }

    public function delete(Request $req) {
        $sid = $req->input('sensor-id');
        $nid = $req->input('node-id');

        $sv = SensorValues::where('sensor_id', $sid);
        $sv->delete();

        $sensor = Sensors::find($sid);
        $sensor->delete();
        return redirect(route('node.view', $nid))->with('success', 'Berhasil menghapus sensor');
    }

    public function clear(Request $req) {
        $sid = $req->input('sensor-id-clear');
        $nid = $req->input('node-id-clear');

        $sv = SensorValues::where('sensor_id', $sid);
        $sv->delete();
        return redirect(route('node.view', $nid))->with('success', 'Berhasil menghapus data sensor');
    }
}

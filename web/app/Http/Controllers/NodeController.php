<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Nodes;
use App\Sensors;
use App\SensorValues;

class NodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['monitor', 'getLive']]);
    }

    public function view($id)
    {
    	$nodes = Nodes::where('user_id', auth()->user()->id)->orderByDesc('created_at')->get();
        $thisNode = Nodes::find($id);
        if($thisNode->user_id == auth()->user()->id) {
            return view('view')->with('nodes', $nodes)->with('thisNode', $thisNode);
        }else{
            return 'You don\'t have access to this node.';
        }
    }

    public function monitor($id)
    {
        $thisNode = Nodes::find($id);
        if($thisNode->access_type == 1) {
            return view('monitor')->with('thisNode', $thisNode);
        }else{
            if($thisNode->user_id == auth()->user()->id) {
                return view('monitor')->with('thisNode', $thisNode);
            }else{
                return 'This node is private';
            }
        }
    }

    public function dbMode($id)
    {
        $thisNode = Nodes::find($id);
        if($thisNode->access_type == 1) {
            return view('db_mode')->with('thisNode', $thisNode);
        }else{
            if($thisNode->user_id == auth()->user()->id) {
                return view('db_mode')->with('thisNode', $thisNode);
            }else{
                return 'This node is private';
            }
        }
    }

    public function geDb($node_id) {
        $data = [];
        $sensors = Sensors::where('node_id', $node_id)->get();
        foreach ($sensors as $sensor) {
            $sv = SensorValues::where('sensor_id', $sensor->id)->latest('added_at')->first();

            $lastValue = $sv->value;
            $limit = json_decode($sensor->limit_values);
            $status = $color = "";
            $one = $two = $three = $four = false;

            if($lastValue < $limit[0]->limit) {
                $status = $limit[0]->name;
                $color = $limit[0]->color;
            }else{
                if($lastValue >= $limit[1]->limit) {
                    $status = $limit[1]->name;
                    $color = $limit[1]->color;
                    if($limit[1]->limit == $limit[2]->limit) $one = true;
                }

                if($lastValue >= $limit[2]->limit) {
                    $status = $limit[2]->name;
                    $color = $limit[2]->color;
                    if($limit[2]->limit == $limit[3]->limit) $two = true;
                }

                if($lastValue >= $limit[3]->limit) {
                    $status = $limit[3]->name;
                    $color = $limit[3]->color;
                    if($limit[3]->limit == $limit[4]->limit) $three = true;
                }

                if($lastValue >= $limit[4]->limit){
                    $status = $limit[4]->name;
                    $color = $limit[4]->color;
                    $four = true;
                }   
            }

            if($one) {
                $status = $limit[1]->name;
                $color = $limit[1]->color;
            }else if($two) {
                $status = $limit[2]->name;
                $color = $limit[2]->color;
            }else if($three) {
                $status = $limit[3]->name;
                $color = $limit[3]->color;
            }else if($four) {
                $status = $limit[4]->name;
                $color = $limit[4]->color;
            }

            $dataSensor = [];
            $dataSensor['id'] = $sensor->id;
            $dataSensor['last_value'] = $sv->value;
            $dataSensor['status_name'] = $status;
            $dataSensor['status_color'] = $color;

            array_push($data, $dataSensor);
        }
        return $data;
    }

    public function post(Request $req) {
    	$node = new Nodes;
    	$node->name = $req->input('name');
    	$node->description = $req->input('description');
    	$node->access_type = $req->input('access_type');
    	$node->user_id = auth()->user()->id;
        $node->api_key = md5(microtime().rand());
        if($node->save()){
            return redirect(route('dashboard'))->with('success', 'Berhasil membuat node');
        }else{
            return redirect(route('dashboard'))->with('error', 'Gagal membuat node');
        }
    }

    public function update(Request $req, $id) {
        $node = Nodes::find($id);
        if($node->user_id == auth()->user()->id) {
            $node->name = $req->input('name');
            $node->description = $req->input('description');
            $node->access_type = $req->input('access_type');
            if($node->save()){
                return redirect(route('node.view', $id))->with('success', 'Berhasil mengubah node');
            }else{
                return redirect(route('node.view', $id))->with('error', 'Gagal mengubah node');
            }
        }else{
            return 'You do not have access to this node';
        }
    }

    public function clear($id) {
        $node = Nodes::find($id);
        if($node->user_id == auth()->user()->id) {
            $sensors = Sensors::where('node_id', $id)->get();
            foreach($sensors as $sensor) {
                $values = SensorValues::where('sensor_id', $sensor->id);
                $values->delete();
            }
            return redirect(route('node.view', $id))->with('success', 'Berhasil menghapus data sensor');    
        }else{
            return 'You do not have access to this node';
        }
        
    }

    public function delete($id) {
        $node = Nodes::find($id);
        if($node->user_id == auth()->user()->id) {
            $sensors = Sensors::where('node_id', $id);
            foreach($sensors->get() as $sensor) {
                $values = SensorValues::where('sensor_id', $sensor->id);
                $values->delete();
            }
            $sensors->delete();
            $node->delete();
            return redirect(route('dashboard'))->with('success', 'Berhasil menghapus node');
        }else{
            return 'You do not have access to this node.';
        }
    }

    //----------------------
    //---- Thsese following methods are called by ajax
    //-----------------------

    public function getLive($node_id, $start_time) {
        $node = Nodes::find($node_id);

        if($node->access_type == 2) {
            if($node->user_id != auth()->user()->id) {
                return 'Not allowed';
            }       
        }

        $arr = [];
        $arr[0] = 0;
        $arr[1] = [];
        $sensors = Sensors::where('node_id', $node_id)->get();
        foreach($sensors as $sensor) {
            $values = SensorValues::where('sensor_id', $sensor->id)
            ->where('added_at', '>', $start_time)
            ->get();
            foreach($values as $value) {

                $lastValue = $value->value;
                $limit = json_decode($value->sensor->limit_values);
                $status = $color = "";
                $one = $two = $three = $four = false;

                if($lastValue < $limit[0]->limit) {
                    $status = $limit[0]->name;
                    $color = $limit[0]->color;
                }else{
                    if($lastValue >= $limit[1]->limit) {
                        $status = $limit[1]->name;
                        $color = $limit[1]->color;
                        if($limit[1]->limit == $limit[2]->limit) $one = true;
                    }

                    if($lastValue >= $limit[2]->limit) {
                        $status = $limit[2]->name;
                        $color = $limit[2]->color;
                        if($limit[2]->limit == $limit[3]->limit) $two = true;
                    }

                    if($lastValue >= $limit[3]->limit) {
                        $status = $limit[3]->name;
                        $color = $limit[3]->color;
                        if($limit[3]->limit == $limit[4]->limit) $three = true;
                    }

                    if($lastValue >= $limit[4]->limit){
                        $status = $limit[4]->name;
                        $color = $limit[4]->color;
                        $four = true;
                    }   
                }

                if($one) {
                    $status = $limit[1]->name;
                    $color = $limit[1]->color;
                }else if($two) {
                    $status = $limit[2]->name;
                    $color = $limit[2]->color;
                }else if($three) {
                    $status = $limit[3]->name;
                    $color = $limit[3]->color;
                }else if($four) {
                    $status = $limit[4]->name;
                    $color = $limit[4]->color;
                }

                $itemArray['sensor_id'] = $value->sensor_id;
                $itemArray['label'] = date('d-m-Y H:i', $value->added_at);
                $itemArray['data'] = $value->value;
                $itemArray['added_at'] = $value->added_at;
                $itemArray['status_name'] = $status;
                $itemArray['status_color'] = $color;
                array_push($arr[1], $itemArray);
            }
        }
        if(auth()->check()) {
            $arr[0] = count(auth()->user()->unreadNotifications);
        }
        return $arr;
    }
}

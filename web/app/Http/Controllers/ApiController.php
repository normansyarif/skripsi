<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Nodes;
use Mail;
use App\Sensors;
use App\User;
use App\SensorValues;
use App\Verification;
use App\Notifications\SensorNotif;

class ApiController extends Controller
{
    public function write(Request $req) {
        $node = Nodes::where('api_key', $req->input('key'))->get()->toArray();
        if(count($node) > 0) {
            $nid = $node[0]['id'];
            $sensorsForThisNode = Sensors::select('id')->where('node_id', $nid)->get()->toArray();
            $allowedSensors = array_column($sensorsForThisNode, 'id');
            $returnArray = array();

            foreach($_GET as $k => $v) {
                if($k != 'key') {
                    if(in_array($k, $allowedSensors)) {
                        $sensor = Sensors::find($k);
                        if($sensor->status == 1) {
                            $sv = new SensorValues;
                            $sv->sensor_id = $k;
                            $sv->value = $v;
                            $sv->added_at = time();
                            $sv->save();

                            // Cek status
                            $lastValue = $sv->value;
                            $limit = json_decode($sv->sensor->limit_values);
                            $status = $color = "";
                            $statusIndex = 0;
                            $one = $two = $three = $four = false;

                            if($lastValue < $limit[0]->limit) {
                                $status = $limit[0]->name;
                                $color = $limit[0]->color;
                                $statusIndex = 0;
                            }else{
                                if($lastValue >= $limit[1]->limit) {
                                    $status = $limit[1]->name;
                                    $color = $limit[1]->color;
                                    $statusIndex = 1;
                                    if($limit[1]->limit == $limit[2]->limit) $one = true;
                                }

                                if($lastValue >= $limit[2]->limit) {
                                    $status = $limit[2]->name;
                                    $color = $limit[2]->color;
                                    $statusIndex = 2;
                                    if($limit[2]->limit == $limit[3]->limit) $two = true;
                                }

                                if($lastValue >= $limit[3]->limit) {
                                    $status = $limit[3]->name;
                                    $color = $limit[3]->color;
                                    $statusIndex = 3;
                                    if($limit[3]->limit == $limit[4]->limit) $three = true;
                                }

                                if($lastValue >= $limit[4]->limit){
                                    $status = $limit[4]->name;
                                    $color = $limit[4]->color;
                                    $statusIndex = 4;
                                    $four = true;
                                }   
                            }

                            if($one) {
                                $status = $limit[1]->name;
                                $color = $limit[1]->color;
                                $statusIndex = 1;
                            }else if($two) {
                                $status = $limit[2]->name;
                                $color = $limit[2]->color;
                                $statusIndex = 2;
                            }else if($three) {
                                $status = $limit[3]->name;
                                $color = $limit[3]->color;
                                $statusIndex = 3;
                            }else if($four) {
                                $status = $limit[4]->name;
                                $color = $limit[4]->color;
                                $statusIndex = 4;
                            }

                            if($sv->sensor->is_notif == 1 && time() >= ($sv->sensor->last_notif + 600) ) {
                                $oldStatus = $sv->sensor->last_status;
                                if($oldStatus == null || $statusIndex != $oldStatus) {
                                    $user = User::find($sv->sensor->node->user->id);
                                    // Kirim notifikasi web
                                    $user->notify(new SensorNotif(
                                        $sv->sensor->id, 
                                        $sv->sensor->node->name, 
                                        $sv->sensor->name, 
                                        $status, 
                                        $sv->value, 
                                        $sv->sensor->unit));

                                    // App pass: hggacsurywcncmzy

                                    // Kirim notifikasi email
                                    if($user->email_verified == 1) {
                                        $this->sendEmail(
                                            $user->email, 
                                            $sv->added_at, 
                                            'http://' . request()->getHttpHost() . '/view/' . $sv->sensor->node->id, 
                                            $sv->sensor->node->name, 
                                            $sv->sensor->id, 
                                            $sv->sensor->name, 
                                            $status, 
                                            $sv->value, 
                                            $sv->sensor->unit);
                                    }

                                    // Kirim notifikasi sms
                                    if($user->phone_verified == 1) {
                                        $this->sendSms($user->phone, "Status sensor " . $sv->sensor->name . " pada node " . $sv->sensor->node->name . " telah berubah menjadi " . $status . ". Nilai saat ini adalah " . $sv->value . ". " . request()->getHttpHost() . "/view/" . $sv->sensor->node->id);
                                    }

                                    $sensor = Sensors::find($sv->sensor->id);
                                    $sensor->last_status = $statusIndex;
                                    $sensor->last_notif = time();
                                    $sensor->save();
                                }
                            }

                            $message = "OK";
                            $time = $sv->added_at;


                        }else{
                            $message = 'Recording is paused';
                            $time = time();
                        }
                    }else{
                        $message = 'Not allowed';
                        $time = time();
                    }
                    $sensorArray['sensor_id'] = $k;
                    $sensorArray['data'] = $v;
                    $sensorArray['added_at'] = $time;
                    $sensorArray['status'] = $message;
                    array_push($returnArray, $sensorArray);
                }
            }
            return $returnArray;
        }else{
            return 'Invalid key';
        }
    }

    public function read(Request $req) {
        $arr = [];
        $node = Nodes::where('api_key', $req->input('key'))->get()->toArray();
        if(count($node) > 0) {
            $nid = $node[0]['id'];
            $sensors = Sensors::where('node_id', $nid)->get();

            if(!empty($req->input('sensor'))) {
                $allowedSensors = array_column($sensors->toArray(), 'id');
                if(in_array($req->input('sensor'), $allowedSensors)) {
                    $sensors = Sensors::where('id', $req->input('sensor'))->get();
                }else{
                    return 'This API key has no access to sensor with ID ' . $req->input('sensor');
                }
            }
            
            foreach($sensors as $sensor) {
                $values = SensorValues::query();
                $values = $values->where('sensor_id', $sensor->id);
                if(!empty($req->input('start')) && !empty($req->input('end'))) {
                    if(is_numeric($req->input('start')) && is_numeric($req->input('end'))) {
                        $values = $values->where('added_at', '>=', $req->input('start'));
                        $values = $values->where('added_at', '<=', $req->input('end'));
                    }else{
                        $values = $values->where('added_at', '>=', strtotime($req->input('start')));
                        $values = $values->where('added_at', '<=', strtotime($req->input('end')));
                    }
                }

                if(!empty($req->input('limit')) && is_numeric($req->input('limit'))) {
                    $values = $values->take($req->input('limit'));
                }

                $values = $values->orderBy('added_at', 'desc')->get();

                foreach($values as $value) {
                    $data['sensor_id'] = $sensor->id;
                    $data['value'] = $value->value;
                    $data['time'] = $value->added_at;
                    array_push($arr, $data);
                }
            }
        }else{
            return 'Invalid key';
        }
        return $arr;
    }

    private function sendEmail($email, $time, $link, $node_name, $sensor_id, $sensor_name, $status_name, $value, $unit)
    {
        try{
            Mail::send('email-template', ['link' => $link , 'node_name' => $node_name, 'sensor_id' => $sensor_id, 'sensor_name' => $sensor_name, 'time' => $time, 'status_name' => $status_name, 'value' => $value, 'unit' => $unit], function ($message) use ($email, $sensor_name)
            {
                $message->subject('Status sensor ' . $sensor_name . ' telah berubah');
                $message->from('normansyarif.27@gmail.com', 'Sistem Monitoring');
                $message->to($email);
            });
            return response (['status' => true,'success' => 'Berhasil']);
        }
        catch (Exception $e){
            return response (['status' => false,'errors' => $e->getMessage()]);
        }
    }

    private function sendSms($number, $text) {

        $fields_string  =   "";
        $fields     =   array(
            'api_key'       =>  'fa801909',
            'api_secret'    =>  'zDKS9pxbsvDqqi7z',
            'to'            =>  '+62' . $number,
            'from'          =>  "SI MONITORING",
            'text'          =>  $text . '-----------------------------'
        );
        $url        =   "https://rest.nexmo.com/sms/json";

        //url-ify the data for the POST
        foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
        }
        rtrim($fields_string, '&');

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        $result = curl_exec($ch);
        curl_close($ch);

        echo "<pre>";
        print_r($result); 
        echo "</pre>";
    }

    public function clearVerification() {
        $ver = Verification::where('expired', '<', time());
        $ver->delete();
        return 'ok';
    }

    public function test() {
        return 'for testing only';
    }

}

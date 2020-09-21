<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Sensors;
use Illuminate\Support\Facades\Hash;
use App\Verification;
use Mail;
use App\Annotation;
use App\SensorValues;

class PagesController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function editProfile() {
    	return view('settings');
    }

    //----------------
    // Settings methods
    //-----------------
    public function editUpdate(Request $req) {
    	$user = User::find(auth()->user()->id);
    	$user->name = $req->input('inputName');
    	if($user->email != $req->input('inputEmail')) {
    		$user->email_verified = 0;
    	}
        if($user->phone != $req->input('inputPhone')) {
            $user->phone_verified = 0;
        }
    	$user->email = $req->input('inputEmail');
        $user->phone = $req->input('inputPhone');
    	$user->save();
    	return redirect(route('profile.edit'))->with('success', 'Berhasil mengubah data.');
    }

    public function passwordUpdate(Request $req) {
    	$user = User::find(auth()->user()->id);

    	if(empty($req->input('old-pass')) || empty($req->input('new-pass')) || empty($req->input('confirm-pass'))) {
    		return redirect(route('profile.edit'))->with('error', 'Password lama, password baru, dan konfirmasi password harus diisi.');
    	}

    	if(!Hash::check($req->input('old-pass'), $user->password)) {
    		return redirect(route('profile.edit'))->with('error', 'Password lama salah.');
    	}

    	if($req->input('new-pass') != $req->input('confirm-pass')) {
    		return redirect(route('profile.edit'))->with('error', 'Password baru tidak cocok dengan konfirmasi password.');
    	}

    	$user->password = Hash::make($req->input('new-pass'));
    	$user->save();
        return redirect(route('profile.edit'))->with('success', 'Berhasil mengubah password.');
    }


    //-------------------
    // Notification methods
    //-------------------
    public function notifIndex() {
        return view('notif');
    }

    public function markAsRead() {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function clearNotif() {
        auth()->user()->notifications()->delete();
    }

    //-----------------
    // Update sensor recording/paused
    //--------------------
    public function changeSensorStatus($id, $status) {
        // 1 = recording
        // 2 = paused
        $sensor = Sensors::find($id);
        if($sensor->node->user->id == auth()->user()->id) {
            $sensor->status = $status;
            $sensor->save();
        }
    }


    //---------------------
    // Verification methods
    //--------------------
    public function generateVerificationMail() {
        $rand = rand(0, 9999);
        $rand = sprintf('%04d', $rand);

        $ver = new Verification;
        $ver->user_id = auth()->user()->id;
        $ver->type = "email";
        $ver->expired = time() + 86400;
        $ver->code = $rand;
        $ver->save();
        $this->sendEmail($rand);
        echo "ok";
    }

    public function generateVerificationPhone() {
        $rand = rand(0, 9999);
        $rand = sprintf('%04d', $rand);

        $ver = new Verification;
        $ver->user_id = auth()->user()->id;
        $ver->type = "phone";
        $ver->expired = time() + 86400;
        $ver->code = $rand;
        $ver->save();

        $this->sendSms($rand);
    }

    public function checkVerificationCode(Request $req) {
        $ver = Verification::where('user_id', auth()->user()->id)
            ->where('code', $req->input('code'))
            ->where('expired', '>', time())
            ->first();
        if($ver != null) {
            $user = User::find(auth()->user()->id);
            if($ver->type == "email") {
                $user->email_verified = 1;
            }else{
                $user->phone_verified = 1;
            }
            $user->save();
            return redirect(route('profile.edit'))->with('success', 'Berhasil memverifikasi kontak info.');
        }else{
            return redirect(route('profile.edit'))->with('error', 'Kode verifikasi invalid atau sudah kadaluarsa');
        }
    }

    private function sendSms($code) {

        $fields_string  =   "";
        $fields     =   array(
            'api_key'       =>  'fa801909',
            'api_secret'    =>  'zDKS9pxbsvDqqi7z',
            'to'            =>  '+62' . auth()->user()->phone,
            'from'          =>  "SI MONITORING",
            'text'          =>  'Kode verifikasi anda adalah ' . $code . ' -----------------------------'
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
    }

    private function sendEmail($code)
    {
        try{
            Mail::send('email-template-verification', ['code' => $code], function ($message) use ($code)
            {
                $message->subject('Konfirmasi Email');
                $message->from('normansyarif.27@gmail.com', 'Sistem Monitoring');
                $message->to(auth()->user()->email);
            });
            return response (['status' => true,'success' => 'Berhasil']);
        }
        catch (Exception $e){
            return response (['status' => false,'errors' => $e->getMessage()]);
        }
    }

    // Finish verification

    public function getNotif() {
        return view('includes.notif-content');
    }

    //------------
    // Annotations method
    //--------------
    public function annotationPost(Request $req) {
        $ann = new Annotation;
        $ann->sensor_id = $req->input('sensor_id');
        $ann->value = $req->input('value');
        $ann->label = $req->input('label');
        $ann->cur_sensor_val = $req->input('sensor_val');
        $ann->save();
        return redirect()->back()->with('success', 'Berhasil menambah event');
    }

    public function annotationIndex($id) {
        $sensor = Sensors::find($id);
        return view('annotation')->with('sensor', $sensor);
    }

    public function annotationDelete($id) {
        $ann = Annotation::find($id);
        $ann->delete();
        return redirect()->back()->with('success', 'Berhasil menghapus event');
    }

    public function bar() {
        $sv = SensorValues::where('sensor_id', 24)->where('added_at', '<', 1582772401);
        $sv->update(["sensor_id" => 30]);
    }
}

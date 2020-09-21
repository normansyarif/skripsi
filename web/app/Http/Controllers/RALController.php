<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sensors;

class RALController extends Controller
{
    private function flip($arr) {
        $out = array();
        foreach ($arr as $key => $subarr) {
                foreach ($subarr as $subkey => $subvalue) {
                     $out[$subkey][$key] = $subvalue;
                }
        }
        return $out;
    }

    public function result(Request $req) {

    	$col = [];

    	$data = json_decode($req->input('data'));
        $varTerikat = $req->input('var-terikat');
        $varBebas = $req->input('var-bebas');
        $data = $this->flip($data);

    	$jumlahPerlakuan = count($data);
    	$jumlahUlangan = count($data[0]);

    	$sum = [];
    	foreach ($data as $item) {
    		array_push($sum, array_sum($item));
    	}

    	$avg = [];
    	for($i = 0; $i < count($data); $i++) {
    		array_push($avg, $sum[$i]/$jumlahUlangan);
    	}

    	// Faktor koreksi
    	$fk = pow(array_sum($sum), 2) / ($jumlahUlangan * $jumlahPerlakuan);

    	$dbPerlakuan = $jumlahPerlakuan - 1;
    	$dbGalat = $jumlahPerlakuan * ($jumlahUlangan - 1);
    	$dbTotal = ($jumlahPerlakuan * $jumlahUlangan) - 1;


    	// Jumlah kuadrat total
    	$jktArr = [];
    	$jktTemp = call_user_func_array('array_merge', $data);
    	foreach ($jktTemp as $temp) {
    		array_push($jktArr, pow($temp, 2));
    	}

    	$jkt = array_sum($jktArr) - $fk;

    	// Jumlah kuadrat perlakuan
	    $jkpArr = [];
	    foreach ($sum as $item) {
	    	array_push($jkpArr, pow($item, 2));
	    }

	    $jkp = (array_sum($jkpArr) / $jumlahUlangan) - $fk;

	    // Jumlah kuadrat galat
	    $jkg = $jkt - $jkp;

		// Jumlah tengah perlakuan
		$ktp = $jkp / ($jumlahPerlakuan - 1);

		// Kuadrat tengah galat
		$ktg = $jkg/$dbGalat;

		// F hitung
		$fhit = $ktp/$ktg;

        $col['var_bebas'] = $varBebas;
        $col['var_terikat'] = $varTerikat;
		$col['jumlah_perlakuan'] = $jumlahPerlakuan;
    	$col['jumlah_ulangan'] = $jumlahUlangan;
    	$col['total'] = $sum;
    	$col['rata_rata'] = $avg;
    	$col['faktor_koreksi'] = $fk;
    	$col['db_perlakuan'] = $dbPerlakuan;
    	$col['db_galat'] = $dbGalat;
    	$col['db_total'] = $dbTotal;
    	$col['jumlah_kuadrat_perlakuan'] = $jkp;
    	$col['jumlah_kuadrat_galat'] = $jkg;
    	$col['jumlah_kuadrat_total'] = $jkt;
    	$col['kuadrat_tengah_perlakuan'] = $ktp;
    	$col['kuadrat_tengah_galat'] = $ktg;
    	$col['f_hitung'] = $fhit;
        return view('result-ral')->with('col', $col);
    }

    public function selectData($id) {
        $sensor = Sensors::find($id);
        
        $baseVal = [];
        $baseVal['name'] = $sensor->name;
        $baseVal['node'] = $sensor->node->name;
        $baseVal['values'] = [];
        foreach($sensor->annotations as $ann) {
            array_push($baseVal['values'], $ann->value);
        }

        $moreVal = [];
    
        $sensorVals = Sensors::whereHas('node', function($node) {
            $node->user_id = auth()->user()->id;
        })->get();
        foreach($sensorVals as $sens) {
            $temp = [];
            $temp['name'] = $sens->name;
            $temp['node'] = $sens->node->name;
            $temp['values'] = [];
            $temp['sensor_vals'] = [];
            foreach($sens->annotations as $sa) {
                array_push($temp['values'], $sa->value);
                array_push($temp['sensor_vals'], $sa->cur_sensor_val);
            }
            if($temp['values'] == $baseVal['values']) {
                array_push($moreVal, $temp);    
            }
            
        }
        
        return view('select-ral-data')
            ->with('sensor', $sensor)
            ->with('data', $moreVal);
    }

    
}

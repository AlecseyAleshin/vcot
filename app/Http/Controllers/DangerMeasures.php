<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DangerMeasures extends Controller
{

    function query(Request $requery){
        $input = json_decode($requery->getContent(), true);
        $id = $input['column1'];
        //'6419a838-509a-4833-9410-9e5b3dae5017';
        //print_r($input['column1']);
        $answer = $this->oldQuery($id);

        return $answer;
    }

    function index(Request $requery, $field){
        print $field;

    }

    function oldQuery($id){

        $a = DB::table('nsi.dct_ok_01694_data as prof')->
                join('workplace as w', 'prof.id', '=', 'w.ok01694')->
                leftjoin('hazard_identification as hi', 'w.id', '=', 'hi.workplace')->
                leftjoin('nsi.dct_danger_event_data as de', 'de.id', '=', 'hi.hazardEvent')->
                join('nsi.dct_danger_data as d', 'd.id', '=', 'hi.dangerous')->
                join('nsi.dct_place_work_2_data as pw2', 'pw2.id', '=', 'hi.dangerPart')->
                join('settlement as s', 'hi.id', '=', 's.hazard')->
                join('additional_settlement as as', 's.id', '=', 'as.settlement')->
                join('nsi.dct_risk_management_data as rm', 'rm.id', '=', 'as.value')->
                select('prof.profession_name', 'de.name as event', 'd.name as danger', 'pw2.name as istoch', 'rm.name as management')->
                where('prof.id', $id)->
                distinct(['de.name', 'd.name'])->get();
        foreach($a as $key=>$val){
            $proc = number_format(rand(0, 10000) / 1000, 2, '.', '');
            $ans[] = [
                'profession' => $val->profession_name,
                'place' => $val->istoch,
                'danger' => $val->danger,
                'event' => $val->event,
                'management' => $val->management,
                'percent' => $proc,
                'check' => $proc > 5 ? true : false
            ];
        }


        return $ans;

    }
}

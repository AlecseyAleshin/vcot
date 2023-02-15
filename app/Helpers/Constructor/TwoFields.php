<?php

namespace App\Helpers\Constructor;

use Illuminate\Support\Facades\DB;


class TwoFields
{
    function __construct()
    {
        $this->field = array();
    }

    public function Two($input){
        //print_r($input);
        foreach($input as $key=>$value){
            $ans = $this->fieldCollection($key,$value);
        }
        if($ans[0][0] === 'employersName' && $ans[1][0] === 'workplacesProfessionOK'){
            $answer = $this->employerProf($ans);
        }elseif($ans[0][0] === 'workplacesProfessionOK' && $ans[1][0] === 'dangersDanger'){
            $answer = $this->professionDanger($ans);
        }elseif($ans[0][0] === 'employersName' && $ans[1][0] === 'dangersDanger'){
            $answer = $this->employerDanger($ans);
        }elseif($ans[0][0] === 'employersName' && $ans[1][0] === 'dangersDangerousEvent'){
            $answer = $this->employerDangerEvent($ans);
        }elseif($ans[0][0] === 'employersOKVED' && $ans[1][0] === 'workplacesProfessionOK'){
            $answer = $this->employerOkvedProf($ans);
        }
        return $answer;
    }

    public static function instance(){
        //return;
        return new TwoFields;
    }

    public function fieldCollection($key, $value){
        $this->field[] = [$key, $value];
        return $this->field;
    }

    function employerProf($ans){
        if($ans[0][1] > "" && $ans[1][1] === ""){
            //print $ans[0][1];
            $answer = DB::table('workplace as w')->
                        leftjoin('employer as e', 'w.employer', '=' ,'e.id')->
                        leftjoin('nsi.dct_ok_01694_data as o', 'o.id', '=', 'w.ok01694')->
                        //select('o.profession_name', 'w.name as work')->get();
                        where('w.employer', '=', $ans[0][1])->
                        orderBy('o.profession_name')->distinct('o.profession_name')->
                        select('e.shortName as employersName','o.profession_name as workplacesProfessionOK')->get();

        }elseif($ans[0][1] > "" && $ans[1][1] > ""){
            $answer = DB::table('workplace as w')->
                        leftjoin('employer as e', 'w.employer', '=' ,'e.id')->
                        leftjoin('nsi.dct_ok_01694_data as o', 'o.id', '=', 'w.ok01694')->
                        //select('o.profession_name', 'w.name as work')->get();
                        where('w.employer', '=', $ans[0][1])->where('o.id','=', $ans[1][1])->
                        orderBy('o.profession_name')->distinct('o.profession_name')->
                        select('e.shortName as employersName','o.profession_name as workplacesProfessionOK')->get();

        }
        if(count($answer) == 0){
            $answer[] = [
                'employersName' => 'Нет данных',
                'workplacesProfessionOK' => ''
            ];
        }
        return $answer;
    }

    function professionDanger($ans){
        if($ans[0][1] > "" && $ans[1][1] === ""){
            $answer = DB::table('hazard_identification as h')->
                        join('workplace as w', 'h.workplace', '=', 'w.id')->
                        join('nsi.dct_ok_01694_data as o', 'o.id', '=', 'w.ok01694')->
                        join('nsi.dct_danger_data as d', 'd.id', '=', 'h.dangerous')->
                        whereNull('h.deleted_at')->where('o.id', '=', $ans[0][1])->distinct('d.name')->
                        select('o.profession_name as workplacesProfessionOK', 'd.name as dangersDanger')->get();
        }elseif($ans[0][1] > "" && $ans[1][1] > ""){
            $answer = DB::table('hazard_identification as h')->
                        join('workplace as w', 'h.workplace', '=', 'w.id')->
                        join('nsi.dct_ok_01694_data as o', 'o.id', '=', 'w.ok01694')->
                        join('nsi.dct_danger_data as d', 'd.id', '=', 'h.dangerous')->
                        whereNull('h.deleted_at')->where('o.id', '=', $ans[0][1])->
                        where('d.id', '=', $ans[1][1])->distinct('d.name')->
                        select('o.profession_name as workplacesProfessionOK', 'd.name as dangersDanger')->get();
        }
        if(count($answer) == 0){
            $answer[] = [
                'workplacesProfessionOK' => 'Нет данных',
                'dangersDanger' => ''
            ];
        }
        return $answer;
    }

    function employerDanger($ans){
        //print '2';
        if($ans[0][1] > "" && $ans[1][1] === ""){
            $answer = DB::table('hazard_identification as h')->
                        join('workplace as w', 'h.workplace', '=', 'w.id')->
                        join('employer as e', 'w.employer', '=' , 'e.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        whereNull('h.deleted_at')->where('e.id', '=', $ans[0][1])->distinct('d.name')->
                        select('e.shortName as employersName', 'd.name as dangersDanger')->get();
        }elseif($ans[0][1] > "" && $ans[1][1] > ""){
            $answer = DB::table('hazard_identification as h')->
                        join('workplace as w', 'h.workplace', '=', 'w.id')->
                        join('employer as e', 'w.employer', '=' , 'e.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        whereNull('h.deleted_at')->where('e.id', '=', $ans[0][1])->
                        where('d.id', '=', $ans[1][1])->distinct('d.name')->
                        select('e.shortName as employersName', 'd.name as dangersDanger')->get();
        }
        if(count($answer) == 0){
            $answer[] = [
                'employersName' => 'Нет данных',
                'dangersDanger' => ''
            ];
        }
        //print_r($answer);
        return $answer;
    }

    function employerDangerEvent($ans){
        if($ans[0][1] > "" && $ans[1][1] === ""){
            $answer = DB::table('hazard_identification as h')->
                        join('workplace as w', 'h.workplace', '=', 'w.id')->
                        join('employer as e', 'w.employer', '=', 'e.id')->
                        join('nsi.dct_danger_event_data as d', 'h.hazardEvent', '=', 'd.id')->
                        whereNull('h.deleted_at')->where('e.id', '=', $ans[0][1])->distinct('d.name')->
                        select('e.shortName as employersName', 'd.name as dangersDangerousEvent')->get();
        }elseif($ans[0][1] > "" && $ans[1][1] > ""){
            $answer = DB::table('hazard_identification as h')->
                        join('workplace as w', 'h.workplace', '=', 'w.id')->
                        join('employer as e', 'w.employer', '=', 'e.id')->
                        join('nsi.dct_danger_event_data as d', 'h.hazardEvent', '=', 'd.id')->
                        whereNull('h.deleted_at')->where('e.id', '=', $ans[0][1])->
                        where('d.id', '=', $ans[1][1])->distinct('d.name')->
                        select('e.shortName as employersName', 'd.name as dangersDangerousEvent')->get();
        }
        if(count($answer) == 0){
            $answer[] = [
                'employersName' => 'Нет данных',
                'dangersDangerousEvent' => ''
            ];
        }
        return $answer;
    }

    function employerOkvedProf($ans){
        if($ans[0][1] > "" && $ans[1][1] === ""){
            $answer = DB::table('nsi.dct_okved_data as o')->
                        join('employer as e', 'e.okved', '=', 'o.id')->
                        join('workplace as w', 'e.id', '=', 'w.employer')->
                        join('nsi.dct_ok_01694_data as ok', 'ok.id', '=', 'w.ok01694')->
                        select('o.economic_activity as employersOKVED', 'ok.profession_name as workplacesProfessionOK')->
                        where('o.id', '=', $ans[0][1])->
                        distinct('ok.profession_name')->
                        get();
        }elseif($ans[0][1] > "" && $ans[1][1] > ""){
            $answer = DB::table('nsi.dct_okved_data as o')->
                        join('employer as e', 'e.okved', '=', 'o.id')->
                        join('workplace as w', 'e.id', '=', 'w.employer')->
                        join('nsi.dct_ok_01694_data as ok', 'ok.id', '=', 'w.ok01694')->
                        select('o.economic_activity as employersOKVED', 'ok.profession_name as workplacesProfessionOK')->
                        where('o.id', '=', $ans[0][1])->
                        where('ok.id', '=', $ans[1][1])->
                        distinct('ok.profession_name')->
                        get();
        }elseif($ans[0][1] === "" && $ans[1][1] > ""){
            $answer = DB::table('nsi.dct_okved_data as o')->
                        join('employer as e', 'e.okved', '=', 'o.id')->
                        join('workplace as w', 'e.id', '=', 'w.employer')->
                        join('nsi.dct_ok_01694_data as ok', 'ok.id', '=', 'w.ok01694')->
                        select('o.economic_activity as employersOKVED', 'ok.profession_name as workplacesProfessionOK')->
                        //where('o.id', '=', $ans[0][1])->
                        where('ok.id', '=', $ans[1][1])->
                        distinct('ok.profession_name')->
                        get();
        }
        return $answer;
    }
}

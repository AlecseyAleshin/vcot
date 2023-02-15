<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\EmployerInProject;
use App\Models\ExpertOrganization;
use App\Models\HazardIdentification;
use App\Models\Address;
use App\Models\EmployerStructure;
use App\Models\Workplace;
//ini_set('max_execution_time', 900);
//ini_set('memory_limit', '4096M');

class ReportConstructorGeneration extends Controller
{
    //
    function __construct()
    {
        $this->field =array();
    }
    function index(Request $request){
        $input = json_decode($request->getContent(), true);
        switch(count($input)) {
            case 1:

                break;
            case 2:
                $answer = \App\Helpers\Constructor\TwoFields::instance()->Two(json_decode($request->getContent(), true));
                //$answer = $this->twoField($input);
                return $answer;
                break;
            case 3:
                //$answer = \App\Helpers\Constructor\TwoFields::instance()->Two(json_decode($request->getContent(), true));
                $answer = $this->threeField($input);
                return $answer;
                break;
            default:
                return "No query";
                break;
        }
    }
/*
    function twoField($input){
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
        }
        return $answer;
    }
*/
    function threeField($input){
        foreach($input as $key=>$value){
            $ans = $this->fieldCollection($key, $value);
        }
        //print_r($ans);
        if($ans[0][0] === 'dangersObjectStudy' && $ans[1][0] === 'dangersDanger' && $ans[2][0] === 'dangersDangerousEvent'){
            $answer = $this->dangerObjectDangerEvent($ans);
        }elseif($ans[0][0] === 'dangersObjectStudy' && $ans[1][0] === 'dangersDangerousEvent' && $ans[2][0] === 'dangersDanger'){
            $answer = $this->dangerObjectEventDanger($ans);
        }
        //print_r($answer);
        return $answer;
    }

    function fieldCollection($key, $value){
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

    function dangerObjectDangerEvent($ans){
        //print_r($ans);
        if($ans[0][1] === "" && $ans[1][1] === "" && $ans[2][1] === ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
                        //print_r($answ);

            foreach($answ as $key=>$val){
                $answer[] = [
                    'dangersObjectStudy' => $val->work,
                    'dangersDanger' => $val->danger,
                    'dangersDangerousEvent' => $val->event
                ];
            }
        }elseif($ans[0][1] > "" && $ans[1][1] === "" && $ans[2][1] === ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        where('p.id', '=', $ans[0][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());

            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    //print_r($val);
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDanger' => $val->danger,
                        'dangersDangerousEvent' => $val->event
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDanger' => "",
                    'dangersDangerousEvent' => ""
                ];
            }
        }elseif($ans[0][1] > "" && $ans[1][1] > "" && $ans[2][1] === ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        where('p.id', '=', $ans[0][1])->
                        where('d.id', '=', $ans[1][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDanger' => $val->danger,
                        'dangersDangerousEvent' => $val->event
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDanger' => "",
                    'dangersDangerousEvent' => ""
                ];
            }
        }elseif($ans[0][1] > "" && $ans[1][1] > "" && $ans[2][1] > ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        where('p.id', '=', $ans[0][1])->
                        where('d.id', '=', $ans[1][1])->
                        where('e.id', '=', $ans[2][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDanger' => $val->danger,
                        'dangersDangerousEvent' => $val->event
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDanger' => "",
                    'dangersDangerousEvent' => ""
                ];
            }
        }elseif($ans[0][1] === "" && $ans[1][1] > "" && $ans[2][1] > ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        //where('p.id', '=', $ans[0][1])->
                        where('d.id', '=', $ans[1][1])->
                        where('e.id', '=', $ans[2][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDanger' => $val->danger,
                        'dangersDangerousEvent' => $val->event
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDanger' => "",
                    'dangersDangerousEvent' => ""
                ];
            }
        }elseif($ans[0][1] === "" && $ans[1][1] > "" && $ans[2][1] === ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        where('d.id', '=', $ans[1][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDanger' => $val->danger,
                        'dangersDangerousEvent' => $val->event
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDanger' => "",
                    'dangersDangerousEvent' => ""
                ];
            }
        }elseif($ans[0][1] === "" && $ans[1][1] === "" && $ans[2][1] > ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        where('e.id', '=', $ans[2][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDanger' => $val->danger,
                        'dangersDangerousEvent' => $val->event
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDanger' => "",
                    'dangersDangerousEvent' => ""
                ];
            }
        }elseif($ans[0][1] > "" && $ans[1][1] === "" && $ans[2][1] > ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        where('p.id', '=', $ans[0][1])->
                        //where('d.id', '=', $ans[1][1])->
                        where('e.id', '=', $ans[2][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDanger' => $val->danger,
                        'dangersDangerousEvent' => $val->event
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDanger' => "",
                    'dangersDangerousEvent' => ""
                ];
            }
        }
            $object = array_column($answer, 'dangersObjectStudy');
            $danger = array_column($answer, 'dangersDanger');
            $event = array_column($answer, 'dangersDangerousEvent');
            array_multisort($object, SORT_ASC, $answer);
        return $answer;
    }

    function dangerObjectEventDanger($ans){
        //print_r($ans);
        if($ans[0][1] === "" && $ans[1][1] === "" && $ans[2][1] === ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
                        //print_r($answ);

            foreach($answ as $key=>$val){
                $answer[] = [
                    'dangersObjectStudy' => $val->work,
                    'dangersDangerousEvent' => $val->event,
                    'dangersDanger' => $val->danger
                ];
            }
        }elseif($ans[0][1] > "" && $ans[1][1] === "" && $ans[2][1] === ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        where('p.id', '=', $ans[0][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());

            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    //print_r($val);
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDangerousEvent' => $val->event,
                        'dangersDanger' => $val->danger
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDangerousEvent' => "",
                    'dangersDanger' => ""
                ];
            }
        }elseif($ans[0][1] > "" && $ans[1][1] > "" && $ans[2][1] === ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        where('p.id', '=', $ans[0][1])->
                        where('e.id', '=', $ans[1][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDangerousEvent' => $val->event,
                        'dangersDanger' => $val->danger
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDangerousEvent' => "",
                    'dangersDanger' => ""
                ];
            }
        }elseif($ans[0][1] > "" && $ans[1][1] > "" && $ans[2][1] > ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        where('p.id', '=', $ans[0][1])->
                        where('d.id', '=', $ans[1][1])->
                        where('e.id', '=', $ans[2][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDangerousEvent' => $val->event,
                        'dangersDanger' => $val->danger
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDangerousEvent' => "",
                    'dangersDanger' => ""
                ];
            }
        }elseif($ans[0][1] === "" && $ans[1][1] > "" && $ans[2][1] > ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        //where('p.id', '=', $ans[0][1])->
                        where('e.id', '=', $ans[1][1])->
                        where('d.id', '=', $ans[2][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDangerousEvent' => $val->event,
                        'dangersDanger' => $val->danger
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDangerousEvent' => "",
                    'dangersDanger' => ""
                ];
            }
        }elseif($ans[0][1] === "" && $ans[1][1] > "" && $ans[2][1] === ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        where('e.id', '=', $ans[1][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDangerousEvent' => $val->event,
                        'dangersDanger' => $val->danger
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDangerousEvent' => "",
                    'dangersDanger' => ""
                ];
            }
        }elseif($ans[0][1] === "" && $ans[1][1] === "" && $ans[2][1] > ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        where('d.id', '=', $ans[2][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDangerousEvent' => $val->event,
                        'dangersDanger' => $val->danger
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDangerousEvent' => "",
                    'dangersDanger' => ""
                ];
            }
        }elseif($ans[0][1] > "" && $ans[1][1] === "" && $ans[2][1] > ""){
            $answ = collect(DB::table('hazard_identification as h')->
                        join('nsi.dct_place_work_data as p', 'h.workplacePart', '=', 'p.id')->
                        join('nsi.dct_danger_data as d', 'h.dangerous', '=', 'd.id')->
                        join('nsi.dct_danger_event_data as e', 'h.hazardEvent', '=', 'e.id')->
                        whereNull('deleted_at')->
                        whereNull('d.closed')->
                        whereNull('p.closed')->
                        whereNull('e.closed')->
                        //pluck('p.name as work', 'd.name'));
                        where('p.id', '=', $ans[0][1])->
                        //where('d.id', '=', $ans[1][1])->
                        where('d.id', '=', $ans[2][1])->
                        select('p.name as work', 'd.name as danger', 'e.name as event')->
                        orderBy('danger')->distinct('danger')->get());
            if(count($answ)>0){
                foreach($answ as $key=>$val){
                    $answer[] = [
                        'dangersObjectStudy' => $val->work,
                        'dangersDangerousEvent' => $val->event,
                        'dangersDanger' => $val->danger
                    ];
                }
            }else{
                $answer[] = [
                    'dangersObjectStudy' => "Нет данных",
                    'dangersDangerousEvent' => "",
                    'dangersDanger' => ""
                ];
            }
        }
            $object = array_column($answer, 'dangersObjectStudy');
            $danger = array_column($answer, 'dangersDanger');
            $event = array_column($answer, 'dangersDangerousEvent');
            array_multisort($object, SORT_ASC, $answer);
        return $answer;
    }
}

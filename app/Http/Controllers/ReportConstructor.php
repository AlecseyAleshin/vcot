<?php

namespace App\Http\Controllers;

use App\Helpers\TwoFields as HelpersTwoFields;
use Illuminate\Http\Request;
use App\Models\Employer;
use Illuminate\Support\Facades\DB;
use App\Models\EmployerInProject;
use App\Models\ExpertOrganization;
use App\Models\HazardIdentification;
use App\Models\Address;
use App\Models\EmployerStructure;
use App\Models\Workplace;


use function Symfony\Component\Translation\t;

ini_set('max_execution_time', 900);
ini_set('memory_limit', '4096M');

class ReportConstructor extends Controller
{
    function __construct()
    {
        $this->field = [
            'employersName' => ['id', 'employersName'],
            'employersRegion' => ['id', 'employersRegion'],
            'employersOKVED' => ['id', 'employersOKVED'],
            'workplacesProfessionOK' => ['id', 'workplacesProfessionOK'],
            'workplacesNameStructure' => ['id', 'workplacesNameStructure'],
            'workplacesPMNumber' => ['id', 'workplacesPMNumber'],
            'dangersObjectStudy' => ['id', 'dangersObjectStudy'],
            'dangersSourceDanger' => ['id', 'dangersSourceDanger'],
            'dangersDanger' => ['id', 'dangersDanger'],
            'dangersDangerousEvent' => ['id', 'dangersDangerousEvent'],
            'dangersLevelRisk' => ['id', 'dangersLevelRisk'],
            'dangersLevelResidualRisk' => ['id', 'dangersLevelResidualRisk'],
            'controlMeasuresTypesMeasure' => ['id', 'controlMeasuresTypesMeasure'],
            'controlMeasuresContentMeasure' => ['id', 'controlMeasuresContentMeasure']
        ];
    }

    function index(Request $request, $field, $name = ""){
        $request->input('value') ? $name = $request->input('value'): $name = "";
        //print $name;

        switch($field){
            case 'employersName':
                $answ = $this->employersName();
                $field = $this->field[$field];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'employersRegion':
                $answ = $this->employersRegion();
                $field = $this->field[$field];
                //print_r($field);
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'employersOKVED':
                $answ = $this->employersOKVED();
                $field = $this->field['employersOKVED'];
                //print_r($field[0]);
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'workplacesProfessionOK':
                $answ = $this->workplacesProfessionOK();
                $field = $this->field['workplacesProfessionOK'];
                //print_r($field[0]);
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'workplacesNameStructure':
                $answ = $this->workplace();
                $field = $this->field['workplacesNameStructure'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'workplacesPMNumber':
                $answ = $this->workplacesPMNumber();
                $field = $this->field['workplacesPMNumber'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'dangersObjectStudy':
                $answ = $this->dangersObjectStudy();
                $field = $this->field['dangersObjectStudy'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'dangersSourceDanger':
                $answ = $this->dangersSourceDanger();
                $field = $this->field['dangersSourceDanger'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'dangersDanger':
                $answ = $this->dangersDanger();
                $field = $this->field['dangersDanger'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'dangersDangerousEvent':
                $answ = $this->dangersDangerousEvent();
                $field = $this->field['dangersDangerousEvent'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'dangersLevelRisk':
                $answ = $this->dangersLevelRisk();
                $field = $this->field['dangersLevelRisk'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'dangersLevelResidualRisk':
                $answ = $this->dangersLevelResidualRisk();
                $field = $this->field['dangersLevelResidualRisk'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'controlMeasuresTypesMeasure':
                $answ = $this->controlMeasuresTypesMeasure();
                $field = $this->field['controlMeasuresTypesMeasure'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            case 'controlMeasuresContentMeasure':
                $answ = $this->controlMeasuresContentMeasure();
                $field = $this->field['controlMeasuresContentMeasure'];
                if($name === 'all'){
                    $answer = $this->findName($answ, $name, $field);
                }else{
                    $answer = $this->findName($answ, $name, $field);
                }
                return $answer;
                break;
            default:
                die('No object!');
        }
    }

    function employersName(){
        return Employer::whereNull('deleted_at')->orderBy('shortName')->pluck('shortName','id');
    }

    function employersRegion(){
        return Address::whereNull('deleted_at')->whereNotNull('workplace')->orderBy('regionName')->pluck('regionName','id');
    }

    function employersOKVED(){
        //return DB::table('nsi.dct_okved_data as o')->pluck('economic_activity', 'id');
        return DB::table('nsi.dct_okved_data as o')->join('employer as e', 'o.id', '=', 'e.okved')->pluck('o.economic_activity', 'o.id');
    }

    function workplacesProfessionOK(){
        return DB::table('nsi.dct_ok_01694_data as o')->whereNull('closed')->join('workplace as w', 'o.id', '=', 'w.ok01694')->orderBy('o.profession_name')->pluck('o.profession_name', 'o.id');
    }

    function workplace(){
        return Workplace::whereNull('deleted_at')->orderBy('name')->pluck('name', 'id');
    }

    function workplacesPMNumber(){
        return Workplace::whereNull('deleted_at')->pluck('code','id');
    }

    function dangersObjectStudy(){
        return DB::table('nsi.dct_place_work_data')->pluck('name', 'id');
    }

    function dangersSourceDanger(){
        return DB::table('nsi.dct_types_work_data')->whereNull('closed')->pluck('name', 'id');
    }

    function dangersDanger(){
        return DB::table('nsi.dct_danger_data')->whereNull('closed')->pluck('name', 'id');
    }

    function dangersDangerousEvent(){
        return DB::table('nsi.dct_danger_event_data')->whereNull('closed')->pluck('name', 'id');
    }

    function dangersLevelRisk(){
        return HazardIdentification::whereNull('deleted_at')->orderBy('current_hazard_level')->distinct('current_hazard_level')->pluck('current_hazard_level', 'id');
    }

    function dangersLevelResidualRisk(){
        return HazardIdentification::whereNull('deleted_at')->orderBy('residual_hazard_level')->distinct('residual_hazard_level')->pluck('residual_hazard_level', 'id');
    }

    function controlMeasuresTypesMeasure(){
        return DB::table('nsi.dct_risk_management_data')->orderBy('type_management')->distinct('type_management')->pluck('type_management', 'id');
    }

    function controlMeasuresContentMeasure(){
        return DB::table('nsi.dct_risk_management_data')->orderBy('name')->pluck('name', 'id');
    }

    function findName($answer, $name, $field){
        $ss = array();
        if($name != 'all'){

            foreach($answer as $key=>$val){
                $str = preg_replace('/[^a-zA-ZА-Яа-я0-9.,:;?!\s]/u', '', $val);
                //print $str . PHP_EOL;
                if(mb_stripos(mb_strtolower($str), mb_strtolower(trim($name))) === 0 || mb_stripos(mb_strtolower($str), mb_strtolower(trim($name)))>0){
                    $ss[] = [
                        $field[0] => $key,
                        $field[1] => $val
                    ];
                }
            }
        }else{
            foreach($answer as $key=>$val){
                $ss[] = [
                    $field[0] => $key,
                    $field[1] => $val
                ];
            }
        }

        return $ss;
    }

    function report(Request $requery){
        //print "1";
        $input = json_decode($requery->getContent(), true);
        //print $input['employersId'];
        $answer = DB::table('employer as emp')->
                join('workplace as work', 'emp.id', '=', 'work.employer')->
                whereNull('work.deleted_at')->
                where('emp.id', $input['employersId'])->
                select('emp.id as emp','emp.shortName', 'work.id as work','work.name')->get();
                //select('e.shortName as emp', 'w.name as work')->
        foreach($answer as $key=>$val){
            $ss[] = [
                'employerId' => $val->emp,
                'employerName' => $val->shortName,
                'workplaceId' => $val->work,
                'workplace' => $val->name
            ];
        }
        return $ss;
                //print_r($answer);
    }

    function test(Request $request){
        //print_r($requery->getContent());
        $field = \App\Helpers\Constructor\TwoFields::instance()->Two(json_decode($request->getContent(), true));
        //print $request->value . PHP_EOL;
        //print_r($field);//['dangersObjectStudy'];
        return $field;
        //print_r($field);
        //foreach($answer as $key){
        //    print_r($key);
        //}
        //return $answer->id->name;
    }
}

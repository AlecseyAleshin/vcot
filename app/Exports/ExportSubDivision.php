<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Models\AdditionalSettlement;
use App\Models\Settlement;
use App\Models\Employer;
use App\Models\EmployerInProject;
use App\Models\EmployerStructure;
use App\Models\ExpertInProject;
use App\Models\Workplace;
use App\Models\HazardIdentification;
use App\Models\Sout;
use App\Models\RiskAssessmentProject;
use App\Models\Matrix;
use App\Models\MatrixRisk;
use App\Models\MatrixTitle;
use Maatwebsite\Excel\Concerns\FromCollection;


class ExportSubDivision implements FromView
{

    public function view(): View
    {
        $query = request()->segments();
        $ss = EmployerStructure::where('id', $query[2])->first();
        return view('excel', ['db' => $this->RiskRegistr(),'header' => $this->headerRegistr(), 'sub' => $ss->name]);
        //return view('excel', ['db' => $this->RiskRegistr(),'header' => $this->headerRegistr()]);
    }


    public function RiskRegistr(){
        $work = "";
        $sizw = "";
        $risk = array();
        $dangerArray = array();
        $aaa = array();
        $arg = request()->segments();
        $id = $arg[2];
        //$employer = Employer::where('id', $id)->first();
        $db = Workplace::where('structure', $id)->get();
        foreach($db as $s){
            $hazard = HazardIdentification::where('workplace', $s->id)->whereNull('deleted_at')->whereNotNull('hazardEvent')->get();

                //print $riskCur;
            $struct =EmployerStructure::where('id', $id)->select('name')->first();
            foreach($hazard as $t){
                $risk = $this->riskCurrent($s->project, $t->current_hazard_level, $t->residual_hazard_level);

                $soutS = "";
                $sou = Sout::where('id', $t->sout)->get();
                foreach($sou as $so) $soutS = $so->value;
                $settl = Settlement::where('hazard', $t->id)->whereNull('deleted_at')->get();
                foreach($settl as $set){
                    $work = "";
                    $sizw = "";
                    //$value = AdditionalSettlement::where('settlement', $set->id)->whereNull('deleted_at')->get();
                    //foreach($value as $val){
                        //print $val->value . " -- " . $set->requirement . PHP_EOL;
                        $siz = $this->sizDb($set->id);
                        //print_r($siz);
                    //}
                    //print count($siz);

                    if($set->requirement === '71cd1a05-b4c8-40a4-9a65-d80b7dbb6191'){
                        $work = implode(' / ', $siz);

                    }
                    if($set->requirement === '71cd1a05-b4c8-40a4-9a65-d80b7dbb6192'){
                        $sizw = implode(' / ', $siz);
                    }
                    //print $work . ' - ' . $sizw . PHP_EOL;
                }


                $dangerArray[] = [
                    'name' => $this->danger($t->dangerous),
                    'danger_event' => $this->dangerEvent($t->hazardEvent),
                    'department' => $struct->name,
                    'num_job_title' => $s->name,
                    'code' => $s->code,
                    'object' => $this->dangerObject($t->workplacePart),
                    'occurrence_object' => $this->occurrenceObject($t->dangerPart),
                    'sout' => $soutS,
                    'current_risk' => $risk['current'],
                    'residual_risk' => $risk['resident'],
                    'work' => $work,
                    'siz' => $sizw,

                ];


            }


        }
        asort($dangerArray);

        return $dangerArray;
    }

    public function danger($danger){
        $dang = DB::table('nsi.dct_danger_data')->
                        where('id', '=', $danger)->get();
        foreach($dang as $d){
            $danger = $d->name;
        };
        //print $danger . PHP_EOL;
        return $danger;
    }

    public function dangerEvent($event){
        $dangerEvent = DB::table('nsi.dct_danger_event_data')->
                            where('id', '=', $event)->get();
        foreach($dangerEvent  as $event){
            //print $event->name . PHP_EOL;
            $answer = $event->name;
        }
        return $answer;
    }

    public function dangerObject($workPart){
        $work = array();
        $obj = DB::table('nsi.dct_place_work_data')->where('id',$workPart)->get();
        foreach($obj as $o) !empty($o) ? $work[] = $o->name : "";
        $obj = DB::table('nsi.dct_types_work_data')->where('id', $workPart)->get();
        foreach($obj as $o) !empty($o) ? $work[] = $o->name : "";
        $obj = DB::table('nsi.dct_accident_situation_data')->where('id', $workPart)->get();
        foreach($obj as $o) !empty($o) ? $work[] = $o->name : "";
        $work = implode(' / ', $work);
        return $work;
    }

    public function occurrenceObject($dangerPart){
        $work = array();
        $obj = DB::table('nsi.dct_place_work_2_data')->where('id',$dangerPart)->get();
        foreach($obj as $o) !empty($o) ? $work[] = $o->name : "";
        $obj = DB::table('nsi.dct_machinery_data')->where('id', $dangerPart)->get();
        foreach($obj as $o) !empty($o) ? $work[] = $o->name : "";
        $obj = DB::table('nsi.dct_material_data')->where('id', $dangerPart)->get();
        foreach($obj as $o) !empty($o) ? $work[] = $o->name : "";
        $obj = DB::table('nsi.dct_tools_data')->where('id', $dangerPart)->get();
        foreach($obj as $o) !empty($o) ? $work[] = $o->name : "";
        $work = implode(' / ', $work);
        return $work;
    }

    public function riskCurrent($workplace, $current, $resident){
        $curRisk = "";
        $resRisk = "";

        $answer = RiskAssessmentProject::where('id', $workplace)->whereNull('deleted_at')->get();
        foreach($answer as $a) $rip = $a->id;
        //unset($answer);
        //print $rip . PHP_EOL;
        if(!empty($rip)){
            //print $rip . PHP_EOL;
            $answer = EmployerInProject::where('project', $rip)->whereNull('deleted_at')->get();
                foreach($answer as $a) $project = $a->matrix;
            $answer = Matrix::where('id', $project)->get();
                foreach($answer as $a) $answer = $a->id;
            $tit = MatrixRisk::where('matrix_id', $answer)->get();
                foreach($tit as $t){
                    if($current >= $t->min && $current <= $t->max){
                        $cur = MatrixTitle::where('id', $t->title_id)->where('type', 'risk')->select('title')->first();
                        $curRisk  = $cur->title;
                        //print $cur->title . PHP_EOL;
                    }
                    if($resident >= $t->min && $resident <= $t->max){
                        $res = MatrixTitle::where('id', $t->title_id)->where('type', 'risk')->select('title')->first();
                        $resRisk = $res->title;
                        //print $res->title . PHP_EOL;
                    }

                }


        }
        //print $curRisk . " ---  " . $resRisk . PHP_EOL;

        $risk = [
            'current' => $curRisk,
            'resident'  => $resRisk
        ];
        //print_r($risk);
        return $risk;
    }

    public function sizDb($req){
        $work = array();
        $siz = array();
        $dsa1d = "";
        $dsd = "";
        $drmd = "";
        $value = AdditionalSettlement::where('settlement', $req)->whereNull('deleted_at')->get();
        //print $req . PHP_EOL;
        //print_r($value);
        foreach($value as $val){
            //print $val->value . PHP_EOL;
            $db = DB::table('nsi.dct_siz_application_1_data')->select('name')->where('id', '=', $val->value)->get();
                foreach($db as $a){
                    $dsa1d = $a->name;
                    !empty($dsa1d)? $work[] = $dsa1d : NULL;
                }
            $db = DB::table('nsi.dct_siz_data')->select('name')->where('id', '=', $val->value)->get();
                foreach($db as $a){
                    $dsd = $a->name;
                    !empty($dsd)? $work[] = $dsd: NULL;
                }
            $db = DB::table('nsi.dct_risk_management_data')->select('name')->where('id', '=', $val->value)->get();
                foreach($db as $a){
                    $drmd = $a->name;
                    !empty($drmd)? $work[] = $drmd : NULL;
                }
        // if($requirement === $workMark){
                !empty($dsa1d)? $work[] = $dsa1d : NULL;
                !empty($dsd) ? $work[] = $dsd : NULL;
                !empty($drmd) ? $work[] = $drmd : NULL;
            //}
       }
        return $work;
    }

    public function headerRegistr(){
        $arg = request()->segments();
        //$id = '2fe611f5-f54d-4f11-b494-64f8e7c8091a';
        $idSub = $arg[2];
        //$idSub = 'f78601ab-6009-4867-99cc-f05257b0ae45';
        //print_r($arg);
        $db = DB::table('employer as e')->
                join('employer_structure as es', 'es.employer', '=', 'e.id')->
                leftJoin('employer_in_project as eip', 'eip.employer', '=', 'e.id')->
                leftJoin('matrix as m', 'eip.matrix', '=', 'm.id')->
                //where('e.id', '=', $id)->
                where('es.id', '=', $idSub)->
                select('e.id')->
                select('e.id as employer_uid', 'e.fullName as employer_fullname', 'es.id as structure_id',
                        'es.name as structure_name', 'm.name as matrix')->first();
        //print_r($db);
        return $db;
    }
}

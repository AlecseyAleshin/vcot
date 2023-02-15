<?php

namespace App\Exports;



use Maatwebsite\Excel\Concerns\FromView;
use App\Models\EmployerStructure;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Models\AdditionalSettlement;
use App\Models\Settlement;
use App\Models\Employer;
use App\Models\EmployerInProject;

use App\Models\Workplace;
use App\Models\HazardIdentification;
use App\Models\Sout;
use App\Models\RiskAssessmentProject;
use App\Models\Matrix;
use App\Models\MatrixRisk;
use App\Models\MatrixTitle;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExportBasic implements FromView
{
    var $dangerArray;


    public function view(): View
    {
        //print_r($this->RiskRegistr());
        return view('excel', ['db' => $this->RiskRegistr(),'header' => $this->headerRegistr(), 'sub' => ""],);
        //return view('excel1');
        //return view('excel1', ['header' => $this->headerRegistr(), 'sub' => ""]);
    }


    public function RiskRegistr(){
        // "22";
        //print '1';
        $risk = array();
        $dangerArray = array();
        $siz = array();
        $arg = request()->segments();
        $id = $arg[2];
        //print $id;
        $employer = Employer::where('id', $id)->first();
        //print_r($employer);
        //$data['employer'] = [
        //    'name' => $employer->fullName,
        //    'idEmployer' => $employer->id
        //];
          //  print '2';
        $db = Workplace::where('employer', $employer->id)->whereNull('deleted_at')->get();
        foreach($db as $s){
            //$db = RiskAssessmentProject::where('id', $s->project)->get();
            //print_r($db);
            //print '3';
            $hazard = HazardIdentification::where('workplace', $s->id)->whereNull('deleted_at')->whereNotNull('hazardEvent')->get();

                //print $riskCur;
            $struct =EmployerStructure::where('id', $s->structure)->select('name')->first();
            foreach($hazard as $t){
              //  print'4';
                $risk = $this->riskCurrent($s->project, $t->current_hazard_level, $t->residual_hazard_level);

                $soutS = "";
                $sou = Sout::where('id', $t->sout)->get();
                foreach($sou as $so) $soutS = $so->value;
                //print $t->id;
                $settl = Settlement::where('hazard', $t->id)->whereNull('deleted_at')->get();
                if($settl->count()>0){
                    foreach($settl as $set){
                  //      print '6';
                        $work = "";
                        $sizw = "";
                            $siz = $this->sizDb($set->id);
                            if($set->requirement === '71cd1a05-b4c8-40a4-9a65-d80b7dbb6191'){
                                //$i>2 ? print " / " : NULL;
                                $work .= implode(' / ', $siz);
                                //$i++;

                            }
                            if($set->requirement === '71cd1a05-b4c8-40a4-9a65-d80b7dbb6192'){
                                //$i>1 ? print " / " : NULL;
                                $sizw .= implode(' / ', $siz);
                                //$i++;
                            }


                    }
                }else{
                    $work ="";
                    $sizw="";
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
        //print_r($dangerArray);
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
        foreach($value as $val){
            $db = DB::table('nsi.dct_siz_application_1_data')->select('name')->where('id', '=', $val->value)->get();
                foreach($db as $a){
                    //print count($db);
                    $dsa1d = $a->name;
                    !empty($dsa1d)? $work[] = $dsa1d : NULL;
                    //print 'dsa1d' . $dsa1d . PHP_EOL;
                }
            $db = DB::table('nsi.dct_siz_data')->select('name')->where('id', '=', $val->value)->get();
                foreach($db as $a){
                    $dsd = $a->name;
                    !empty($dsd) ? $work[] = $dsd : NULL;
                    //print 'dsd' . $dsd . PHP_EOL;
                }
            $db = DB::table('nsi.dct_risk_management_data')->select('name')->where('id', '=', $val->value)->get();
                foreach($db as $a){
                    $drmd = $a->name;
                    !empty($drmd) ? $work[] = $drmd : NULL;
                    //print 'drmd' . $drmd . PHP_EOL;
                }
            }
            //print_r($work);

        return $work;
    }

    public function headerRegistr(){

        $arg = request()->segments();
        $id = $arg[2];
        $db = DB::table('employer as e')->
                join('employer_structure as es', 'es.employer', '=', 'e.id')->
                leftJoin('employer_in_project as eip', 'eip.employer', '=', 'e.id')->
                leftJoin('matrix as m', 'eip.matrix', '=', 'm.id')->
                where('e.id', '=', $id)->
                select('e.id')->
                select('e.id as employer_uid', 'e.fullName as employer_fullname', 'es.id as structure_id',
                        'es.name as structure_name', 'm.name as matrix')->first();
        //print_r($db);

        return $db;
    }


}

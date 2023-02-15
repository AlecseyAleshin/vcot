<?php

namespace App\Http\Controllers;


use App\Models\AdditionalSettlement;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Employer;
use App\Models\Workplace;
use App\Models\Matrix;
use App\Models\EmployerInProject;
use App\Models\ExpertOrganization;
use App\Models\HazardIdentification;
use App\Models\RiskAssessmentProject;
use App\Models\Settlement;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

ini_set('max_execution_time', 900);
ini_set('memory_limit', '4096M');

class AnalyticalReport extends Controller
{
    //

    function index(Request $request,$vid, $tip){

        $title = [
            'title' => "",
            'column2' => "",
            'column3' => "",
            'column4' => "Количество РМ на которых идентифицировано",
            'column5' => "всего",
            'column6' => "с высоким уровнем риска",
        ];
        $answer = array();
        switch($tip){
            case 'analysis_by_organization':
                $title['title'] = "Анализ по организациям по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] = "Наименование организации";
                $title['column3'] = "Матрица оценки";
                if(!Storage::exists('analysis_by_organization.json')){
                    $answer = $this->Organization();
                    Storage::put('analysis_by_organization.json', json_encode($answer, true));
                }else{
                    $answer = json_decode(Storage::get('analysis_by_organization.json'), true);
                }
                $name = array_column($answer, 'name');
                $risk = array_column($answer, 'risk');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_industry':
                $title['title'] = "Анализ по отраслям экономики по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] = "Наименование организации";
                $title['column3'] = "Матрица оценки";
                $title['column5'] = "Количество РМ";
                $title['column6'] = "С высоким уровнем риска";
                //print '1';
                if(!Storage::exists('analysis_by_industry.json')){
                    $answer = $this->okved();
                    Storage::put('analysis_by_industry.json', json_encode($answer, true));
                }else{
                    $answer = json_decode(Storage::get('analysis_by_industry.json'));
                }
                //print '2'; print_r($answer);
                $okved = array_column($answer, 'okved');
                $name = array_column($answer, 'name');
                $risk = array_column($answer, 'risk');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_occupation':
                $title['title'] = "Анализ по профессиям по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] = "Наименование профессии (ОK-016-94)";
                $title['column3'] = "Организация";
                $title['column4']  = "Количество опасностей";
                $title['column5'] = "идентифицированных на рабочем месте";
                $title['column6'] = "с высоким уровнем риска";
                if(!Storage::exists('analysis_by_occupation.json')){
                    $answer = $this->professions2();
                    Storage::put('analysis_by_occupation.json', json_encode($answer, true));
                }else{
                    $answer = json_decode(Storage::get('analysis_by_occupation.json'), true);
                }

                $name = array_column($answer, 'name');
                $risk = array_column($answer, 'risk');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_type_of_work_performed':
                $title['title'] = "Анализ по местам выполнения работ по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] = "Наименование места выполнения работ";
                $title['column3'] = "Опасность";
                if(!Storage::exists('analysis_by_type_of_work_performed.json')){
                    $answer = $this->workPerformed();
                    Storage::put('analysis_by_type_of_work_performed.json', json_encode($answer, true));
                }else{
                    $answer = json_decode(Storage::get('analysis_by_type_of_work_performed.json'),true);
                }
                $name = array_column($answer, 'name');
                $risk = array_column($answer, 'risk');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_place_of_work':
                $title['title'] = "Анализ по местам выполнения работ по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] = "Наименование места выполнения работ";
                $title['column3'] = "Опасность";
                if(!Storage::exists('analysis_by_place_of_work.json')){
                    $answer = $this->dataWork();
                    Storage::put('analysis_by_place_of_work.json', json_encode($answer, true));
                }else{
                    $answer = json_decode(Storage::get('analysis_by_place_of_work.json'), true);
                }
                $name = array_column($answer, 'name');
                $risk = array_column($answer, 'risk');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_abnormal_and_emergency_situations':
                $title['title'] = "Анализ по нештатным и аварийным ситуациям по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] =  "Наименование места выполнения работ";
                $title['column3'] = "Опасность";
                if(!Storage::exists('analysis_by_abnormal_and_emergency_situations.json')){
                    $answer = $this->abnormalData();
                    Storage::put('analysis_by_abnormal_and_emergency_situations.json', json_encode($answer, true));
                }else{
                    $answer = json_decode(Storage::get('analysis_by_abnormal_and_emergency_situations.json'), true);
                }
                $name = array_column($answer, 'name');
                $risk = array_column($answer, 'risk');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_hazardous_locations':
                $title['title'] = "Анализ по объектам возникновения опасностей по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] =  "Наименование объекта возникновения опасности";
                $title['column3'] = "Опасность";
                if(!Storage::exists('analysis_by_hazardous_locations.json')){
                    $answer = $this->toolsData();
                    Storage::put('analysis_by_hazardous_locations.json', json_encode($answer, true));
                }else{
                    $answer = json_decode(Storage::get('analysis_by_hazardous_locations.json'), true);
                }
                $name = array_column($answer, 'name');
                $risk = array_column($answer, 'risk');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_hazards':
                $title['title'] = "Анализ по объектам возникновения опасностей по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] =  "Наименование объекта возникновения опасности";
                $title['column3'] = "Опасность";
                if(!Storage::exists('analysis_by_hazards.json')){
                    $answer = $this->hazardsData();
                    Storage::put('analysis_by_hazards.json', json_encode($answer, true));
                }else{
                    $answer = json_decode(Storage::get('analysis_by_hazards.json'), true);
                }
                $name = array_column($answer, 'name');
                $risk = array_column($answer, 'risk');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_hazardous_events':
                $title['title'] = "Анализ по опасным событиям по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] =  "Наименование опасного события";
                $title['column3'] = "Опасность";
                $title['column4'] = "Вид выполняемых работ";
                if(!Storage::exists('analysis_by_hazardous_events.json')){
                    $answer = $this->eventsHazardsData();
                    Storage::put('analysis_by_hazardous_events.json', json_encode($answer, true));
                }else{
                    $answer = json_decode(Storage::get('analysis_by_hazardous_events.json'), true);
                }
                $name = array_column($answer, 'name');
                $risk = array_column($answer, 'risk');
                $work = array_column($answer, 'work');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_control_measures':
                $title['title'] = "Анализ по опасным событиям по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] =  "Наименование опасного события";
                $title['column3'] = "Опасность";
                $title['column4'] = "Вид выполняемых работ";
                    //if(!Storage::exists('analysis_by_control_measures.json')){
                        $answer = $this->measureControl2();
                    //    Storage::put('analysis_by_control_measures.json', json_encode($answer, true));
                    //}else{
                    //    $answer = json_decode(Storage::get('analysis_by_control_measures.json'), true);
                    //}
                $name  = array_column($answer, 'name');
                $countPM = array_column($answer, 'countPM');
                $countPMRisk = array_column($answer, 'countPMRisk');
                array_multisort($countPM, SORT_DESC, $name, SORT_ASC, $answer);
                break;
            case 'analysis_by_time_and_quality_indicators':
                $title['title'] = "Анализ по временным и качественным показателям по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title9['column2'] = "Наименование организации";
                $title['column3'] =  "Наименование экспертной организации";
                $title['column4'] = "Среднее количество опасностей на одно рабочее место ";
                $title['column5'] = "Количество РМ с высоким уровнем риска";
                $title['column6'] = "июнь";
                $title['column7'] =  "июль";
                $title['column8'] = "август";
                $title['column9'] = "сентябрь";
                $title['column10'] = "октябрь";
                $answer = $this->quality();
                //return view('analyticreport.quality',['db' => $answer, 'title' => $title]);
                break;
            case 'analysis_of_erp_occupational_safety_and_health_training':
                $title['title'] = "Анализ по обучению по охране труда ОПР по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] = "Наименование предприятия";
                $title['column3'] = "Количество РМ всего";
                $title['column4'] = "46 (а)";
                $title['column5'] = "46 (б)";
                $title['column6'] = "46 (в)";
                $answer = $this->safity();
                break;
            case 'analysis_of_personal_protective_equipment':
                $title['title'] = "Анализ по средствам индивидуальной защиты по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] = "Наименование СИЗ";
                $title['column3'] = "Количество РМ на которых выдается (текущие меры)";
                $title['column4'] = "Количество РМ на которых необходимо выдавать (дополнительные меры)";
                $answer = $this->siz();
                break;
            case 'analysis_by_health_inspections':
                $title['title'] = "Анализ по медицинским осмотрам по результатам проекта по апробации законодательства в сфере оценки профессиональных рисков";
                $title['column2'] = "Наименование предприятия";
                $title['column3'] = "Количество РМ всего";
                $title['column4'] = "Количество РМ на которых необходимо проводить медицинские осмотры";
                $answer = $this->health();
                break;
            default:
                //print "No";
                break;
        }
        if($vid === 'view'){
            return $answer;
        }elseif($vid === 'pdf'){
            switch($tip){
                case 'analysis_by_organization':
                    $pdf = Pdf::loadView('analyticreport.analysis', ['db' => $answer, 'title' => $title]);
                    //$pdf->save('report/analysis_by_organization.pdf');
                    //return "Ok";
                    return $pdf->download('analysis_by_organization.pdf');
                    break;
                case 'analysis_by_industry':
                    $pdf = Pdf::loadView('analyticreport.analysis2', ['db' => $answer, 'title' => $title]);
                    //$pdf->save('report/analysis_by_industry.pdf');
                    //return "Ok";
                    return  $pdf->download('analysis_by_industry.pdf');
                    break;
                case 'analysis_by_occupation':
                    $pdf = Pdf::loadView('analyticreport.analysis2', ['db' => $answer, 'title'=>$title]);
                    //$pdf->save('report/analysis_by_occupation.pdf');
                    //return "Ok";
                    return $pdf->download(('analysis_by_occupation.pdf'));
                    //return Excel::download(new ExcelConvert($answer, $title) , 'analysis_by_occupation.xlsx');
                    break;
                case 'analysis_by_type_of_work_performed':
                    $pdf = Pdf::loadView('analyticreport.analysis', ['db' => $answer,  'title' =>  $title]);
                    //$pdf->save('report/analysis_by_type_of_work_performed.pdf');
                    //return "Ok";
                    return  $pdf->download('analysis_by_type_of_work_performed.pdf');
                    break;
                case 'analysis_by_place_of_work':
                    $pdf = Pdf::loadView('analyticreport.analysis', ['db' => $answer, 'title' => $title]);
                    //$pdf->save('report/analysis_by_place_of_work.pdf');
                    //return "Ok";
                    return  $pdf->download('analysis_by_place_of_work.pdf');
                    break;
                case 'analysis_by_abnormal_and_emergency_situations':
                    $pdf = Pdf::loadView('analyticreport.analysis', ['db' => $answer, 'title' => $title]);
                    //$pdf->save('report/analysis_by_abnormal_and_emergency_situations.pdf');
                    //return "Ok";
                    return $pdf->download('analysis_by_abnormal_and_emergency_situations.pdf');
                    break;
                case 'analysis_by_hazardous_locations':
                    $pdf = Pdf::loadView('analyticreport.analysis', ['db' => $answer, 'title' => $title]);
                    return $pdf->download('analysis_by_hazardous_locations.pdf');
                    break;
                case 'analysis_by_hazards':
                    $pdf = Pdf::loadView('analyticreport.analysis', ['db' => $answer, 'title' => $title]);
                    return $pdf->download('analysis_by_hazards.pdf');
                    break;
                case 'analysis_by_hazardous_events':
                    $pdf = Pdf::loadView('analyticreport.analysis1', ['db' => $answer, 'title' => $title]);
                    return $pdf->download('analysis_by_hazardous_events.pdf');
                    break;
                case 'analysis_by_control_measures':
                    $pdf = Pdf::loadView('analyticreport.analysis2', ['db' => $answer, 'title' => $title]);
                    return $pdf->download('analysis_by_control_measures.pdf');
                    break;
                case 'analysis_by_time_and_quality_indicators':
                    $pdf = Pdf::loadView('analyticreport.quality', ['db' => $answer, 'title' => $title]);
                    return $pdf->download('analysis_by_time_and_quality_indicators.pdf');
                    break;
                case 'analysis_of_erp_occupational_safety_and_health_training':
                    $pdf = Pdf::loadView('analyticreport.quality', ['db' => $answer, 'title' => $title]);
                    return $pdf->download('analysis_of_erp_occupational_safety_and_health_training.pdf');
                    break;
                case 'analysis_of_personal_protective_equipment':
                    $pdf = Pdf::loadView('analyticreport.quality', ['db' => $answer, 'title' => $title]);
                    return $pdf->download('analysis_of_personal_protective_equipment.pdf');
                    break;
                case 'analysis_by_health_inspections':
                    $pdf = Pdf::loadView('analyticreport.quality', ['db' => $answer, 'title' => $title]);
                    return $pdf->download('analysis_by_health_inspections.pdf');
                    break;
                default:
                    return $answer;
                    break;

            }



        }
    }



   /*  public function view($answer): View
    {
        return view('analyticreport.analysis_by_occupation', ['db' => $answer]);
    } */

    public function Organization(){

        $db = Employer::whereNull('deleted_at')->get();
        foreach($db as $employer){
            $workplace = Workplace::where('employer', $employer->id)->whereNull('deleted_at')->count();
            $matrix = $this->Matr($employer->id);
            $risk = $this->risk($employer->id);
            //print $employer->fullName . " ---- ---  ". $workplace . PHP_EOL;
            $org[] = [
                'name' => $employer->shortName,
                'risk' => $matrix,
                'countPM' => $workplace,
                'countPMRisk' => $risk
            ];
        }
        return $org;
    }

    function okved (){
        $okved = array();
        $ss = array();
        $db = Employer::select('okved')->whereNotNull('okved')->groupBy('okved')->distinct()->get();
        foreach($db as $d){
            $okv = DB::table('nsi.dct_okved_data')->where('id', $d->okved)->first();
            if(!empty($okv)){
            $okved[trim($okv->class_okved)] = [
                'id' => trim($d->okved),
                'name'=> $okv->economic_activity,
            ];
            }else{print PHP_EOL;}
        }
        ksort($okved);

        foreach($okved as $key => $value){
            $db = Employer::where('okved', $value['id'])->get();
            foreach($db as $employer){
                $workplace = Workplace::where('employer', $employer->id)->whereNull('deleted_at')->count();
                $matrix = $this->Matr($employer->id);
                $risk = $this->risk($employer->id);
                $answer[] = [
                            'okved' => $value['name'],
                            'name' => $employer->shortName,
                            'risk' => $matrix,
                            'countPM' => $workplace,
                            'countPMRisk' => $risk,
                ];
            }
        }

        return $answer;
    }

    function professions(){
        //$answer = array();
        $emp = Employer::all();
        //$db = DB::table('nsi.dct_ok_01694_data')->whereNull('closed')->get();
        foreach($emp as $e){
            $s = Workplace::where('employer', $e->id)->distinct('ok01694')->get();
            foreach($s as $o){
            $ok[] = [
                'employer' => $e->id,
                'shortName' => $e->shortName,
                'workplace' => $o->id,
                'ok' => $o->ok01694,
            ];
            }
        }
            //print_r($ok);
            foreach($ok as $v){
                //foreach($v as $value){
                    $countPM = Workplace::where('employer', $v['employer'])->where('ok01694', $v['ok'])->count();
                    $ok01694 = DB::table('nsi.dct_ok_01694_data')->where('id', $v['ok'])->value('profession_name');
                    $countRisk = $this->riskH($v['workplace']);
                    $ok01694 == NULL ? $ok01694 = "" : NULL;
                    $answer[] = [
                        'name' => $ok01694,
                        'risk' => $v['shortName'],
                        'countPM' => $countPM,
                        'countPMRisk' => $countRisk,
                    ];
                //}

            }
            unset($ok);
            unset($emp);
            unset($countPM, $ok01694,  $countRisk);
            //print_r($answer);
            //asort($answer);
        return $answer;

    }

    function professions2(){
        $answer =  array();
        //print '4';
        $ok01694 = DB::table('nsi.dct_ok_01694_data')->pluck('id', 'profession_name');
            //print_r($ok01694);
            foreach($ok01694 as $key =>$val){
                $countRisk = 0;

                $workplace = Workplace::whereNull('deleted_at')->where('ok01694', $val)->pluck('id');
                $count = $workplace->count();
                //print $count . PHP_EOL;
                //print_r($workplace);
                foreach($workplace as $w){
                    //print_r($w);
                    $risk = HazardIdentification::whereNull('deleted_at')->where('workplace', $w)->where('current_hazard_level', '>', 49)->pluck('id');
                    $countRisk += $risk->count();
                }

                if($count > 0 && $countRisk >0){
                    $answer[] = [
                        'name' => $key,
                        'countPM' => $count,
                        'countPMRisk' => $countRisk
                    ];
                }
            }

        return $answer;
    }

    function prof(){
        $count = Workplace::whereNull('deleted_at')->join('nsi.dct_ok_01694_data as ok', 'ok.id', '=', 'ok01694')->
                        select('id')->count();
                        print $count . PHP_EOL;
    }

    //analysis_by_type_of_work_performed
    function workPerformed(){
        $workPerformed = HazardIdentification::whereNull('deleted_at')->distinct('workplacePart')->get();
        //print $workPerformed->count() . PHP_EOL;
        foreach($workPerformed as $wP){
            $name = DB::table('nsi.dct_types_work_data')->where('id', $wP->workplacePart)->get();
            if($name->count() >0) {
                foreach($name as $n){
                    //print $n->id;
                    $countWork = HazardIdentification::where('workplacePart', $n->id)->count();
                    $dangerous = DB::table('nsi.dct_danger_data as d')->
                                join('hazard_identification as h', 'h.dangerous', '=', 'd.id')->
                                where('h.workplacePart', $n->id)->select('d.name')->first();
                    $countWorkRisk = HazardIdentification::where('workplacePart', $n->id)->where('residual_hazard_level','>', 49)->count();
                    //print_r($dangerous);
                    $answer[] =
                    [
                        'name' => $n->name,
                        'risk' => $dangerous->name,
                        'countPM' => $countWork,
                        'countPMRisk' => $countWorkRisk,
                    ];
                }
            }
        }
        return $answer;
    }

    function dataWork(){
        $workPerformed = HazardIdentification::whereNull('deleted_at')->distinct('workplacePart')->get();
        foreach($workPerformed as $w){
            $name = DB::table('nsi.dct_place_work_data')->where('id',$w->workplacePart)->get();
            if($name->count() > 0){
                foreach($name as $n){
                    $countWork = HazardIdentification::where('workplacePart', $n->id)->count();
                    $dangerous = DB::table('nsi.dct_danger_data as d')->
                                join('hazard_identification as h', 'h.dangerous', '=', 'd.id')->
                                where('h.workplacePart', $n->id)->select('d.name')->first();
                    $countWorkRisk = HazardIdentification::where('workplacePart', $n->id)->where('residual_hazard_level','>', 49)->count();
                    //print_r($dangerous);
                    $answer[] =
                    [
                        'name' => $n->name,
                        'risk' => $dangerous->name,
                        'countPM' => $countWork,
                        'countPMRisk' => $countWorkRisk,
                    ];
                }
            }
        }
        return $answer;
    }

    function abnormalData(){
        $abnormal = DB::table('nsi.dct_accident_situation_data')->get();
        foreach($abnormal as $a){
            $dangerous = DB::table('nsi.dct_danger_data as d')->
                                join('hazard_identification as h', 'h.dangerous', '=', 'd.id')->
                                where('h.workplacePart', $a->id)->select('d.name')->first();
            $countWork = HazardIdentification::where('workplacePart', $a->id)->count();
            $countWorkRisk = HazardIdentification::where('workplacePart', $a->id)->where('residual_hazard_level','>', 49)->count();
            $dangerous ? $risk=$dangerous->name : $risk ="";
            $answer[] =
                    [
                        'name' => $a->name,
                        'risk' => $risk,
                        'countPM' => $countWork,
                        'countPMRisk' => $countWorkRisk,
                    ];
        }
        return $answer;
    }

    function toolsData(){
        $tools = DB::table('nsi.dct_tools_data')->get();
        foreach($tools as $a){
            $dangerous = DB::table('nsi.dct_danger_data as d')->
                                join('hazard_identification as h', 'h.dangerous', '=', 'd.id')->
                                where('h.dangerPart', $a->id)->select('d.name')->first();
            $countWork = HazardIdentification::where('dangerPart', $a->id)->count();
            $countWorkRisk = HazardIdentification::where('dangerPart', $a->id)->where('residual_hazard_level','>', 49)->count();
            $dangerous ? $risk=$dangerous->name : $risk ="";
            $answer[] =
                    [
                        'name' => $a->name,
                        'risk' => $risk,
                        'countPM' => $countWork,
                        'countPMRisk' => $countWorkRisk,
                    ];
        }
        return $answer;
    }

    function hazardsData(){

        $hazards = DB::table('nsi.dct_danger_data')->orderBy('name')->select(['name', 'id'])->get();
        foreach($hazards as $a){
            $dangerous = DB::table('nsi.dct_danger_event_data as d')->
                                join('hazard_identification as h', 'h.hazardEvent', '=', 'd.id')->
                                where('h.dangerous', $a->id)->select('d.name')->first();
            $countWork = HazardIdentification::whereNull('deleted_at')->where('dangerous', $a->id)->count();
            $countWorkRisk = HazardIdentification::whereNull('deleted_at')->where('dangerous', $a->id)->where('residual_hazard_level','>', 49)->count();
            $dangerous ? $risk=$dangerous->name : $risk ="";
            $answer[] =
                    [
                        'name' => $a->name,
                        'risk' => $risk,
                        'countPM' => $countWork,
                        'countPMRisk' => $countWorkRisk,
                    ];
        }

        return $answer;
    }

    function eventsHazardsData(){

        $hazards = DB::table('nsi.dct_danger_event_data')->orderBy('name')->select(['name', 'id'])->get();
            foreach($hazards as $a){
                $dangerous = DB::table('nsi.dct_danger_data as d')->
                                    join('hazard_identification as h', 'h.dangerous', '=', 'd.id')->
                                    whereNull('deleted_at')->where('h.hazardEvent', $a->id)->select('d.name as name', 'h.workplacePart as workplacePart')->first();
                $countWork = HazardIdentification::whereNull('deleted_at')->where('hazardEvent', $a->id)->count();
                $countWorkRisk = HazardIdentification::whereNull('deleted_at')->where('hazardEvent', $a->id)->where('residual_hazard_level','>', 49)->count();
                if($dangerous){
                $work = DB::table('nsi.dct_types_work_data')->where('id', $dangerous->workplacePart)->select('name')->first();

                $work ? $work_q = $work->name : $work_q = "";
                }else{$work_q= "";}
                $dangerous ? $risk=$dangerous->name : $risk ="";
                $a ? $name = $a->name : "";
                $answer[] =
                        [
                            'name' => $name,
                            'risk' => $risk,
                            'work' => $work_q,
                            'countPM' => $countWork,
                            'countPMRisk' => $countWorkRisk,
                        ];

            }

        return $answer;
    }
/*
    function measureControl(){
        //$hazard = DB::table('settlement')->whereNull('deleted_at')->select(['id','hazard'])->distinct('hazard')->get();
        $control = DB::table('nsi.dct_risk_management_data')->orderBy('name')->select(['id','name'])->get();
        foreach($control as $c){
            $count = 0;
            $countRisk = 0;
            $ss = AdditionalSettlement::whereNull('deleted_at')->where('value', $c->id)->select(['id', 'settlement'])->get();
            foreach($ss as $s){
                $count += DB::table('settlement as s')->join('hazard_identification as h', 's.hazard', '=', 'h.id')->where('s.id', $s->settlement)
                        ->whereNull('h.deleted_at')->select(['h.id', 's.id'])->count();
                $countRisk += DB::table('settlement as s')->join('hazard_identification as h', 's.hazard', '=', 'h.id')->where('s.id', $s->settlement)
                        ->whereNull('h.deleted_at')->where('residual_hazard_level','>', 49)->select(['h.id', 's.id'])->count();
                        //print $ss . PHP_EOL;
                //print $s->settlement . PHP_EOL;
            }
            if($c->name > ""){
                $answer[]= [
                    'name' => $c->name,
                    'countPM' => $count,
                    'countPMRisk' => $countRisk,
                ];
            //print $c->name . ' -  ' . $count . " - - " . $countRisk . PHP_EOL;
            }
        }
        return $answer;
    }
*/
    function measureControl2(){
        $control = DB::table('nsi.dct_risk_management_data')->
                        orderBy('name')->distinct('name')->pluck('name','id');
        //print_r($control);
        foreach($control as $key=>$value){
            $countDop = DB::table('additional_settlement as a')->leftJoin('settlement as s', 'a.settlement', '=', 's.id')->
            where('a.value', '=', $key)->count();
            $countTek = DB::table('taken_settlement as a')->leftJoin('settlement as s', 'a.settlement', '=', 's.id')->
            where('a.value', '=', $key)->count();
            //print $value . ' --- ' . $countTek . ' --- ' . $countDop . PHP_EOL;
            $answer[]= [
                'name' => $value,
                'countPM' => $countTek,
                'countPMRisk' => $countDop
            ];
        }


        return $answer;

    }

    function quality(){
        $i = 1;
        $hazard = array();
        $employerInProject = EmployerInProject::whereNull('deleted_at')->pluck('project','employer');
        foreach($employerInProject as $employer => $project){
            $employ = Employer::where('id', $employer)->whereNull('deleted_at')->pluck('shortName');
            $expert = RiskAssessmentProject::where('id', $project)->pluck('organizationId');
            if($employ->count() > 0 && $expert->count() > 0){
                $nameExpert = ExpertOrganization::where('id', $expert)->pluck('shortName');
                $s = $employer;
                $work = Workplace::where('employer', $s)->whereNull('deleted_at')->pluck('id', 'created_at');
                $a6 = 0;$a7 = 0;$a8 = 0;$a9 = 0;$a10 = 0;$risk = 0;
                foreach($work as $create => $id){
                    $haz = HazardIdentification::whereNull('deleted_at')->where('workplace', $id)->pluck('id');
                    $hazRisk = HazardIdentification::whereNull('deleted_at')->where('workplace', $id)->pluck('current_hazard_level');
                    foreach($hazRisk as $r){
                        if($r > 49 ){
                            $risk++;
                        }
                    }
                    $hazard[] = $haz->count();
                    $dd = Carbon::parse($create)->format('m');
                    if($dd == 6){
                        $a6++;
                    }elseif($dd == 7){
                        $a7++;
                    }elseif($dd == 8){
                        $a8++;
                    }elseif($dd == 9){
                        $a9++;
                    }elseif($dd == 10){
                        $a10++;
                    }
                }
                    $average = round(array_sum($hazard)/count($hazard),2);
                $answer[] = [
                    'employer' => $employ[0],
                    'expert' => $nameExpert[0],
                    'averageCount' => $average,
                    'countPM' => $risk,
                    'month6' => $a6,
                    'month7' => $a7,
                    'month8' => $a8,
                    'month9' => $a9,
                    'month10' => $a10
                ];

            }
            $i++;
        }
        return $answer;
    }

    function safity(){
        $saf = DB::table('training as t')->
                join('workplace as w','t.workplace_id', '=', 'w.id')->
                join('employer as e', 'w.employer', '=', 'e.id')->
                groupBy('e.id')->
                select('e.id', 'e.shortName')->
                selectRaw("sum(is_need_education_general_issues_employer::int) as qq, sum(is_need_education_safe_tricks_work::int) as ee, sum(is_need_education_safe_tricks_employer::int) as ss, sum(is_need_education_danger_employer::int) as ww, count(w.id) as cou")->get();
                //s_need_education_safe_tricks_work
                //print_r($saf);

            $coun = DB::table('workplace as w')->
                        join('employer as e', 'w.employer', '=', 'e.id')->whereNull('w.deleted_at')->
                        groupBy('e.id')->
                        select('e.id')->
                        selectRaw('count(w.id) as cc')->pluck('cc','id');
                        //print_r($coun);
        foreach($saf as $val){


            $answer[] = [
                'employer' => $val->shortName,
                'countPM' => $coun[$val->id],
                '46a' => $val->qq,
                '46b' => $val->ee + $val->ss,
                '46d' => $val->ww
            ];
        }
        return $answer;
    }

    function siz(){

        $answer[] = [
            'nameSiz' => "",
            'countPM' => "",
            'countPMdop' => ""
        ];
        return $answer;
    }


    function health(){

        $answer[] = [
            'employer' => "",
            'countPM' => "",
            'countPMdop' => ""
        ];

    }


    function Matr($id){
        $project = EmployerInProject::where('employer', $id)->get();
            foreach($project as $pr){
                $m = Matrix::where('id', $pr->matrix)->first();
                if($m > "" ){
                    return $m->name;
                }
            }

    }

    function risk($id){
        $i = 0;
        $work = Workplace::where('employer', $id)->whereNull('deleted_at')->get();
        foreach($work as $w){
            //$ris = HazardIdentification::where('workplace', $w->id)->whereNull('deleted_at')->where('current_hazard_level', '>', 49)->get();
            //$s = DB::table('hazard_identification')->where('workplace', $w->id)->whereNull('deleted_at')->count();
            $ris = DB::table('hazard_identification')->where('workplace', $w->id)->whereNull('deleted_at')->get();
                foreach($ris as $r){
              //      print $r->current_hazard_level . PHP_EOL;
                    if(intval($r->current_hazard_level) > 49){
                        $i++;
                    }
                }
                //print $s . ' -  -  ' . $i . PHP_EOL;
        }
        return $i;
    }

    function riskH($id){
        $i = 0;
        //$work = Workplace::where('employer', $id)->whereNull('deleted_at')->get();
        //foreach($work as $w){
            //$ris = HazardIdentification::where('workplace', $w->id)->whereNull('deleted_at')->where('current_hazard_level', '>', 49)->get();
            //$s = DB::table('hazard_identification')->where('workplace', $w->id)->whereNull('deleted_at')->count();
            $ris = DB::table('hazard_identification')->where('workplace', $id)->whereNull('deleted_at')->get();
                foreach($ris as $r){
              //      print $r->current_hazard_level . PHP_EOL;
                    if(intval($r->current_hazard_level) > 49){
                        $i++;
                    }
                }
                //print $s . ' -  -  ' . $i . PHP_EOL;
        //}
        return $i;
    }


}




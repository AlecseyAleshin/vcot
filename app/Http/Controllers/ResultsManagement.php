<?php

namespace App\Http\Controllers;

use App\Http\Resources\HazardCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Workplace;
use App\Models\Matrix;
use App\Models\HazardIdentification;
use App\Models\EmployerInProject;

ini_set('max_execution_time', 900);
ini_set('memory_limit', '4096M');

class ResultsManagement extends Controller
{


    function index(Request $request,$uid){
        $workplace = $this->workplac($uid);
        $matrix = $this->matrix($uid);
        $workCount = $this->workplacecount($uid);
        $workApproved = $this->workApproved($uid);
        switch($matrix){
            case 3:
                $workColor = $this->matrixThree($workplace);
                $hazCount = $this->hazardCountThree($workplace);
                break;
            case 4:
                $workColor = $this->matrixFour($workplace);
                $hazCount = $this->hazardCountFour($workplace);
                break;
            case 5:
                $workColor = $this->matrixFive($workplace);
                $hazCount = $this->hazardCountFive($workplace);
                break;
            default:
                print "No matrix";
                break;
        }
        //$this->hazardCount($workplace);
            $answer[] = [
                'workplace' => $workCount,
                'workplaceApproved' => $workApproved,
                'matrix' => $matrix,
                'risk_1' => $workColor['a'],
                'risk_2' => $workColor['b'],
                'risk_3' => $workColor['c'],
                'risk_4' => $workColor['d'],
                'risk_5' => $workColor['e'],
                'hazardAll' => $hazCount['count'],
                'hazard_1' => $hazCount['a'],
                'hazard_2' => $hazCount['b'],
                'hazard_3' => $hazCount['c'],
                'hazard_4' => $hazCount['d'],
                'hazard_5' => $hazCount['e'],
                'risk_res1' => $workColor['aR'],
                'risk_res2' => $workColor['bR'],
                'risk_res3' => $workColor['cR'],
                'risk_res4' => $workColor['dR'],
                'risk_res5' => $workColor['eR'],
            ];
        return $answer;

    }

    function workplac($uid){
        $workplace = Workplace::whereNull('deleted_at')->where('employer', $uid)->get();
        return $workplace;
    }

    function workplacecount($uid){
        $workCount = Workplace::where('employer', $uid)->whereNull('deleted_at')->count();
        return $workCount;
    }

    function workApproved($uid){
        $workApproved = Workplace::whereNull('deleted_at')->where('employer', $uid)->where('approved', true)->count();
        return $workApproved;
    }

    function matrix($uid){
        $eip = EmployerInProject::where('employer', $uid)->first();
        $matrix = Matrix::select('risk')->where('id',$eip->matrix)->get();
        return $matrix[0]->risk;
    }

    function hazardCountFive($workplace){
        $hazCount = [
            'a' => 0,
            'b' => 0,
            'c' => 0,
            'd' => 0,
            'e' => 0,
            'count' => 0,

        ];
        foreach($workplace as $w){
            $hazCount['count'] += HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->select('current_hazard_level as cur')->count();
            $hazard = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->select('current_hazard_level as cur')->get();
            //$hazard = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->pluck('current_hazard_level as cur', 'residual_hazard_level');
            //$hazCount['count'] = $hazard->count();
            foreach($hazard as $h){
                if($h->cur >+ 1 && $h->cur <5){
                    $hazCount['a']++;
                }elseif($h->cur >= 5 && $h->cur < 15){
                    $hazCount['b']++;
                }elseif($h->cur >= 15 && $h->cur < 50){
                    $hazCount['c']++;
                }elseif($h->cur >= 50 && $h->cur < 71){
                    $hazCount['d']++;
                }elseif($h->cur >=71){
                    $hazCount['e']++;
                }

            }
        }
        return $hazCount;
    }

    function hazardCountFour($workplace){
        $hazCount = [
            'a' => 0,
            'b' => 0,
            'c' => 0,
            'd' => 0,
            'e' => 0,
            'count' => 0
        ];
        foreach($workplace as $w){
            $hazCount['count'] += HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->select('current_hazard_level as cur')->count();
            $hazard = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->select('current_hazard_level as cur')->get();
            foreach($hazard as $h){
                if($h->cur >+ 1 && $h->cur < 10){
                    $hazCount['a']++;
                }elseif($h->cur >= 10 && $h->cur < 25){
                    $hazCount['b']++;
                }elseif($h->cur >= 25 && $h->cur < 49){
                    $hazCount['c']++;
                }elseif($h->cur >= 49){
                    $hazCount['d']++;
                }
            }
        }
        //print_r($hazCount);
        return $hazCount;
    }

    function hazardCountThree($workplace){
        $hazCount = [
            'a' => 0,
            'b' => 0,
            'c' => 0,
            'd' => 0,
            'e' => 0,
            'count' => 0
        ];
        foreach($workplace as $w){
            $hazCount['count'] += HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->select('current_hazard_level as cur')->count();
            $hazard = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->select('current_hazard_level as cur')->get();
            foreach($hazard as $h){
                if($h->cur >+ 1 && $h->cur < 15){
                    $hazCount['a']++;
                }elseif($h->cur >= 15 && $h->cur < 50){
                    $hazCount['b']++;
                }elseif($h->cur >= 50){
                    $hazCount['c']++;
                }
            }
        }
        return $hazCount;
    }

    function matrixFive($workplace){
        $work = [
            'a' => 0,
            'b' => 0,
            'c' => 0,
            'd' => 0,
            'e' => 0,
            'aR' => 0,
            'bR' => 0,
            'cR' => 0,
            'dR' => 0,
            'eR' => 0,
        ];
        foreach($workplace as $w){
            $workColor = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->max('current_hazard_level');
            $workColorRes = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->max('residual_hazard_level');
            if($workColor >= 1 && $workColor < 5){
                $work['a']++;
            }elseif($workColor>=5 && $workColor < 15){
                $work['b']++;
            }elseif($workColor >= 15 && $workColor < 50){
                $work['c']++;
            }elseif($workColor >= 50 && $workColor < 71){
                $work['d']++;
            }elseif($workColor >= 71){
                $work['e']++;
            }
            if($workColorRes >= 1 && $workColorRes < 5){
                $work['aR']++;
            }elseif($workColorRes>=5 && $workColorRes < 15){
                $work['bR']++;
            }elseif($workColorRes >= 15 && $workColorRes < 50){
                $work['cR']++;
            }elseif($workColorRes >= 50 && $workColorRes < 71){
                $work['dR']++;
            }elseif($workColorRes >= 71){
                $work['eR']++;
            }
        }
        return $work;
    }

    function matrixFour($workplace){
        $work = [
            'a' => 0,
            'b' => 0,
            'c' => 0,
            'd' => 0,
            'e' => 0,
            'aR' => 0,
            'bR' => 0,
            'cR' => 0,
            'dR' => 0,
            'eR' => 0,
        ];
        foreach($workplace as $w){
            $workColor = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->max('current_hazard_level');
            $workColorRes = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->max('residual_hazard_level');
            if($workColor >= 1 && $workColor < 10){
                $work['a']++;
            }elseif($workColor>=10 && $workColor < 25){
                $work['b']++;
            }elseif($workColor >= 25 && $workColor < 49){
                $work['c']++;
            }elseif($workColor >= 49){
                $work['d']++;
            }
            if($workColorRes >= 1 && $workColorRes < 10){
                $work['aR']++;
            }elseif($workColorRes>=10 && $workColorRes < 25){
                $work['bR']++;
            }elseif($workColorRes >= 25 && $workColorRes < 49){
                $work['cR']++;
            }elseif($workColorRes >= 49){
                $work['dR']++;
            }
        }
        return $work;
    }

    function matrixThree($workplace){
        $work = [
            'a' => 0,
            'b' => 0,
            'c' => 0,
            'd' => 0,
            'e' => 0,
            'aR' => 0,
            'bR' => 0,
            'cR' => 0,
            'dR' => 0,
            'eR' => 0,
        ];
        foreach($workplace as $w){
            $workColor = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->max('current_hazard_level');
            $workColorRes = HazardIdentification::whereNull('deleted_at')->where('workplace', $w->id)->max('residual_hazard_level');
            if($workColor >= 1 && $workColor < 15){
                $work['a']++;
            }elseif($workColor>=15 && $workColor < 50){
                $work['b']++;
            }elseif($workColor >= 50){
                $work['c']++;
            }
            if($workColorRes >= 1 && $workColorRes < 15){
                $work['aR']++;
            }elseif($workColorRes>=15 && $workColorRes < 50){
                $work['bR']++;
            }elseif($workColorRes >= 50){
                $work['cR']++;
            }
        }
        return $work;
    }
}

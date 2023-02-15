<?php
namespace App\Http\Controllers;

use App\Exports\ExportBasic;
use Illuminate\Http\Request;
use App\Exports\ExportSubDivision;
use App\Exports\test;
use App\Http\Middleware\EnterpriseRisks;

use Maatwebsite\Excel\Facades\Excel;


class ExportXlsxController extends Controller
{


    public function RiskRegistr(){

        //$exc =new ExportBasic;
        //$str = $exc->view();
        //return $str;
        return Excel::download(new ExportBasic, 'Risk registry.xlsx');
        //return $db1;

    }

    public function RiskRegistrSub(){
        //$exc = new ExportSubDivision;
        //$str = $exc->view();

        return Excel::download(new ExportSubDivision, 'Risk registry subdivision.xlsx');
        //return $str;
    }


    public function EnterpriseRisks(){
        $db = new EnterpriseRisks;
        $ss = $db->enterprise();
        return $ss;
    }
}

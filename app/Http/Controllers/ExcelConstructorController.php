<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ExcelConstructor;
use Maatwebsite\Excel\Facades\Excel;

class ExcelConstructorController extends Controller
{
    //

    function index(){
/*
    $exc = new ExcelConstructor();
    $str = $exc->view();
    return $str;
*/
    return Excel::download(new ExcelConstructor, "Отчет.xlsx");
    }
}

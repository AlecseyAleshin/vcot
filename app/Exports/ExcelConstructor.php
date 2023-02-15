<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ExcelConstructor implements FromView
{

    function __construct()
    {
        $this->field = [
            'employersName' => "Работодатель",
            'employersRegion' => "Регион",
            'employersOKVED' => "ОКВЭД",
            'workplacesProfessionOK' => "Профессия по ОК",
            'workplacesNameStructure' => "Наименование в структуре",
            'workplacesPMNumber' => "Номер РМ",
            'dangersObjectStudy' => "Объект исследования",
            'dangersSourceDanger' => "Источник опасности",
            'dangersDanger' => "Опасность",
            'dangersDangerousEvent' => "Опасное событие",
            'dangersLevelRisk' => "Уровень риска",
            'dangersLevelResidualRisk' => "Уровень остаточного риска",
            'controlMeasuresTypesMeasure' => "Типы мер",
            'controlMeasuresContentMeasure' => "Содержание мер"
        ];
    }

    public function view(): View
    {
        $s = request()->getContent();

        $field = json_decode(request()->getContent(), true);

        foreach($field[0] as $key => $val){
            $name[] = $key;
            $name2[] = $this->field[$key];
        }

        //print_r($name);
        return view('constructor', ['count' => count($field[0]), 'arr' => $field, 'field' =>$name, 'pole'=>$name2]);
    }


}



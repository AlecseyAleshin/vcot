<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ExcelConvert implements FromView
{
    public function __construct($answer, $title)
    {
        $this->answer = $answer;
        $this->title = $title;
    }

    public function view(): View
    {
        return view('analyticreport.analysis', ['db'  => $this->answer, 'title' => $this->title]);
    }


}

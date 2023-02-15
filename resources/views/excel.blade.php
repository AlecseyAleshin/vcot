<table>
    <tr>
        <td colspan="10" align="right"><b>Приложение 5</b></td>
    </tr>
    <tr height="50">
        <td colspan="10" align="center" style="font-size:18px;"><b><h3>Реестр оценки профессиональных рисков с результатами оценки остаточного риска</h3></b></td>
    </tr>
    <tr>
        <td colspan="10" align="center" style="border-bottom: solid 1px;">{{$header->employer_fullname}}</td>
    </tr>
    <tr>
        <td colspan="10" align="center" valign="top" style="font-size: 9px;">(полное наименование работодателя)</td>
    </tr>
    <tr>
        <td colspan="10" align=center style="border-bottom: solid 1px;">{{$sub}}</td>
    </tr>
    <tr>
        <td colspan="10" align="center" valign="top" style="font-size: 9px;">(наименование структурного подразделения)</td>
    </tr>
    <tr>
        <td colspan="10" align="left">Метод оценки: {{$header->matrix}}</td>
    </tr>
    <tr>
        <td colspan="10" align="center" style="font-size: 16px;font-weight:bold;">Часть 1. Оценка профессиональных рисков общедоступных мест</td>
    </tr>
    <tr>
        <td colspan="10" align="center">(мест общего пользования, мест пребывания людей, общественных мет)</td>
    </tr>
    <tr height=30>
       <th rowspan="2" width=5 align="center" valign="middle" style="border: solid 1px;">№</th>
       <th rowspan="2" width=20 align="center" valign="middle"  style="border: solid 1px;">Опасность/<br>Опасные места</th>
       <th rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;">Объект исследоввания/<br>источник опасности</th>
       <th rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;">Количество рабочих<br> мест</th>
       <th rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;">Класс условий труда<br> по результатам СОУТ</th>
       <th rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;"> Существующий уровень оценки<br> ПР и отношение к нему</th>
       <th colspan="2" align="center" valign="middle" style="border: solid 1px;">Дополнительные меры управления/контроля ПР</th>

       <th colspan="2" rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;word-wrap: break-word;">Остаточный уровень оценки ПР и отношение к нему</th>
    </tr>
    <tr height=30>
        <td width=20 align="center" valign="middle" style="border: solid 1px;">Меры по безопасному<br> проведению работ</td>
        <td width=20 align="center" valign="middle" style="border: solid 1px;">СИЗ</td>

    </tr>
    <tr>
        <td valign="middle" align="center" style="border: solid 1px;">1</td>
        <td valign="middle" align="center" style="border: solid 1px;">2</td>
        <td valign="middle" align="center" style="border: solid 1px;">3</td>
        <td valign="middle" align="center" style="border: solid 1px;">4</td>
        <td valign="middle" align="center" style="border: solid 1px;">5</td>
        <td valign="middle" align="center" style="border: solid 1px;">6</td>
        <td valign="middle" align="center" style="border: solid 1px;">7</td>
        <td valign="middle" align="center" style="border: solid 1px;">8</td>
        <td colspan="2" valign="middle" align="center" style="border: solid 1px;">9</td>
    </tr>
</table>
<table>
    <tr>



    </tr>
</table>
<table>
    <tr>
        <td colspan="10" align="center" style="font-size: 16px; font-weight: bold;">Часть 2. Оценка профессиональных рисков на рабочих местах</td>
    </tr>
    <tr height=30>
        <td rowspan="2" width=5 align="center" valign="middle" style="border: solid 1px;">№</td>
        <td rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;">Опасность/<br>Опасное событие</td>
        <td rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;">Объект исследования/<br>Источник опасности</td>
        <td rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;">Наименование<br>структурного<br>подразделения</td>
        <td rowspan="2" width=10 align="center" valign="middle" style="border:solid 1px;">Номер и<br>наименование<br>РМ</td>
        <td rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;">Класс условий<br>труда по<br>результатм<br>СОУТ</td>
        <td rowspan="2" width=20 align="center" valign="middle" style="border: solid 1px;">Существующий<br>уровень оценки<br>ПР и отношение<br>к нему</td>
        <td colspan="2" align="center" valign="middle" style="border:solid 1px;">Дополнительные меры управления/контроля ПР</td>
        <td rowspan="2" width=20 align="center" valign="middle" style="border:solid 1px;word-wrap: break-word;">Остаточный уровень оценки ПР и отношение к нему</td>
    </tr>
    <tr height=30>
        <td width=20 align="center" valign="middle" style="border: solid 1px;">Меры по безопасному<br> проведению работ</td>
        <td width=20 align="center" valign="middle" style="border: solid 1px;">СИЗ</td>
    </tr>
    <?php $i=1;?>
    @foreach($db as $item)
        <tr>
            <td width=5 align="center" valign="middle" style="border: solid 1px;"><?=$i?></td>
            <?php $i++;?>
            <td width=20 align="center" valign="middle" style="border: solid 1px;word-wrap: break-word;">{{$item['name']}}/<br>{{$item['danger_event']}}</td>
            <td width=20 align="center" valign="middle" style="border: solid 1px;word-wrap: break-word;">{{$item['object']}}/<br>{{$item['occurrence_object']}}</td>
            <td width=20 align="center" valign="middle" style="border: solid 1px;word-wrap: break-word;">{{$item['department']}}</td>
            <td width=10 align="center" valign="middle" style="border:solid 1px;word-wrap: break-word;"><?php echo $item['code'];?><br>{{$item['num_job_title']}}</td>
            <td width=20 align="center" valign="middle" style="border: solid 1px;word-wrap: break-word;"><?php echo $item['sout'];?></td>
            <td width=20 align="center" valign="middle" style="border: solid 1px;word-wrap: break-word;">{{$item['current_risk']}}</td>
            <td width=20 align="center" valign="middle" style="border: solid 1px;word-wrap: break-word;">
                {{$item['work']}}</td>
            <td width=20 align="center" valign="middle" style="border: solid 1px;word-wrap: break-word;">{{$item['siz']}}</td>
            <td align="center" valign="middle" style="border:solid 1px;word-wrap: break-word;">{{$item['residual_risk']}}</td>


        </tr>
    @endforeach

</table>
<table>
    <tr>
        <td colspan="10" align="left" style="border-bottom: solid 1px;font-size:18px;"><b><h3>Подготовил</h3></b></td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="top" style="font-size: 9px;"></td>
        <td align="center" valign="top" style="font0size: 9px;">должность</td>
        <td align="center" valign="top" style="font0size: 9px;"></td>
        <td align="center" valign="top" style="font0size: 9px;">личная подпись</td>
        <td colspan="2" align="center" valign="top" style="font0size: 9px;"></td>
        <td align="center" valign="top" style="font0size: 9px;">расшифровка подписи</td>
    </tr>
    <tr>
        <td colspan="3" align="left" style="border-bottom: solid 1px;font-size:18px;"><b><h3>Дата</h3></b></td>
    </tr>
</table>

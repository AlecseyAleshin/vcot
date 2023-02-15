<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/app.css">
    <style type="text/css">

        /* TD{

         border: 1px solid black; /* Параметры рамки */
        */

       </style>
  </head>
<body style="font-family: DejaVu Sans, sans-serif;font-size: 10px;">

<table>
    <tr>
        <td></td>
        <td colspan="3" align="center" style="font-size:14px"><b><h3>Анализ по организациям по результатам проекта по апробации <br>
            законодательства в сфере оценки профессиональных рисков</h3></b></td>
        <td></td>
    </tr>
</table>

<table style="border-collapse: collapse;">
    <tr>
        <td rowspan='2' align="center" valign="middle" style="border: 1px solid black">№</td>
        <td rowspan='2' align="center" valign="middle" style="border: 1px solid black">Наименование организации</td>
        <td rowspan='2' align="center" valign="middle" style="border: 1px solid black">Матрица оценки</td>
        <td colspan="2" align="center" valign="middle" style="border: 1px solid black">Количество РМ</td>
    </tr>
    <tr>
        <td align='center' style="border: 1px solid black;width:80px;">Общее</td>
        <td align='center' style="border: 1px solid black;width:80px;word-wrap:break-word;">С высоким уровнем риска</td>
    </tr>
    <?php
    $i=1;
    $okved = "";
    ?>
@foreach($db as $key => $a)
@if($okved <> $key)
    <?php $okved = $key;?>
    <tr>
        <td colspan="5" align="center" valign="middle" style="border: 1px solid black">ОКВЭД {{$key}}, {{$a['name']}}</td>
    </tr>
@endif
        <tr>
            <td style="border: 1px solid black; word-wrap: break-word;"><?=$i;?></td>
            <td style="border: 1px solid black; word-wrap: break-word;">{{$a['name']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;">{{$a['risk']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;width:80px;">{{$a['countPM']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;width:80px;">{{$a['countPMRisk']}}</td>
        </tr>
        <?php $i++; ?>

@endforeach
</table>
</body>

</html>

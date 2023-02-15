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
        <td colspan="3" align="center" style="font-size:14px"><b><h3>{{$title['title']}}</h3></b></td>
        <td></td>
    </tr>
</table>

<table style="border-collapse: collapse;">
    <tr>
        <td rowspan='2' align="center" valign="middle" style="border: 1px solid black;word-wrap: break-word;">№</td>
        <td rowspan='2' align="center" valign="middle" style="border: 1px solid black;word-wrap: break-word;">{{$title['column2']}}</td>
        <td rowspan='2' align="center" valign="middle" style="border: 1px solid black;word-wrap: break-word;">{{$title['column3']}}</td>
        <td rowspan="2" align="center" valign="middle" style="border: 1px solid black;word-wrap: break-word;">{{$title['column4']}}</td>
        <td rowspan="2" align="center" valign="middle" style="border: 1px solid black;word-wrap: break-word;">{{$title['column5']}}</td>
        <td colspan="5" align="center" valign="middle" style="border: 1px solid black">Количество РМ в системе по проекту</td>
    </tr>
    <tr>
        <td align='center' style="border: 1px solid black;">{{$title['column6']}}</td>
        <td align='center' style="border: 1px solid black;">{{$title['column7']}}</td>
        <td align='center' style="border: 1px solid black;">{{$title['column8']}}</td>
        <td align='center' style="border: 1px solid black;">{{$title['column9']}}</td>
        <td align='center' style="border: 1px solid black;">{{$title['column10']}}</td>
    </tr>
    <?php
    $i=1;
    ?>
@foreach($db as $key => $a)
        <tr>
            <td style="border: 1px solid black; word-wrap: break-word;"><?=$i;?></td>
            <td style="border: 1px solid black; word-wrap: break-word;">{{$a['employer']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;">{{$a['expert']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;width:80px;">{{$a['averageCount']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;width:80px;">{{$a['countPM']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;width:50px;">{{$a['month6']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;width:50px;">{{$a['month7']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;width:50px;">{{$a['month8']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;width:50px;">{{$a['month9']}}</td>
            <td align="center" style="border: 1px solid black; word-wrap: break-word;width:50px;">{{$a['month10']}}</td>
        </tr>
        <?php $i++; ?>

@endforeach
</table>
</body>

</html>

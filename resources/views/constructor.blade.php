<?php $width = 900 / $count; ?>
<table>
    <tr>
        @for($i = 1; $i <= $count; $i++)
                <td align="center" style="width: {{$width}}px; word-wrap: break-word;"><b>{{$pole[$i-1]}}</b></td>
        @endfor
    </tr>
        @foreach($arr as $val)
            <tr>
                @for($i = 1; $i <= $count; $i++)
                        <td style="word-wrap: break-word;">{{$val[$field[$i-1]]}}</td>
                @endfor
            </tr>
        @endforeach
</table>

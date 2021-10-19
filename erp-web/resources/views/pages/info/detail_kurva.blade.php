<?php
function countDate($start, $end){
    $startTimeStamp = strtotime($start);
    $endTimeStamp = strtotime($end);

    $timeDiff = abs($endTimeStamp - $startTimeStamp);

    $numberDays = $timeDiff/86400;  // 86400 seconds in one day

    // and you might want to convert to integer
    $numberDays = intval($numberDays);
    return $numberDays;
}
function cekDate($date_check, $start, $end){
    $paymentDate = $date_check;
    $contractDateBegin = strtotime($start);
    $contractDateEnd = strtotime($end);

    if($paymentDate > $contractDateBegin && $paymentDate < $contractDateEnd) {
        return 1;
    } else {
        return 0;
    }    
}
function getLongWork($start, $end){
    $diff = strtotime(($end != null ? $end : date('Y-m-d H:i:s'))) - strtotime($start);
    return $diff;
}
function countLongWork($long_work){
    if ($long_work == 0) {
        return '';
    }else{
        return gmdate('H:i:s', $long_work);
    }
}
function formatNumber($val){
    $val=number_format($val, 0, '.', '.');
    return $val;
}
$dev_start=$get_dev->work_start != null ? $get_dev->work_start : $get_pw[0]->work_start;
$dev_end=$get_dev->work_end != null ? $get_dev->work_end : $get_pw[0]->finish_date;

$req_start=date('Y-m-d', strtotime($get_pw[0]->work_start));
$req_end=date('Y-m-d', strtotime($get_pw[0]->finish_date));

$get_start=strtotime($get_pw[0]->work_start) < strtotime($dev_start) ? $get_pw[0]->work_start : $dev_start;
$get_end=strtotime($get_pw[0]->finish_date) > strtotime($dev_end) ? $get_pw[0]->finish_date : $dev_end;
$work_start=date('Y-m-d', strtotime($get_start));
$work_end=date('Y-m-d', strtotime($get_end));
?>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Pekerjaan</th>
                <th></th>
                <th>Estimasi</th>
                @for($i = strtotime($work_start); $i <= strtotime($work_end); $i = $i + 86400)
                <?php $colour=strtotime($req_start) == $i ? '#2962FF; color:white' : (strtotime($req_end) == $i ? '#7460ee; color:white' : '') ?>
                <th style="background-color: {{$colour}}">{{date("d-m-Y", $i)}}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
        <?php 
        $a=count($get_pw);
        ?>
        @foreach($get_pw as $value)
            <tr>
                <td>{{$a}}</td>
                <td>{{$value->name}}</td>
                <td></td>
                <td></td>
                @for($i = strtotime($work_start); $i <= strtotime($work_end); $i = $i + 86400)
                <td></td>
                @endfor
            </tr>
            @foreach($value->pws as $v)
                <?php $total_days=0;?>
                @for($i = strtotime($work_start); $i <= strtotime($work_end); $i = $i + 86400)
                    <?php $in_days=0;?>
                    @if($v->dps_duration != null)
                        
                        @foreach($v->dps_duration as $dd)
                            <?php 
                            $date=date('Y-m-d', strtotime($dd->work_start));
                            if ($date == date('Y-m-d', $i)) {
                                $in_days=1;
                            }
                            ?>
                        @endforeach
                    @endif
                    <?php $total_days+=$in_days?>
                @endfor
                <?php
                $estimasi=countDate($v->work_start, $v->work_end);
                $color=$total_days < $estimasi ? '#36bea6; color:white' : '#ffbc34;  color:white';
                $num_estimasi=0;
                ?>
                <tr>
                    <td></td>
                    <td>{{$v->name}}</td>
                    <td class="text-center">{{formatNumber($v->amount * $v->base_price)}}</td>
                    <td class="text-center">{{$estimasi}}</td>
                    @for($i = strtotime($work_start); $i <= strtotime($work_end); $i = $i + 86400)
                    @php $long_work=$b=0 @endphp
                    @if($v->dps_duration != null)
                    @foreach($v->dps_duration as $dd)
                    <?php 
                    $date=date('Y-m-d', strtotime($dd->work_start));
                    if ($date == date('Y-m-d', $i)) {
                        $long_work+=getLongWork($dd->work_start, $dd->work_end);
                        $b=1;
                    }
                    ?>
                    @endforeach
                    @endif
                    <?php $num_estimasi+=$b?>
                    <?php $color=$i > strtotime($req_end) || $num_estimasi > $estimasi ? '#f62d51; color:white' : $color ?>
                    <td class="text-center"  style="background-color: {{$long_work != 0 ? $color : ''}}">{{countLongWork($long_work)}}</td>
                    @endfor
                </tr>
            @endforeach
            <?php $a-- ?>
        @endforeach
        </tbody>
    </table>
    Keterangan : 
    <span class="badge badge-info text-info">asdf</span> Perkiraan Project dimulai 
    <span class="badge badge-primary text-primary">asdf</span> Deadline Project
    <span class="badge badge-success text-success">asdf</span> Lebih Cepat dari Estimasi
    <span class="badge badge-warning text-warning">asdf</span> Sesuai dengan Estimasi
    <span class="badge badge-danger text-danger">asdf</span> Lebih lambat dari Estimasi
</div>
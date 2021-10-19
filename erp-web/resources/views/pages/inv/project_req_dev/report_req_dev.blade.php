@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Permintaan Pengerjaan Project</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Daftar Permintaan Pengerjaan Project</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<?php
    function formatDate($date) {
      if($date == null){
        return '-';
      }else{
        $temp=date("d-m-Y H:i:s", strtotime($date));
        return $temp;
      }
    }

    function countLongWork($long_work){
        return gmdate('H:i:s', $long_work);
        // $day = Math.floor($long_work / 86400);
        // $long_work -= day * 86400;
        // // calculate hours
        // $hour = Math.floor(diffInMilliSeconds / 3600) % 24;
        // $long_work -= hour * 3600;

        // // calculate minutes
        // $minutes = Math.floor(diffInMilliSeconds / 60) % 60;
        // $long_work -= minutes * 60;

        // $seconds = $long_work % 60;
    }
?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Aktivitas Pengerjaan</h4>
            @foreach($data as $value)
            {{$value->total}} Kavling {{$value->type_kavling}}

                <div class="profiletimeline">
                @foreach($value->dev_project_ds as $dd)
                    <div class="sl-item">
                        <div class="sl-left"> <img src="{!! asset('theme/assets/images/users/worker.png') !!}" alt="user" class="rounded-circle" /> </div>
                        <div class="sl-right">
                            <div>
                                <h5>{{$dd->work_detail}}</h5>
                                <p>Pengawas : {{$dd->name}}</p>
                                <!-- <p>Pekerjaan Dimulai : {{formatDate($dd->work_start)}}</p>
                                <p>Pekerjaan Berahir : {{formatDate($dd->work_end)}}</p> -->
                                <p>Durasi Kerja : {{countLongWork($dd->long_work)}}</p>
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th>Mulai</th>
                                            <th>Istirahat / Selesai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dd->durations as $duration)
                                        <tr>
                                            <td>{{formatDate($duration->work_start)}}</td>
                                            <td>{{formatDate($duration->work_end)}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                @if($dd->worker != null)
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Nama Pekerja </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dd->worker as $w)
                                        <tr>
                                            <td>
                                              <i class="mdi mdi-arrow-right"></i>  {{$w->name_worker}} :
                                            </td>
                                            <th>{{$w->notes}}</th>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Label Yang Dikerjakan : </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dd->label as $w)
                                        <tr>
                                            <td>
                                              <i class="mdi mdi-arrow-right"></i>  {{$w->no}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- <div class="like-comm"> <a href="javascript:void(0)" class="link m-r-10">2 comment</a> <a href="javascript:void(0)" class="link m-r-10"><i class="fa fa-heart text-danger"></i> 5 Love</a> </div> -->
                            </div>
                        </div>
                    </div>
                    <hr>
                @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
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

<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="text-right">
                <a href="{{ URL::to('project_req_dev/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Permintaan Pengerjaan Project</h4>
                    <div class="table-responsive">
                        <table id="order_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nomor Permintaan</th>
                                    <th class="text-center">Nomor SPK</th>
                                    <th class="text-center">Nomor RAB</th>
                                    <th class="text-center">Kavling</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Tanggal Permintaan</th>
                                    <th class="text-center">Tanggal Perkiraan Mulai Pekerjaan</th>
                                    <th class="text-center">Tanggal Deadline</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- /.modal -->
    </div>                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#order_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('project_req_dev/json') }}",
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "no"},
            {"data": "spk_number"},
            {"data": "rab_no"},
            {"data": "type_kavling"},
            {"data": "total"},
            {"data": "request_date", "class" : 'text-center', "render": function(data, type, row){return formatDate2(row.request_date)}},
            {"data": "work_start", "class" : 'text-center', "render": function(data, type, row){return formatDate2(row.work_start)}},
            {"data": "finish_date", "class" : 'text-center', "render": function(data, type, row){return formatDate2(row.finish_date)}},
            {"data": "action"}
        ],
    } );
});
function formatTanggal(date) {
  var temp=date.split('-');
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}
function getDetail(el){
    var id=$(el).data('id');
    var order_no=$(el).data('order_no');
    $('#title_detail').html('Detail Order '+order_no);
    t = $('#detail_order').DataTable();
    t.clear().draw(false);
    $.ajax({
        // type: "post",
        url: "{{ URL::to('order/detail') }}"+'/'+id,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            var j=0;
            for(i = 0; i < arrData.length; i++){
                j+=1;
                t.row.add([
                    '<div>'+arrData[i]['item']+' '+arrData[i]['name']+' Series : '+arrData[i]['series']+' Dimensi W :'+arrData[i]['panjang']+' H : '+arrData[i]['lebar']+'</div>',
                    '<div>'+arrData[i]['total']+'</div>',
                    '<div>'+(arrData[i]['no'] != null ? arrData[i]['no'] : '-') +'</div>',
                    '<div>'+(arrData[i]['estimate_end'] != null ? formatDateID(new Date(arrData[i]['estimate_end'])) : '-') +'</div>',
                    '<div>'+(arrData[i]['is_final'] != null ? (arrData[i]['is_final'] == 0 ? 'In Rab' : (arrData[i]['is_final_production'] != null ? (arrData[i]['is_final_production'] == 0 ? 'Running' : 'Final') : 'In Rab')) : '-') +'</div>',
                    '</div>'
                ]).draw(false);
            }
        }
    });
}
function formatDate2(date){
        if (date == null) {
            return '-';
        }else{

            var myDate = new Date(date);
            var tgl=date.split(/[ -]+/);
            // var output = tgl[2] + "-" +  tgl[1] + "-" + tgl[0] + ' ' + tgl[3];
            var output = tgl[2] + "-" +  tgl[1] + "-" + tgl[0];
            return output;
        }
    }
</script>
@endsection
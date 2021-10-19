@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Penagihan Customer</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Daftar Penagihan Customer</li>
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
                <!-- <a href="{{ URL::to('order/bill') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Buat Tagihan</button></a> -->
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Penagihan Customer</h4>
                    <div class="table-responsive">
                        <table id="bill_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Customer</th>
                                    <th class="text-center">Nomor Tagihan</th>
                                    <th class="text-center">Tanggal Jatuh Tempo</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Alasan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="min-width:70px"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="add_reason" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail_install">Isi Follow Up Penagihan</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <form method="POST" action="#" id="report_bill" enctype="multipart/form-data">
                        @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <select name="status" id="status" required class="form-control select2" style="width:100%">
                                        <option value="">Pilih Status</option>
                                        <option value="1">Tidak Bisa Dihubungi</option>
                                        <option value="2">Bisa Dihubungi (No JB)</option>
                                        <option value="3">Bisa Dihubungi (JB)</option>
                                        <option value="4">Serahkan BG Belum Isi</option>
                                        <option value="5">BG Siap Cair / Sudah Transfer</option>
                                        <option value="6">Selesai</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Alasan</label>
                                    <textarea name="alasan" id="alasan" required class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="cb_id" id="cb_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                        <button class="btn btn-success waves-effect text-left" type="submit">Simpan</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail">Detail Penagihan</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <h4></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="list_followup">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Alasan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>                
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    dt = $('#bill_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('order/list_penagihan_json') }}",
        aaSorting: [[2, 0, 'desc']],
        "columns": [
            {"data": "coorporate_name", "class" : "text-center"},
            {"data": "bill_no", "class" : "text-center"},
            {"data": "due_date", "class" : "text-center",
            "render": function(data, type, row){return row.due_date != null ? formatDateID(new Date((row.due_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount)}},
            {"data": "reason_of_bill", "class" : "text-center"},
            {"data": "status", "class" : "text-center",
            "render": function(data, type, row){return getStatus(row.status)}},
            {"data": "id", "class" : "text-center", 
            "render" : function(data, type, row){
                return '<button type="button" id="modal_detail" class="btn btn-primary btn-sm" data-toggle="modal" data-id="'+row.id+'" onclick="setId(this)" data-target="#add_reason"><i class="mdi mdi-plus"></i></button> <button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-id="'+row.id+'" data-target=".bs-example-modal-lg" onclick="getDetail(this)"><i class="mdi mdi-eye"></i></button>'
            }},
        ]
    } );

    $("form#report_bill").on("submit", function( event ) {
        var form = $('#report_bill')[0];
        var data = new FormData(form);
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ URL::to('order/report_bill') }}", //json get site
            dataType : 'json',
            data: data,
            processData: false,
            contentType: false,
            success: function(response){
                dt.ajax.reload();
            }
        });
        $('#add_reason').modal('hide');
        
    });    
    
});
function formatTanggal(date) {
    if (date == null) {
        return '-';
    }else{
        var temp=date.split('-');
        return temp[2] + '-' + temp[1] + '-' + temp[0];
    }
}
function setId(eq){
    id=$(eq).data('id')
    $('#cb_id').val(id);
}
function getStatus(status){
    if(status == null){
        return '-';
    }else if(status == 1){
        return 'Tidak Bisa Dihubungi'
    }else if(status == 2){
        return 'Bisa Dihubungi (No JB)'
    }else if(status == 3){
        return 'Bisa Dihubungi (JB)'
    }else if(status == 4){
        return 'Serahkan BG Belum Isi'
    }else if(status == 5){
        return 'BG Siap Cair / Sudah Transfer'
    }else if(status == 6){
        return 'Selesai'
    }
}

function getDetail(el){
    var id=$(el).data('id');
    t = $('#list_followup').DataTable();
    t.clear().draw(false);
    $.ajax({
        // type: "post",
        url: "{{ URL::to('order/list_followup') }}"+'/'+id,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            var j=0;
            for(i = 0; i < arrData.length; i++){
                j+=1;
                jam=arrData[i]['date_bill'].split(' ')
                t.row.add([
                    j,
                    '<div>'+arrData[i]['reason_of_bill']+'</div>',
                    '<div>'+getStatus(arrData[i]['status_bill'])+'</div>',
                    '<div>'+(arrData[i]['date_bill'] != null ? formatDateID(new Date(arrData[i]['date_bill']))+ ' ' +jam[1] : '-') +'</div>'
                ]).draw(false);
            }
        }
    });
}
function formatRupiah(angka, prefix)
{
    if(angka == 'NaN' || angka === null){
        return 0;
    }
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}

</script>
@endsection
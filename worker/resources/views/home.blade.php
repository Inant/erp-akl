@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-md-5 align-self-center">
            <h4 class="page-title">Dashboard</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Library</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-7 align-self-center">
            
        </div>
    </div>
</div>
@endsection

@section('content')
<style>
    @media only screen and (max-width: 600px) {
      table {
        font-size: 14px;
      }
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 id="titleJurnal">Cari Pekerjaan</h4>
                            <form method="POST" action="{{ URL::to('material_request/get_project') }}" class="mt-4">
                              @csrf
                                <div class="form-group">
                                    <select id="customer_id" class="form-control select2" name="customer_id" style="width:100%">
                                        <option value="">--- Pilih Customer ---</option>
                                        @foreach($customer as $value)
                                        <option value="{{$value['id']}}">{{$value['coorporate_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- &nbsp; 
                                <select id="select_bagian" class="form-control select2" name="pws_id" style="width:80%">
                                    <option value="">--- Cari Bagian ---</option>
                                </select>
                                &nbsp;
                                <button type="submit" class="btn btn-success"><i class="fa fa-eye"></i></button>&nbsp; -->
                                <div class="form-group">
                                    <select id="select_product" class="form-control select2" name="inv_id" style="width:100%" required>
                                        <option value="">--- Cari Kavling ---</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select id="work_header" class="form-control select2" name="work_header" onchange="getWorkDetail(this.value)" style="width:100%">
                                        <option value="">--- Pilih Pekerjaan ---</option>
                                        <option value="Pembuatan Daun Jendela">Pembuatan Daun Jendela</option>
                                        <option value="Pembuatan Daun Pintu">Pembuatan Daun Pintu</option>
                                        <option value="Pembuatan Kusen Pintu">Pembuatan Kusen Pintu</option>
                                        <option value="Pembuatan Kusen Jendela Swing/Hung">Pembuatan Kusen Jendela Swing/Hung</option>
                                        <option value="Pembuatan Jendela Kaca Mati">Pembuatan Jendela Kaca Mati</option>
                                        <option value="Pembuatan Kusen Pintu Sliding">Pembuatan Kusen Pintu Sliding</option>
                                        <option value="Pembuatan Daun Pintu Sliding">Pembuatan Daun Pintu Sliding</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select id="work_detail" class="form-control select2" name="work_detail" style="width:100%" required>
                                    </select>
                                </div>
                                <!-- <div class="form-group">
                                    <select id="pw_id" class="form-control select2" name="pw_id" style="width:100%">
                                        <option value="">--- Cari Pekerjaan ---</option>
                                    </select>
                                </div>
                                <input type="" name="product_id" id="product_id">
                                <div class="form-group">
                                    <select id="select_bagian" class="form-control select2" name="pws_id" style="width:100%" required>
                                        <option value="">--- Cari Bagian ---</option>
                                    </select>
                                </div> -->
                                <button type="submit" class="btn btn-success btn-block">pantau</button>
                             </form>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table id="pengambilan_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Pekerjaan</th>
                                    <th class="text-center" width="200px">Kavling</th>
                                    <th class="text-center">Nomor Permintaan Material</th>
                                    <th class="text-center">Nomor RAB</th>
                                    <th class="text-center">Nomor Permintaan Pengerjaan</th>
                                    <th class="text-center">Created at</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="min-width:150px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Material List Material Request</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Material Request Detail</h4>
                <p id="label-detail"></p>
                <div class="table-responsive">
                <h4>Material Utuh</h4>
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Material No</th>
                                <th class="text-center">Material Name</th>
                                <!-- <th class="text-center">Pilih</th> -->
                                <!-- <th class="text-center">Qty Pengajuan</th> -->
                                <th class="text-center">Qty</th>
                                <th class="text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <hr>
                    <h4>Material Tidak Utuh</h4>
                    <table id="zero_config3" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Material No</th>
                                <th class="text-center">Material Name</th>
                                <!-- <th class="text-center">Pilih</th> -->
                                <th class="text-center">Kondisi</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade bs-example-modal-lg" id="modalProgress" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Progress</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                    <table id="detail_progress" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Pekerjaan</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

var t2 = $('#zero_config2').DataTable();
var t3 = $('#zero_config3').DataTable();
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#pengambilan_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "material_request/list",
        "aaSorting": [[ 5, "desc" ]],
        "columns": [
            {"data": "work_header"},
            // {"data": "project_name"},
            {"data": "pr_name", "render": function(data, type, row){return row.type_kavling}},
            {"data": "no"},
            {"data": "rab_no"},
            {"data": "req_no"},
            {"data": "created_at", "render": function(data, type, row){return formatDate(row.created_at)}},
            {"data": "total"},
            {"data": "is_done", "render": function(data, type, row){return row.is_done == null || row.is_done == false ? 'Belum Selesai' : 'Selesai'}},
            {"data": "id", "render": function(data, type, row){
                return '<div class="form-inline"><button onclick="doShowDetail2('+row.id+');" data-toggle="modal" data-target="#modalDetail" class="btn waves-effect waves-light btn-xs btn-info">Detail</button>&nbsp;<button onclick="doShowProgress(this);" data-id="'+row.dp_id+'" data-inv_id="'+row.id+'" data-work_header="'+row.work_header+'" data-toggle="modal" data-target="#modalProgress" class="btn waves-effect waves-light btn-xs btn-primary">Pantau</button>&nbsp;<a href="{{URL::to('material_request/report')}}'+'/'+row.dp_id+'" class="btn waves-effect waves-light btn-xs btn-success">Laporan</a></div>';
            }}
        ],
    } );
    var resultSuggest=[];
    $('#customer_id').change(function(){
        resultSuggest=[];
        $('#pw_id').val('');
        $('#product_id').val('');
        $('#select_product').empty();
        $('#select_product').append('<option value="">-- Pilih Kavling --</option>');
        $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/get_project_by_cust') }}" + "/" + this.value, //json get site
            dataType : 'json',
            success: function(response){
                arrData=response['data'];
                $.each(arrData, function(i, item){
                    $('#select_product').append('<option value="'+item.id+'">'+item.inv_no+' Kavling : '+item.type_kavling+'</option>');
                })
            }
        });
    });
    
    $('#select_product').change(function(){
        resultSuggest=[];
        var id=$('#select_product').val();
        $('#pw_id').empty();
        $('#pw_id').append('<option value="">-- Pilih Pekerjaan ---</option>');
        $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/get_pw_by_inv') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData=response['data'];
                resultSuggest=arrData;
                $.each(arrData, function(i, item){
                    $('#pw_id').append('<option value="'+item.id+'">'+item.name+' ('+item.item+' '+item.series+')</option>');
                })
            }
        });
    });
    $('#pw_id').change(function(){
        var product_id=0;
        var id=this.value;
        $.each(resultSuggest, function(i, item){
            console.log(item.id)
            console.log(id)
            if (item.id == id) {
                product_id=item.product_id;
            }
        })
        $('#product_id').val(product_id);
        getPws(id);
    });
});

function getPws(id){
    var select_bagian=$('#select_bagian');
    select_bagian.empty();
    select_bagian.append('<option value="">-- Pilih Bagian --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/getProjectWorkSub') }}" + "/" + id, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for (var i = 0; i < arrData.length; i++) {
                select_bagian.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}
function formatDate(date) {
  var temp=date.split(/[.,\/ -]/);
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}

function doShowDetail2(id){
    idRequest = id;
    t2.clear().draw(false);
    t3.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/list_detail_acc') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data']['detail'];
                arrData2 = response['data']['detail_rest'];
                arrData3 = response['data']['prod_sub'];
                var label='';
                for(i = 0; i < arrData3.length; i++){
                    label+=arrData3[i]['no']+', ';
                }
                $('#label-detail').html('Untuk Pengerjaan Produk : '+label.replace(/, +$/, ''));

                for(i = 0; i < arrData.length; i++){
                    let amount_auth = arrData[i]['amount_auth'] != null ? arrData[i]['amount_auth'] : arrData[i]['amount'];
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+parseFloat(arrData[i]['amount'])+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>'
                    ]).draw(false);
                }
                for(i = 0; i < arrData2.length; i++){
                    t3.row.add([
                        '<div class="text-left">'+arrData2[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData2[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+parseFloat(arrData2[i]['amount'])+'</div>',
                        '<div class="text-right">'+parseFloat(arrData2[i]['total'])+'</div>',
                        '<div class="text-center">'+arrData2[i]['m_units']['name']+'</div>'
                    ]).draw(false);
                }
            }
    });
}
function getWorkDetail(val){
    var work_window=["Pemotongan Pintu/Jendela","Memasang Karet, Spigot","Menyiapkan Mesin Clamping","Perapian Karet dan Pemasangan Glass Bite","Pemasangan Engsel Cassmen, Handle, lubang setting lock dan jalan air","Merakit Daun Jendela ke Kusen"];
    var work_door=["Pemotongan Pintu/Jendela","Plong Bor Manual","Pemasangan Handle, lubang setting lock dan Engsel Swing","Merakit Daun Jendela ke Kusen"];
    var work_kusen_door=["Pemotongan Bahan Kusen","Menyiapkan Mesin Clamping","bor hinge dan backplate","Pasang Shimreciever","Pasang Karet", "Pasang Sealerpad, Pasang Setting Blok", 'Rakit Kusen Pintu, Sealent', 'Packing Pintu'];
    var work_kusen_window=["Pemotongan Bahan Kusen","Menyiapkan Mesin Clamping","Pasang Shimreciever","Pasang Karet", "Pasang Sealerpad", 'Pasang Sealerpad', 'Rakit Kusen Pintu', 'Packing Pintu'];
    var work_jendela_kaca_mati=["Pemotongan Bahan","Menyiapkan Mesin Clamping","rakit jendela","Pasang Shimreciever", 'Pasang Glasbit'];
    var work_kusen_pintu_sliding=["Pemotongan Bahan","Menyiapkan Mesin Clamping","Pasang Shimreciever", "Pasang Karet dan sealerpad", 'Packing'];
    var work_kusen_pintu_sliding=["Pemotongan Bahan","Menyiapkan Mesin Clamping","Pasang Daun Sliding", "Pasang Lockset dan Handle"];
    $('#work_detail').empty();
    if (val == 'Pembuatan Daun Jendela') {
        for(i = 0; i < work_window.length; i++){
            $('#work_detail').append('<option value="'+work_window[i]+'">'+work_window[i]+'</option>');
        }
    }else if (val == 'Pembuatan Daun Pintu') {
        for(i = 0; i < work_door.length; i++){
            $('#work_detail').append('<option value="'+work_door[i]+'">'+work_door[i]+'</option>');
        }
    }else if (val == 'Pembuatan Kusen Pintu'){
        for(i = 0; i < work_kusen_door.length; i++){
            $('#work_detail').append('<option value="'+work_kusen_door[i]+'">'+work_kusen_door[i]+'</option>');
        }
    }else if (val == 'Pembuatan Kusen Jendela Swing/Hung'){
        for(i = 0; i < work_kusen_window.length; i++){
            $('#work_detail').append('<option value="'+work_kusen_window[i]+'">'+work_kusen_window[i]+'</option>');
        }
    }else if (val == 'Pembuatan Jendela Kaca Mati'){
        for(i = 0; i < work_jendela_kaca_mati.length; i++){
            $('#work_detail').append('<option value="'+work_jendela_kaca_mati[i]+'">'+work_jendela_kaca_mati[i]+'</option>');
        }
    }else if (val == 'Pembuatan Kusen Pintu Sliding'){
        for(i = 0; i < work_kusen_pintu_sliding.length; i++){
            $('#work_detail').append('<option value="'+work_kusen_pintu_sliding[i]+'">'+work_kusen_pintu_sliding[i]+'</option>');
        }
    }else if (val == 'Pembuatan Daun Pintu Sliding'){
        for(i = 0; i < work_kusen_pintu_sliding.length; i++){
            $('#work_detail').append('<option value="'+work_kusen_pintu_sliding[i]+'">'+work_kusen_pintu_sliding[i]+'</option>');
        }
    }
}
function doShowProgress(eq){
    id=$(eq).data('id')
    inv_id=$(eq).data('inv_id')
    work_header=$(eq).data('work_header')
    console.log(work_header)
    idRequest = id;
    t4 = $('#detail_progress').DataTable();
    t4.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/get_detail_progress') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response;
                for(i = 0; i < arrData.length; i++){
                    let amount_auth = arrData[i]['amount_auth'] != null ? arrData[i]['amount_auth'] : arrData[i]['amount'];
                    t4.row.add([
                        '<div class="text-left">'+arrData[i]['work_detail']+'</div>',
                        '<div class="text-left"><button class="btn btn-success btn-sm" data-id="'+inv_id+'" data-work_header="'+work_header+'" data-work_detail="'+arrData[i]['work_detail']+'" onclick="submitForm(this)"><i class="fa fa-eye"></i></button></div>'
                    ]).draw(false);
                }
            }
    });
}
function submitForm(eq){
    inv_id=$(eq).data('id')
    work_header=$(eq).data('work_header')
    work_detail=$(eq).data('work_detail')

    var form = $('<form action="{{ URL::to('material_request/get_project') }}" method="post">' +
    '<input type="text" name="_token" value="{{ csrf_token() }}" />' +
    '<input type="text" name="inv_id" value="' + inv_id + '" />' +
    '<input type="text" name="work_header" value="' + work_header + '" />' +
    '<input type="text" name="work_detail" value="' + work_detail + '" />' +
    '</form>');
    $('body').append(form);
    form.submit();
}
</script>
@endsection

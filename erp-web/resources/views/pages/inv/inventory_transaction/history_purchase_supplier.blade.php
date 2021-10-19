@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Laporan Pembelian per Supplier</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Tagihan</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')
@php
    function formatRupiah($num){
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd-m-Y');
    }
@endphp
<style>
  .floatLeft { width: 100%;}
  .floatRight {width: 100%;}
  /* .floatLeft { width: 100%; float: left; }
  .floatRight {width: 100%; float: right; } */
    #table th, #table td{
        border:1px solid #7c8186;
        padding : 5px
    }
    .no-border{
        border:1px solid white !important;
        /* padding : 5px */
    }
  </style>
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Laporan Pembelian per Supplier</h4>
                    <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('inventory/po_history') }}">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="card-title">Pilih Supplier</h5>
                                    <div class="form-group">
                                        <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="suppl_single[]" name="suppl_single[]">
                                            <option value="all" {{$all_supplier == true ? 'selected' : ''}}>Semua</option>
                                            @foreach($suppliers as $value)
                                                <?php $same=false; ?>
                                                @foreach($supplier_selected as $v)
                                                    @if($v == $value['id'])
                                                        @php $same=true @endphp
                                                    @endif
                                                @endforeach
                                            <option value="{{$value['id']}}" {{$same == true ? 'selected' : ''}}>{{$value['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Tanggal Awal :</label>
                                        <input type="date" name="date" id="date" class="form-control" required value="{{$date1}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label >Tanggal Ahir :</label>
                                        <input type="date" name="date2" id="date2" class="form-control" required value="{{$date2}}">
                                    </div>
                                </div>
                                <div class="col-12 form-group">
                                    <button class="btn btn-primary" name="submit" value="1"><i class="fa fa-search"></i> Cari</button>
                                </div>
                            </div>
                        </form>
                    <!-- </div> -->
                    <br><br>
                    @if($data != null)
                    <!-- <form method="POST" action="{{ URL::to('inventory/export_history_po') }}" class="float-right" target="_blank">
                        @csrf
                        <div hidden>
                            <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="suppl_single1" name="suppl_single[]">
                                <option value="all" {{$all_supplier == true ? 'selected' : ''}}>Semua</option>
                                @foreach($suppliers as $value)
                                    <?php $same=false; ?>
                                    @foreach($supplier_selected as $v)
                                        @if($v == $value['id'])
                                            @php $same=true @endphp
                                        @endif
                                    @endforeach
                                <option value="{{$value['id']}}" {{$same == true ? 'selected' : ''}}>{{$value['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="date" name="date" hidden class="form-control"  required value="{{$date1}}">
                        <input type="date" name="date2" hidden class="form-control"  required value="{{$date2}}">
                        <div class="form-group">
                            <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                        </div>
                    </form> -->
                    <div class="form-group float-right">
                        <button class="btn btn-success" onclick="exportData()"><i class="fa fa-file-excel"></i> Export</button>
                    </div>
                    <div class="table-responsive">
                        <table style="border-collapse:collapse; width:100%">
                            <thead>
                                <tr id="table" class="text-primary">
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">No PO</th>
                                    <th class="text-center">No Invoice</th>
                                    <th class="text-center">No Surat Jalan</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Nilai DPP</th>
                                    <th class="text-center">Nama Pemasok</th>
                                    <th class="text-center">Detail</th>
                                    <th class="text-center">
                                    Export 
                                    <input style="min-width:20px" type="checkbox" name="select-all" id="select-all" checked>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $total_saldo_all=0 ?>
                            @foreach($data as $value)
                                <?php $total_saldo=$total_penambahan=$total_penurunan=0 ?>
                                @if($value['data'] != null)
                                <tr id="table">
                                    <td colspan="5" class="text-center"><a href="#" data-id="{{$value['supplier']->id}}" onclick="getReport(this)" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Lihat Tagihan Supplier"> {{$value['supplier']->name}}</a></td>
                                    <td></td>
                                    <td class="text-right"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @foreach($value['data'] as $v)
                                    @foreach($v['dt'] as $v1)
                                    <?php 
                                    $total_saldo+=$v1->total;
                                    $total_saldo_all+=$v1->total;
                                    ?>
                                    <tr id="table">
                                        <td>{{date('d-m-Y', strtotime($v['date']))}}</td>
                                        <td><a href="#" @if($v1->purchase_id != null) onclick="doShowDetail('{{$v1->purchase_id}}');" @else onclick="doShowDetail2('{{$v1->purchase_asset_id}}');" @endif data-toggle="modal" data-target="#modalShowDetail"  data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Lihat Detail PO">{{$v1->purchase_no != null ? $v1->purchase_no : $v1->purchase_asset_no}}</a></td>
                                        <td>{{$v1->paid_no}}</td>
                                        <td>{{$v1->no_surat_jalan}}</td>
                                        <td>{{$v1->purchase_notes != null ? $v1->purchase_notes : $v1->purchase_asset_notes}}</td>
                                        <td class="text-right">{{formatRupiah($v1->total)}}</td>
                                        <td>{{$value['supplier']->name}}</td>
                                        <td class="text-center"><a href="#" data-no="{{$v1->no_surat_jalan}}" data-date="{{$v1->inv_trx_date}}" class="btn btn-sm btn-info" onclick="doShowInv(this)" data-toggle="modal" data-target="#modalShowInv"><i class="fa fa-eye"></i></a></td>
                                        <td class="text-center">
                                        <input type="checkbox" name="export_id[]" id="export_id[]" onclick="cekChecked()" checked value="{{$v1->inv_trx_id}}">
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                <tr id="table" class="text-primary">
                                    <td>Total Penambahan</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{formatRupiah($total_saldo)}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endif
                            @endforeach
                                <tr id="table" class="text-primary">
                                    <td>Total</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{formatRupiah($total_saldo_all)}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>                
</div>
<div class="modal fade" id="modalShowInv" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Penerimaan Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="inv_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="modalBillDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{URL::to('payment/save_giro_detail')}}" method="post">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal">Laporan Pembelian per Supplier </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="giro_id" id="giro_id">
                <div class="form-group">
                    <label for="">Total Laporan Pembelian per Supplier :</label>
                    <input type="" readonly class="form-control" id="total1">
                    <input type="hidden" class="form-control" name="total" id="total">
                </div>
                <div class="form-group pull-right">
                    <button class="btn btn-primary" type="button" onclick="addRow()">add</button>
                </div>
                
                <table id="listDetail" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:40%">Total</th>
                            <th style="width:50%">Pilih Pencairan Akun</th>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
                <button class="btn btn-success waves-effect btn-sm text-left">Simpan</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="dt_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Total PO</th>
                                <th class="text-center">Total yang diterima</th>
                                <th class="text-center">Total yang belum diterima</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
    $(document).ready(function(){
        $('#select-all').click(function(event) {   
            if(this.checked) {
                // Iterate each checkbox
                $(':checkbox').each(function() {
                    this.checked = true;        
                    cekChecked();                
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;                       
                    cekChecked();
                });
            }
        });
    });
    function cekChecked(){
        var cb = $('[id^=export_id]');
        total=0;
        for(i = 0; i < cb.length; i++){
            if(cb.eq(i).prop('checked') === true){
                total++;
            }
        }
        
        if(total == cb.length){
            $('#select-all').prop('checked', true);
        }else{
            $('#select-all').prop('checked', false);
        }
    }
    dt_detail = $('#dt_detail').DataTable();
    inv_detail=$('#inv_detail').DataTable();
    function doShowDetail(id){
        dt_detail.clear().draw(false);
        $.ajax({
                type: "GET",
                url: "{{ URL::to('penerimaan_barang/detail_by_id') }}" + "/" + id, //json get site
                dataType : 'json',
                success: function(response){
                    arrData = response['data'];
                    for(i = 0; i < arrData.length; i++){
                        if (arrData[i]['purchase_id'] == id) {
                            dt_detail.row.add([
                                '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                                '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                                '<div class="text-right">'+parseFloat(arrData[i]['amount_po'])+'</div>',
                                '<div class="text-right">'+(parseFloat(arrData[i]['amount_po']) - arrData[i]['amount'])+'</div>',
                                '<div class="text-right">'+(arrData[i]['amount'])+'</div>',
                                '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                                '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>',
                            ]).draw(false);
                        }
                    }
                }
        });
    }
    function doShowDetail2(id){
        dt_detail.clear().draw(false);
        $.ajax({
                type: "GET",
                url: "{{ URL::to('penerimaan_barang/detail_atk_by_id') }}" + "/" + id, //json get site
                dataType : 'json',
                success: function(response){
                    arrData = response['data'];
                    for(i = 0; i < arrData.length; i++){
                        dt_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+parseFloat(arrData[i]['amount_po'])+'</div>',
                            '<div class="text-right">'+(parseFloat(arrData[i]['amount_po']) - arrData[i]['amount'])+'</div>',
                            '<div class="text-right">'+(arrData[i]['amount'])+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>'
                        ]).draw(false);
                    }
                }
        });
    }
    function doShowInv(eq){ 
        var no=$(eq).data('no')
        var date=$(eq).data('date')
        inv_detail.clear().draw(false);
        $.ajax({
                type: "GET",
                url: "{{ URL::to('akuntansi/detail-inv-by-no') }}", //json get site
                dataType : 'json',
                data : {no : no, date : date},
                success: function(response){
                    arrData = response['data'];
                    for(i = 0; i < arrData.length; i++){
                        total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0)
                        inv_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+parseInt(arrData[i]['amount'])+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(0))+'</div>',
                            '<div class="text-right">'+formatCurrency(total.toString())+'</div>'
                        ]).draw(false);
                    }
                }
        });
    }
    function getReport(eq){
        id=$(eq).data('id')
        $('[id^=suppl_single2]').val(id);
        $('#form-bill').submit();
    }
    function exportData(){
        date = $('#date').val()
        date2 = $('#date2').val()
        var suppl_single = $('[id^=suppl_single]').val();
        
        var cb = $('[id^=export_id]');
        export_id_form='';
        for(i = 0; i < cb.length; i++){
            if(cb.eq(i).prop('checked') === true){
                export_id_form+='<input type="text" name="export_id1[]" value="' + cb.eq(i).val() + '" />'
            }
        }
        suppl_form='';
        for(i = 0; i < suppl_single.length; i++){
            suppl_form+='<input type="text" name="suppl_single[]" value="' + suppl_single[i] + '" />'
        }
        var form = $('<form id="export_form" action="{{ URL::to('inventory/export_history_po') }}" method="post" target="_blank" >' +
        '<input type="text" name="_token" value="{{ csrf_token() }}" />' +
        '<input type="text" name="date" value="' + date + '" />' +
        '<input type="text" name="date2" value="' + date2 + '" />' +
        suppl_form +
        export_id_form +
        '</form>');
        $('body').append(form);
        form.submit();
        $('#export_form').remove();
    }
</script>


@endsection
@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Laporan Stok Masuk</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Home</li>
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
                    <h4 class="card-title">Laporan Stok Masuk</h4>
                    <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('inventory/report_stock_in') }}">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">No PO / No Surat Jalan :</label>
                                        <input type="" name="no" class="form-control" value="{{$no}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Tanggal Awal :</label>
                                        <input type="date" name="date" class="form-control" required value="{{$date1}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label >Tanggal Ahir :</label>
                                        <input type="date" name="date2" class="form-control" required value="{{$date2}}">
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
                    
                    <form method="POST" action="{{ URL::to('akuntansi/jurnal') }}" class="float-right" target="_blank" style="margin-left:10px">
                        @csrf
                        <input type="date" name="date" hidden class="form-control"  required value="{{$date1}}">
                        <input type="date" name="date2" hidden class="form-control"  required value="{{$date2}}">
                        <div class="form-group">
                            <button class="btn btn-primary"><i class="mdi mdi-view-list"></i> Jurnal</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ URL::to('inventory/export_stock_in') }}" class="float-right" target="_blank" style="margin-left:10px">
                        @csrf
                        <input type="" hidden name="no" class="form-control" value="{{$no}}">
                        <input type="date" name="date" hidden class="form-control"  required value="{{$date1}}">
                        <input type="date" name="date2" hidden class="form-control"  required value="{{$date2}}">
                        <div class="form-group">
                            <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ URL::to('inventory/export_stock_in_tax') }}" class="float-right" target="_blank">
                        @csrf
                        <input type="" hidden name="no" class="form-control" value="{{$no}}">
                        <input type="date" name="date" hidden class="form-control"  required value="{{$date1}}">
                        <input type="date" name="date2" hidden class="form-control"  required value="{{$date2}}">
                        <div class="form-group">
                            <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export Tax</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <!-- <table id="" class="table table-striped table-bordered"> -->
                        <table style="border-collapse:collapse; width:100%">
                            <thead>
                                <tr id="table" class="text-primary">
                                    <th class="text-center">No</th>
                                    <th class="text-center">Kode Barang</th>
                                    <th class="text-center">Nama Barang</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">No PO</th>
                                    <th class="text-center">No PO ATK</th>
                                    <th class="text-center">No Surat Jalan</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Tanggal Penerimaan</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php $total_all=$n=0 @endphp
                            @foreach($data as $value)
                                @php 
                                $total=($value->amount * $value->base_price); 
                                $total_all+=$total;
                                $n++;
                                @endphp
                                <tr id="table">
                                    <td>{{$n}}</td>
                                    <td>{{$value->code_item}}</td>
                                    <td>{{$value->name}}</td>
                                    <td class="text-right">{{formatRupiah($value->amount)}}</td>
                                    <td class="text-right">{{formatRupiah($value->base_price)}}</td>
                                    <td>{{$value->purchase_no != null ? $value->purchase_no : '-'}}</td>
                                    <td>{{$value->purchase_asset_no != null ? $value->purchase_asset_no : '-'}}</td>
                                    <td>{{$value->no_surat_jalan != null ? $value->no_surat_jalan : '-'}}</td>
                                    <td class="text-right">{{formatRupiah($total)}}</td>
                                    <td>{{formatDate($value->inv_trx_date)}}</td>
                                    
                                </tr>
                            @endforeach
                                <tr id="table" class="table-info">
                                    <td colspan="8" class="text-center">Sub Total</td>
                                    <td class="text-right">{{formatRupiah($total_all)}}</td>
                                    <td class="text-right"></td>
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
                                <th class="text-center">Perkiraan Harga</th>
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
                <h4 class="modal-title" id="title-modal">Laporan Stok Masuk </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="giro_id" id="giro_id">
                <div class="form-group">
                    <label for="">Total Laporan Stok Masuk :</label>
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

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
    inv_detail=$('#inv_detail').DataTable();
    function doShowInv(id){ 
        inv_detail.clear().draw(false);
        $.ajax({
                type: "GET",
                url: "{{ URL::to('akuntansi/detail-inv') }}" + "/" + id, //json get site
                dataType : 'json',
                success: function(response){
                    arrData = response['data'];
                    for(i = 0; i < arrData.length; i++){
                        inv_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrData[i]['amount']+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>'
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
</script>


@endsection
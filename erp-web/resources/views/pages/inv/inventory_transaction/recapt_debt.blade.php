@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Laporan Hutang Usaha</h4>
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
                    <h4 class="card-title">Laporan Hutang Usaha</h4>
                    <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('inventory/recapt_debt') }}">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="card-title">Pilih Supplier</h5>
                                    <div class="form-group">
                                        <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="suppl_single" name="suppl_single[]">
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
                    <form method="POST" action="{{ URL::to('inventory/purchase') }}" class="form-inline" id="form-bill" target="_blank">
                        @csrf
                        <input type="text" hidden name="suppl_single[]" id="suppl_single2[]" class="form-control">
                        <input type="date" name="date" class="form-control" hidden required value="{{$date1}}">
                        <input type="date" name="date2" class="form-control" hidden required value="{{$date2}}">
                        <input name="submitBtn" value="1" hidden>
                    </form>
                    @if($data != null)
                    <form method="POST" action="{{ URL::to('inventory/export_recapt_debt') }}" class="float-right" target="_blank">
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
                    </form>
                    <div class="table-responsive">
                        <!-- <table id="" class="table table-striped table-bordered"> -->
                        <table style="border-collapse:collapse; width:100%">
                            <thead>
                                <tr id="table" class="text-primary">
                                    <th></th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">No Surat Jalan</th>
                                    <th class="text-center">Supplier</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Kredit</th>
                                    <th class="text-center">Saldo(Asing)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php $sub_total=$total_debit=$total_kredit=0 @endphp
                                <tr id="table">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{formatRupiah($saldo_awal)}}</td>
                                </tr>
                            @php 
                            $sub_total+=$saldo_awal;
                            $n=0;
                            @endphp
                            @foreach($data as $value)
                                @php 
                                $sub_total=($value->tipe == 'DEBIT' ? ($sub_total - $value->jumlah) : ($sub_total + $value->jumlah)); 
                                $total_debit+=($value->tipe == 'DEBIT' ? $value->jumlah : 0);
                                $total_kredit+=($value->tipe == 'KREDIT' ? $value->jumlah : 0);
                                $n++;
                                @endphp
                                <tr id="table">
                                    <td>{{$n}}</td>
                                    <td>{{formatDate($value->tanggal)}}</td>
                                    <td>{{$value->no_surat_jalan != null ? $value->no_surat_jalan : '-'}}</td>
                                    <td>{{$value->name}}</td>
                                    <td>{{$value->deskripsi}}</td>
                                    <td>{{$value->tipe == 'DEBIT' ? formatRupiah($value->jumlah) : '-'}}</td>
                                    <td>{{$value->tipe == 'KREDIT' ? formatRupiah($value->jumlah) : '-'}}</td>
                                    <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                </tr>
                            @endforeach
                                <tr id="table" class="table-info">
                                    <td colspan="5" class="text-center">Sub Total</td>
                                    <td class="text-right">{{formatRupiah($total_debit)}}</td>
                                    <td class="text-right">{{formatRupiah($total_kredit)}}</td>
                                    <td class="text-right">{{formatRupiah($sub_total)}}</td>
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
                <h4 class="modal-title" id="title-modal">Laporan Hutang Usaha </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="giro_id" id="giro_id">
                <div class="form-group">
                    <label for="">Total Laporan Hutang Usaha :</label>
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
                        total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                        inv_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrData[i]['amount']+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>',
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
</script>


@endsection
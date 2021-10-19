@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Jurnal Umum</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Akuntansi</li>
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
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 id="titleJurnal">Jurnal Umum</h4>
                            <form method="POST" action="{{ URL::to('akuntansi/jurnal') }}" class="form-inline float-right">
                              @csrf
                                <label>Cari Rentang Tanggal :</label>&nbsp;
                                <input type="date" name="date" class="form-control" required value="{{$date}}">&nbsp;
                                <input type="date" name="date2" class="form-control" required  value="{{$date2}}">&nbsp;
                                <button class="btn btn-success">cari</button>&nbsp;
                                <button class="btn btn-info" type="button"  data-toggle="modal" data-target="#modalImport" ><i class="mdi mdi-file-excel"></i> Import Jurnal</button>
                                &nbsp;
                                <a class="btn btn-success pull-right" target="_blank"
                                href="{{ URL::to('akuntansi/export_journal?date='.$date.'&date2='.$date2) }}" >
                                <i class="mdi mdi-file-excel"></i> Export Jurnal</a>&nbsp;
                                <a class="btn btn-primary pull-right" 
                                href="{{ URL::to('akuntansi/createjournal') }}" >
                                <i class="fa fa-plus"></i> Tambah</a>
                             </form>
                        </div>
                    </div>
                     <br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="jurnal_list">
                            <thead>
                                <tr>
                                    <th width="100px">Tanggal</th>
                                    <th>No Sumber</th>
                                    <th>No Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                    <th>Keterangan</th>
                                    <th>Customer</th>
                                    <th>Supplier</th>
                                    <th width="100px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            @foreach ($data as $key => $value)
                                {{-- @php //$k=0 @endphp --}}
                                @foreach ($data[$key]['detail'] as $k => $v)
                                <?php 
                                $day=explode('-', $v->tanggal);
                                ?>
                                <tr style="background-color:{{$key % 2 == 0 ? '#24a08b' : '#8181b5'}}; color:white">
                                    <td>{{formatDate($v->tanggal)}}</td>
                                    <td>
                                    @if($v->paid_customer_id != null)
                                    <a href="#" class="text-white" data-toggle="modal" data-target="#modalShowPaidCust" data-id="{{$v->paid_customer_id}}" data-no="{{$v->paid_customers->no}}" onclick="showPaidCust(this)">
                                    @elseif($v->paid_supplier_id != null)
                                    <a href="#" class="text-white" data-toggle="modal" data-target="#modalShowPaidSppl" data-id="{{$v->paid_supplier_id}}" data-no="{{$v->paid_suppliers->no}}" onclick="showPaidSppl(this)">
                                    @elseif($v->paid_debt_id != null)
                                    <a href="#" class="text-white" data-toggle="modal" data-target="#modalShowPaidDebt" data-id="{{$v->paid_debt_id}}" data-no="{{$v->paid_debts->no}}" onclick="showPaidDebt(this)">
                                    @else
                                    <a href="#" class="text-white">
                                    @endif

                                    {{$v->no != null ? $v->no : ($v->inv_trxes != null ? $v->inv_trxes->no : ($v->inv_trx_services != null ? $v->inv_trx_services->no : ($v->purchases != null ? $v->purchases->no : ($v->purchase_assets != null ? $v->purchase_assets->no : ($v->orders != null ? $v->orders->order_no : ($v->ts_warehouses != null ? $v->ts_warehouses->no : ($v->debts != null ? $v->debts->no : ($v->install_orders != null ? $v->install_orders->no : ($v->giros != null ? $v->giros->no : ($v->paid_customers != null ? $v->paid_customers->no : ($v->paid_suppliers != null ? $v->paid_suppliers->no : ($v->bill_vendors != null ? $v->bill_vendors->no : ($v->payment_suppliers != null ? $v->payment_suppliers->no : '-')))))))))))))}}

                                    </a>

                                    @if(in_array($v->id_akun, json_decode($account_payment)))
                                    <button class="btn btn-xs btn-danger" data-toggle="modal" data-target="#edit_spk" data-id="{{$v->id_trx_akun_detail}}" data-spk_number="{{$v->no}}" onclick="editSPK(this)"><i class="mdi mdi-pencil"></i></button>
                                    @endif
                                    </td>
                                    <td>{{$v->no_akun}}</td>
                                    <td>{{$v->nama_akun}}</td>
                                        @if($v->tipe == 'KREDIT')
                                    
                                    <td><div class="text-left"></div></td>
                                    <td><div class="text-right">
                                    <form action="{{URL::to('akuntansi/detail-gl/'.$v->id_akun)}}" method="post">
                                    @csrf
                                    <!-- <input type="hidden" name="bulan" value="{{$day[1]}}">
                                    <input type="hidden" name="tahun" value="{{$day[0]}}"> -->
                                    <input type="hidden" name="date" class="form-control" required value="{{$v->tanggal}}">
                                    <input type="hidden" name="date2" class="form-control" required  value="{{$v->tanggal}}">
                                    <input type="hidden" name="id_trx_akun_detail" value="{{$v->id_trx_akun_detail}}">
                                    <button type="submit" class="btn btn-link text-white">Rp. {{formatRupiah(round($v->jumlah, 0))}}</button>
                                    </form>
                                    </div></td>
                                    <th>
                                    @if($value['detail'][0]->inv_trx_id == null)
                                    {{$value['deskripsi'].' '.$v->code_item}}
                                    @else
                                    <a href="#" data-id="{{$value['detail'][0]->inv_trx_id}}" class="text-white" onclick="doShowInv({{$value['detail'][0]->inv_trx_id}})" data-toggle="modal" data-target="#modalShowInv"><ins>{{$value['deskripsi'].' '.$v->code_item}}</ins></a>
                                    @endif
                                    </th>
                                    <td>{{$v->customer}}</td>
                                    <td>{{$v->supplier}}</td>
                                    
                                    <th>
                                    @if(in_array($v->id_akun, json_decode($account_payment))) <a href="{{URL::to('akuntansi/cetak_bukti_kas_keluar').'/'.$v->id_trx_akun_detail}}" target="_blank" class="btn btn-sm btn-warning"  data-target="#modalShowInv"><i class="fa fa-print"></i></a> @endif
                                    @if($v->inv_trx_id == null && $v->inv_sale_id == null)
                                    <a href="{{URL::to('akuntansi/delete_jurnal/'.$v->id_trx_akun)}}" onclick="return confirm('Apakah anda yakin akan membatalkan jurnal?')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                    @elseif($v->inv_trx_id != null && $v->purchase_id != null)
                                    <a href="{{URL::to('akuntansi/delete_jurnal/'.$v->id_trx_akun)}}" onclick="return confirm('Apakah anda yakin akan membatalkan jurnal?')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                    @endif
                                    </th>
                                </tr>
                                        @else

                                    <td><div class="text-right">
                                    <form action="{{URL::to('akuntansi/detail-gl/'.$v->id_akun)}}" method="post">
                                    @csrf
                                    <!-- <input type="hidden" name="bulan" value="{{$day[1]}}">
                                    <input type="hidden" name="tahun" value="{{$day[0]}}">
                                    <input type="hidden" name="id" value="{{$v->id_trx_akun_detail}}"> -->
                                    <input type="hidden" name="date" class="form-control" required value="{{$v->tanggal}}">
                                    <input type="hidden" name="date2" class="form-control" required  value="{{$v->tanggal}}">
                                    <input type="hidden" name="id_trx_akun_detail" value="{{$v->id_trx_akun_detail}}">
                                    <button type="submit" class="btn btn-link text-white">Rp. {{formatRupiah(round($v->jumlah, 0))}}</button>
                                    </form>
                                    </div></td>
                                    <td><div class="text-left"></div></td>
                                    <th>
                                    @if($value['detail'][0]->inv_trx_id == null)
                                    {{$value['deskripsi'].' '.$v->code_item}}
                                    @else
                                    <a href="#" data-id="{{$value['detail'][0]->inv_trx_id}}"  class="text-white" onclick="doShowInv({{$value['detail'][0]->inv_trx_id}})" data-toggle="modal" data-target="#modalShowInv"><ins>{{$value['deskripsi'].' '.$v->code_item}}</ins></a>
                                    @endif
                                    </th>
                                    <td>{{$v->customer}}</td>
                                    <td>{{$v->supplier}}</td>
                                    
                                    <th>
                                    @if(in_array($v->id_akun, json_decode($account_payment))) <a href="{{URL::to('akuntansi/cetak_bukti_kas_masuk').'/'.$v->id_trx_akun_detail}}" target="_blank" class="btn btn-sm btn-success"  data-target="#modalShowInv"><i class="fa fa-print"></i></a> @endif
                                    </th>
                                </tr>
                                        @endif
                                    
                                
                                @endforeach
                                <!-- <tr style="background-color:#fefefe">
                                    <th></th>
                                    <th>
                                    @if($value['detail'][0]->inv_trx_id == null)
                                    {{$value['deskripsi']}}
                                    @else
                                    <a href="#" data-id="{{$value['detail'][0]->inv_trx_id}}" onclick="doShowInv({{$value['detail'][0]->inv_trx_id}})" data-toggle="modal" data-target="#modalShowInv">{{$value['deskripsi']}}</a>
                                    @endif
                                    </th>
                                    <th colspan="4"></th>
                                </tr> -->
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
</div>
<div class="modal fade" id="edit_spk" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="title_detail_install">Edit No BKK/BKM/BBK/BBM</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form method="POST" action="{{ URL::to('akuntansi/edit_source_no') }}" class="mt-4" enctype="multipart/form-data">
                @csrf
            <div class="modal-body">
                <input type="text" class="form-control" id="spk_no" name="spk_no">
                <input type="hidden" name="id_trx_akun_detail" id="id_trx_akun_detail">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                <button class="btn btn-success waves-effect text-left">Update</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div id="modalImport" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Import Jurnal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{URL::to('akuntansi/import_jurnal')}}" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <div class="form-group">
                    <label>Pilih File</label>
                    <input type="file" name="importFile" id="importFile" required class="form-control" accept=".csv, .xls, .xlsx">
                </div>
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                <button class="btn btn-info waves-effect">Submit</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="modalShowInv" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Inventory Detail</h4>
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
<div class="modal fade" id="modalShowPaidCust" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Detail Pembayaran Customer</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="listDetailCust" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Tagihan</th>
                                <th class="text-center">No Faktur</th>
                                <th class="text-center">No Invoice</th>
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
<div class="modal fade" id="modalShowPaidDebt" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Detail Pembayaran Hutang Usaha</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="listDetailDebt" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Tagihan</th>
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
<div class="modal fade" id="modalShowPaidSppl" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Detail Pembayaran Supplier</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="listDetailSppl" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Tagihan</th>
                                <th class="text-center">No Invoice</th>
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
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
    var bulan='{{$date}}';
    $('#jurnal_list').DataTable({
        'aaSorting' : [0, 'DESC'],
        "lengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]]
    });
    inv_detail=$('#inv_detail').DataTable();
    // $('#titleJurnal').html('Jurnal Umum '+formatBulan(bulan));
    function formatBulan(val){
        var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        val=val.split('-');
        var getMonth=val[1];
        return val[2]+' '+bulan[getMonth-1]+' '+val[0];
    }
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
    function editSPK(eq){
        var id=$(eq).data('id');
        var spk_number=$(eq).data('spk_number');
        $('#id_trx_akun_detail').val(id)
        $('#spk_no').val(spk_number)
    }
    function showPaidCust(eq){
        id=$(eq).data('id')
        t2 = $('#listDetailCust').DataTable();
        t2.clear().draw(false);
        $.ajax({
            url: "{{ URL::to('inventory/list_paid_customers_d') }}"+'/'+id,
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-center">'+arrData[i]['no']+'</div>',
                        '<div class="text-center">'+arrData[i]['bill_no']+'</div>',
                        '<div class="text-center">'+arrData[i]['invoice_no']+'</div>',
                        '<div class="text-right">'+formatCurrency(parseInt(arrData[i]['amount']))+'</div>'
                    ]).draw(false);
                }
            }
        });
    }
    function showPaidSppl(eq){
        id=$(eq).data('id')
        t3 = $('#listDetailSppl').DataTable();
        t3.clear().draw(false);
        $.ajax({
            url: "{{ URL::to('inventory/list_paid_supplier_d') }}"+'/'+id,
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t3.row.add([
                        '<div class="text-center">'+arrData[i]['no']+'</div>',
                        '<div class="text-center">'+arrData[i]['paid_no']+'</div>',
                        '<div class="text-right">'+formatCurrency(parseInt(arrData[i]['amount']))+'</div>',
                    ]).draw(false);
                }
            }
        });
    }
    function showPaidDebt(eq){
        id=$(eq).data('id')
        t4 = $('#listDetailDebt').DataTable();
        t4.clear().draw(false);
        $.ajax({
            url: "{{ URL::to('payment/get_paid_debt_detail') }}"+'/'+id,
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t4.row.add([
                        '<div class="text-center">'+arrData[i]['no']+'</div>',
                        '<div class="text-right">'+formatCurrency(parseInt(arrData[i]['amount']))+'</div>',
                    ]).draw(false);
                }
            }
        });
    }
</script>
@endsection
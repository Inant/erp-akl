@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Laba Rugi Sementara Proyek</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page"></li>
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
                    <div class="col-12">
                    <h4 class="card-title">Laba Rugi Sementara Proyek</h4>
                        <form method="POST" action="{{ URL::to('akuntansi/temp_profit_loss') }}">
                                @csrf
                            <div class="row"> 
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Proyek</label>
                                        <select name="customer_project_id" class="form-control select2" style="width: 100%;">
                                            <option value="">--- Pilih Proyek ---</option>
                                            @if($customerProjectList != null)
                                            @foreach($customerProjectList as $value)
                                                <option value="{{ $value->id }}" @if($value->id == $customerProjectId) selected @endif>{{ $value->name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="" class="text-white">-</label><br>
                                        <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                        <br><br>
                        @if($data != null)
                            <div class="row">
                                <div class="ml-auto mr-2">
                                    <a target="_blank" href="{{ url('akuntansi/export_temp_profit_loss'.'?proyek_id='.$customerProjectId)}}">
                                    <button class="btn btn-success"><i class="mdi mdi-file-excel"></i> Export</button>
                                    </a>
                                </div>
                            </div>
                            @php
                                $totalPenjualan = 0;
                                $totalProduksi = 0;
                            @endphp
                            @foreach ($data as $key => $order)
                                @php
                                    $sum_pendapatan=0;
                                        foreach($order['dp'] as $value){
                                        $sum_pendapatan += $value->jumlah;
                                    }
                                @endphp
                                <h5 class="mt-3">
                                    {{$order['spk_number']}} - {{$order['customer']->coorporate_name}}
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead style="background-color:#3c8dbc; color:white">
                                            <tr>
                                                <th>Pendapatan dari Penjualan</th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="background-color:#ddd">
                                                <th>Total Pendapatan dari Penjualan</th>
                                                <th colspan="2" data-toggle="collapse" href="#collapseExample{{$key}}" aria-expanded="false" aria-controls="collapseExample{{$key}}" class="text-right"><p data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Klik untuk melihat detail">Rp. {{formatRupiah($sum_pendapatan)}}</p></th>
                                                {{-- <th colspan="2" data-toggle="collapse" href="#collapseExample{{$key}}" aria-expanded="false" aria-controls="collapseExample{{$key}}" class="text-right"><p data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Klik untuk melihat detail">Rp. {{formatRupiah($order['paid']->total - ($order['tax']->total_ppn - $order['tax']->total_pph))}}</p></th> --}}
                                            </tr>
                                        </tbody>
                                        {{-- @php $sum_pendapatan=$order['paid']->total - ($order['tax']->total_ppn - $order['tax']->total_pph) @endphp --}}
                                        {{-- @php $sum_pendapatan=0 @endphp --}}
                                        <tbody class="collapse" id="collapseExample{{$key}}">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Deskripsi</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                            @foreach($order['dp'] as $value)
                                            <tr>
                                                <td>{{formatDate($value->dtm_upd)}}</td>
                                                <td>{{'Pembayaran uang muka'}}</td>
                                                <th class="text-right">Rp. {{formatRupiah($value->jumlah)}}</th>
                                            </tr>
                                            @endforeach
                                            {{-- @foreach($order['paid_detail_amount'] as $value)
                                            <tr>
                                                <td>{{formatDate($value->paid_date)}}</td>
                                                <td>{{$value->deskripsi}}</td>
                                                <th class="text-right">Rp. {{formatRupiah(($value->total - $value->total_tax))}}</th>
                                            </tr>
                                            @endforeach --}}
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <th></th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </tbody>
                                        <thead style="background-color:#3c8dbc; color:white">
                                            <tr>
                                                <th>Biaya Produksi No Permintaan</th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </thead>
                                        <tr style="background-color:#cc7575; color : white">
                                            <th>Total Produksi</th>
                                            <th colspan="2" data-toggle="collapse" href="#" aria-expanded="false" aria-controls="" class="text-right">
                                            <!-- <p data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Klik untuk melihat detail">Rp. {{formatRupiah($order['biaya']->total)}}</p> -->
                                            <a href="{{ URL::to('akuntansi/hpp-proyek') }}?id={{$order['order_id']}}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Klik untuk melihat detail" target="_blank" class="text-white">Rp. {{formatRupiah($order['biaya']->total)}}</a>
                                            </th>
                                        </tr>
                                        @php $sum_beban=$order['biaya']->total @endphp
                                        <tbody class="collapse" id="collapseExample">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th colspan="2" class="text-right">Total</th>
                                            </tr>
                                            @foreach($order['biaya_detail'] as $value)
                                            <tr>
                                                <td>{{formatDate($value->tanggal)}}</td>
                                                <th colspan="2" class="text-right">
                                                <a href="#" data-id="{{$value->inv_trx_id}}" onclick="doShowInv(this)" data-toggle="modal" data-target="#modalShowInv"><p data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Klik untuk melihat detail item">Rp. {{formatRupiah($value->total)}}</p></a>
                                                </th>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <th></th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </tbody>
                                        <tr style="background-color:#ddd">
                                            <th>Gross Profit</th>
                                            <th colspan="2" class="text-right">Rp. {{formatRupiah($sum_pendapatan - $sum_beban)}}</th>
                                        </tr>
                                    </table>
                                </div>
                                @php
                                    $totalPenjualan += $sum_pendapatan;
                                    $totalProduksi += $sum_beban;
                                @endphp
                            @endforeach
                            <div class="col-md-6">
                                <h4>Total Penjualan : Rp. {{formatRupiah($totalPenjualan)}}</h4>
                            </div>
                            <div class="col-md-6">
                                <h4>Total Produksi : Rp. {{formatRupiah($totalProduksi)}}</h4>
                            </div>
                        @endif
                    </div>
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
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
inv_detail=$('#inv_detail').DataTable();
function doShowInv(eq){ 
    var id=$(eq).data('id')
    inv_detail.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('akuntansi/detail-inv') }}"+'/'+id, //json get site
            dataType : 'json',
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
</script>
@endsection
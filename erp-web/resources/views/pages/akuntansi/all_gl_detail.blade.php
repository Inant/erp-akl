@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">General Ledger</h4>
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
<?php
function formatDate($date){
    $tgl=date('d-m-Y', strtotime($date));
    return $tgl;
}
function formatRupiah($val){
    $a=number_format($val, 0, '.', '.');
    return $a;
}
?>
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">General Ledger</h4>
                    <div class="form-group">
                        <form method="POST" action="{{ URL::to('akuntansi/gl_all') }}" class="form-inline float-right">
                            @csrf
                            <div class="form-inline">
                                <!-- <div class="form-group">
                                <select name="" id="" class="form-control select2" style="width:120px"></select>
                                </div>&nbsp; -->
                                <label>Pilih Bulan : </label>&nbsp;
                                <select class="form-control select2" name="bulan" id="bulan" required style="width:120px">
                                    <option value="">--Pilih Bulan--</option>
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                &nbsp;
                                <select class="form-control select2" name="tahun" id="tahun" required  style="width:120px">
                                    <option value="">--Pilih Tahun--</option>
                                    @for ($i = date('Y') - 5; $i <= date('Y'); $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>&nbsp;
                                <button class="btn btn-primary"  onclick="cekAbsensiDate()"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                    @foreach($data as $data)
                        <h4 class="card-title">{{$data->no_akun}} : {{$data->nama_akun}}</h4>
                        <table class="table table-bordered" id="">
                            <thead>
                                <tr>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">No Akun</th>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Kredit</th>
                                    <th class="text-center">Total Saldo</th> 
                                </tr>
                            </thead>
                            <?php $sub_total=($data->detail['saldo_before']->jumlah_saldo != null ? $data->detail['saldo_before']->jumlah_saldo : 0)?>
                            <tbody>
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                </tr>
                            </tbody>
                            @foreach($data->detail['detail'] as $value)
                            <tbody>
                                @if(count($value['dt']) == 0)
                                <tr>
                                    <td class="text-center">{{formatDate($value['date'])}}</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                </tr>
                                @else
                                    @foreach($value['dt'] as $k => $v)
                                    <?php
                                    $total=round($v->jumlah, 0);
                                    if ($v->tipe == 'DEBIT') {
                                        if ($data->sifat_debit == 1) {
                                            $sub_total+=$total;
                                        }else{
                                            $sub_total-=$total;
                                        }
                                    }else{
                                        if ($data->sifat_kredit == 1) {
                                            $sub_total+=$total;
                                        }else{
                                            $sub_total-=$total;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center">{{ $k == 0 ? formatDate($value['date']) : ''}}</td>
                                        <td class="text-center">{{$v->nama_akun}}</td>
                                        <td class="text-center">
                                        @if($v->tipe == 'DEBIT')
                                        <a href="{{URL::to('akuntansi/cetak_bukti_kas_masuk').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                        @else
                                        <a href="{{URL::to('akuntansi/cetak_bukti_kas_keluar').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                        @endif
                                        </td>
                                        <td class="text-left">{{$v->deskripsi}}</td>
                                        <td class="text-right">@if($v->tipe == 'DEBIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                        <td class="text-right">@if($v->tipe == 'KREDIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                        <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            @endforeach
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-center">Total</th>
                                    <th class="text-right">{{formatRupiah($sub_total)}}</th>
                                </tr>
                            </tfoot>
                        </table>
                        @foreach($data->child as $data1)
                            <h4 class="card-title">{{$data1->no_akun}} : {{$data1->nama_akun}}</h4>
                            <table class="table table-bordered" id="">
                                <thead>
                                    <tr>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">No Akun</th>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Deskripsi</th>
                                        <th class="text-center">Debit</th>
                                        <th class="text-center">Kredit</th>
                                        <th class="text-center">Total Saldo</th> 
                                    </tr>
                                </thead>
                                <?php $sub_total=($data1->detail['saldo_before']->jumlah_saldo != null ? $data1->detail['saldo_before']->jumlah_saldo : 0)?>
                                <tbody>
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                    </tr>
                                </tbody>
                                @foreach($data1->detail['detail'] as $value)
                                <tbody>
                                    @if(count($value['dt']) == 0)
                                    <tr>
                                        <td class="text-center">{{formatDate($value['date'])}}</td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                    </tr>
                                    @else
                                        @foreach($value['dt'] as $k => $v)
                                        <?php
                                        $total=round($v->jumlah, 0);
                                        if ($v->tipe == 'DEBIT') {
                                            if ($data1->sifat_debit == 1) {
                                                $sub_total+=$total;
                                            }else{
                                                $sub_total-=$total;
                                            }
                                        }else{
                                            if ($data1->sifat_kredit == 1) {
                                                $sub_total+=$total;
                                            }else{
                                                $sub_total-=$total;
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-center">{{ $k == 0 ? formatDate($value['date']) : ''}}</td>
                                            <td class="text-center">{{$v->nama_akun}}</td>
                                            <td class="text-center">
                                            @if($v->tipe == 'DEBIT')
                                            <a href="{{URL::to('akuntansi/cetak_bukti_kas_masuk').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                            @else
                                            <a href="{{URL::to('akuntansi/cetak_bukti_kas_keluar').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                            @endif
                                            </td>
                                            <td class="text-left">{{$v->deskripsi}}</td>
                                            <td class="text-right">@if($v->tipe == 'DEBIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                            <td class="text-right">@if($v->tipe == 'KREDIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                            <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-center">Total</th>
                                        <th class="text-right">{{formatRupiah($sub_total)}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                            @foreach($data1->child as $data2)
                                <h4 class="card-title">{{$data2->no_akun}} : {{$data2->nama_akun}}</h4>
                                <table class="table table-bordered" id="">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">No Akun</th>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Deskripsi</th>
                                            <th class="text-center">Debit</th>
                                            <th class="text-center">Kredit</th>
                                            <th class="text-center">Total Saldo</th> 
                                        </tr>
                                    </thead>
                                    <?php $sub_total=($data2->detail['saldo_before']->jumlah_saldo != null ? $data2->detail['saldo_before']->jumlah_saldo : 0)?>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                        </tr>
                                    </tbody>
                                    @foreach($data2->detail['detail'] as $value)
                                    <tbody>
                                        @if(count($value['dt']) == 0)
                                        <tr>
                                            <td class="text-center">{{formatDate($value['date'])}}</td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-right">0</td>
                                            <td class="text-right">0</td>
                                            <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                        </tr>
                                        @else
                                            @foreach($value['dt'] as $k => $v)
                                            <?php
                                            $total=round($v->jumlah, 0);
                                            if ($v->tipe == 'DEBIT') {
                                                if ($data2->sifat_debit == 1) {
                                                    $sub_total+=$total;
                                                }else{
                                                    $sub_total-=$total;
                                                }
                                            }else{
                                                if ($data2->sifat_kredit == 1) {
                                                    $sub_total+=$total;
                                                }else{
                                                    $sub_total-=$total;
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td class="text-center">{{ $k == 0 ? formatDate($value['date']) : ''}}</td>
                                                <td class="text-center">{{$v->nama_akun}}</td>
                                                <td class="text-center">
                                                @if($v->tipe == 'DEBIT')
                                                <a href="{{URL::to('akuntansi/cetak_bukti_kas_masuk').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                                @else
                                                <a href="{{URL::to('akuntansi/cetak_bukti_kas_keluar').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                                @endif
                                                </td>
                                                <td class="text-left">{{$v->deskripsi}}</td>
                                                <td class="text-right">@if($v->tipe == 'DEBIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                                <td class="text-right">@if($v->tipe == 'KREDIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                                <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    @endforeach
                                    <tfoot>
                                        <tr>
                                            <th colspan="6" class="text-center">Total</th>
                                            <th class="text-right">{{formatRupiah($sub_total)}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                @foreach($data2->child as $data3)
                                    <h4 class="card-title">{{$data3->no_akun}} : {{$data3->nama_akun}}</h4>
                                    <table class="table table-bordered" id="">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">No Akun</th>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Deskripsi</th>
                                                <th class="text-center">Debit</th>
                                                <th class="text-center">Kredit</th>
                                                <th class="text-center">Total Saldo</th> 
                                            </tr>
                                        </thead>
                                        <?php $sub_total=($data3->detail['saldo_before']->jumlah_saldo != null ? $data3->detail['saldo_before']->jumlah_saldo : 0)?>
                                        <tbody>
                                            <tr>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                            </tr>
                                        </tbody>
                                        @foreach($data3->detail['detail'] as $value)
                                        <tbody>
                                            @if(count($value['dt']) == 0)
                                            <tr>
                                                <td class="text-center">{{formatDate($value['date'])}}</td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-right">0</td>
                                                <td class="text-right">0</td>
                                                <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                            </tr>
                                            @else
                                                @foreach($value['dt'] as $k => $v)
                                                <?php
                                                $total=round($v->jumlah, 0);
                                                if ($v->tipe == 'DEBIT') {
                                                    if ($data3->sifat_debit == 1) {
                                                        $sub_total+=$total;
                                                    }else{
                                                        $sub_total-=$total;
                                                    }
                                                }else{
                                                    if ($data3->sifat_kredit == 1) {
                                                        $sub_total+=$total;
                                                    }else{
                                                        $sub_total-=$total;
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center">{{ $k == 0 ? formatDate($value['date']) : ''}}</td>
                                                    <td class="text-center">{{$v->nama_akun}}</td>
                                                    <td class="text-center">
                                                    @if($v->tipe == 'DEBIT')
                                                    <a href="{{URL::to('akuntansi/cetak_bukti_kas_masuk').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                                    @else
                                                    <a href="{{URL::to('akuntansi/cetak_bukti_kas_keluar').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                                    @endif
                                                    </td>
                                                    <td class="text-left">{{$v->deskripsi}}</td>
                                                    <td class="text-right">@if($v->tipe == 'DEBIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                                    <td class="text-right">@if($v->tipe == 'KREDIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                                    <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        @endforeach
                                        <tfoot>
                                            <tr>
                                                <th colspan="6" class="text-center">Total</th>
                                                <th class="text-right">{{formatRupiah($sub_total)}}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Detail Jurnal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table style="width:100%">
                    <thead>
                        <tr>
                            <td>Deskripsi</td>
                            <td>:</td>
                            <td id="deskripsi"></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td id="date">/td>
                        </tr>
                    </thead>
                </table>
                <br>
                <button class="btn btn-primary" id="btn-po" data-id="1" onclick="doShowPO()" data-toggle="modal" data-target="#modalShowPurchase">PO</button>
                <button class="btn btn-primary" id="btn-po-asset" data-id="1" onclick="doShowPOAsset()" data-toggle="modal" data-target="#modalShowPurchaseAsset">PO Asset</button>
                <button class="btn btn-primary" id="btn-inv" data-id="1" onclick="doShowInv()" data-toggle="modal" data-target="#modalShowInv">Penerimaan</button>
                <button class="btn btn-primary" id="btn-req-dev" data-id="1" onclick="doShowReqDev()" data-toggle="modal" data-target="#modalShowReqDev">Jurnal Permintaan</button>
                <br><br>
                <div class="table-responsive">
                    <table id="dt_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Akun</th>
                                <th class="text-center">Tipe</th>
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
<div class="modal fade" id="modalShowReqDev" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Journal Permintaan Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="req_dev_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Deskripsi</th>
                                <th class="text-center">Akun</th>
                                <th class="text-center">No Akun</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>     
                        <tbody></tbody>                                 
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowPurchase" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="po_detail" class="table table-bordered">
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
<div class="modal fade" id="modalShowPurchaseAsset" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="po_asset_detail" class="table table-bordered">
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
$(document).ready( function(){

});
dt_detail=$('#dt_detail').DataTable();
po_detail=$('#po_detail').DataTable();
inv_detail=$('#inv_detail').DataTable();
po_asset_detail=$('#po_asset_detail').DataTable();
req_dev_detail=$('#req_dev_detail').DataTable();
function doShowDetail(id){
    dt_detail.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('akuntansi/detail-trx-akun') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                $('#deskripsi').html(arrData[0]['deskripsi'])
                $('#date').html(formatDateID(new Date((arrData[0]['tanggal']).substring(0,10))))
                               
                if (arrData[0]['purchase_id'] == null || arrData[0]['purchase_id'] == 0) {
                    $('#btn-po').hide();
                }else{
                    $('#btn-po').show();
                    $('#btn-po').data('id', arrData[0]['purchase_id'])
                }
                if (arrData[0]['inv_trx_id'] == null || arrData[0]['inv_trx_id'] == 0) {
                    $('#btn-inv').hide();
                }else{
                    $('#btn-inv').show();
                    $('#btn-inv').data('id', arrData[0]['inv_trx_id'])
                }
                if (arrData[0]['purchase_asset_id'] == null || arrData[0]['purchase_asset_id'] == 0) {
                    $('#btn-po-asset').hide();
                }else{
                    $('#btn-po-asset').show();
                    $('#btn-po-asset').data('id', arrData[0]['purchase_asset_id'])
                }
                if (arrData[0]['project_req_development_id'] == null || arrData[0]['project_req_development_id'] == 0) {
                    $('#btn-req-dev').hide();
                }else{
                    $('#btn-req-dev').show();
                    $('#btn-req-dev').data('id', arrData[0]['project_req_development_id'])
                }
                for(i = 0; i < arrData.length; i++){
                    // a = i+1;
                    dt_detail.row.add([
                        '<div class="text-left">'+arrData[i]['no_akun']+'</div>',
                        '<div class="'+(arrData[i]['tipe'] == 'DEBIT' ? 'text-left' : 'text-center')+'">'+arrData[i]['nama_akun']+'</div>',
                        '<div class="text-left">'+arrData[i]['tipe']+'</div>',
                        '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['jumlah']).toFixed(2))+'</div>',
                    ]).draw(false);
                }
            }
    });
}
function doShowPO(){
    $('#modalShowDetail').modal('toggle');
    po_detail.clear().draw(false);
    var id=$('#btn-po').data('id');
    $.ajax({
            type: "GET",
            url: "{{ URL::to('po_konstruksi/detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                    po_detail.row.add([
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
function doShowPOAsset(){
    $('#modalShowDetail').modal('toggle');
    po_asset_detail.clear().draw(false);
    var id=$('#btn-po-asset').data('id');
    $.ajax({
            type: "GET",
            url: "{{ URL::to('po_konstruksi/detail_atk') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                    po_asset_detail.row.add([
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
function doShowInv(){
    $('#modalShowDetail').modal('toggle');
    inv_detail.clear().draw(false);
    var id=$('#btn-inv').data('id');
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
function doShowReqDev(){
    $('#modalShowDetail').modal('toggle');
    po_detail.clear().draw(false);
    var id=$('#btn-req-dev').data('id');
    $('#req_dev_detail > tbody').empty();
    $.ajax({
            type: "GET",
            url: "{{ URL::to('akuntansi/detail-req-dev') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(var i = 0; i < arrData.length; i++){
                    var a=0;
                    for(var j = 0; j < arrData[i]['detail'].length; j++){
                        var td='<tr>'+
                                '<td class="text-center">'+(j == 0 ? formatDate2(arrData[i]['tanggal']) : '')+'</td>'+
                                '<td class="text-center">'+(j == 0 ? arrData[i]['deskripsi'] : '')+'</td>'+
                                '<td class="text-center">'+arrData[i]['detail'][j]['nama_akun']+'</td>'+
                                '<td class="text-center">'+arrData[i]['detail'][j]['no_akun']+'</td>'+
                                '<td class="text-center">'+arrData[i]['detail'][j]['tipe']+'</td>'+
                                '<td class="text-center">'+formatCurrency(parseFloat(arrData[i]['detail'][j]['jumlah']).toFixed(2))+'</td>'+
                            '</tr>';
                        console.log(td)
                        $('#req_dev_detail').find('tbody:last').append(td);
                    }
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
function formatNumber(angka, prefix)
{
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa  = split[0].length % 3,
        rupiah  = split[0].substr(0, sisa),
        ribuan  = split[0].substr(sisa).match(/\d{3}/gi);
        
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}
</script>
@endsection
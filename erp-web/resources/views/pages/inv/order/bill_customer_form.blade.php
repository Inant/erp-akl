@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tagihan Customer</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('order') }}">Tagihan Customer</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Form</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')

<style>
.delete{
    background-color:#a57575;
    color:white
}
.checkbox label:after,
.radio label:after {
  content: '';
  display: table;
  clear: both;
}

.checkbox .cr,
.radio .cr {
  position: relative;
  display: inline-block;
  border: 1px solid #a9a9a9;
  border-radius: .25em;
  width: 1.3em;
  height: 1.3em;
  float: left;
  margin-right: .5em;
}

.radio .cr {
  border-radius: 50%;
}

.checkbox .cr .cr-icon,
.radio .cr .cr-icon {
  position: absolute;
  font-size: .8em;
  line-height: 0;
  top: 50%;
  left: 15%;
}

.radio .cr .cr-icon {
  margin-left: 0.04em;
}

.checkbox label input[type="checkbox"],
.radio label input[type="radio"] {
  display: none;
}

.checkbox label input[type="checkbox"]+.cr>.cr-icon,
.radio label input[type="radio"]+.cr>.cr-icon {
  opacity: 0;
}

.checkbox label input[type="checkbox"]:checked+.cr>.cr-icon,
.radio label input[type="radio"]:checked+.cr>.cr-icon {
  opacity: 1;
}

.checkbox label input[type="checkbox"]:disabled+.cr,
.radio label input[type="radio"]:disabled+.cr {
  opacity: .5;
}
<?php
function formatNumber($val){
    return number_format($val, 0, '.', '.');
}
$total_all=0;
$total_other=0;
$total_produk=$total_addendum_discount=0;
$total_tagihan=$tagihan['total_product'];
?>
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tagihan Customer</h4>
                    <table style="width:100%">
                        <thead>
                            <tr>
                                <td>Nomor SPK</td>
                                <td>:</td>
                                <td>{{$order['spk_number']}}</td>
                                <td>Nomor</td>
                                <td>:</td>
                                <td>{{$order['order_no']}}</td>
                            </tr>
                            <tr>
                                <td>Customer</td>
                                <td>:</td>
                                <td>{{$customer['coorporate_name']}}</td>
                                <td>Total Produk</td>
                                <td>:</td>
                                <td>{{formatNumber($tagihan['total_product'])}}</td>
                            </tr>
                            <!-- <tr>
                                <td>Nomor Order</td>
                                <td>:</td>
                                <td>{{$order['order_no']}}</td>
                                <td>Jasa Instalasi</td>
                                <td>:</td>
                                <td>{{formatNumber($tagihan['total_installation'])}}</td>
                            </tr> -->
                            <tr>
                                <td>Tanggal Order</td>
                                <td>:</td>
                                <td>{{date('d-m-Y', strtotime($order['order_date']))}}</td>
                                <td>Total Tagihan</td>
                                <td>:</td>
                                <td>{{formatNumber($total_tagihan)}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <!-- <td>PPN (10%)</td>
                                <td>:</td>
                                <td>{{formatNumber($tagihan['ppn'])}}</td> -->
                            </tr>
                        </thead>
                    </table>
                    
                    <div class="table-responsive">
                        <h4 class="card-title">Detail Order</h4>
                        <table class="table table-bordered" id="detail-order">
                        <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Type Kavling</th>
                                    <th>Series</th>
                                    <th colspan="2">Dimensi (W(m<sup>1</sup>) x H(m<sup>1</sup>))</th>
                                    <!-- <th>Foto Sketch</th> -->
                                    <th width="150px">Total</th>
                                    <th width="150px">Total Set</th>
                                    <th width="150px">Harga</th>
                                    <!-- <th width="150px">Jasa Pemasangan</th> -->
                                    <th width="150px">Sub Total</th>
                                </tr>
                            </thead>    
                            <tbody>
                                @foreach($order_d as $value)
                                <tr>
                                    <td>{{$value['item']}}</td>
                                    <td>{{$value['name']}}</td>
                                    <td>{{$value['series']}}</td>
                                    <td>{{$value['panjang']}}</td>
                                    <td>{{$value['lebar']}}</td>
                                    <!-- <td><img src="/upload/product/{{$value['image']}}" width="60px" onclick="showImage(this.src)"></td> -->
                                    <td>{{$value['total']}}</td>
                                    <td>{{$value['amount_set']}}</td>
                                    <td>{{formatNumber($value['price'])}}</td>
                                    <!-- <td>{{formatNumber($value['installation_fee'])}}</td> -->
                                    <td>{{formatNumber(($value['total'] * $value['price']))}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8">Total</td>
                                    <td>{{formatNumber($tagihan['total_product'])}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @if(count($customer_bill_other) != 0)
                    <div class="table-responsive">
                        <h4 class="card-title">Detail Addendum</h4>
                        <table class="table table-bordered" id="detail-order">
                        <thead>
                                <tr>
                                    <th>Catatan</th>
                                    <th>Tipe</th>
                                    <th>Total</th>
                                </tr>
                            </thead>    
                            <tbody>
                                @foreach($customer_bill_other as $value)
                                <?php 
                                $total_other=$value->is_increase == 1 ? $total_other + $value->amount : $total_other - $value->amount; 
                                $total_addendum_discount+=$value->is_increase == 0 ? $value->amount : 0;
                                ?>
                                <tr>
                                    <td>{{$value->notes}}</td>
                                    <td>{{($value->description == 'addendum' ? 'Permintaan Tambahan' : 'Permintaan Diskon')}}</td>
                                    <td>{{formatNumber($value->amount)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" id="total_addendum_discount" value="{{$total_addendum_discount}}">
                    @endif
                    <h4 class="card-title" {{$order['paid_off_date'] != '' ? 'hidden' : ''}}>Tambah Addendum</h4>
                    <form method="POST" action="{{ URL::to('order/bill_other') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_id" value="{{$order['id']}}">
                        <input type="hidden" name="customer_id" value="{{$order['customer_id']}}">
                        <div class="row" {{$order['paid_off_date'] != '' ? 'hidden' : ''}}>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Catatan</label>
                                    <textarea name="notes" id="notes" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Pilih Tipe</label><br>
                                    <select name="tipe" id="tipe" class="form-control select2" style="width:100%" required>
                                        <!-- <option value="">-- Pilih Tipe --</option> -->
                                        <option value="addendum">Permintaan Tambahan</option>
                                        <option value="discount_payment">Permintaan Diskon</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <input type="text" name="total" id="total_other" onkeyup="formatTotalOther(this)" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <button type="submit" id="submit" class="btn btn-primary">Simpan</button>
                            </div>
                            
                        </div>
                    </form>
                    <hr>
                    <br>
                    <div class="table-responsive">
                        <h4 class="card-title">Daftar Tagihan</h4>
                        <table class="table table-bordered" id="detail-billing">
                        <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">No Faktur</th>
                                    <th class="text-center">No Invoice</th>
                                    <th class="text-center" width="200px">Deskripsi</th>
                                    <th class="text-center">Tanggal Pembuatan</th>
                                    <!-- <th class="text-center">Tipe Pembayaran</th>
                                    <th class="text-center">Bank</th>
                                    <th class="text-center">Nomor Bank</th>
                                    <th class="text-center">Atas Nama</th>
                                    <th class="text-center">Ref Code</th> -->
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Due Date</th>
                                    <th></th>
                                </tr>
                            </thead>    
                            <tbody>
                                @foreach($customer_bill as $value)
                                <?php $total_all+=$value->amount; ?>
                                <tr>
                                    <td class="text-center">{{$value->no}}</td>
                                    <td class="text-center">{{$value->bill_no}}</td>
                                    <td class="text-center">{{$value->invoice_no}}</td>
                                    <td class="text-center">{{$value->description}}</td>
                                    <td class="text-center">{{$value->create_date != null ? date('d-m-Y', strtotime($value->create_date)) : '-'}}</td>
                                    <td class="text-right">{{formatNumber($value->amount)}}</td>
                                    <td class="text-center">{{date('d-m-Y', strtotime($value->due_date))}}</td>
                                    <td><a hidden href="{{URL::to('order/delete_bill/'.$value->id)}}" class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i></a><a href="{{URL::to('order/print_bill/'.$value->id)}}" class="btn btn-success btn-sm" target="_blank"><i class="mdi mdi-printer"></i></a>&nbsp;<button hidden onclick="doShowDetail(this);" data-toggle="modal" data-no="{{$value->no}}" data-id="{{$value->id}}" data-amount="{{$value->amount}}" data-end_payment="{{$value->end_payment}}" data-target="#modalBillDetail" class="btn waves-effect waves-light btn-xs btn-info"><i class="mdi mdi-credit-card-plus"></i></button></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-center">Total Kekurangan Tagihan</th>
                                    <th colspan="3" class="text-center">Rp. {{formatNumber((($total_tagihan - $total_all) + $total_other))}}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <hr>
                    <form method="POST" action="{{ URL::to('order/bill') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_id" value="{{$order['id']}}">
                        <input type="hidden" name="customer_id" value="{{$order['customer_id']}}">
                        <input type="hidden" id="total_tagihan" value="{{$total_tagihan}}">
                        <input type="hidden" id="total_all" value="{{$total_all}}">
                        <input type="hidden" id="total_addendum" name="total_addendum" value="{{$total_other}}">
                        <input type="hidden" id="sub_total" name="sub_total" value="{{(($total_tagihan - $total_all) + $total_other)}}">
                        <h4 class="card-title"  {{$order['paid_off_date'] != '' ? 'hidden' : ''}}>Tambahkan Tagihan</h4>
                        <div class="row" {{$order['paid_off_date'] != '' ? 'hidden' : ''}}>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Nomor Faktur</label>
                                    <input type="text" name="no" id="no" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Nomor Invoice</label>
                                    <input type="text" name="invoice_no" id="invoice_no" class="form-control" value="{{$inv}}" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Deskripsi</label>
                                    <input type="text" name="deskripsi" id="deskripsi" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total Persen</label>
                                    <input type="text" name="persen" id="persen" class="form-control"  onkeyup="persenTotal(this)">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <input type="text" name="total" id="total" class="form-control"  onkeyup="cekTotal(this)">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Tanggal Pembuatan Tagihan</label>
                                    <input type="date" name="date_create" id="date_create" value="{{date('Y-m-d')}}" required class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Alamat Tagihan</label>
                                    <textarea name="address" id="address" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Catatan</label>
                                    <textarea name="notes" id="notes" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">PPN</label>
                                    <input readonly type="" name="ppn_bill" id="ppn_bill" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total Tagihan</label>
                                    <input readonly type="" name="bill_amount" id="bill_amount" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" id="submit" class="btn btn-primary">Simpan</button>
                            </div>
                            
                        </div>
                    </form>
                    <hr>
                    <br>
                    <div class="table-responsive" hidden>
                        <h4 class="card-title">Daftar Tagihan</h4>
                        <table class="table table-bordered" id="detail-billing">
                        <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Deskripsi</th>
                                    <!-- <th class="text-center">Tipe Pembayaran</th>
                                    <th class="text-center">Bank</th>
                                    <th class="text-center">Nomor Bank</th>
                                    <th class="text-center">Atas Nama</th>
                                    <th class="text-center">Ref Code</th> -->
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Due Date</th>
                                    <th></th>
                                </tr>
                            </thead>    
                            <tbody>
                                @foreach($customer_paid as $value)
                                <?php $total_all+=$value->amount; ?>
                                <tr>
                                    <td class="text-center">{{$value->no}}</td>
                                    <td class="text-center">{{$value->description}}</td>
                                    <td class="text-right">{{formatNumber($value->amount)}}</td>
                                    <td class="text-center">{{date('d-m-Y', strtotime($value->pay_date))}}</td>
                                    <td><a hidden href="{{URL::to('order/delete_bill/'.$value->id)}}" class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i></a><a href="{{URL::to('order/print_bill/'.$value->id)}}" class="btn btn-success btn-sm" target="_blank"><i class="mdi mdi-printer"></i></a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form method="POST" hidden action="{{ URL::to('order/paid') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_id" value="{{$order['id']}}">
                        <input type="hidden" name="customer_id" value="{{$order['customer_id']}}">
                        <input type="hidden" id="total_tagihan2" value="{{$total_tagihan}}">
                        <input type="hidden" id="total_all2" value="{{$total_all}}">
                        <!-- <input type="hidden" id="sub_total" name="sub_total" value="{{($total_tagihan - $total_all)}}"> -->
                        <h4 class="card-title"  {{$order['paid_off_date'] != '' ? 'hidden' : ''}}>Bayar Langsung</h4>
                        <div class="row" {{$order['paid_off_date'] != '' ? 'hidden' : ''}}>
                            <!-- <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="">Persen Tagihan</label>
                                    <input type="text" name="percent" id="percent" class="form-control" onkeyup="cekTotal(this.value)" required>
                                </div>
                            </div> -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <input type="text" name="total" id="total2" class="form-control"  onkeyup="cekTotal2(this)">
                                </div>
                            </div>
                            <div class="col-sm-6" hidden>
                                <div class="form-group">
                                    <label for="">PPN</label>
                                    <input type="text" name="ppn" id="ppn" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tipe Pembayaran</label><br>
                                    <select name="wop" id="wop" class="form-control select2" style="width:100%" onchange="cekTipe(this.value)" required>
                                        <option value="">-- Pilih Tipe Pembayaran --</option>
                                        <option value="cash">Tunai</option>
                                        <option value="bank_transfer">Transfer Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Tanggal Bayar</label>
                                    <input type="date" name="pay_date" id="pay_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Catatan</label>
                                    <textarea name="notes" id="notes" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6" id="card" style="display:none">
                                <div class="form-group">
                                    <label for="">Ref Code</label>
                                    <input type="" name="ref_code" id="ref_code" class="form-control" style="100%">
                                </div>
                            </div>
                            <div class="col-sm-6"  id="bank" style="display:none">
                                <div class="form-group">
                                    <label>Bank</label><br>
                                    <select name="id_bank" id="id_bank" class="form-control select2" style="width:100%">
                                        <option value="">-- Pilih Bank --</option>
                                        @foreach($list_bank as $value)
                                        <option value="{{$value->id_bank}}">{{$value->bank_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6" id="bank_no" style="display:none">
                                <div class="form-group">
                                    <label for="">Nomor Rekening</label>
                                    <input type="" name="bank_number" id="bank_number" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6" id="bank_an" style="display:none">
                                <div class="form-group">
                                    <label for="">Atas Nama</label>
                                    <input type="" name="atas_nama" id="atas_nama" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <button type="submit" id="submit" class="btn btn-primary">Simpan</button>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>
<!-- /.modal -->
<div class="modal fade bs-example-modal-lg" id="modalBillDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{URL::to('order/save_bill_detail')}}" method="post">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal">Pembayaran Tagihan </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Daftar Tagihan</h4>
                <table>
                    <tr>
                        <td>Total Tagihan(Include PPN)</td>
                        <td id="bill_amount"></td>
                    </tr>
                    <!-- <tr>
                        <td>PPN(10%)</td>
                        <td id="bill_ppn"></td>
                    </tr> -->
                    <tr>
                        <td>Total Bayar</td>
                        <td id="bill_all"></td>
                    </tr>
                </table>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered" id="detail_payment">
                        <thead>
                            <tr>
                                <th class="text-center">Tipe Pembayaran</th>
                                <th class="text-center">Bank</th>
                                <th class="text-center">Nomor Bank</th>
                                <th class="text-center">Atas Nama</th>
                                <!-- <th class="text-center">Ref Code</th> -->
                                <th class="text-center">Total</th>
                                <th class="text-center">Pay Date</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <p id="label-detail"></p>
                <input type="hidden" name="order_id" value="{{$order['id']}}">
                <input type="hidden" id="bill_id" name="bill_id">
                <input type="hidden" id="amount_bill" name="amount_bill">
                <input type="hidden" id="total_min" name="total_min">
                <input type="hidden" id="total_awal" name="total_awal">
                <input type="hidden" id="total_ppn" name="total_ppn">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Total</label>
                            <input type="" onkeyup="checkTotalBill(this)" name="total_bill" id="total_bill" class="form-control" style="100%">
                            <input type="hidden" readonly name="paid_more" id="paid_more" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Tipe Pembayaran</label><br>
                            <select name="wop" id="wop" class="form-control select2" style="width:100%" onchange="cekTipe(this.value)" required>
                                <option value="">-- Pilih Tipe Pembayaran --</option>
                                <option value="cash">Tunai</option>
                                <option value="giro">Giro</option>
                                <option value="bank_transfer">Transfer Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="card" style="display:none">
                        <div class="form-group">
                            <label for="">Nomor Giro</label>
                            <input type="" name="ref_code" id="ref_code" class="form-control" style="100%">
                        </div>
                    </div>
                    <div class="col-sm-6"  id="bank" style="display:none">
                        <div class="form-group">
                            <label>Bank</label><br>
                            <select name="id_bank" id="id_bank" class="form-control select2" style="width:100%">
                                <option value="">-- Pilih Bank --</option>
                                @foreach($list_bank as $value)
                                <option value="{{$value->id_bank}}">{{$value->bank_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="bank_no" style="display:none">
                        <div class="form-group">
                            <label for="">Nomor Rekening</label>
                            <input type="" name="bank_number" id="bank_number" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6" id="bank_an" style="display:none">
                        <div class="form-group">
                            <label for="">Atas Nama</label>
                            <input type="" name="atas_nama" id="atas_nama" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Akun Pembayaran</label>
                            <select name="account_payment" id="account_payment" class="select2 form-control" style="width:100%" required>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
                <button class="btn btn-success waves-effect btn-sm text-left" id="submit_bill">Simpan</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- <script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script> -->
<script src="{!! asset('theme/assets/libs/sweetalert2/dist/sweetalert2.all.min.js') !!}"></script>
<script>
$(document).ready(function(){
    $('#account_payment').empty();
    $('#account_payment').append('<option value="">-- Pilih Akun --</option>');
    $.ajax({
        // type: "post",
        url: "{{ URL::to('akuntansi/account_payment') }}",
        dataType : 'json',
        success: function(response){
            arrData = response;
            for(i = 0; i < arrData.length; i++){
                $('#account_payment').append('<option value="'+arrData[i]['label']+'">'+arrData[i]['value']+'</option>');
            }
        }
    });
});
function cekTotal2(eq){
    var paid=(eq.value).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    // var total_tagihan=$('#total_tagihan').val();
    // var total_all=$('#total_all').val();
    var total_tagihan=$('#total_tagihan2').val();
    // var countPersen=parseFloat(total_tagihan) * (parseFloat(persen)/ 100);
    
    if (total_paid > parseFloat(total_tagihan)) {
        $('#total2').val(0);
        $('#ppn').val(0);
    }else{
        $('#total2').val(formatNumber(paid));
        var ppn=paid * (1/10);
        $('#ppn').val(formatNumber(ppn.toString()));
    }
}
function persenTotal(eq){
    var persen=eq.value;
    var total_tagihan=$('#total_tagihan').val();
    var total_persen=parseFloat(total_tagihan).toFixed(0)*(persen/100);
    var sub_total=$('#sub_total').val();
    if (total_persen > parseFloat(sub_total)) {
        $('#persen').val(0);
        $('#total').val(0);
    }else{
        total_persen=total_persen.toFixed(0);
        $('#total').val(formatNumber(total_persen.toString()));
        ppn=(parseFloat(total_persen)*0.1).toFixed(0);
        $('#ppn_bill').val(formatNumber(ppn))
        bill_amount=parseFloat(total_persen) + parseFloat(ppn);
        $('#bill_amount').val(formatNumber(bill_amount.toString()))
    }
}
function cekTotal(eq){
    var paid=(eq.value).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    // var total_tagihan=$('#total_tagihan').val();
    // var total_all=$('#total_all').val();
    var sub_total=parseFloat($('#sub_total').val()).toFixed(0);

    if (total_paid > parseFloat(sub_total)) {
        $('#total').val(0);
    }else{
        $('#total').val(formatNumber(paid));
        ppn=(parseFloat(paid)*0.1).toFixed(0);
        $('#ppn_bill').val(formatNumber(ppn.toString()))
        bill_amount=parseFloat(paid) + parseFloat(ppn);
        $('#bill_amount').val(formatNumber(bill_amount.toString()))
    }
}
function changeNumber(paid, sub_total){
    
    if (paid > sub_total) {
        $('#total').val(0);
    }else{
        var isi=formatNumber(paid)
        $('#total').val(isi);
    }
}
function formatRupiah(angka, prefix)
{
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}
function cekTipe(val){
    if (val == 'giro') {
        $('#bank').show()
        $('#bank_no').hide()
        $('#card').show()
        $('#bank_an').hide()
    }else if(val == 'bank_transfer'){
        $('#card').hide()
        $('#bank_no').show()
        $('#bank').show()
        $('#bank_an').show()
    }else{
        $('#bank_no').hide()
        $('#bank').hide()
        $('#card').hide()
        $('#bank_an').hide()
    }
}
function formatTotalOther(){
    var amount=$('#total_other').val()
    var val=(amount).replace(/[^,\d]/g, '').toString();
    var tipe=$('#tipe').val();
    if (tipe == 'discount_payment') {
        var sub_total=$('#sub_total').val();
        var total_addendum_discount=$('#total_addendum_discount').val();
        var total_addendum=$('#total_addendum').val();
        var count_total=parseFloat(sub_total);
        if (parseFloat(val) > parseFloat(count_total)) {
            $('#total_other').val('');
            val='';
        }    
    }
    var total=formatNumber(val);
    $('#total_other').val(total);
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
function doShowDetail(eq){
    var no=$(eq).data('no');
    var id=$(eq).data('id');
    var amount=$(eq).data('amount');
    var end_payment=$(eq).data('end_payment');
    var total_addendum=$('#total_addendum').val();
    t = $('#detail_payment').DataTable();
    t.clear().draw(false);
    var total_min=0;
    $.ajax({
            type: "GET",
            url: "{{ URL::to('order/detail_customer_bill') }}"+'/'+id, //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total_min+=parseFloat(arrData[i]['amount']);
                    t.row.add([
                        '<div class="text-left">'+arrData[i]['wop']+'</div>',
                        '<div class="text-left">'+arrData[i]['bank_name'] != null ? arrData[i]['bank_name'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['bank_number'] != null ? arrData[i]['bank_number'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['atas_nama'] != null ? arrData[i]['atas_nama'] : '-'+'</div>',
                        '<div class="text-center">'+formatNumber(arrData[i]['amount'])+'</div>',
                        '<div class="text-center">'+formatDateID(new Date((arrData[i]['pay_date']).substring(0,10)))+'</div>',
                        '<div class="text-center"><a href="{{URL::to('order/print_kwitansi/')}}/'+arrData[i]['id']+'" class="btn btn-success btn-sm" target="_blank"><i class="mdi mdi-printer"></i></a></div>'
                    ]).draw(false);
                }
            }
    });
    amount=(end_payment == 1 ? parseFloat(amount) + parseFloat(total_addendum) : parseFloat(amount)).toFixed(0);
    // var ppn=(parseFloat(amount) * (1/10)).toFixed(0);
    var ppn=0;
    $('#title-modal').html('Pembayaran Tagihan '+no);
    $('#bill_id').val(id);
    $('#total_ppn').val(ppn);
    $('#total_awal').val(amount);
    $('#amount_bill').val(parseFloat(amount) + parseFloat(ppn));
    $('#bill_amount').html(': '+formatRupiah(parseFloat(amount)));
    $('#bill_ppn').html(': '+formatRupiah(parseFloat(ppn)));
    $('#bill_all').html(': '+formatRupiah(parseFloat(amount) + parseFloat(ppn)));
    $('#total_min').val((parseFloat(amount) + parseFloat(ppn)) - parseFloat(total_min))
}
function checkTotalBill(eq){
    var paid=(eq.value).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    var sub_total=$('#total_min').val();
    $('#total_bill').val(formatCurrency(paid));
    var paid_more=0;
    if (total_paid >= parseFloat(sub_total)) {
        paid_more=parseFloat(total_paid) - parseFloat(sub_total);
    }
    $('#paid_more').val(paid_more);
}
</script>

@endsection
@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Tambah Payment</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('menu/payment') }}">Payment</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">
                                </h4>
                                <form method="POST" action="{{ URL::to('menu/payment/add_payment') }}" >
                                  @csrf
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label>Nama Bank</label>
                                      <select name="bank_name" required class="form-control" id="bank_name" onchange="bank()">
                                            <option value="">- Pilih Bank -</option>
                                            @foreach($bank as $key)
                                                @if($key->status == 'sudah terpakai')
                                                <option value="{{$key->bank_code}}" disabled>{{$key->bank_name}}</option>
                                                @else
                                                <option value="{{$key->bank_code}}">{{$key->bank_name}}</option>
                                                @endif
                                            @endforeach
                                      </select>
                                  
                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label>Kode Bank</label>
                                      <input type="text" class="form-control" name="kode" required maxlength="5"  id="kode">
                                      <div class="invalid-tooltip">
                                          Please fill out this field.
                                      </div>
                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <button class="btn btn-primary" type="button" id="tambah_kategori" onclick="tambah()">+</button>
                                    </div>
                                  </div>
                                  <p id="terima"></p>
                                  <input type="hidden" class="form-control" name="sum" required id="total" value="1">
                                  <div class="form-row" id="form_progress">
                                    <div class="col-md-12 mb-3">
                                      <label>Kategori Proses</label>
                                      <input type="" class="form-control" name="proses1" required placeholder="">
                                      <label>Pencairan (%)</label>
                                      <input type="number" class="form-control" name="1" id="1" required placeholder="misal : 30, 30, 40" onchange="cek_progress()">
                                    </div>
                                  </div>
                                  <button class="btn btn-primary mt-4" type="submit" disabled id="btn_submit">Submit</button>
                                  <a href="{{ URL::to('menu/payment') }}" class="btn btn-danger mt-4">Batal</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <script>
        function bank(){
            var a=$('#bank_name').val();
            $('#kode').val(a);
        }
        var sum=1;
        function tambah(){
            sum+=1;
            if(sum <=6 ){
                $('#form_progress').append('<div class="col-md-12 mb-3"><label>Kategori Proses</label><input autofocus type="" class="form-control" name="proses'+sum+'"  required placeholder=""><label>Pencairan (%)</label><input type="number" onchange="cek_progress()" class="form-control" name='+sum+' id='+sum+' required placeholder="misal : 30, 30, 40"></div>'); 
                $("#total").val(sum);
            }
        }
        
        function cek_progress(){
            var count=0;
            for(var i=0; i < 7; i++){
                if( i==0 && $('#1').val() != null && $('#1').val() != 0){
                    var id=$('#1').val();
                    count=count + parseInt(id);
                }else if( i==1 && $('#2').val() != null && $('#2').val() != 0){
                    var id=$('#2').val();
                    count=count + parseInt(id);
                }else if( i==2 && $('#3').val() != null && $('#3').val() != 0){
                    var id=$('#3').val();
                    count=count + parseInt(id);
                }else if( i==3 && $('#4').val() != null && $('#4').val() != 0){
                    var id=$('#4').val();
                    count=count + parseInt(id);
                }else if( i==4 && $('#5').val() != null && $('#5').val() != 0){
                    var id=$('#5').val();
                    count=count + parseInt(id);
                }else if( i==5 && $('#6').val() != null && $('#6').val() != 0){
                    var id=$('#6').val();
                    count=count + parseInt(id);
                }else if( i==6 && $('#7').val() != null && $('#7').val() != 0){
                    var id=$('#7').val();
                    count=count + parseInt(id);
                }
            }
            if(count == 100){
                $("#terima").html('progress pencairan sudah '+count+'(%)');
                $("#btn_submit").attr("disabled", false);
                // $('form').prop('readonly', true);
            }else if(count < 100){
                $("#terima").html('progress pencairan kurang '+(100-count)+'(%)');
                $("#btn_submit").attr("disabled", true);
            }else{
                $("#terima").html('progress pencairan lebih '+(count-100)+'(%)');
                $("#btn_submit").attr("disabled", true);
            }
        }
    </script>
@endsection

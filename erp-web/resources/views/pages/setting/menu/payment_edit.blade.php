@extends('theme.default')

@section('breadcrumb')
      <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Edit Payment</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('menu/payment') }}">Payment</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                                <form method="POST" action="{{ URL::to('menu/payment/edit_payment') }}" >
                                  @csrf

                                  <p id="test"></p>
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label>Nama Bank</label>
                                      <select name="bank_name" required class="form-control" id="bank_name" onchange="bank()" readonly>
                                            <option value="">- Pilih Bank -</option>
                                            @foreach($bank as $key)
                                                @if($key->status == 'sudah terpakai' && $id == $key->bank_code)
                                                <option value="{{$key->bank_code}}" disabled selected>{{$key->bank_name}}</option>
                                                @elseif($key->status == 'sudah terpakai')
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
                                      <input type="text" class="form-control" name="kode" required maxlength="5"  id="kode" value="{{$id}}" readonly>
                                      <div class="invalid-tooltip">
                                          Please fill out this field.
                                      </div>
                                    </div>
                                  </div>
                                  
                                  <table class="table table-bordered">
                                      <thead>
                                          <th>Kategori Proses</th>
                                          <th>Pencairan</th>
                                          <th>Aksi</th>
                                      </thead>
                                      @foreach($data as $num => $key)
                                      @php $percent1=round($key->payment_percent, 2) @endphp
                                      <tbody id="tb_content{{$num}}">
                                          <td>{{$key->progress_category}}</td>
                                          <td>{{$percent1}}%</td>
                                          <td>
                                              <button class="btn btn-primary" type="button" id="btnedit{{$num}}" onclick="btn_edit{{$num}}()"><i class="fas fa-pencil-alt"></i></button>
                                              <button class="btn btn-danger" type="button" id="btnhapus{{$num}}" onclick="btn_hapus{{$num}}({{$percent1}})"><i class="fas fa-trash-alt"></i></button>
                                          </td>
                                      </tbody>
                                      @endforeach
                                  </table>
                                  
                                  @php $total_data=count($data) @endphp
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label></label>
                                        <button class="btn btn-primary" type="button" id="tambah_kategori" onclick="tambah()">tambah kategori +</button>
                                    </div>
                                  </div>
                                  <input type="hidden" class="form-control" id="total" value="{{$total_data}}" name="total">
                                  @foreach($data as $num => $key)
                                  @php $percent=round($key->payment_percent, 2) @endphp
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3 hide" id="form{{$num}}">
                                      <input type="hidden" class="form-control" name="id{{$num}}" required placeholder="" value="{{$key->id}}">
                                      <label>Kategori Proses</label>
                                      <input type="" class="form-control" name="proses{{$num}}" required placeholder="" value="{{$key->progress_category}}">
                                      <!--<input type="" class="form-control" required placeholder="" value="{{$key->id}}">-->
                                      <label>Pencairan</label>
                                      <input type="number" class="form-control" name="{{$num}}" id="{{$num}}" required placeholder="misal : 30, 30, 40" onchange="cek_progress({{$num}}, this.value)" value="{{$percent}}">
                                    </div>
                                  </div>
                                  @endforeach
                                  <div class="form-row" id="form_progress">
                                  </div>
                                  <button class="btn btn-primary mt-4" type="submit" id="btn_submit">Simpan</button>
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
        var count=100;
        function btn_edit0(){
            $('#form0').show();
        }
        function btn_edit1(){
            $('#form1').show();
        }
        function btn_edit2(){
            $('#form2').show();
        }
        function btn_edit3(){
            $('#form3').show();
        }
        function btn_edit4(){
            $('#form3').show();
        }
        function btn_edit5(){
            $('#form3').show();
        }
        function btn_edit6(){
            $('#form3').show();
        }
        function btn_hapus0(nilai){
            $('#form0').remove();
            $('#tb_content0').remove();
            count-=parseInt(nilai);
            $("#btn_submit").attr("disabled", true);
        }
        function btn_hapus1(nilai){
            $('#form1').remove();
            $('#tb_content1').remove();
            count-=parseInt(nilai);
            $("#btn_submit").attr("disabled", true);
        }
        function btn_hapus2(nilai){
            $('#form2').remove();
            $('#tb_content2').remove();
            count-=parseInt(nilai);
            $("#btn_submit").attr("disabled", true);
        }
        function btn_hapus3(nilai){
            $('#form3').remove();
            $('#tb_content3').remove();
            count-=parseInt(nilai);
            $("#btn_submit").attr("disabled", true);
        }
        function btn_hapus4(nilai){
            $('#form4').remove();
            $('#tb_content4').remove();
            count-=parseInt(nilai);
            $("#btn_submit").attr("disabled", true);
        }
        function btn_hapus5(nilai){
            $('#form5').remove();
            $('#tb_content5').remove();
            count-=parseInt(nilai);
            $("#btn_submit").attr("disabled", true);
        }
        function btn_hapus6(nilai){
            $('#form6').remove();
            $('#tb_content6').remove();
            count-=parseInt(nilai);
            $("#btn_submit").attr("disabled", true);
        }
        var sum={{$total_data}};
        function tambah(){
            if(sum <=6 ){
                $('#form_progress').append('<div class="col-md-12 mb-3"><label>Kategori Proses</label><input autofocus class="form-control" name="proses'+sum+'" required placeholder=""><label>Pencairan</label><input type="number" onchange="cek_progress()" class="form-control" name='+sum+' id='+sum+' required placeholder="misal : 30, 30, 40"></div>'); 
                $("#total").val(sum);
            }
            sum+=1;
        }
        
        function cek_progress(){
            var count=0;
            for(var i=0; i < 7; i++){
                if( i==0 && $('#0').val() != null && $('#0').val() != 0){
                    var id=$('#0').val();
                    count=count + parseInt(id);
                }else if( i==1 && $('#1').val() != null && $('#1').val() != 0){
                    var id=$('#1').val();
                    count=count + parseInt(id);
                }else if( i==2 && $('#2').val() != null && $('#2').val() != 0){
                    var id=$('#2').val();
                    count=count + parseInt(id);
                }else if( i==3 && $('#3').val() != null && $('#3').val() != 0){
                    var id=$('#3').val();
                    count=count + parseInt(id);
                }else if( i==4 && $('#4').val() != null && $('#4').val() != 0){
                    var id=$('#4').val();
                    count=count + parseInt(id);
                }else if( i==5 && $('#5').val() != null && $('#5').val() != 0){
                    var id=$('#5').val();
                    count=count + parseInt(id);
                }else if( i==6 && $('#6').val() != null && $('#6').val() != 0){
                    var id=$('#6').val();
                    count=count + parseInt(id);
                }
            }
            if(count == 100){
                $("#terima").html('progress pencairan sudah '+count+'(%)');
                $("#btn_submit").attr("disabled", false);
                // $('form').prop('readonly', true);
            }else{
                $("#terima").html('progress pencairan kurang '+(100-count)+'(%)');
                $("#btn_submit").attr("disabled", true);
            }
        }
        // function cek_progress(id, baru){
        //     count=count-parseInt(nilai);
        //     var old=$('#old'+id+'').val();
        //     alert(old);
        //     count=count+parseInt(baru);
        //     alert(count);
        //     $("#btn_submit").attr("disabled", true);
        //     // alert((parseInt(id)+2));
        //     if(count == 100){
        //         $("#btn_submit").attr("disabled", false);
        //         // $('form').prop('readonly', true);
        //     }
        // }
    </script>        
@endsection

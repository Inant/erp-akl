@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">List Akun</h4>
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
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Akun</h4>
                    <form method="POST" action="{{ URL::to('akuntansi/storeakun') }}" >
                      @csrf
                      <div class="form-row">
                          <div class="col-12">
                            <label>Level</label>  
                          </div>
                          <div class="col-md-12 mb-3">
                          <select class="form-control select2" name="level" required onchange="setLevel()" id="level" style="width: 100%;">
                                  <option value="">--Pilih Level--</option>
                                  <option value="0">0</option>
                                  <option value="1">1</option>
                                  <option value="2">2</option>
                                  <option value="3">3</option>
                                  <option value="4">4</option>
                          </select>
                          </div>
                      </div>
                      <div class="form-row">
                          <div class="col-12">
                            <label>Akun : *</label>
                          </div>
                          <div class="col-md-12 mb-3">
                            <select class="form-control select2" name="id_parent"  onchange="cekParent()" id="id_parent" style="width: 100%;">
                                <option>Pilih Parent</option>
                                @foreach($parent_option as $value)
                                <option value="{{$value->id_akun}}">{{$value->nama_akun}}</option>
                                @endforeach
                            </select>
                          </div>
                      </div>
                      <div class="form-row" id="level2" style="display:none">
                          <div class="col-12">
                            <label>Sub Akun Parent</label>
                          </div>
                          <div class="col-md-12 mb-3">
                            <select class="form-control select2" name="level2"  onchange="cekSubLevel1()" id="sublevel1" style="width: 100%;">
                            </select>
                          </div>
                      </div>
                      <div class="form-row" id="level3" style="display:none">
                          <div class="col-12">
                            <label>Sub Sub Akun Parent</label>
                          </div>
                          <div class="col-md-12 mb-3">
                              <select class="form-control select2" name="level3"  onchange="cekSubLevel2()" id="sublevel2" style="width: 100%;">
                              </select>
                          </div>
                      </div>
                      <div class="form-row" id="level4" style="display:none">
                          <div class="col-12">
                            <label>Sub Sub Akun Parent</label>
                          </div>
                          <div class="col-md-12 mb-3">
                              <select class="form-control select2" name="level4"  onchange="cekSubLevel3()" id="sublevel3" style="width: 100%;">
                              </select>
                          </div>
                      </div>
                      <div class="form-row">
                          <div class="col-12">
                            <label>Nama Akun</label>    
                          </div>
                          <div class="col-md-12 mb-3">
                              <input class="form-control" name="nama_akun" required >
                          </div>
                      </div>
                      <div class="form-row">
                          <div class="col-12">
                            <label>Kode Akun</label>    
                          </div>
                          <div class="col-md-12 mb-3">
                              <input class="form-control" name="no_akun" id="no_akun" required readonly="">
                          </div>
                      </div>
                      <button class="btn btn-primary mt-4" type="submit">Submit</button>                
                  </form>
                </div>
            </div>
        </div>
    </div>
                
</div>


<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript">
  var level=1;
    function setLevel(){
        level=parseInt($('#level').val());
        if (level == 0) {
            $('#parent').hide();
            $('#level2').hide();
            $('#level3').hide();
            $('#level4').hide();
            $('#kode_parent').val('');
        }else if(level == 1){
            $('#parent').show();
            $('#level2').hide();
            $('#level3').hide();
            $('#level4').hide();
            $('#kode_parent').val('');
            $('#sublevel1').empty();
        }else if(level == 2){
            $('#parent').show();
            $('#level2').show();
            $('#level3').hide();
            $('#level4').hide();
            $('#kode_parent').val('');
            $('#sublevel2').empty();
        }else if(level == 3){
            $('#parent').show();
            $('#level2').show();
            $('#level3').show();
            $('#level4').hide();
            $('#kode_parent').val('');
        }else if(level == 4){
            $('#parent').show();
            $('#level2').show();
            $('#level3').show();
            $('#level4').show();
            $('#kode_parent').val('');
        }
    }
    var id_parent=0;
    function cekParent(){
        var id=$('#id_parent').val();
        if (level == 1) {
            cekKodeAkun(id);
        }else if(level > 1){
            $('#sublevel1').empty();
            var subAkun=$('#sublevel1');
            $.ajax({
                url : 'getLevel/'+id, 
                type : 'GET',
                dataType : 'json',
                success: function(response){
                    // console.log(response);
                    arrData=response;
                    var option = '<option value="">Pilih Akun</option>';
                    for (var i = 0; i < arrData.length; i++) {
                        option+='<option value="'+arrData[i]['id_akun']+'">'+arrData[i]['nama_akun']+'</option>';
                    }
                    subAkun.append(option);
                }
            });
        }
        var parent=@php echo $parent_option_js @endphp;
        for (var i = 0; i < parent.length; i++) {
            if (parent[i]['label'] == id) {
                id_parent=parent[i]['value'];
            }
        }
    }
    function cekSubLevel1(){
        var id=$('#sublevel1').val();
        if (level == 2) {
            cekKodeAkun(id);
        }else if(level > 2){
            $('#sublevel2').empty();
            var subAkun=$('#sublevel2');
            $.ajax({
                url : 'getLevel/'+id, 
                type : 'GET',
                dataType : 'json',
                success: function(response){
                    // console.log(response);
                    arrData=response;
                    var option = '<option value="">Pilih Akun</option>';
                    for (var i = 0; i < arrData.length; i++) {
                        option+='<option value="'+arrData[i]['id_akun']+'">'+arrData[i]['nama_akun']+'</option>';
                    }
                    // console.log(option);
                    subAkun.append(option);
                }
            });
        }
    }
    function cekSubLevel2(){
        var id=$('#sublevel2').val();
        if (level == 3) {
            cekKodeAkun(id);
        }else if(level > 3){
            $('#sublevel3').empty();
            var subAkun=$('#sublevel3');
            $.ajax({
                url : 'getLevel/'+id, 
                type : 'GET',
                dataType : 'json',
                success: function(response){
                    // console.log(response);
                    arrData=response;
                    var option = '<option value="">Pilih Akun</option>';
                    for (var i = 0; i < arrData.length; i++) {
                        option+='<option value="'+arrData[i]['id_akun']+'">'+arrData[i]['nama_akun']+'</option>';
                    }
                    // console.log(option);
                    subAkun.append(option);
                }
            });
        }
    }
    function cekSubLevel3(){
        var id=$('#sublevel3').val();
        if (level == 4) {
            cekKodeAkun(id);
        }
    }
    var dataAkun=0;
    function cekKodeAkun(id){
        $.ajax({
            url : "{{URL::to('akuntansi/getNoAkun')}}"+'/'+id, 
            type : 'GET',
            dataType : 'json',
            "success": function(response){
                dataAkun=response;
                var nomor=pecahAkun(dataAkun['no_akun']);
                if (level == 1) {
                    var iterate=parseInt(dataAkun['total'])+1;
                    $('#no_akun').val(dataAkun['no_akun_main']+'.'+iterate+'.0.0');
                }else if (level == 2) {
                    var iterate=parseInt(dataAkun['total'])+1;
                    var pecah=dataAkun['no_akun_main'].split('.');
                    $('#no_akun').val(pecah[0]+'.'+pecah[1]+'.'+iterate+'.0');
                }
                else if (level == 3) {
                    var iterate=parseInt(dataAkun['total'])+1;
                    var pecah=dataAkun['no_akun_main'].split('.');
                    $('#no_akun').val(pecah[0]+'.'+pecah[1]+'.'+pecah[2]+'.'+iterate);
                }
                else if (level == 4) {
                    var iterate=parseInt(dataAkun['total'])+1;
                    var pecah=dataAkun['no_akun_main'].split('.');
                    $('#no_akun').val(pecah[0]+'.'+pecah[1]+'.'+pecah[2]+'.'+pecah[3]+'.'+iterate);
                }
            }
        });
    }

    function pecahAkun(val){
        if (val != null) {
            var a=val.split('.');
        }else{
            var a=[0,0,0,0];
        }
        return a;
    }
</script>
@endsection
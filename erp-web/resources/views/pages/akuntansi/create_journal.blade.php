@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Tambah Jurnal</h4>
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
                    <h4 class="card-title">Tambah Jurnal</h4>
                    <form method="POST" action="{{ URL::to('akuntansi/storejournal') }}" >
                      @csrf
                        <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="form-group">
                                        <label>Tanggal</label>  
                                        <input type="date" value="{{date('Y-m-d')}}" class='form-control' required placeholder='Tanggal' name="tanggal">
                                    </div>
                                </div>
                                <div class="col-6 mb-3" @if($site_id != 0) hidden @endif>
                                    <div class="form-group">
                                        <label>Lokasi</label>  
                                        <select class="form-control select2" name="location_id" id="location_id" style="width: 100%;" required>
                                            <option>Pilih Lokasi</option>
                                            @foreach($business_locations as $value)
                                            <option value="{{$value->id}}" {{$value->id == $site_id ? 'selected' : ''}}>{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                            <label>Deskripsi : *</label>
                                            <textarea id="deskripsi" name="deskripsi" class="form-control" required placeholder='Keterangan Jurnal'></textarea>
                                    </div>
                                </div>
                        </div>
                    
                      <!-- <div class="form-row">
                          <div class="col-4">
                                <label>Akun</label>    
                                <select class="form-control select2" name="akun" id="akun" style="width: 100%;" required>
                                    @foreach($data['akun_option'] as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                          </div>
                          <div class="col-4">
                            <label>Jumlah</label>    
                              <input type="number" class="form-control" name="jumlah_akun" id="jumlah_akun" required onkeyup="cekJumlahLawan()">
                          </div>
                          <div class="col-4">
                                <label>Tipe</label>    
                                <select class="form-control select2" name="tipe_akun" id="tipe_akun" style="width: 100%;" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="1">Debit</option>
                                    <option value="0">Kredit</option>
                                </select>
                          </div>
                      </div>
                      <br>
                      <hr> -->
                      <!-- <div class="form-row">
                          <div class="col-3">
                                <label>Lawan Akun</label>    
                                <select class="form-control" name="lawan_akun[]" id="lawan_akun[]" style="width: 100%;" required>
                                    @foreach($data['akun_option'] as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                          </div>
                          <div class="col-3">
                            <label>Jumlah</label>    
                              <input type="number" class="form-control" name="jumlah_lawan_akun[]" id="jumlah_lawan_akun[]" required onkeyup="cekJumlahLawan()">
                          </div>
                          <div class="col-2">
                                <label>Tipe</label>    
                                <select class="form-control select2" name="tipe_lawan_akun[]" id="tipe_lawan_akun[]" style="width: 100%;" required>
                                    <option value="1">Debit</option>
                                    <option value="0">Kredit</option>
                                </select>
                          </div>
                          <div class="col-2">
                                <label>Jenis</label>    
                                <select class="form-control select2" name="tipe_lawan_akun[]" id="tipe_lawan_akun[]" style="width: 100%;" required>
                                    <option value="1">Debit</option>
                                    <option value="0">Kredit</option>
                                </select>
                          </div>
                          <div class="col-sm-2">
                            <button class="button">hapus</button>
                          </div>
                      </div>
                      <br> -->
                      <!-- <div id="input_lawan_akun">
                      </div> -->
                      <div class="form-group">
                          <!-- <div align="right"> -->
                              <!-- <br> -->
                              <button id="add_lawan_akun" type="button" class="btn btn-info" onclick="addrow()"><i class="fa fa-plus"></i>Tambah</button>
                          <!-- </div> -->
                      </div>
                      <table class="table table-bordered table-striped" id="detail_jurnal">
                            <thead>
                                <tr>
                                    <th>Akun</th>
                                    <th>Tipe Jurnal</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>No Sumber</th>
                                    <th style="width:100px">Action</th>
                                </tr>
                            </thead>    
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control select2" name="akun[]" style="width: 100%;" required>
                                            @foreach($data['akun_option'] as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" name="tipe_akun[]" style="width: 100%;" required onchange="cekJumlahLawan()">
                                            <option value="1">Debit</option>
                                            <option value="0">Kredit</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" name="sifat_akun[]" style="width: 100%;" required onchange="cekJumlahLawan()">
                                            <option value="akun">Akun</option>
                                            <option value="lawan">Lawan</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="jumlah[]" required onkeyup="cekJumlahLawan()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="no[]">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select class="form-control select2" name="akun[]" style="width: 100%;" required>
                                            @foreach($data['akun_option'] as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" name="tipe_akun[]" style="width: 100%;" required onchange="cekJumlahLawan()">
                                            <option value="1">Debit</option>
                                            <option value="0">Kredit</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" name="sifat_akun[]" style="width: 100%;" required onchange="cekJumlahLawan()">
                                            <option value="akun">Akun</option>
                                            <option value="lawan">Lawan</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="jumlah[]" required onkeyup="cekJumlahLawan()">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="no[]">
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                      </table>
                      
                      <button class="btn btn-primary mt-4" id="submit" disabled type="submit">Submit</button>                
                  </form>
                </div>
            </div>
        </div>
    </div>
                
</div>


<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
        $('.select2').select2();
        var max_input=10;
        var add_lawan_akun=$('#add_lawan_akun');
        var input_lawan_akun=$('#input_lawan_akun');
        var y = 1; //initlal text box count
        // $(add_lawan_akun).click(function(e){
        //     e.preventDefault();
        //     var akun_option='<option value="">Pilih Akun / No Akun</option>';
        //     var akun_lawan=@php echo $data['akun_option_js'] @endphp;
        //     for (var i = 0; i < akun_lawan.length; i++) {
        //         akun_option+='<option value="'+akun_lawan[i].label+'">'+akun_lawan[i].value+'</option>';
        //     }

        //     var input_akun = '<select class="custom-select" id="inputGroupSelect01">'+akun_option+'</select>';
        //     var input_jumlah = '<input class="form-control" name="jumlah_lawan[]" id="jumlah_lawan[]" required type="number" onkeyup="cekJumlahLawan()">';
        //     var input_tipe = '<select class="form-control" name="tipe_akun_lawan[]" style="width: 100%;"><option value="1">Debit</option><option value="0">Kredit</option></select>';
        //     if (y < max_input) {
        //         y++;
        //         $(input_lawan_akun).append('<div class="form-row"><div class="col-4"><div class="input-group mb-3"><div class="input-group-prepend"><button class="input-group-text remove_field_obat" id="remove_field_obat" for="inputGroupSelect01">X</button></div>'+input_akun+'</div></div><div class="col-4">'+input_jumlah+'</div><div class="col-4">'+input_tipe+'</div></div>'); //add input box
        //     }
            
        // });
        // $(input_lawan_akun).on("click","#remove_field_obat", function(e){ //user click on remove text
        //     e.preventDefault(); 
        //     $(this).closest('.form-row').remove(); y--;
        //     cekJumlahLawan(null);
        // });
    });
    // function cekLawan(){
    //     var lawan_length = $("[id^=lawan_akun]").length;
    //     var jumlah=$("[id^=jumlah_akun]").val()
    //     for (var x = 0; x < lawan_length; x++) {
    //         if ($("[id^=lawan_akun]").eq(x).val() == 15) {
    //             $('#add_lawan_akun').attr('disabled', true);
    //             $("[id^=jumlah_lawan]").eq(x).val(jumlah);
    //             $('#submit').attr('disabled', false);
    //         }else{
    //             $('#add_lawan_akun').attr('disabled', false);
    //         }
    //     };
    // }
    function addrow(){
        var tdAdd='<tr>'+
                        '<td>'+
                            '<select class="form-control select2" name="akun[]" style="width: 100%;" required>'+
                                '@foreach($data['akun_option'] as $key => $value)'+
                                '<option value="{{$key}}">{{$value}}</option>'+
                                '@endforeach'+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<select class="form-control select2" name="tipe_akun[]" style="width: 100%;" required onchange="cekJumlahLawan()">'+
                                '<option value="1">Debit</option>'+
                                '<option value="0">Kredit</option>'+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<select class="form-control select2" name="sifat_akun[]" style="width: 100%;" required onchange="cekJumlahLawan()">'+
                                '<option value="akun">Akun</option>'+
                                '<option value="lawan">Lawan</option>'+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<input type="text" class="form-control" name="jumlah[]" required onkeyup="cekJumlahLawan()">'+
                        '</td>'+
                        '<td>'+
                            '<input type="text" class="form-control" name="no[]">'+
                        '</td>'+
                        '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                    '</tr>';
        $('#detail_jurnal').find('tbody:last').append(tdAdd);
        $('.select2').select2();
    }
    $("#detail_jurnal").on("click", ".removeOption", function(event) {
        event.preventDefault();
        $(this).closest("tr").remove();
        cekJumlahLawan()
    });

    function cekJumlahLawan() {
        var akun = $("[name^=akun]");
        var tipe_akun = $("[name^=tipe_akun]");
        var sifat_akun = $("[name^=sifat_akun]");
        var jumlah = $("[name^=jumlah]");
        var akun_debit=0, akun_kredit=0, lawan_debit=0, lawan_kredit=0;
        for (var i = 0; i < akun.length; i++) {
            var dt_akun=akun.eq(i).val();
            var dt_tipe_akun=tipe_akun.eq(i).val();
            var dt_sifat_akun=sifat_akun.eq(i).val();
            var conv_jumlah=jumlah.eq(i).val();
            var dt_jumlah = conv_jumlah.replace(/[^,\d]/g, '').toString();
            if (dt_akun == '' || dt_tipe_akun == '' || dt_sifat_akun == '' || dt_jumlah == '') {
                
            }else{
                if (dt_tipe_akun == '1' && dt_sifat_akun == 'akun') {
                    akun_debit+=parseFloat(dt_jumlah);
                }else if(dt_tipe_akun == '0' && dt_sifat_akun == 'akun') {
                    akun_kredit+=parseFloat(dt_jumlah);
                }else if(dt_tipe_akun == '1' && dt_sifat_akun == 'lawan') {
                    lawan_debit+=parseFloat(dt_jumlah);
                }else{
                    lawan_kredit+=parseFloat(dt_jumlah);
                }
            }
            console.log(dt_jumlah)
            jumlah.eq(i).val(formatCurrency(dt_jumlah));
        }
        var total_akun=parseFloat(akun_debit) - parseFloat(akun_kredit);
        var total_lawan=parseFloat(lawan_kredit) - parseFloat(lawan_debit);
        if (total_akun == total_lawan) {
            if (total_akun != 0) {
                $('#submit').attr('disabled', false);
            }else{
                $('#submit').attr('disabled', true);
            }
        }else{
            $('#submit').attr('disabled', true);
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

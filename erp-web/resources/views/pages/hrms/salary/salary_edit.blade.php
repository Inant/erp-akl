@extends('theme.default')

@section('breadcrumb')
      <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Edit Setting Gaji</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('salary') }}">Setting Gaji</a></li>
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
                            <form method="POST" action="{{ URL::to('salary/edit_post') }}" >
                              @csrf
                              <div class="form-row">
                                <div class="col-12">
                                  <label>Nama Pegawai</label>
                                </div>
                                <div class="col-md-12 mb-3">
                                  <input type="hidden" name="id" value="{{$detail->id_setting_gaji}}">
                                  <select class="form-control select2" name="id_pegawai" id="id_pegawai" required style="width:100%" readonly="true">
                                      <option value="">Pilih Pegawai</option>
                                      @foreach($employee as $value)
                                      <option value="{{$value->id}}" {{$value->id == $detail->m_employee_id ? 'selected' : ''}}>{{$value->name}}</option>
                                      @endforeach
                                  </select>
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Gaji Pokok</label>
                                  <input type="text" class="form-control" name="gaji_pokok" id="gaji_pokok" required onkeyup="cekGaji(this.value)" value="{{$detail->gaji_pokok}}">
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Uang Makan</label>
                                  <input type="text" class="form-control" name="uang_makan" id="uang_makan" required onkeyup="cekUangMakan(this.value)" value="{{$detail->uang_makan}}">
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Uang Transport</label>
                                  <input type="text" class="form-control" name="uang_transport" id="uang_transport" required onkeyup="cekUangTransport(this.value)" value="{{$detail->uang_transport}}">
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Denda</label>
                                  <input type="text" class="form-control" name="denda" id="denda" required onkeyup="cekDenda(this.value)" value="{{$detail->denda}}">
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Denda Telat</label>
                                  <input type="text" class="form-control" name="denda_telat" id="denda_telat" required onkeyup="cekDendaTelat(this.value)"  value="{{$detail->denda_telat}}">
                                </div>
                              </div>
                              <button class="btn btn-primary mt-4" type="submit" id="btn_submit">Submit</button>
                              <a href="{{ URL::to('salary') }}" class="btn btn-danger mt-4">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script>
  function cekGaji(val){
    $('#gaji_pokok').val(formatRupiah(val));
  }
  function cekDenda(val){
    $('#denda').val(formatRupiah(val));
  }
  function cekDendaTelat(val){
    $('#denda_telat').val(formatRupiah(val));
  }
  function cekUangMakan(val){
    $('#uang_makan').val(formatRupiah(val));
  }
  function cekUangTransport(val){
    $('#uang_transport').val(formatRupiah(val));
  }
  function formatRupiah(angka, prefix)
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
  $("select").prop("disabled", true);
</script>
@endsection

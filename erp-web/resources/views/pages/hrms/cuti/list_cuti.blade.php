@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">List Cuti</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Cuti</li>
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
                    <h4 class="card-title">List Cuti</h4>
                    <div class="text-right">
                        <form accept="cuti" method="post">
                            @csrf
                                <!-- <div class="col-sm-12"> -->
                                    <div class="form-inline">
                                        <label>Pilih Bulan : </label>&nbsp;
                                        <select data-plugin-selectTwo class="form-control select2" name="bulan" id="bulan" required>
                                            <option value="">Pilih Bulan</option>
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
                                        <select data-plugin-selectTwo class="form-control select2" name="tahun" id="tahun" required>
                                            <option value="">Pilih Tahun</option>
                                            @for ($i = date('Y') - 5; $i <= date('Y'); $i++) { 
                                            <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>&nbsp;
                                       
                                        <button class="btn btn-primary"  onclick="cekAbsensiDate()"><i class="fa fa-search"></i></button>
                                    </div>
                                <!-- </div> -->
                        </form>
                        <a href="{{ URL::to('cuti/add') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add Cuti</button></a>
                    </div>
                    <div class="table-responsive">
                        <table id="cuti_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">id</th>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Tanggal Cuti</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <!--<tbody>
                                @for($i=0; $i < count($detail); $i++)
                                <tr>
                                    <td>{{$detail[$i]['id']}}</td>
                                    <td>{{$detail[$i]['name']}}</td>
                                    <td>{{$detail[$i]['tanggal_cuti']}}</td>
                                    <td><a href="/cuti/form/{{$detail[$i]['id']}}/{{$month}}" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a></td>
                                </tr>
                                @endfor
                            </tbody>-->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>                
</div>

<!-- <script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script> -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#cuti_list').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            type: "POST",
            url: '/cuti/json',
            data: {tahun: '{{$tahun}}',bulan: '{{$bulan}}', _token: '{{csrf_token()}}' },
        },
        // "cuti",
        "columns": [
            {"data": "id"},
            {"data": "name"},
            {"data": "tanggal_cuti"},
            {"data": "action"}
        ],
    });
    // $.ajax({
    //     type: "POST",
    //     url: '/cuti/json',
    //     data: {tahun: '{{$tahun}}',bulan: '{{$bulan}}', _token: '{{csrf_token()}}' },
    //     success: function (data) {
    //        console.log(data);
    //     },
    //     error: function (data, textStatus, errorThrown) {
    //         console.log(data);

    //     },
    // });
});
</script>
<script>
  var bulan='{{$bulan}}';
  var tahun='{{$tahun}}';
  $(document).ready(function() {
    if (typeof bulan != 'undefined') {
        $('#bulan').val(bulan).change();
        $('#tahun').val(tahun).change();
    }
  });
</script>
@endsection
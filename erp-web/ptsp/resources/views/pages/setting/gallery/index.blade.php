@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Gambar</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Data Gambar</li>
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
                    {{-- @if($error['is_error'])
                    <div class="col-12">
                        <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                        </div>
                    </div>
                    @endif --}}
                    <div class="col-12">
                         <div class="text-right">
                            <a href="{{ URL::to('menu/gambar/add') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add new gambar</button></a>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Daftar Gambar</h4>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Filename</th>
                                                <th class="text-center">Creator</th>
                                                <th class="text-center">Gambar</th>
                                                <th class="text-center"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
</div>


<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
    <script>

        dt_detail = $('#dt_detail').DataTable();

        $(document).ready(function(){
            // console.log(arrMaterialPembelianRutin);
            t = $('#zero_config').DataTable();
            t.clear().draw(false);
            $.ajax({
                    type: "GET",
                    url: "{{ URL::to('menu/getGambarJson') }}", //json get site
                    dataType : 'json',
                    success: function(response){
                        arrData = response['data'];
                        for(i = 0; i < arrData.length; i++){
                                a = i+1;
                                link = '{{ URL::to('menu/simulasi_kpr/edit') }}';
                                linkDel = '{{ URL::to('menu/gambar/delete/') }}';
                                asset = '{{ URL::asset('public/upload/photo/') }}'
                                id = arrData[i]['id'];
                                t.row.add([
                                    '<div class="text-center">'+a+'</div>',
                                    '<div class="text-center">'+arrData[i]['filename']+'</div>',
                                    '<div class="text-center">'+arrData[i]['name']+'</div>',
                                    '<div class="text-center"><img src="'+ asset +'/'+arrData[i]['filename']+'" width="30px"></div>',
                                    '<div class="text-center"><a href="'+ link +'/'+ id +'" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>&nbsp;<a href="'+ linkDel +'/'+ id +'" class="btn waves-effect waves-light btn-xs btn-danger"><i class="fas fa-times"></i></a></div>'
                                ]).draw(false);
                        }
                    }
            });
        });

        </script>

@endsection

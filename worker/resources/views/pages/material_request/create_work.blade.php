@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-md-5 align-self-center">
            <h4 class="page-title">Progress Proyek</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Progress Proyek</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-7 align-self-center">
            
        </div>
    </div>
</div>
@endsection

@section('content')
<style>
    @media only screen and (max-width: 600px) {
      table {
        font-size: 14px;
      }
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- <div class="card-header">Dashboard</div> -->
                <div class="card-body">
                    <!-- <h4 class="card-title">Temp Guide</h4> -->
                    <table class="table no-border mini-table m-t-20">
                        <tbody>
                            <tr>
                                <td class="text-medium">Project</td>
                                <td class="font-medium">
                                    {{$inv_requests->name}}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-medium">No Permintaan</td>
                                <td class="font-medium">
                                    {{$inv_requests->req_no}}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-medium">Work Header</td>
                                <td class="font-medium">
                                    {{$dev_projects->work_header}}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-medium">Pekerjaan</td>
                                <td class="font-medium">
                                    {{$work_detail}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <table class="table no-border mini-table m-t-20">
                        <thead>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Pantau</th>
                        </thead>
                        <tbody>
                            @foreach($dev_project_ds as $value)
                            <tr>
                                <td>{{$value->notes}}</td>
                                <td>{{$value->is_done == 0 ? 'Pengerjaan' : 'Selesai'}}</td>
                                <td><a href="{{URL::to('material_request/run_project/'.$value->id)}}" class="btn btn-sm btn-success"><i class="mdi mdi-eye"></i></a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(count($product_sub) != 0)
                <div class="card-body">
                    <form action="{{URL::to('material_request/create_work')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="">Catatan</label>
                            <input type="text" name="notes" class="form-control" required>
                        </div>
                        <br>
                        <input type="hidden" name="dev_project_id" id="dev_project_id" value="{{$dev_projects->id}}" />
                            <input type="hidden" class="form-control" name="work_detail" id="work_detail"  value="{{$work_detail}}" />
                        <table id="detail_worker" style="min-width:100%; border-collapse : collapse">
                            <thead>
                                <tr>
                                    <td style="min-width:90%">Nama Pekerja</td>
                                    <td><button class="btn btn-info btn-sm float-right" onclick="addWorker()" type="button"><i class="mdi mdi-plus"></i></button></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="min-width:90%"><input id="worker[]" name="worker[]" required class="form-control"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <label for="">Label</label>
                        <br>
                        @foreach($product_sub as $value)
                        <label>
                        <input type="checkbox" name="product_sub_id[]" onclick="testCheck()" required value="{{$value->pd_id}}"> {{$value->no}}
                        </label>&nbsp;
                        @endforeach
                        <br>
                        <table id="detail_label" style="min-width:100%; border-collapse : collapse" hidden>
                            <thead>
                                <tr>
                                    <td style="min-width:90%">Label</td>
                                    <td><button class="btn btn-info btn-sm float-right" onclick="addLabel()" type="button"><i class="mdi mdi-plus"></i></button></td>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                        <br>
                        <div class="form-group">
                            <button type="submit" id="submit" class="btn btn-block btn-success">Mulai</button>
                        </div>

                    </form>
                </div>
                @else
                    @if($dev_projects->is_done == 0)
                    <div class="card-body">
                        <a href="{{URL::to('material_request/close_work/'.$dev_projects->id)}}" class="btn btn-block btn-danger">Selesai Bagian</a>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
function addWorker(){
   var tdAdd='<tr>'+
                   '<td>'+
                       '<input id="worker[]" name="worker[]" required class="form-control">'+
                   '</td>'+
                   '<td class="text-center"><button class="btn btn-danger float-right btn-sm removeOption"><i class="mdi mdi-delete"></i></button></td>'+
               '</tr>';
   $('#detail_worker').find('tbody:last').append(tdAdd);
}
$("#detail_worker").on("click", ".removeOption", function(event) {
   event.preventDefault();
   $(this).closest("tr").remove();
});
function addLabel(){
    var option_material='<option value="">--- Pilih Label ---</option>';
    @foreach($product_sub as $value)
    option_material+='<option value="{{$value->pd_id}}">{{$value->no}}</option>';
    @endforeach
    var tdAdd='<tr>'+
                    '<td style="min-width:90%">'+
                        '<select id="product_sub_id[]" name="product_sub_id[]" onchange="cekItem()" required class="form-control custom-select" style="width: 100%; height:32px;">'+
                            option_material+
                        '</select>'+
                    '</td>'+
                    '<td class="text-center"><button class="btn btn-danger float-right btn-sm removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                '</tr>';
    $('#detail_label').find('tbody:last').append(tdAdd);
}
$("#detail_label").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
function testCheck(){
    checked = $("input[type=checkbox]:checked").length;
    if(!checked) {
        $("input[type=checkbox]").prop('required', true);
    }else{
        $("input[type=checkbox]").prop('required', false);
    }
}
</script>
@endsection

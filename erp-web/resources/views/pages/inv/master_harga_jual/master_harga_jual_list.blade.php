@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Harga Jual</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Master Harga Jual</li>
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
            <div class="text-right">
                <a href="{{ URL::to('master_harga_jual/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Master Material</h4>
                    <div class="table-responsive">
                        <table id="material_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Material No</th>
                                    <th class="text-center">Material Name</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Lead Time</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Satuan Turunan</th>
                                    <th class="text-center">Retail</th>
                                    <th class="text-center">Grosir</th>
                                    <th class="text-center">Distributor</th>
                                    <th class="text-center">Action</th>
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
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#material_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('master_harga_jual/list') }}", //json get site
        aaSorting: [[6, 'desc']],
        "columns": [
            {"data": "no"},
            {"data": "name"},
            {"data": "category"},
            {"data": "late_time"},
            {"data": "m_units.name"},
            {"data": "m_unit_child",
                "render" : function(data, type, row){ return row.m_unit_child != null ? row.amount_unit_child +' '+ row.m_unit_childs.name +' / '+ row.m_units.name : '-'}
            },
            {"data": "retail"},
            {"data": "grosir"},
            {"data": "distributor"},
            {"data": "id", 
            "render" : function(data, type, row){ return '<div class="text-center"><a href="{{ URL::to('master_harga_jual/') }}/'+row.id+'/edit" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a> <a href="{{ URL::to('master_harga_jual/delete') }}/'+row.id+'" class="btn waves-effect waves-light btn-xs btn-danger" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="fas fa-trash-alt"></i></a></div>'}
            }
        ],
    } );
});

// listSatuan = [];

// $(document).ready(async function(){
//     await $.ajax({
//         type: "GET",
//         url: "{{ URL::to('master_satuan/list') }}", //json get site
//         dataType : 'json',
//         success: function(response){
//             arrData = response['data'];
//             listSatuan = arrData;
//         }
//     });

//     // console.log(arrMaterialPembelianRutin);
//     t = $('#zero_config').DataTable();
//     t.clear().draw(false);
//     $.ajax({
//             type: "GET",
//             url: "{{ URL::to('master_material/list') }}", //json get site
//             dataType : 'json',
//             success: function(response){
//                 arrData = response['data'];
//                 for(i = 0; i < arrData.length; i++){
//                     urlEdit = "{{ URL::to('master_material/edit') }}" + "/" +arrData[i]['id'];
//                     urlDelete = "{{ URL::to('master_material/delete') }}" + "/" +arrData[i]['id'];
//                     satuan = '';
//                     for(j = 0; j < listSatuan.length; j++) {
//                         if(listSatuan[j]['id'] == arrData[i]['m_unit_id'])
//                             satuan = listSatuan[j]['name'];
//                     }
                    
//                     t.row.add([
//                         '<div class="text-center">'+arrData[i]['no']+'</div>',
//                         '<div class="text-left">'+arrData[i]['name']+'</div>',
//                         '<div class="text-right">'+arrData[i]['late_time']+'</div>',
//                         '<div class="text-right">'+satuan+'</div>',
//                         '<div class="text-center"><a href="'+urlEdit+'" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a> <a href="'+urlDelete+'" class="btn waves-effect waves-light btn-xs btn-danger" onclick="return confirm("Are you sure to delete item?")"><i class="fas fa-trash-alt"></i></a></div>'
//                     ]).draw(false);
//                 }
//             }
//     });
// });

</script>


@endsection
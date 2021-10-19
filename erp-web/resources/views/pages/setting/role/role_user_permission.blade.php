@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Role</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('role') }}">Role</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Permission</li>
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
                                <h4 class="card-title">User Permission : Role {{ $role_by_id->role_name }}</h4>
                                <!-- <a href="/user/add"><button class="btn btn-success mb-2"><i class="fas fa-plus"></i>&nbsp; Add new user</button></a> -->
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="15px">No</th>
                                                <th class="text-center">Menu / Module</th>
                                                <th class="text-center">Permission</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($i = 0; $i < count($modules); $i++)
                                            <tr>
                                                <td class="text-center">{{ $i+1 }}</td>
                                                <td>{{ $modules[$i]->title }}</td>
                                                <td class="text-center" width="20px">
                                                    <?php
                                                    $user_permission = DB::table('user_permission')
                                                        ->where('menu_id', $modules[$i]->id) 
                                                        ->where('role_id', $role_by_id->id)
                                                        ->get();
                                                    $checked = '';
                                                    if(count($user_permission) > 0)
                                                        $checked = 'checked';
                                                    ?>
                                                    <input type="checkbox" {{ $checked }}
                                                    onclick="giveAccess({{ $modules[$i]->id }}, {{ $role_by_id->id }});">
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
</div>

<script>
function giveAccess(menu_id, role_id){
    $.ajax({
        url:"{{ URL::to('role/give_access_ajax') }}",
        data:"menu_id=" + menu_id + "&role_id="+ role_id ,
        success: function(html){}
    });
}
</script>

@endsection
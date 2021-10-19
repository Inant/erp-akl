<template>
    <div>
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Project RAB</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Project RAB</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

<div class="container-fluid">
    <!-- basic table -->
                <div class="row">
                    <!-- @if($error['is_error'])
                    <div class="col-12">
                        <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                        </div>
                    </div>
                    @endif -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">List Project RAB</h4>
                                <a href="/rab/add"><button class="btn btn-success mb-2"><i class="fas fa-plus"></i>&nbsp; Create New Project RAB</button></a>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center"  width="15px">No</th>
                                                <th class="text-center">RAB Number</th>
                                                <th class="text-center">Site Name</th>
                                                <th class="text-center">Site Location</th>
                                                <th class="text-center">Project Name</th>
                                                <th class="text-center">Input Data Status</th>
                                                <th class="text-center" width="50px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="rab in rabs" :key="rab.rab_id">
                                                <td></td>
                                                <td>{{ rab.rab_no }}</td>
                                                <td>{{ rab.site_name }}</td>
                                                <td>{{ rab.site_location }}</td>
                                                <td>{{ rab.project_name }}</td>
                                                <td>Incomplete</td>
                                                <td class="text-center">
                                                    <!-- <router-link :to="{name: 'edit', params: { id: rab.rab_id }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></router-link> -->
                                                    <a href="jay" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                                </td>
                                            </tr>
                                        <!-- @if($rab != null)
                                            @for($i = 0; $i < count($rab); $i++)
                                            <tr>
                                                <td class="text-center">{{ $i+1 }}</td>
                                                <td>{{ $rab[$i]['rab_no'] }}</td>
                                                <td>{{ $rab[$i]['site_name'] }}</td>
                                                <td>{{ $rab[$i]['site_location'] }}</td>
                                                <td>{{ $rab[$i]['project_name'] }}</td>
                                                <td>Incomplete</td>
                                                <td class="text-center">
                                                <a href="{{ URL::to('rab/edit/'.$rab[$i]['rab_id']) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endfor
                                        @endif -->
                                        </tbody>
                                        <!-- <tfoot>
                                            <tr>
                                                <th>No</th>
                                                <th>Title</th>
                                                <th>Url</th>
                                                <th>Icon</th>
                                                <th>Is Active</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
</div>
    </div>
</template>

<script>
  export default {
    data() {
        return {
          rabs: [],
          number : 0
        }
    },
    created() {
      let uri = 'http://localhost/erp-api-dummy/rab/rab_list.json';
      this.axios.get(uri).then(response => {
        this.rabs = response.data.data[0].rab;
        console.log(response.data.data[0].rab)
      });
    }
  }
</script>
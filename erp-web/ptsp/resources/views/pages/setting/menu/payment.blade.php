@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Payment</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Payment</li>
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
                            <a href="{{ URL::to('menu/payment/add') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add new payment</button></a>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">List Payment</h4>
                                <div class="table-responsive">
                                    
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center"  width="15px">No</th>
                                                <th class="text-center">Nama Bank</th>
                                                <th class="text-center">Kategori Progress</th>
                                                <th class="text-center">Persen Pencairan</th>
                                                <th class="text-center">Kode Bank</th>
                                                <th class="text-center" width="50px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @for($i = 0; $i < count($data); $i++)
                                            <tr>
                                                <td class="text-center">{{ $data[$i]->id }}</td>
                                                <td>{{ $data[$i]->bank_name }}</td>
                                                <td>{{ $data[$i]->progress_category }}</td>
                                                <td>{{ $data[$i]->payment_percent }}</td>
                                                <td>{{ $data[$i]->bank_code }}</td>
                                                <td class="text-center">
                                                    <a href="{{ URL::to('menu/payment/edit/'.$data[$i]->bank_code) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                                    <a href="{{ URL::to('menu/payment/delete/'.$data[$i]->bank_code) }}" class="btn waves-effect waves-light btn-xs btn-danger" onclick="return confirm('Are you sure to delete theme of payment with {{$data[$i]->bank_name}} ?')"><i class="fas fa-trash-alt"></i></a>
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

@endsection
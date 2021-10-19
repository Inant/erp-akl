@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Ringkasan Umur Hutang</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Tagihan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php
    function formatRupiah($num)
    {
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date)
    {
        $date = date_create($date);
        return date_format($date, 'd-m-Y');
    }
    @endphp
    <!-- <style>
      .floatLeft { width: 100%;}
      .floatRight {width: 100%;}
      /* .floatLeft { width: 100%; float: left; }
      .floatRight {width: 100%; float: right; } */
        #table th, #table td{
            border:1px solid #7c8186;
            padding : 5px
        }
        .no-border{
            border:1px solid white !important;
            /* padding : 5px */
        } -->
    </style>
    <div class="container-fluid">
        <!-- basic table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Ringkasan Umur Hutang</h4>

                        <br><br>
                        <form method="POST" action="{{ URL::to('inventory/export_age_debt') }}" class="float-right"
                            target="_blank">
                            @csrf
                            <div class="form-group">
                                <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table style="width:100%" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nama Pemasok</th>
                                        <th class="text-center">Jumlah Tagihan</th>
                                        {{-- <th class="text-center">Belum</th> --}}
                                        <th class="text-center">1-30</th>
                                        <th class="text-center">30-60</th>
                                        <th class="text-center">60-90</th>
                                        <th class="text-center">90-120</th>
                                        <th class="text-center">> 120</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $value)
                                        @if ($value->detail->total_in_one_months > 0 || $value->detail->total_in_two_months > 0 || $value->detail->total_in_three_months || $value->detail->total_in_four_months || $value->detail->total_in_five_months > 0)
                                            @php
                                                $totalTagihan = $value->detail->total_in_one_months + $value->detail->total_in_two_months + $value->detail->total_in_three_months + $value->detail->total_in_four_months + $value->detail->total_in_five_months;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $value->name }}</td>
                                                <td class="text-right">{{ formatRupiah($totalTagihan) }}</td>
                                                {{-- <td></td> --}}
                                                <td class="text-right"><a href="#" onclick="doShowDetail(this);"
                                                        data-toggle="modal" data-target="#modalAgeDetail" data-month="1"
                                                        data-start_date="{{ date('Y-m-d') }}"
                                                        data-end_date="{{ $one_month_before }}"
                                                        data-id="{{ $value->id }}"
                                                        class="text-info">{{ formatRupiah($value->detail->total_in_one_months) }}</a>
                                                </td>
                                                <td class="text-right"><a href="#" onclick="doShowDetail(this);"
                                                        data-toggle="modal" data-target="#modalAgeDetail" data-month="2"
                                                        data-start_date="{{ $one_month_before }}"
                                                        data-end_date="{{ $two_month_before }}"
                                                        data-id="{{ $value->id }}"
                                                        class="text-info">{{ formatRupiah($value->detail->total_in_two_months) }}</a>
                                                </td>
                                                <td class="text-right"><a href="#" onclick="doShowDetail(this);"
                                                        data-toggle="modal" data-target="#modalAgeDetail" data-month="3"
                                                        data-start_date="{{ $two_month_before }}"
                                                        data-end_date="{{ $three_month_before }}"
                                                        data-id="{{ $value->id }}"
                                                        class="text-info">{{ formatRupiah($value->detail->total_in_three_months) }}</a>
                                                </td>
                                                <td class="text-right"><a href="#" onclick="doShowDetail(this);"
                                                        data-toggle="modal" data-target="#modalAgeDetail" data-month="4"
                                                        data-start_date="{{ $three_month_before }}"
                                                        data-end_date="{{ $four_month_before }}"
                                                        data-id="{{ $value->id }}"
                                                        class="text-info">{{ formatRupiah($value->detail->total_in_four_months) }}</a>
                                                </td>
                                                <td class="text-right"><a href="#" onclick="doShowDetail(this);"
                                                        data-toggle="modal" data-target="#modalAgeDetail" data-month="5"
                                                        data-start_date="{{ $four_month_before }}" data-end_date="-"
                                                        data-id="{{ $value->id }}"
                                                        class="text-info">{{ formatRupiah($value->detail->total_in_five_months) }}</a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-lg" id="modalAgeDetail" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="title-modal">Rincian Tagihan Hutang </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <table id="listDetail" class="table table-bordered table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <td>No</td>
                                <th>Total</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger waves-effect btn-sm text-left"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
    <script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
    <script>
        var account_payment = []
        $(document).ready(function() {});

        function doShowDetail(eq) {
            var id = $(eq).data('id');
            var month = $(eq).data('month');
            var start_date = $(eq).data('start_date');
            var end_date = $(eq).data('end_date');
            t2 = $('#listDetail').DataTable();
            t2.clear().draw(false);
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: "post",
                url: "{{ URL::to('inventory/age_debt_json') }}",
                dataType: 'json',
                data: {
                    _token: CSRF_TOKEN,
                    id: id,
                    month: month,
                    start_date: start_date,
                    end_date: end_date
                },
                success: function(response) {
                    arrData = response['data'];
                    for (i = 0; i < arrData.length; i++) {
                        t2.row.add([
                            '<div class="text-left">' + arrData[i]['no'] + '</div>',
                            '<div class="text-right">' + formatCurrency(parseInt(arrData[i][
                            'amount'])) + '</div>',
                            '<div class="text-center">' + formatDateID(new Date(arrData[i][
                            'due_date'])) + '</div>',
                        ]).draw(false);
                    }
                }
            });
        }
    </script>


@endsection

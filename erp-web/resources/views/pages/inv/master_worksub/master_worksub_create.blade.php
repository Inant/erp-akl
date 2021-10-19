@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Pekerjaan</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('master_material') }}">Master Pekerjaan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
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
                    <h4 class="card-title">Create Material</h4>
                    <form method="POST" action="{{ URL::to('master_worksub/create') }}" class="form-horizontal">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Satuan</label>
                            <div class="col-sm-9">
                                <select id="satuan" name="m_unit_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Satuan ---</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Biaya Pekerjaan</label>
                            <div class="col-sm-9">
                                <input type="" class="form-control" name="amount" id="amount" onkeyup="cekFormat(this.value)"/>
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary mt-4" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>


<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script>

$(document).ready(function(){
    formSatuan = $('[id^=satuan]');
    formSatuan.empty();
    formSatuan.append('<option value="">-- Select Satuan --</option>');
    formSatuan2 = $('[id^=m_unit_id2]');
    formSatuan2.empty();
    formSatuan2.append('<option value="">-- Select Satuan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_satuan/list') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formSatuan.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
                formSatuan2.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
    formItemSet = $('[id^=item_set]');
    formItemSet.empty();
    formItemSet.append('<option value="">-- Pilih Spare Part --</option>');
    $.ajax({
        url : '{{URL::to("master_material/get_spare_part")}}',
        type : 'GET',
        dataType : 'json',
        async : false,
        success : function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                formItemSet.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    })
});
function cekFormat(val){
    $('#amount').val(formatNumber(val));
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
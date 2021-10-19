@php
    function formatRupiah($num){
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd/m/Y');
    }
@endphp
<table>
    <thead>
        <tr>
            <th colspan="10">Laporan Purchase Order per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</th>
        </tr>
        <tr>
            <th colspan="10"></th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th>PO Number</th>
            <th>Nomor SPK</th>
            <th>Supplier Name</th>
            <th>PO Value</th>
            <th>PO Date</th>
            <th>Tanggal Permintaan Pengiriman</th>
            <th>Way Of Payment</th>
            <th>PO Status</th>
            <th>Status</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data['data'] as $value)
        <tr>
            <td>{{$value->no}}</td>
            <td>{{$value->spk_number}}</td>
            <td>{{$value->m_suppliers->name}}</td>
            <td>{{$value->base_price}}</td>
            <td>{{formatDate($value->purchase_date)}}</td>
            <td>{{formatDate($value->delivery_date)}}</td>
            <td>{{$value->wop}}</td>
            <td>{{$value->is_closed == false ? 'Closed' : 'Open'}}</td>
            <td>{{$value->acc_ao == false ? 'Belum Acc' : 'Sudah Acc'}}</td>
            <td>{{$value->notes}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
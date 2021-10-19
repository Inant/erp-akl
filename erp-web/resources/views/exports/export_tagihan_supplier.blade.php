@php
    function formatRupiah($num){
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd/m/Y');
    }
@endphp
<center>
  <h3>Daftar Tagihan Supplier per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</h3>
</center>
<table border="1">
    <thead>
      <tr>
        <th>No</th>
        <th>No Invoice</th>
        <th>No Tagihan</th>
        <th>Nama Supplier</th>
        <th>Nomor Surat Jalan</th>
        <th>Nomor Surat Jalan Jasa</th>
        <th style="min-width:100px">Tanggal Tagihan</th>
        <th style="min-width:100px">Tanggal Jatuh Tempo</th>
        <th  style="min-width:100px">Tanggal Bayar</th>
        <th>Jumlah Tagihan</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
      @foreach ($data['data'] as $value)
        <tr>
          <td>{{$value->no}}</td>
          <td style="mso-number-format:\@;">{{$value->paid_no}}</td>
          <td style="mso-number-format:\@;">{{$value->bill_no}}</td>
          <td>{{$value->supplier}}</td>
          <td style="mso-number-format:\@;">{{$value->no_surat_jalan}}</td>
          <td style="mso-number-format:\@;">{{$value->no_surat_jalan_jasa}}</td>
          <td>{{$value->create_date != null ? formatDate($value->create_date) : '-'}}</td>
          <td>{{$value->due_date != null ? formatDate($value->due_date) : '-'}}</td>
          <td>{{$value->pay_date != null ? formatDate($value->pay_date) : '-'}}</td>
          <td>{{formatRupiah($value->amount)}}</td>
          <td>{{$value->is_paid == true ? 'Lunas' : 'Belum Lunas'}}</td>
        </tr>
      @endforeach
    </tbody>
</table>
@php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Daftar Tagihan Supplier.xls");
@endphp
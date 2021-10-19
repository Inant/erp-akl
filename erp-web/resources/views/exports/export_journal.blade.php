
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
            <th colspan="6"><h2>Jurnal Umum Tanggal {{formatDate($date)}} Sampai {{formatDate($date2)}}</h2></th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th colspan="6"></th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>No Sumber</th>
            <th>No Akun</th>
            <th>Nama Akun</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Keterangan</th>
            <th>Customer</th>
            <th>Supplier</th>
        </tr>
    </thead>
    <tbody>
        
    @foreach ($data as $key => $value)
        @php $k=0 @endphp
        @foreach ($data[$key]['detail'] as $k => $v)
            
        <tr>
            <td>{{formatDate($v->tanggal)}}</td>
            <td>
            {{$v->no != null ? $v->no : ($v->inv_trxes != null ? $v->inv_trxes->no : ($v->inv_trx_services != null ? $v->inv_trx_services->no : ($v->purchases != null ? $v->purchases->no : ($v->purchase_assets != null ? $v->purchase_assets->no : ($v->orders != null ? $v->orders->order_no : ($v->ts_warehouses != null ? $v->ts_warehouses->no : ($v->debts != null ? $v->debts->no : ($v->install_orders != null ? $v->install_orders->no : ($v->giros != null ? $v->giros->no : ($v->paid_customers != null ? $v->paid_customers->no : ($v->paid_suppliers != null ? $v->paid_suppliers->no : ($v->bill_vendors != null ? $v->bill_vendors->no : '-'))))))))))))}}
            </td>
            <td>{{$v->no_akun}}</td>
                @if($v->keterangan == 'akun')                                
            <td>{{$v->nama_akun}}</td>
                @else
            <td>{{$v->nama_akun}}</td>
                @endif
            
                @if($v->tipe == 'KREDIT')
            
            <td></td>
            <td>{{$v->jumlah}}</td>
            <th>
            {{$value['deskripsi'].' '.$v->code_item}}
            </th>
            <th>{{$v->customer != null ? $v->customer : '-'}}</th>
            <th>{{$v->supplier != null ? $v->supplier : '-'}}</th>
                        
        </tr>
                @else

            <td>{{$v->jumlah}}</td>
            <td></td>
            <th>
            {{$value['deskripsi'].' '.$v->code_item}}
            </th>
            <th>{{$v->customer != null ? $v->customer : '-'}}</th>
            <th>{{$v->supplier != null ? $v->supplier : '-'}}</th>
           
        </tr>
                @endif
            
        
        @endforeach
    @endforeach
    </tbody>
</table>
                   
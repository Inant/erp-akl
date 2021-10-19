
<table>
    <!-- <thead>
        <tr>
            <th>Kode</th>
            <th>Harga</th>
        </tr>
    </thead> -->
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td colspan="4">{{$value->no}}</td>
        </tr>
        <tr>
            <td>No</td>
            <td>Nama</td>
            <td>Kategori</td>
            <td>Total</td>
        </tr>
            @foreach($value->material as $v)
            <tr>
                <td>{{$v->no}}</td>
                <td>{{$v->name}}</td>
                <td>{{$v->category}}</td>
                <td>{{$v->amount}}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
                   
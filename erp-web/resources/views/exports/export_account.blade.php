<table>
    <thead>
        <tr>
            <th>No Akun</th>
            <th>Nama Akun</th>
            <th>Level</th>
            <th>Main</th>
            <!-- <th>Action</th>  -->
        </tr>
    </thead>
    <tbody>
    @foreach($data as $value)
        <tr>
            <td>{{$value->no_akun}}</td>
            <td>{{$value->nama_akun}}</td>
            <td>{{$value->level}}</td>
            <td>{{$value->main_akun}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
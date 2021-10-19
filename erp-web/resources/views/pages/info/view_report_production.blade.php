<?php
function formatNumber($val){
    $val=number_format($val, 0, '.', '.');
    return $val;
}
?>
<style>
table, th, td{
    border : 1px solid #536677;
    padding : 5px
}
</style>
<div class="table-responsive">
    <h4>Perkiraan</h4>
    <table style="width:100%">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kavling</th>
                <th rowspan="2">Item</th>
                <th rowspan="2">Set</th>
                <th colspan="3">Kebutuhan</th>
            </tr>
            <tr>
                <th>Material</th>
                <th>Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $a=1; ?>
            @foreach($get_pw as $key => $value)
                <?php $b=0; ?>
                @foreach($value->product as $v)
                    @foreach($v->request as $m)
                    <tr>
                        <td>{{$b == 0 ? $a : ''}}</td>
                        <td>{{$b == 0 ? $v->type_kavling : ''}}</td>
                        <td>{{$b == 0 ? $v->item.' '.$v->name.' '.$v->series : ''}}</td>
                        <td>{{$b == 0 ? $v->amount_set : ''}}</td>
                        <td>{{$m->m_items->name}}</td>
                        <td>{{$m->m_units->name}}</td>
                        <td>{{round($m->amount, 0)}}</td>
                    </tr>
                    
                    <?php $b++ ?>
                    @endforeach
                <?php $a++ ?>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <h4>Produksi</h4>
    <table style="width:100%">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Permintaan</th>
                <th colspan="3">Kebutuhan</th>
            </tr>
            <tr>
                <th>Material</th>
                <th>Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $a=1; ?>
            @foreach($get_pw as $key => $value)
                <?php $b=0; ?>
                @foreach($value->detail_inv as $v)
                    <tr>
                        <td>{{$b == 0 ? $a : ''}}</td>
                        <td>{{$b == 0 ? $value->no : ''}}</td>
                        <td>{{$v->m_items->name}}</td>
                        <td>{{$v->m_units->name}}</td>
                        <td>{{round($v->amount, 0)}}</td>
                    </tr>
                    
                    <?php $b++ ?>
                @endforeach
                <?php $a++ ?>
            @endforeach
        </tbody>
    </table>
</div>
<br>

<div class="table-responsive">
    <h4>Summary</h4>
    <table style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Material</th>
                <th>Unit</th>
                <th>Kebutuhan Rab</th>
                <th>Kebutuhan Produksi</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?php $a=1; ?>
            @foreach($req as $key => $value)
            <?php
            $item_amount=!empty($value['item_amount']) ? round($value['item_amount'], 0) : 0;
            $prod_amount=!empty($value['prod_amount']) ? round($value['prod_amount'], 0) : 0;
            ?>
                <tr>
                    <td>{{$a}}</td>
                    <td>{{$value['m_items']->name}}</td>
                    <td>{{$value['m_units']->name}}</td>
                    <td>{{$item_amount}}</td>
                    <td>{{$prod_amount}}</td>
                    <td>{{$item_amount - $prod_amount}}</td>
                </tr>
                <?php $a++ ?>
            @endforeach
        </tbody>
    </table>
</div>
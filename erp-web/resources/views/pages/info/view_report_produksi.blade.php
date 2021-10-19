<?php
function formatNumber($val){
    $val=number_format($val, 0, '.', '.');
    return $val;
}
?>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Pekerjaan</th>
                <th colspan="3">Perkiraan</th>
                <th colspan="3">Produksi</th>
            </tr>
            <tr>
                <th>Material</th>
                <th>Unit</th>
                <th>Total</th>
                <th>Material</th>
                <th>Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <?php $a=0 ?>
        <tbody>
            @foreach($get_pw as $key => $value)
            <?php
            $count_dt=count($value->detail);
            $count_dt_inv=count($value->detail_inv);
            $a++;
            $z=($count_dt > $count_dt_inv ? $count_dt : $count_dt_inv);
            ?>
            @for($i=0; $i < $z; $i++)
            <tr>
                <td>{{ ($i == 0 ? $a : '') }}</td>
                <td>{{ ($i == 0 ? $value->name : '') }}</td>
                <td>{{ (!empty($value->detail[$i]) ? $value->detail[$i]->m_items->name : '') }}</td>
                <td>{{ (!empty($value->detail[$i]) ? $value->detail[$i]->m_units->name : '') }}</td>
                <td>{{ (!empty($value->detail[$i]) ? (int)$value->detail[$i]->amount : '') }}</td>
                <td>{{ (!empty($value->detail_inv[$i]) ? $value->detail_inv[$i]->m_items->name : '') }}</td>
                <td>{{ (!empty($value->detail_inv[$i]) ? $value->detail_inv[$i]->m_units->name : '') }}</td>
                <td>{{ (!empty($value->detail_inv[$i]) ? (int)$value->detail_inv[$i]->amount : '') }}</td>
            </tr>
            @endfor
            @endforeach
            <tr>
                <td colspan="2">Total Item</td>
                <td colspan="3" class="text-right">{{formatNumber($total_item_rab)}}</td>
                <td colspan="3" class="text-right">{{formatNumber($total_item_produksi)}}</td>
            </tr>
            <tr>
                <td colspan="2">Total Jasa dll</td>
                <td colspan="3" class="text-right">{{formatNumber($total_jasa_rab)}}</td>
                <td colspan="3" class="text-right">{{formatNumber($total_jasa_produksi)}}</td>
            </tr>
            <tr>
                <td colspan="2">Total Biaya</td>
                <td colspan="3" class="text-right">{{formatNumber($total_rab)}}</td>
                <td colspan="3" class="text-right">{{formatNumber($total_jasa_produksi + $total_item_produksi)}}</td>
            </tr>
        </tbody>
    </table>
</div>
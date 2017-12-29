<?php
require_once 'connection.php';
ini_set('memory_limit', '-1');

$res = $db
    ->where('fcfecfac', "{$_REQUEST['de']}", '>=')
    ->where('fcfecfac', "{$_REQUEST['hasta']}", '<=')
    ->get($sqlTable);
if ($_REQUEST['tipo'] == 'json'){
    $rows = [];
    foreach ($res as $row) {
        $rowSubTotal = $row['fcsubtot'] + $row['fcexento'];
        $rowFcIVA = $row['fciva01'] + $row['fciva02'] + $row['fciva03'] + $row['fciva04'] + $row['fciva05'];
        $rowTotal = $row['fctotfac'];
        $fixedFecha = substr($row['fcfecfac'], 0, 10) . ' ' . $row['fchrasis'];

        $rows[] = [
            $fixedFecha,
            $row['fccodclt'],
            $row['fcnumfac'],
            $row['fcimpfac'],
            $rowSubTotal,
            $rowFcIVA,
            $rowTotal
        ];
    }
    echo json_encode($rows);

} else {

?>
<table class="table table-condensed table-hover">
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Cod Cliente</th>
        <th>Num Control</th>
        <th>Num Factura</th>
        <th>Subtotal</th>
        <th>IVA</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <?php

    $subTotalTotal = 0;
    $fcIVATotal = 0;
    $totalTotal = 0;
    $toecho = '';

    foreach ($res as $row) {
        $rowSubTotal = number_format($row['fcsubtot'] + $row['fcexento'], 2);
        $rowFcIVA = (string)number_format($row['fciva01'] + $row['fciva02'] + $row['fciva03'] + $row['fciva04'] + $row['fciva05'], 2);
        $rowTotal = (string)number_format($row['fctotfac'], 2);
        $fixedFecha = substr($row['fcfecfac'], 0, 10) . ' ' . $row['fchrasis'];

//        var_dump($rowTotal);

        echo <<<HTMl
<tr><td>{$fixedFecha}</td><td>{$row['fccodclt']}</td><td>{$row['fcnumfac']}</td><td>{$row['fcimpfac']}</td><td>\${$rowSubTotal}</td><td>\${$rowFcIVA}</td><td>\${$rowTotal}</td></tr>

HTMl;

        $subTotalTotal += $rowSubTotal;
        $fcIVATotal += $rowFcIVA;
        $totalTotal += $rowTotal;

    }

    setlocale(LC_MONETARY, 'es_MX');
    $subTotalTotal = money_format('%i', $subTotalTotal);

    echo <<<HTML
</tbody>
<tfooter>
<tr>
<td colspan="4"></td>
<td >{$subTotalTotal}</td>
<td >{$fcIVATotal}</td>
<td >{$totalTotal}</td>
</tr>    
</tfooter>
</table>

HTML;
    }
    ?>

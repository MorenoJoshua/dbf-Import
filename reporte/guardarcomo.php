<?php
require_once 'connection.php';
ini_set('memory_limit', '-1');

$res = $db
    ->where('fcfecfac', "{$_REQUEST['de']}", '>=')
    ->where('fcfecfac', "{$_REQUEST['hasta']}", '<=')
    ->get($sqlTable);

$subTotalTotal = 0;
$fcIVATotal = 0;
$totalTotal = 0;
$toecho = '';
$reporte = [];

$reporte[] = [ //encabezados
    'Fecha',
    'Cod Cliente',
    'Num Control',
    'Num Factura',
    'Subtotal',
    'IVA',
    'Total'
];
foreach ($res as $row) {
    // fcsubtot, fcexento, fciva01, fciva02, fciva03, fciva04, fciva05, fctotfac, fcfecfac, fchrasis, fccodcltdclt,fcnumfac,fcimpfac
    $rowSubTotal = $row['fcsubtot'] + $row['fcexento'];
    $rowFcIVA = $row['fciva01'] + $row['fciva02'] + $row['fciva03'] + $row['fciva04'] + $row['fciva05'];
    $rowTotal = $row['fctotfac'];
    $fixedFecha = substr($row['fcfecfac'], 0, 10) . ' ' . $row['fchrasis'];

    $reporte[] = [ //cada record
        $fixedFecha,
        $row['fccodclt'],
        $row['fcnumfac'],
        $row['fcimpfac'],
        $rowSubTotal,
        $rowFcIVA,
        $rowTotal,
    ];

    $subTotalTotal += $rowSubTotal;
    $fcIVATotal += $rowFcIVA;
    $totalTotal += $rowTotal;

}

$reporte[] = [ // totales
    '',
    '',
    '',
    '',
    $subTotalTotal,
    $fcIVATotal,
    $totalTotal
];


require_once '../php-xbase-master/src/XBase/Table.php';
$filename = time() . '.' . $_REQUEST['formato'];


$all = $reporte;

switch (strtolower($_REQUEST['formato'])) {
    case 'csv':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        foreach ($all as $row) {
            echo '"' . join('", "', $row) . '"' . "\r\n";
        }
        break;

    case 'xls':
        require_once '../excell/PHPExcel.php';
        $x = new PHPExcel();
        $sh1 = $x->createSheet();
        $sh1->fromArray($all);
        $writer = PHPExcel_IOFactory::createWriter($x, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        break;

    case 'xslx':
        require_once '../excell/PHPExcel.php';
        $x = new PHPExcel();
        $sh1 = $x->createSheet();
        $sh1->fromArray($all);
        $writer = PHPExcel_IOFactory::createWriter($x, 'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        break;
}

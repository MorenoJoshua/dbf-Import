<?php
require_once 'connection.php';

require_once '../php-xbase-master/src/XBase/Table.php';

$all = [];

$headers = $db->getOne('reporte.demo');
$headerRow = [];
foreach ($headers as $k => $v) {
    $headerRow[] = $k;
}

$all[] = $headerRow;
$res = $db->get('reporte.demo');
foreach ($res as $resRow) {
    $all[] = $resRow;
}

$filename = time() . '.' . $_REQUEST['formato'];

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

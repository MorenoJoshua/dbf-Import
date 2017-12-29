<?php

date_default_timezone_set('America/Tijuana');
require 'xbase.php';
$table = new \XBase\Table('dbase_f5.dbf');
$arrayfile = [];
$cols = [];
foreach ($table->getColumns() as $key => $val) {
    $cols[] = $key;
}
$arrayfile[] = $cols;

$joined = join('", "', $cols) ;
$toecho = <<<CSV
"$joined"

CSV;

while ($row = $table->nextRecord()) {
    $thisrow = [];
    foreach ($cols as $col) {
        $thisrow[] = $row->$col;
    }
    $arrayfile[] = $thisrow;
    $joinedrow = join('", "', $thisrow) ;
    $toecho .= <<<CSV
"$joinedrow"

CSV;
}

//echo $toecho;

require_once 'excell/PHPExcel.php';
$x = new PHPExcel();
$sh1 = $x->createSheet();
$sh1->fromArray($arrayfile);
$writer = PHPExcel_IOFactory::createWriter($x, 'Excel5');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="01simple.xls"');
header('Cache-Control: max-age=0');
$writer->save('php://output');


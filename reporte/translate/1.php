<?php
require_once 'connection.php';
$jsonDir = 'jsons';
if(isset($_FILES['dbf']['tmp_name'])){
    $dbfname = $_FILES['dbf']['tmp_name'];
} else {
    $dbfname = __DIR__ . '/../../_.DAT';
}


function echo_dbf($dbfname, $dir)
{
    $linebreak = "\r\n";
    $recordDelim = '';

    $fdbf = fopen($dbfname, 'r');
    $fields = array();
    $buf = fread($fdbf, 32);
    $header = unpack("VRecordCount/vFirstRecord/vRecordLength", substr($buf, 4, 8));

    $goon = true;
    $unpackString = '';
    while ($goon && !feof($fdbf)) { // read fields:
        $buf = fread($fdbf, 32);
        if (substr($buf, 0, 1) == chr(13)) {
            $goon = false;
        } // end of field list
    else {
            $field = unpack("a11fieldname/A1fieldtype/Voffset/Cfieldlen/Cfielddec", substr($buf, 0, 18));

            $unpackString .= "A$field[fieldlen]$field[fieldname]/";
            array_push($fields, $field);
        }
    }
    fseek($fdbf, $header['FirstRecord'] + 1); // move back to the start of the first record (after the field definitions)
    $toreturn = '';

//    init forlder
    $jsonsRootRoute = 'rm -rf ' . __DIR__ . '/../' . $dir . '/*.json';
    exec($jsonsRootRoute);
//    die($jsonsRootRoute . ' --asd');
//    echo '--------' . $jsonsRootRoute . '----------';

    $file = fopen('./' . $dir . '/out0.json', 'w');
    ini_set('memory_limit', '-1');
    //    start json
    $split = 1000;
    for ($i = 1; $i <= $header['RecordCount']; $i++) {
//        por cada $split records, escribir a archivo
        if ($i % $split == 0) {
            fwrite($file, $toreturn);
            fclose($file);
            $file = fopen('./' . $dir . '/out' . $i . '.json', 'w');
//            echo $i . "\r\n";
            unset($toreturn);
            $toreturn = '';
        }
        $buf = fread($fdbf, $header['RecordLength']);
        $record = unpack($unpackString, $buf);
        $toecho = '';

        $toecho .= $recordDelim . json_encode($record);

        $toecho .= ',' . $linebreak;
        $toreturn .= $toecho;

    } //raw record
    fwrite($file, $toreturn);
    fclose($file);
    fclose($fdbf);
}
echo_dbf($dbfname, $jsonDir);

require_once './translate/2.php';
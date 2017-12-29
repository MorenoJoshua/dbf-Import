<?php
$jsonDir = 'jsons';
require_once 'connection.php';
$files = scandir($jsonDir);
unset($files[0], $files[1]);

$dullcsv = fopen('full.txt', 'w');
$headersDone = false;
foreach ($files as $eachJson) {
    $rawJson = '[' . substr(file_get_contents('./jsons/' . $eachJson), 0, -3) . ']';
    $jsonBatch = json_decode($rawJson, true);
    $keys = [];

    if ($headersDone == false) {
        foreach ($jsonBatch[0] as $key => $v) {
            $keys[] = strtolower($key);
        }
        $keys = '"' . join('", "', $keys) . '"';
        fwrite($dullcsv, $keys . "\r\n");
        $headersDone = true;
//        echo 'headers
//';
    }
    foreach ($jsonBatch as $key => $record) {
//        echo '.';
        if (strlen($record['FCFECFAC']) < 7) {
            unset($jsonBatch[$key]);
        } else {
            $record['FCFECFAC'] = substr($record['FCFECFAC'], 0, 4) . '-' . substr($record['FCFECFAC'], 4, 2) . '-' . substr($record['FCFECFAC'], 6, 2) . ' 00:00:00';
            $record['FCFECCAN'] = '0000-00-00 00:00:00';
            $lineValues = join('", "', $record);
            $towrite = <<<TXT
"$lineValues"

TXT;
            fwrite($dullcsv, $towrite);
        }
    }

}
//echo 'ended';
$thisroute = __DIR__ . '/query.sql';

require_once './translate/3.php';
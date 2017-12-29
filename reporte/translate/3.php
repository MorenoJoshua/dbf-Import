<?php
require_once 'connection.php';

$pdoDb->query('truncate reporte.demo');
$thisroute = __DIR__ . '/../full.txt';

try {
    $query = <<<SQL
load data infile '$thisroute' 
into table reporte.demo 
fields terminated by ', ' 
enclosed by '"' 
ignore 1 rows;
SQL;
    $pdoDb->query($query
);
} catch (PDOException $e) {
    echo $e;
}

header('location: ./');
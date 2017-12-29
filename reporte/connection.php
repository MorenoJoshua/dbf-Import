<?php
require_once 'MysqliDb.php';
$db = new MysqliDb('localhost', 'root', '-CHANGEME-');
$sqlTable = 'reporte.demo';

$pdoDb = new PDO('mysql:host=localhost', 'root', '-CHANGEME-');
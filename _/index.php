<?php

require_once '../DBF.php';
ini_set('memory_limit', '-1');
$dbf = new DBF('../_.DAT');
echo $dbf->createQuery('probando');
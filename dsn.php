<?php

echo __DIR__;
try {
    $db = new PDO('uri:file:///var/www/sites/dbf/dbase_f5.dbf', '', '');
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
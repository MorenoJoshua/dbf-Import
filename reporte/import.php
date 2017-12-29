<?php
date_default_timezone_set('America/Tijuana');

$jsonfolder = __DIR__ . "/jsons";
rmdir($jsonfolder);
mkdir($jsonfolder);

require_once './translate/1.php';
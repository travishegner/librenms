<?php

// MYSQL Check - FIXME
// 1 UNKNOWN
require 'config.php';

if (!isset($sql_file)) {
    $sql_file = 'build.sql';
}

$sql_fh = fopen($sql_file, 'r');
if ($sql_fh === false) {
    echo 'ERROR: Cannot open SQL build script '.$sql_file."\n";
    exit(1);
}

$connection = mysql_connect($config['db_host'], $config['db_user'], $config['db_pass']);
if ($connection === false) {
    echo 'ERROR: Cannot connect to database: '.mysql_error()."\n";
    exit(1);
}

$select = mysql_select_db($config['db_name']);
if ($select === false) {
    echo 'ERROR: Cannot select database: '.mysql_error()."\n";
    exit(1);
}

while (!feof($sql_fh)) {
    $line = fgetss($sql_fh);
    if (!empty($line)) {
        $creation = mysql_query($line);
        if (!$creation) {
            echo 'WARNING: Cannot execute query ('.$line.'): '.mysql_error()."\n";
        }
    }
}

fclose($sql_fh);

require 'includes/sql-schema/update.php';

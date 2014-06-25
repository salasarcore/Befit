<?php

$link=mysql_connect ("localhost", "root", "") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("befit_ladies_gym");
define("js", "http://alpha.se.dev.salasarserver.com");
@session_start();
$rowsPerPage=20;
$currency="$";
?>

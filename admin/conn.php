<?php

$link=mysql_connect ("localhost", "root", "sou_mendas") or die ('I cannot connect to the database because: ' . mysql_error());

mysql_select_db ("befit");
@session_start();
$rowsPerPage=20;
$currency="$";
?>

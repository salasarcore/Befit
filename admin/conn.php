<?php

ini_set('date.timezone', 'Asia/Calcutta');
$hostname=str_replace("www.","",$hostname);
$urlParts = explode('.', $hostname);
$subdomainname= $urlParts[0];
$link=mysql_connect ("localhost", "root", "sou_mendas") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("befit");
//define("js", "http://alpha.se.dev.salasarserver.com");
define("SITE_URL_BRANCH_ADMIN", 'http://localhost/befit1/admin');
@session_start();
$rowsPerPage=20;
$currency="Rs";
?>

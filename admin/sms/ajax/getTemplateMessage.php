<?php 

/**
 * This ajax call will display the message text of the template as per the template id passed.
 */

@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");

$template_id = makeSafe($_REQUEST['templateid']);

$sql = "SELECT * FROM global_sms_templates WHERE template_id='$template_id'";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvenience caused");
$row = mysql_fetch_assoc($res);
$count = substr_count($row['template_format'],'#');
preg_match_all('/((#\w+\b#))/i', $row['template_format'], $matches);
$data = array('value' => $row['template_format'], 'count' => ($count/2)-2, 'matches' => $matches[0]);
echo $data = json_encode($data);
?>
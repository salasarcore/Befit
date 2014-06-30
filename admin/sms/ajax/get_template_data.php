<?php

/**
 * This ajax call will be used to list the details of the selected template ID in the sms settings panel. It will fetch an array which will get all the details of the selected template ID from the
 * global sms templates table.
 */

include("../../../globalConfig.php");
include_once("../../../functions/common.php");
$value=makeSafe($_GET['moduleid']);


$sql1 = "SELECT * FROM global_sms_templates WHERE template_id=".$value;
$row = mysql_fetch_assoc(mysql_query($sql1,$scslink));
echo json_encode($row);

?>
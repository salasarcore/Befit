<?php 
@session_start();
require_once("../../../globalConfig.php");
include('../../check_session.php');
include_once("../../../functions/common.php");

/**
 * We are referencing two tables from different databases(school db and SCS Admin). The database name constant is being appended before the respective table name to identify
 * the database to which that table belongs.
 * This file globalconn.php refers to the SCS Admin database connection settings whereas the globalConfig.php refers to the school database.
 * The getModuleList ajax call will be used to populate the dropdown used in SMS settings popup with the list of available modules from global sms templates table.
 */

makeSafe(extract($_REQUEST));
$query = "select module_name from notification_module_master e, global_sms_templates g  WHERE e.module_id=g.module_id and available_for_school='Y' ";
$query3 = mysql_query($query,$scslink);
$modules = array();
while($row = mysql_fetch_assoc($query3))
{
	$modules[] = $row['module_name'];
}
$modulequery = "SELECT mm.* FROM notification_module_master mm INNER JOIN global_sms_templates t ON mm.module_id=t.module_id WHERE t.template_type='T' and t.approved='Y' and t.available_for_school='Y' and module_name not in('".implode("','",$modules)."')";

$query2 = mysql_query($modulequery,$scslink) or die("Query failed.");
$containarray = array();
?>
<option name="select" value="select">SELECT</option>
<?php 
while($row3 = mysql_fetch_assoc($query2))
{
	if(in_array(@$row3['module_name'],$containarray))
		continue;
	?>
	<option value="<?php echo @$row3['module_name'];?>"><?php echo @$row3['module_name'];?></option>";
<?php
array_push($containarray,@$row3['module_name']);
}
?>
<?php 

/**
 * This file is an ajax which will populate a dropdown of available templates to the school. This list will contain only those modules which have been included in the sms settings of that school.
 */

@session_start();
require_once("../../../globalConfig.php");
include('../../check_session.php');
include_once("../../../functions/common.php");

$sql = "SELECT e.*, g.* FROM notification_module_master e, global_sms_templates g  WHERE e.module_id=g.module_id and available_for_school='Y' and module_name != 'Contact Us' and module_name != 'Admin Online Application' and module_name != 'Help Desk'";
$res = mysql_query($sql,$scslink) or die('Query failed.');?>

<option value="select" selected>---TEMPLATES---</option>
<?php 
while($row = mysql_fetch_assoc($res))
{ ?>
<option value="<?php echo $row['template_id'];?>"> <?php echo $row['module_name']?></option>
<?php }?>
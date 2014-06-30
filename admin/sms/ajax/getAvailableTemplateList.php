<?php

/**
 * This ajax call will be used to populate the dropdown of avalable template IDs based on the module selected. This call will be made on selection of module in the add sms settings popup.
 */

include("../../../globalConfig.php");
include_once("../../../functions/common.php");
$value=makeSafe($_GET['moduleid']);

$sql = mysql_fetch_assoc(mysql_query("SELECT module_id FROM sms_module_master WHERE module_name='$value'",$scslink));
$sql1 = "SELECT * FROM global_sms_templates WHERE module_id=".$sql['module_id'];
$res = mysql_query($sql1,$scslink);
?>
<option value="select">SELECT</option>
<?php 
while($row = mysql_fetch_assoc($res))
{ ?>
<option value="<?php echo $row['template_id'];?>"> <?php echo $row['template_id'];?></option>
<?php }?>

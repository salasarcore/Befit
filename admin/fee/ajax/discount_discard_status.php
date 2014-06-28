
<?php
@session_start();

include("../../conn.php");
include('../../functions/comm_functions.php');
include_once("../../functions/common.php");

makeSafe(extract($_REQUEST));
$sql="update fee_discount set discarded ='".$act."' where 	fee_discount_id=".$id;

$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");

?>
<a href ="javascript:Publish('<?php if($act=='Y') echo 'N'; else echo 'Y';?>',<?php echo $id;?>,'varif')"><img src="<?php if($act=="Y") echo "../images/publish.png"; else  echo "../images/publish_x.png";?>"/></a>

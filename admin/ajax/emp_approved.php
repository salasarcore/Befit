
<?php
@session_start();

include("../conn.php");

$sql="update employee set activated ='".$_POST['act']."' where 	empid=".$_POST['empid'];
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");

?>
<a href ="javascript:Publish('<?php if($_POST['act']=='Y') echo 'N'; else echo 'Y';?>',<?php echo $_POST['empid'];?>,'pub')"><img src="<?php if($_POST['act']=="Y") echo "../images/publish.png"; else  echo "../images/publish_x.png";?>"/></a>
	
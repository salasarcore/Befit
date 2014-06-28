<?php 
@session_start();
include_once("functions/common.php");
include("conn.php");

if (makeSafe($_POST['field'])=="freeze")
{
	if($_SESSION['access_level']=="Super Admin" || $_SESSION['access_level']=="Admin")
	{
		$sql="update  ".makeSafe($_POST['tab'])." set ".makeSafe($_POST['field'])."='".makeSafe($_POST['act'])."',updated_by='".makeSafe($_SESSION['emp_name'])."' where ".makeSafe($_POST['condition_field'])."=".makeSafe($_POST['id']);
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		if(mysql_affected_rows()>0)
		{
			?>
			<a href ="javascript:Publish('<?php if(makeSafe($_POST['act'])=='Y') echo 'N'; else echo 'Y';?>','freeze','f_<?php echo makeSafe(@$_POST['id']);?>',<?php echo makeSafe(@$_POST['id']);?>)"><img src="../images/<?php if(makeSafe($_POST['act'])=="Y") echo "publish.png"; else  echo "publish_x.png";?>"/></a>
			<?php
		}
	}
	else 
		echo "<font color='#FF0000'>Contact Administrator To Freeze/Unfreeze.</font>";
}
else if(makeSafe($_POST['field'])=="admission_open")
{
	$sql="update  ".makeSafe($_POST['tab'])." set ".makeSafe($_POST['field'])."='".makeSafe($_POST['act'])."',updated_by='".makeSafe($_SESSION['emp_name'])."' where ".makeSafe($_POST['condition_field'])."=".makeSafe($_POST['id']);
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	if(mysql_affected_rows()>0)
	{
	?>
		<a href ="javascript:Publish('<?php if(makeSafe($_POST['act'])=='Y') echo 'N'; else echo 'Y';?>','<?php echo makeSafe($_POST['field']); ?>','s_<?php echo makeSafe(@$_POST['id']);?>',<?php echo makeSafe(@$_POST['id']);?>)"><img src="../images/<?php if(makeSafe($_POST['act'])=="Y") echo "publish.png"; else  echo "publish_x.png";?>"/></a>
	<?php
	}
}
else
{
	$sql="update  ".makeSafe($_POST['tab'])." set ".makeSafe($_POST['field'])."='".makeSafe($_POST['act'])."',updated_by='".makeSafe($_SESSION['emp_name'])."' where ".makeSafe($_POST['condition_field'])."=".makeSafe($_POST['id']);
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	if(mysql_affected_rows()>0)
	{
		?>
			<a href ="javascript:Publish('<?php if(makeSafe($_POST['act'])=='Y') echo 'N'; else echo 'Y';?>',<?php echo makeSafe(@$_POST['id']);?>)"><img src="../images/<?php if(makeSafe($_POST['act'])=="Y") echo "publish.png"; else  echo "publish_x.png";?>"/></a>
		<?php
		}
}
?>
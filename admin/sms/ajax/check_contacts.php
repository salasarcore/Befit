<?php 
@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");

makeSafe(extract($_REQUEST));
if($number!="" && $name!=""){
	$sql="select * from sms_non_registered_users where mobile_num=".$number;
	$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	if(mysql_num_rows($res)>0)
		echo json_encode("false");
	else {
		$newid=getNextMaxId("sms_non_registered_users","user_id")+1;
		$sql="insert into sms_non_registered_users(user_id,name,mobile_num,updated_by)";
		$sql = $sql ." values('". @$newid. "','".@$name."','".@$number."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
		mysql_query($sql,$link);
		echo json_encode("true");
	}
}
else 
	echo json_encode("true");
?>
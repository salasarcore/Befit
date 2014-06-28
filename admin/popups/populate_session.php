
<?php 
include("../conn.php");
include("../functions/common.php");
//ECHO $sql="select *  from session_section where department_id=".$_POST['deptid']." and freeze='N' and admission_open='Y'";

echo "<select size=\"1\" name=\"session\" id=\"session\">";
echo "<option value=\"0\" selected>--Select session--</option>";
$sql="select distinct session_id, session,section  from session_section where department_id=".makeSafe($_POST['deptid'])." and freeze='N' and admission_open='Y'";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	while($row1=mysql_fetch_array($res))
		echo"<option value='".$row1['session_id']."'>".$row1['session']."-Section:".$row1['section']."</option>";
	
echo"</select>";
?>
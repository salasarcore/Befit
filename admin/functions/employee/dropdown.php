<?php 
function department($department_id)
{
if(@$department_id=="") $department_id=0;
echo "<select size=\"1\" name=\"department\" id=\"department\">";
echo "<option value=\"0\" selected>--Select Department--</option>";
$sql="select department_id,department_name  from mst_departments where br_id=".$_SESSION['br_id']." order by department_name";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['department_id']."'";
		if($department_id==$row1['department_id']) echo "selected"; echo">".$row1['department_name']."</option>";
	}
echo"</select>";
}
function session($session_id)
{
if(@$session_id=="") $session_id=0;
echo "<select size=\"1\" name=\"session\" id=\"session\">";
echo "<option value=\"0\" selected>--Select Session--</option>";
$sql="select session_id,session_name  from session order by session_id desc";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['session_name']."'";
		if($session_id===$row1['session_name']) echo "selected"; echo">".$row1['session_name']."</option>";
	}
echo"</select>";
}	
function designation($designation_id)
{
if(@$designation_id=="") $designation_id=0;

echo "<select size=\"1\" name=\"designation\" id=\"designation\">";
echo "<option value=\"0\" selected>--Select Designation--</option>";
$sql="select designation_id,designation_name  from mst_designations order by designation_name";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['designation_id']."'";
		if($designation_id==$row1['designation_id']) echo "selected"; echo">".$row1['designation_name']."</option>";
	}
echo"</select>";
}		
function branches($br_id)
{
echo "<select size=\"1\" name=\"branches\"  id=\"branches\">";
echo "<option value=\"0\" selected>--Select Branch--</option>";
$sql="select br_id,br_name,br_code  from mst_branch order by br_name";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['br_id']."'";
		if($br_id==$row1['br_id']) echo "selected"; echo">".$row1['br_name']."[".$row1['br_code']."]</option>";
	}
echo"</select>";
}	

function emp_department($department_id)
{
	if(@$department_id=="") $department_id=0;
	echo "<select size=\"1\" name=\"empdepartment\" id=\"empdepartment\">";
	echo "<option value=\"0\" selected>--Select Department--</option>";
	$sql="select department_id,department_name  from employee_department order by department_name";
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['department_id']."'";
		if($department_id==$row1['department_id']) echo "selected"; echo">".$row1['department_name']."</option>";
	}
	echo"</select>";
}
?>			
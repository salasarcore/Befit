<?php 
@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
$regno=makeSafe($_REQUEST['regno']);
$str="";
$stuid=0;
$sql="select stu.stu_id,department_name,section,rollno,concat_ws(' ',stu.stu_fname,stu.stu_mname,stu.stu_lname) as stu_name from mst_departments as dept,
session_section as ss, student_class as sc,mst_students as stu where
sc.department_id=dept.department_id and sc.session_id=ss.session_id and
stu.stu_id=sc.stu_id and stu.br_id=".$_SESSION['br_id']." and stu.reg_no='".$regno."'";
$result  = mysql_query($sql) or die('Error, query failed');
if(mysql_affected_rows($link)>0)
	{
		$row     = mysql_fetch_array($result, MYSQL_ASSOC);
		$stuid=$row['stu_id'];
		$str="<table class='table table-bordered'>";
		$str .="<tr><td>Name :</td><td>".$row['stu_name']."</td></tr>";
		$str .="<tr><td>Roll Number :</td><td>".$row['rollno']."</td></tr>";
		$str .="<tr><td>Section :</td><td>".$row['section']."</td></tr>";
		$str .="<tr><td>Department :</td><td>".$row['department_name']."</td></tr>";
		$str .="</table>";
	}
	
	if(strlen($str)>0)
	{
	    echo  $str;
	    echo "<input type='hidden' name='stuid' id='stuid' value=".$stuid." />";
	}
	else
        echo "no:Invalid Registration Number";

?>

<?php
include("../../conn.php");
include_once("../../functions/common.php");
$value=makeSafe($_GET['value']);
$results=array();
$query="select department_name, section, rollno, concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name, ms.stu_id,ms.reg_no
from mst_students as ms, student_class as sc, mst_departments as dept, session_section as ss
where (ms.stu_fname LIKE '%".$value."%' OR ms.reg_no LIKE '%".$value."%') and sc.department_id=dept.department_id and sc.session_id=ss.session_id
and ms.stu_id=sc.stu_id and ms.br_id=".$_SESSION['br_id'];


	$result2 = mysql_query($query);
	while($row=mysql_fetch_array($result2))
	{
		$value=$row['stu_name']." (Reg No.".$row['reg_no'].", Roll No. ".$row['rollno'].", ".$row['department_name']."-".$row['section'].")";
		$results[] = array('value' => $value ,'id' => $row['reg_no']);
	}
echo json_encode($results);
?>
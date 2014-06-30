<?php

/**
 * This file is an ajax call which will populate the list of sections based on the department selected.
 */

@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
$act=makeSafe($_GET['act']);
$dept_id=makeSafe(@$_REQUEST['department']);
$section_id=makeSafe(@$_REQUEST['section']);

if($act=="sectionlist")
{
	$sql="select s.* from mst_departments d, session_section s where d.department_id=s.department_id and s.freeze='N' and s.session='".makesafe($_SESSION['d_session'])."' and s.department_id='".$dept_id."' order by department_name,session_id desc";
	echo $sql;
	
	$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	echo "<option value=''>ALL SECTIONS</option>";
	while($row=mysql_fetch_array($res))
	{
		echo "<option value='".$row['session_id']."'";
		echo ">".$row['section']."</option>";
	}
}
?>
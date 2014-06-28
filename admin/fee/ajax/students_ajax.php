<?php 
@session_start();
include('../../conn.php');
include('../../check_session.php');
include("../../functions/common.php");

if(makeSafe($_REQUEST['action'])=='search')
{	
	/**
	 * This code will display options of students of the selected section.
	 * Students are fetched on the basis of selected section id from student class table.
	 */
	$sectionid = explode(",",makeSafe($_REQUEST['value']));
	$sectionname = explode(",",makeSafe($_REQUEST['name']));
	for($i=0;$i<count($sectionid);$i++)
	{	
		echo "<optgroup  label='".$sectionname[$i]."' >";
	    $query="select sc.*,concat_ws(' ',s.stu_fname,s.stu_mname,s.stu_lname) as stu_name from student_class sc,mst_students s WHERE sc.stu_id=s.stu_id and sc.session_id=".$sectionid[$i]." and s.br_id=".$_SESSION['br_id'];
		$result=mysql_query($query);
		while($row=mysql_fetch_assoc($result))
			echo "<option value='".$row['stu_id']."' title='".$row['stu_name']."'>".$row['rollno']." - ".$row['stu_name']."</option>";
		echo "</optgroup>";
	}
}
if(makeSafe($_REQUEST['action'])=='editsection')
{
	/**
	 * This code will display checkbox of students of the selected section.
	 * Students are fetched on the basis of selected section id from student class and student master table.
	 */
	if(makeSafe($_REQUEST['value']!="")){
	$expectedid = makeSafe($_REQUEST['expected_id']);
	$sectionid = explode(",",makeSafe($_REQUEST['value']));
	$sectionname = explode(",",makeSafe($_REQUEST['name']));
	$row=array();
	for($i=0;$i<count($sectionid);$i++)
	{
		$sql="select stu_id from student_expected_fees where fee_expected_id=".$expectedid." and section_id=".$sectionid[$i];
		$ressql=mysql_query($sql);
		while($result1=mysql_fetch_assoc($ressql))
			$row[]=$result1['stu_id'];
		
		echo "<label><b>".$sectionname[$i]."</b></label><br>";
		$query="select sc.*,concat_ws(' ',s.stu_fname,s.stu_mname,s.stu_lname) as stu_name,s.stu_id as stu_id from student_class sc,mst_students s WHERE sc.stu_id=s.stu_id and sc.session_id=".$sectionid[$i]." and br_id=".$_SESSION['br_id'];
		$result=mysql_query($query);
		while($row1=mysql_fetch_assoc($result))
		{
			echo "<input type='checkbox' name='student[]' value='$row1[stu_id]' ";
			
			if(in_array($row1['stu_id'], $row)) echo "checked";
			echo ">".$row1['rollno']." - ".$row1['stu_name']."<br>"; 
			            
			 }
			            
	}
	}
}
?>
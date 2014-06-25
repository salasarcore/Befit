<?php 
@session_start();
include("../../../globalConfig.php");
include('../../check_session.php');
include_once("../../../functions/common.php");
include_once("../../functions/comm_functions.php");

$department_id=makeSafe($_GET['dept_id']);
$departdetails=getDetailsById("employee_department","department_id",$department_id);
?>
<table width="100%" cellspacing="1" class="shadow adminform" style=" frame:box;border: 1px solid; border-collapse: collapse;">
  <thead>	
  <tr style="border-bottom: 1px solid;">
    <th colspan="3"  style="border-right: 1px solid;"><?php echo strtoupper($departdetails['department_name']);?></th>
   </tr>	
   <tr style="border-bottom: 1px solid;">
    <th style="border-right: 1px solid;">NAME</th>
    <th style="border-right: 1px solid;">EMP ID</th>
   	<th  style="border-right: 1px solid;">LOGIN</th>
   </tr>
   <?php
		$i=0;
		$sql="select employee.empid,emp_id,activated,emp_name,sex,emp_qualification,br_name,emp_doj,emp_dob,emp_addr_pre,emp_ecn,emp_mob,email,mime";
		$sql .=" from employee,mst_designations,mst_branch where employee.br_id=mst_branch.br_id and mst_designations.designation_id=employee.designation_id and mst_branch.br_id='".@$_SESSION['br_id']."'";
		$sqlWhere =" and employee.department_id=".$department_id." ";
			
		$sqlOrder =" order by empid desc";
		 $sql=$sql." ".$sqlWhere;
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			while($row=mysql_fetch_array($res))
		{
		$i=$i+1;
	?>

	<tr >
		<td align="center" style="border-right: 1px solid; width: 200px;"><?php echo $row['emp_name']; ?></td>
		<td align="center" style="border-right: 1px solid;"><?php echo $row['emp_id']; ?></td>
		<td align="center"  style="border-right: 1px solid;"><img src="<?php if($row["activated"]=="Y") echo "../images/publish.png"; else  echo "../images/publish_x.png";?>"/></td>
	</tr>
	<?php
	}
	?> 
</thead>
<tfoot>
    <tr style="border-top: 1px solid">
	<th colspan="3">
	<?php 
	$sql="SELECT count(emp_id) as numrows FROM employee,mst_designations,mst_branch where employee.br_id=mst_branch.br_id and mst_designations.designation_id=employee.designation_id and mst_branch.br_id='".@$_SESSION['br_id']."'";
	 $sql=$sql." ".$sqlWhere;		
	$result  = mysql_query($sql) or die('Error, query failed');
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
	$numrows = $row['numrows'];
	if($numrows>0)
		echo "TOTAL RECORDS : ".$numrows;
	else 
		echo "NO RECORDS FOUND";
	
?>

</th></tr>
</tfoot>
</table>
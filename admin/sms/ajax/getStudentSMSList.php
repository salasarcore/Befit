<?php

/**
 * This file is an ajax call which will display the list of students based on the department selected. This file will be used in this listing of students for Manual and transactional SMS.
 */

@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");

$modulename = array();
$flag = "";
if(makeSafe($_REQUEST['template']) == 'select')
{
	$sqlWhere="";
	$i=0;
	$sql = "SELECT msts.*, sc.* FROM mst_students msts, student_class sc WHERE sc.stu_id=msts.stu_id AND msts.br_id=".@$_SESSION['br_id'];

	if(makeSafe(isset($_REQUEST['department_id'])) && makeSafe($_REQUEST['department_id'])!=0)
		$sqlWhere .=" AND sc.department_id=".makeSafe($_REQUEST['department_id'])." ";
	if((makeSafe($_REQUEST['section'])!="") || (makeSafe($_REQUEST['section'])!=null))
		$sqlWhere .= " AND sc.session_id=".makeSafe($_REQUEST['section']);
	$sqlOrder =" order by msts.stu_fname ";
	$sql=$sql." ".$sqlWhere.$sqlOrder;
		$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused 1");
}
else
{
	$query = "SELECT e.*, g.* FROM notification_module_master e, global_sms_templates g  WHERE e.module_id=g.module_id and template_id=".$_REQUEST['template'];
	$result = mysql_query($query) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
	$modulename = mysql_fetch_assoc($result);
	if((@$modulename['module_name'] == 'Online Application') || (@$modulename['module_name'] == 'Application Rejected'))
	{
		$flag = 'online';
		$sqlWhere="";
		$i=0;
		$sql = "SELECT adm_form_no as stu_id, stu_fname, stu_mname, stu_lname, mob,email FROM admission_application where br_id=".@$_SESSION['br_id'];
		//if(makeSafe(isset($_REQUEST['department_id'])) && makeSafe($_REQUEST['department_id'])!=0)
		//	$sqlWhere .=" AND department_id=".makeSafe($_REQUEST['department_id'])." ";
		$sqlOrder =" order by stu_fname ";
		$sql=$sql." ".$sqlWhere.$sqlOrder;
		$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused 2");
	}
	else
	{
		$sqlWhere="";
		$i=0;
		$sql = "SELECT msts.*, sc.* FROM mst_students msts, student_class sc WHERE sc.stu_id=msts.stu_id AND msts.br_id=".@$_SESSION['br_id'];
		if(makeSafe(isset($_REQUEST['department_id'])) && makeSafe($_REQUEST['department_id'])!=0)
			$sqlWhere .=" AND sc.department_id=".makeSafe($_REQUEST['department_id'])." ";
		if((makeSafe($_REQUEST['section'])!="") || (makeSafe($_REQUEST['section'])!=null))
			$sqlWhere .= " AND sc.session_id=".makeSafe($_REQUEST['section']);
		$sqlOrder =" order by msts.stu_fname ";
		$sql=$sql." ".$sqlWhere.$sqlOrder;
		$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused 3");
	}
}


?>
<table width="100%" border="0" style="cursor: pointer;"
	class="table table-bordered">
	<thead>
		<tr>
			<th><input type="checkbox" id="selectall" name="selectall" value="" onclick="selectallid();" /></th>
			<th><?php if($flag!="") echo 'ADMISSION FORM NUMBER'; else echo 'STUDENT ROLL NUMBER'; ?></th>
			<?php if($flag==""){?>
			<th>DEPARTMENT</th>
			<?php }?>
			<th>
			<?php if(makeSafe(isset($_REQUEST['sendto'])))
			{
			
				echo 'MEMBER NAME';
			}
			?></th>
			<th>MOBILE</th>
			<th nowrap>EMAIL</th>
		</tr>
		</thead>
<tbody>

	<?php
	while($row=mysql_fetch_array($res))
	{
		$i=$i+1;
		?>

	<tr onclick="getSelected();">
		<td align='center'><input type="checkbox" id="<?php echo $row['stu_id'];?>" name="rdoID[]" value="<?php echo $row['stu_id'];?>" onclick="getSelected();" /></td>
		<td align="center"><?php if($flag == "") echo $row['rollno']; else echo $row['stu_id']; ?></td>
		<?php if($flag==""){?>
			<td align="center"><?php $deptname = mysql_fetch_assoc(mysql_query("SELECT * from mst_departments where department_id=".$row['department_id']));
			echo $deptname['department_name'];
			?>			
			</td>
			<?php }?>
		<td align="center"><?php 
						echo $row['stu_fname'].' '.$row['stu_mname'].' '.$row['stu_lname'];
		?></td>
		<td align="center">
		<?php 
			echo $row['mob'];
		
		?>
		</td>
		<td align="center"><?php echo $row['email']; ?></td>
	</tr>
	<?php
	}
	?>
	</tbody>
	</table>
	<b>Total: <?php echo $i;?></b>
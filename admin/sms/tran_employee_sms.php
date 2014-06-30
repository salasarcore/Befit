<?php 
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_tran_employee_sms,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_tran_employee_sms))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}
*/

$br_id=makeSafe(@$_POST['branches']);
?>

<script>
function ActionScript(act)
{
	 var checkValues = $('input[name=rdoID]:checked').map(function()
            {
                return $(this).val();
            }).get();
     if($.trim(checkValues)==""){
    	 alert("Please select an employee to send message.");
 		return false;
     	}
     else{
	    url="sms/popups/send_employee_tran_sms.php?act="+act+"&values="+checkValues;
		open_modal(url,620,420,"TRANSACTIONAL EMPLOYEE SMS")
		return true;
     }
}
function selectallid()
{

	var elms=document.getElementsByName("rdoID");
	if($('#selectall').is(':checked')) {
		for (i=0;i<elms.length;i++){
			if (elms[i].type="checkbox" ){
				elms[i].checked = true;
				}
			}
	}
	else{
		for (i=0;i<elms.length;i++){
			if (elms[i].type="checkbox" ){
				elms[i].checked = false;
				
				}
			}
	}
}

function getSelected()
{
	var elms=document.getElementsByName("rdoID");
	for (i=0;i<elms.length;i++){
		if (elms[i].type="checkbox" ){
			if(!$('#'+elms[i].value).is(':checked')) {
				$('#selectall').attr('checked',false);
			}
		}
}
}
</script>
<div class="page_head">
<div id="navigation"><a href="pages.php">Home</a><a> Utilities</a><a> SMS</a>  <span style="color: #000000;">Employee Transactional SMS</span> </div>


<form name="myForm" id="myForm" action="pages.php?src=sms/tran_employee_sms.php" method="POST">

<table width="100%">
<tr>
<td><h2>Employee Transactional SMS</h2></td>
	<td>
	<div id="option_menu">
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Send SMS</a>
	</div>
	</td>
</tr>	
</table>
</div>
<br>

<table width="100%" cellspacing="1" class="table table-bordered" style="cursor: pointer;">
  <thead>		
   <tr>
    <th><input type="checkbox" id="selectall" name="selectall" value="" onclick="javascript:selectallid();"/></th>
    <th>NAME</th>
    <th>EMP ID</th>
   	<th nowrap>ECN</th>
   	<th nowrap>MOBILE</th>
   	<th nowrap>EMAIL</th>
   </tr>
  <thead>
  <tbody>
   <?php




	$i=0;
		$sql="select employee.empid,emp_id,activated,emp_name,sex,emp_qualification,br_name,emp_doj,emp_dob,emp_addr_pre,emp_ecn,emp_mob,email,mime";
		$sql .=" from employee,mst_designations,mst_branch where employee.br_id=mst_branch.br_id and mst_designations.designation_id=employee.designation_id and mst_branch.br_id='".@$_SESSION['br_id']."' order by emp_name ";
		
		$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			while($row=mysql_fetch_array($res))
		{
		$i=$i+1;
	?>

	<tr class=<?php if($i%2==0) echo "row0"; else echo "#row1"; ?>  onclick="getSelected();" >
	<td align='center'><input type="checkbox" id="<?php echo $row['empid'];?>" name="rdoID" value="<?php echo $row['empid'];?>" onclick="getSelected();"/></td>
		<td><?php echo $row['emp_name']; ?></td>
		<td><?php echo $row['empid']; ?></td>
		<td><?php echo $row['emp_ecn']; ?></td>
		<td><?php echo $row['emp_mob']; ?></td>
		<td><?php echo $row['email']; ?></td>
	</tr>
	<?php
	}
	?> 
  </tbody>
  <tfoot>
<tr>
<th colspan="12">
	<?php 
$sql="SELECT count(emp_id) as numrows FROM employee,mst_designations,mst_branch where employee.br_id=mst_branch.br_id and mst_designations.designation_id=employee.designation_id  and mst_branch.br_id='".@$_SESSION['br_id']."'";	
$result  = mysql_query($sql,$link) or die('Error, query failed');
$row     = mysql_fetch_array($result, MYSQL_ASSOC);
$numrows = $row['numrows'];
echo "TOTAL RECORDS : ".$numrows;
?>
</th>
  </tr>
</tfoot>


</table>
</form>
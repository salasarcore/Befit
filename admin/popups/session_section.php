<?php 
@session_start();
include("../conn.php");
include('../check_session.php');
include("../functions/employee/dropdown.php");
include("../functions/common.php");
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
$session="";
$section="";
$department_id="";
$empid="";
$act=makeSafe(@$_GET['act']);
include('../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../modulemaster.php");
if($act=="add")
	$id=option_session_section_list_add;
elseif($act=="edit")
$id=option_session_section_list_edit;
elseif($act=="delete")
$id=option_session_section_list_delete;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$sessionID=makeSafe(@$_GET['sessionID']);
$action=makeSafe(@$_POST['action']);
$msg="";
if(@$action=="SAVE" || @$action=="UPDATE")
{
	$session=makeSafe(@$_POST['session']);
	$section=makeSafe(@$_POST['txtSection']);
	$department_id=makeSafe(@$_POST['department']);
	$empid=makeSafe(@$_POST['empid']);

	if(@$department_id=="0")
		$msg= "<div class='error'>Please Select Course</div>";
	elseif(@$session=="0")
		$msg= "<div class='error'>Please Select Session</div>";
	elseif(trim(@$section)=="")
		$msg= "<div class='error'>Please Enter Batch</div>";
	else
	{
		if(@$action=="SAVE")
		{
			$sql=" select * from session_section where session='".@$session."' and section='".@$section."' and department_id=".@$department_id;
			$res=mysql_query($sql,$link); 
			if(mysql_affected_rows($link)>0)
				$msg= "<div class='error'>Duplicate Batch and session found for selected Course</div>";
			else
			{
				$newsessionid=getNextMaxId("session_section","session_id")+1;
				$sql="insert into session_section(session_id,session,section,department_id,updated_by) values('".@$newsessionid."','".@$session."','".@$section."',".@$department_id.",'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0){
					$msg= "<div class='success'>Record Saved Successfully</div>";
					$session="";
					$section="";
					$department_id="";
				}
				else
					$msg= "<div class='error'>Record Not Saved Successfully</div>";
			}
		}
		else
		{
			$sql=" select * from session_section where session='".@$session."' and section='".@$section."' and department_id=".@$department_id." and session_id!=".@$sessionID;
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$msg= "<div class='error'>Duplicate batch and session found for selected course</div>";
			else
			{
				$sql="update session_section set session='".@$session."',section='".@$section."',updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where session_id=".@$sessionID;
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)==0)
					$msg= "<div class='success'>No Data Changed</div>";
				elseif(mysql_affected_rows($link)>0)
					$msg= "<div class='success'>Record Updated Successfully</div>";
				elseif(mysql_affected_rows($link)<0)
					$msg="<div class='error'>Record Not Updated Successfully</div>";
			}
		}
	}
}

if(@$action=="SAVE")
	@$act=="add";
else
	@$act=="edit";


if(@$action=="DELETE")
{
	$sql="SELECT * FROM student_class where session_id=".$sessionID;	
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
	if(mysql_num_rows($res)==0)
	{		
		$query   = "delete FROM session_section where session_id=".@$sessionID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0){
		$msg ="<div class='success'>Batch Deleted Successfully</div>"; 
		$session="";
		$section="";
		$department_id="";
		}
	}
	else
	{
		$msg="<div class='error'>You can not delete this record as it is already assigned</div>";
	}
}

if(@$act=="edit" || @$act=="delete")
{
	/**
	 * The below code is commented because it will only be used while deleting the section, and we have not provided delete functionality yet
	 * This code will be used for future reference
	 */
	/*$query   = "SELECT  stu_id FROM student_class  where session_id=".$sessionID;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$msg= "<div class='error'>Student Already registered in this session and class<br /> Please contact Administrator</div>";
		exit;
	}*/
	$query = "SELECT section,department_id, session, date_updated,updated_by FROM session_section where session_id=".@$sessionID;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$session=@$row['session'];
		$department_id=@$row['department_id'];
		$section=@$row['section'];
		$last_updated=@$row['date_updated'];
		$updated_by=@$row['updated_by'];
		
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>



<SCRIPT>
function validatesection(frm)
{
	
	 if(frm.session.value==0)
	{
		alert("Select Session");
		frm.session.focus();
		return false;
	}
	else if(frm.department.value==0)
	{
		alert("Select Course");
		frm.department.focus();
		return false;
	}
	else if(frm.txtSection.value.trim()=="")
	{
		alert("Enter Batch");
		frm.txtSection.value="";
		frm.txtSection.focus();
		return false;
	}
	else if(frm.employee.value.trim()!="")
	{
		if(frm.empid.value.trim()=="")
		{
			alert("Please select an employee from the list only.");
			frm.empid.value="";
			frm.empid.focus();
			return false;
		}
		else
			return true;
	}
	return true;
}
 
function ClearField(frm){
	  frm.txtSection.value = "";
	  frm.department.value = 0;
	  frm.session.value = 0;
	}
</SCRIPT>

</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper(@$act);?> COURSE WISE BATCH</h2></div>
		<span id="spErr"><?php echo @$msg;?> </span>
		<form method="post" name="frmManu"
			action="session_section.php?sessionID=<?php echo makeSafe(@$sessionID);?>&act=<?php echo makeSafe(@$act); ?>"
			onsubmit="return validatesection(this);">
			<table class="adminform">
				<tr>
					<td align="right" class="redstar" nowrap>Session Name :</td>
					<td><?php session(@$session); ?> <br>
					</td>
				</tr>
				<tr>
					<td align="right" class="redstar" nowrap>Course :</td>
					<td>
					<?php  department(@$department_id);?>
					</td>
				</tr>
				<tr>
					<td align="right" class="redstar">Batch :</td>
					<td><input type="text" name="txtSection" id="txtSection" size="30"
						value='<?php echo @$section; ?>' <?php if(@$act=='delete') echo "readonly"; ?> maxlength="100"/><br>
						<p class="hint_text">Ex: 1st Same, A, B, Morning</p></td>
				</tr>
								<tr>
					<td align="right">last Updated :</td>
					<td><?php echo @$last_updated; ?></td>
				</tr>
				<tr>
					<td align="right">Updated By :</td>
					<td><?php echo @$updated_by; ?></td>
				</tr>
				<tr>
					<td align="center" colspan=2><input type="submit" class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1" />
			<?php if(@$act!="delete"){?> <input type="button" class="btn reset" value="RESET" id="reset"	name="reset" onClick="ClearField(this.form)" /><?php } ?>
				 <input	type="button" class="btn close" value="CLOSE" onClick="parent.emailwindow.close();" />
				 <input type='hidden' name='action'	value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' />
					</td>
				</tr>
			</table>
		</form>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo @$msg; ?>";

var act='<?php echo $act;?>';
if(act=='delete')
{
	$('#session').attr("disabled",true);
	$('#department').attr("disabled",true);
}

</script>
</body>
</html>

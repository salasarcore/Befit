<?php
@session_start();
include("../conn.php");

include('../check_session.php');
include("../../fckeditor/fckeditor.php");
include("../functions/common.php"); 

$ua=getBrowser();


?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../modulemaster.php");
if($act=="add")
	$id=option_employee_department_list_add;
elseif($act=="edit")
$id=option_employee_department_list_edit;
elseif($act=="delete")
$id=option_employee_department_list_delete;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>


<script type="text/javascript">
function validatedepartment()
{
	var dept_code=document.frmManu.txtdept_code.value;
	var dept_name=document.frmManu.txtdeptName.value;

if(dept_name.trim()=="")
{
	alert('Department name should not be blank');
	document.frmManu.txtdeptName.value="";
	document.frmManu.txtdeptName.focus();
	return false;
}
else if(dept_code.trim()=="")
{
	alert('Department code should not be blank');
	document.frmManu.txtdept_code.value="";
	document.frmManu.txtdept_code.focus();
	return false;
}

return true;
}

function ClearField(frm){
	  frm.txtdeptName.value = "";
	  frm.txtdept_code.value = "";
	  var editor = FCKeditorAPI.GetInstance('txtabout_dept');
	  editor.SetHTML('');
	 }

</SCRIPT>
</head>
<body>
	<?php 
	
	$dept_name="";
	$dept_code="";
	$about_dept="";

	$departmentID=makeSafe(@$_GET['departmentID']);
	$action=makeSafe(@$_POST['action']);
	$Errs="";
	if(@$action=="SAVE" || @$action=="UPDATE")
	{
		$dept_name=makeSafe(@$_POST['txtdeptName']);
		$dept_code=makeSafe(@$_POST['txtdept_code']);
		$about_dept=makeSafe(@$_POST['txtabout_dept']);

		if($action=="SAVE")
		{

			if(trim($dept_name)=="")
				$Errs= "<div class='error'>Please Enter Department Name</div>";
			elseif(trim($dept_code)=="")
				$Errs= "<div class='error'>Please Enter Department Code</div>";
			else
			{
				$query="select * from employee_department where department_name='".$dept_name."' or department_code='".$dept_code."'";
				$res=mysql_query($query,$link);
				if(mysql_affected_rows($link)>0)
				{
					$row = mysql_fetch_array($res, MYSQL_ASSOC);
					if(@$dept_name==@$row['department_name'])
						$Errs= "<div class='error'>Duplicate Department Name</div>";
					elseif(@$dept_code==@$row['department_code'])
						$Errs= "<div class='error'>Duplicate Department Code</div>";
				}
				else
				{
				$newdeptid=getNextMaxId("employee_department","department_id")+1;
					$sql="insert into employee_department(department_id,br_id,department_name,department_code,department_text,updated_by)";
					$sql = $sql ." values(".$newdeptid.",'".$_SESSION['br_id']."','".$dept_name."','".$dept_code."','".$about_dept."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
					$res=mysql_query($sql,$link);
					if(mysql_affected_rows($link)>0)
						$Errs= "<div class='success'>Record Saved Successfully</div>";
					else
						$Errs="<div class='error'>Record Not Saved Successfully</div>";
				}
			}
		}
		else
		{
			
			if(trim($dept_name)=="")
				$Errs= "<div class='error'>Please Enter Department Name</div>";
			elseif(trim($dept_code)=="")
				$Errs= "<div class='error'>Please Enter Department Code</div>";
			else
			{
				
					$query="select * from employee_department where (department_name='".$dept_name."' or department_code='".$dept_code."') and department_id!=".$departmentID;
					$res=mysql_query($query,$link);
					if(mysql_affected_rows($link)>0)
					{
						$row = mysql_fetch_array($res, MYSQL_ASSOC);
						if(@$dept_name==@$row['department_name'])
							$Errs= "<div class='error'>Duplicate Department Name</div>";
						elseif(@$dept_code==@$row['department_code'])
							$Errs= "<div class='error'>Duplicate Department Code</div>";
					}
					else
					{
						$sql="update employee_department set department_name='".$dept_name."',department_code='".$dept_code."',department_text='".$about_dept."',updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where department_id=".$departmentID;
						$res=mysql_query($sql,$link);
						if(mysql_affected_rows($link)==0)
							$Errs="<div class='success'>No Data Changed</div>";
						if(mysql_affected_rows($link)>0)
							$Errs="<div class='success'>Record Updated successfully</div>";
						if(mysql_affected_rows($link)<0)
							$Errs="<div class='error'>Record Not Updated successfully</div>";
						
					}
				}
			
		}




		if($action=="SAVE")
			$act=="add";
		else
			$act=="edit";
	}

	if(@$action=="DELETE")
	{
		$query   = "SELECT empid FROM employee where department_id=".$departmentID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
		{
			$Errs= "<div class='error'>You can not delete this department as it is already assigned to some employee(s)</div>";
		}
		else
		{
		$query   = "delete FROM employee_department where department_id=".$departmentID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
		{
			$Errs= "<div class='success'>Employee Department Deleted Successfully</div>";
		}
		else 
			{
				$Errs= "<div class='error'>Employee Department Not Deleted Successfully</div>";
			}
		}
	}

	if(@$act=="edit" || @$act=="delete" )
	{

		$query   = "SELECT  department_code, department_name, department_text, date_updated,updated_by FROM employee_department  where department_id=".$departmentID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
		{
			$row     = mysql_fetch_array($result, MYSQL_ASSOC);

			$dept_name=@$row['department_name'];
			$dept_code=@$row['department_code'];
			$about_dept=@$row['department_text'];
			$last_updated=@$row['date_updated'];
			$updated_by=@$row['updated_by'];
		}
		
	}
	?>


	<div id="middleWrap">
		<div class="head"><h2>EMPLOYEE DEPARTMENTS</h2></div>

			<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"
			action="employee_department.php?departmentID=<?php echo $departmentID;?>&act=<?php echo $act; ?>" onsubmit="return validatedepartment();" >

			<table class="adminform">

				<tr>
					<td align="right" class="redstar" nowrap>Department Name :</td>
					<td><input type="text" name="txtdeptName" id="txtdeptName"
						size="40" value='<?php if($act=="add") echo ""; else echo @$dept_name; ?>'  maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?>/></td>
				</tr>
				<tr>
					<td align="right" class="redstar">Code :</td>
					<td><input type="text" name="txtdept_code" id="txtdept_code"
						size="40" value='<?php if($act=="add") echo ""; else echo @$dept_code; ?>' maxlength="10" <?php if(@$act=="delete") echo "readonly"; ?>/></td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:top;">About department:</td>
					<td <?php if ( @$ua['name']=='Google Chrome') echo "style='border-bottom: 1px solid black;  border-top: 1px solid black;'";?> style=" border-top: 1px solid black;'";>
					<?php 
					
					$sBasePath="../../fckeditor/";
					$oFCKeditor = new FCKeditor('txtabout_dept') ;
					$oFCKeditor->BasePath = $sBasePath ;
					$oFCKeditor->ToolbarSet = "Default" ;
					$oFCKeditor->Width = "530" ;
					$oFCKeditor->Height = "300" ;
					$oFCKeditor->Value =  (($act=='add')  ? "" :@$about_dept);
						
					//$oFCKeditor->Value = @$about_dept;
						
					$oFCKeditor->Create();
					
					/*elseif($act=="add")
					{
						echo "<script>ckclear(this.form);</script>";
					}*/
					?>
					</td>
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
				
				
				<tr>


					<td align="center" colspan=2><input type="submit" class="btn save"
						value='<?php 
	                if(@$act=="add") echo "SAVE";
					
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>'
						name="B1" />  
						<?php if(@$act!="delete"){?>
				<input type="button" class="btn reset" value="RESET" id= "reset "name="reset" onClick="ClearField(this.form)" />
					<?php }	?>
					 <input
						type="button" class="btn close" value="CLOSE" onClick="parent.emailwindow.close(); " />
						<input type='hidden' name='action'
						value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
   </script>
</body>
</html>
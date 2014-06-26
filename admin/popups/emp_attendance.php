<?php 
@session_start();
include("../conn.php");
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/common.php");
include('../check_session.php');
$msg="";
$err="";
$hrs=0;
makeSafe(extract($_REQUEST));
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../modulemaster.php");

$id=option_employee_attendance_edit;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$level=$_SESSION['access_level'];
if(@$action=="UPDATE")
{
	$today=mysql_fetch_assoc(mysql_query("select curdate() as today",$link))['today'];
	if($today!=$date)
	if(($level!='Super Admin')&&($level!='Admin'))
		$err="error";
	
	if($err==""){
		if(trim($att_type)=='P' && trim($hrs)==""){
			$msg="<div class='error'>Working Hours Should Not Be Blank</div>";
		}
		elseif(trim($att_type)=='P' && intval(trim($hrs))==0){
			$msg="<div class='error'>Please Give Working Hours Greater Than 0(Zero).</div>";
		}
		else{
			if($hrs=="") $hrs=0;
			if(strpos($hrs, '.') !== FALSE && substr(strstr($hrs, '.'),1)=="60")
				$hrs = intval(current(explode(".", $hrs)))+1;
			
			
			$sql="update emp_attendance_register set att_type='".$att_type."', work_hours='".$hrs."' where att_date='".$date."' and empid=".$empID;
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>=0)
				$msg= "<div class='success'>Attendance Record Updated Successfully.</div>";
			else
				$msg="<div class='error'>Attendance Record Not Updated Successfully.</div>";
			}
	}
	else
		$msg= "<div class='error'>You Are Only Authorised To Mark Today's Attendance. Please Contact Administrator.</div>";
}

if(@$act=="EDIT")
{$paytype="";

$query   = "select e.empid,e.emp_id,e.emp_name, d.department_name,b.br_name,es.* from employee as e join employee_department as d on e.department_id=d.department_id join mst_branch as b on d.br_id=".$_SESSION['br_id']." join emp_sal_settings as es on e.empid=es.empid where e.empid=".$empID;
$result  = mysql_query($query,$link) or die('Error, query failed');
if(mysql_affected_rows($link)>0)
{
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
	$emp_name=@$row['emp_name'];
	$empID=@$row['empid'];
	$emp_code=@$row['emp_id'];
	$dept_name=@$row['department_name'];
	$branch_name=@$row['br_name'];
	$paytype=$row['pay_type'];
	if($paytype=="H")
	{
						
	$msg="<div class='error'>To update attenadance of this employee update it from Academic Calender.</div>";
	}
					
}	
	$leave_approved="";
	$cause="";
	$l=false;
	$att_type="";
	$holiday="";
	$sql="SELECT leave_approved,cause FROM emp_leave_applications where empid=".$empID."  and '". $date ."' between date_from and date_to";

	$resC=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	
	if(mysql_affected_rows($link)>0)
	{
		$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
		$leave_approved=$rowL['leave_approved'];
		$cause=$rowL['cause'];
		$l=true;
	}
	$sql="SELECT event_type FROM event where '". $date ."' between date(event_start_date) and date(event_end_date)";
	$resC=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	if(mysql_affected_rows($link)>0)
	{
		$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
		$holiday=$rowL['event_type'];
			
	}

	$sql="SELECT att_type,work_hours FROM emp_attendance_register where empid=".$empID."  and att_date='". $date ."'";

	$resC=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	if(mysql_affected_rows($link)>0)
	{
		$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
		$att_type=$rowL['att_type'];
		$hrs=$rowL['work_hours'];
	}
	else 
		$msg="<div class='error'>Attendance For This Date is Not Yet Registered.</div>";
				
}
?>

<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>Employee Attendance</title>
<link rel="shortcut icon" href="../favicon.ico">
<script language="javascript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	if(document.getElementById('P').checked)
	{
		document.getElementById("hrs").disabled = false;
	}
	else
		document.getElementById("hrs").disabled = true;
});	
function checkhrs(objchk)
{
	if(objchk.value=='P'){
		document.getElementById("hrs").disabled = false;
		document.getElementById("hrs").value="8";
	}
	else{
		document.getElementById("hrs").disabled = true;
		document.getElementById("hrs").value="0";
	}
}
function validate(frm)
{
	if(document.getElementById("hrs").value.trim()=="")
	{
		alert('Working hours should not be blank');
		document.getElementById("hrs").value="";
		document.getElementById("hrs").focus();
		return false;
	}
	if(document.getElementById('P').checked && parsefloat(document.getElementById("hrs").value.trim())==0 )
	{
		alert('Please give working hours greater then 0(Zero)');
		document.getElementById("hrs").value="";
		document.getElementById("hrs").focus();
		return false;
	}
}
</script>
</head>
<body>
<div id="middleWrap">
		<div class="head"><h2>EDIT EMPLOYEE ATTENDANCE</h2></div>

<span id="spErr"><?php echo $msg;?> </span>
		<form method="post" name="frmManu"
			action="emp_attendance.php?empID=<?php echo makeSafe($empID);?>&act=<?php echo makeSafe($act); ?>"  onsubmit="return validate(this);">
			<input type="hidden" name="date" id="date"
				value="<?php echo $date;?>"/>
			<table class="adminform" width="90%" align="center">

				<tr>
					<td width="25%" align="right" colspan="1"
						style="font-weight: bold;">Employee Name :</td>
					<td width="70%" colspan="4"><?php  echo $emp_name; ?></td>
				</tr>
				<tr>
					<td width="25%" align="right" colspan="1"
						style="font-weight: bold;">Department Name :</td>
					<td width="70%" colspan="4"><?php echo $dept_name; ?></td>
				</tr>
				<tr>
					<td align="right" style="font-weight: bold;">Attendance :</td>
					<td><input type='radio' id='P' name='att_type' value="P"
					<?php if($l==false) echo "checked";?> onchange="checkhrs(this);"/>Present</td>
					<td><input type='radio' id='A' name='att_type' value="A"
					<?php
					if(@$att_type=="A")
						echo "checked";
					?> onchange="checkhrs(this);"/>Absent</td>
					<td><input type='radio' id='L' name='att_type' value="L"
					<?php
					if($l==true)
						echo "checked";
					elseif(@$att_type=="L")
					echo "checked";
					?> onchange="checkhrs(this);"/>Leave</td>
					<td><input type='radio' id='H' name='att_type' value="H"
					<?php
					if($holiday=='HOLIDAY')
						echo "checked";
					elseif(@$att_type=="H")
					echo "checked";
					?> onchange="checkhrs(this);"/>Holiday</td>

				</tr>
				<tr>
				<td align="right" style="font-weight: bold;" >Work Hours :</td>
					<td colspan="3">
					<input type="text" id="hrs" name="hrs"  maxlength="5" style="max-width: 5em;" <?php if($paytype=="H") echo "readonly";?> value="<?php echo (@$hrs!="")?@$hrs:"0"; ?>"	/>Hrs
				</td>
				</tr>
				<tr>
					<td colspan="5"><?php echo $cause==""?"":"Cause: ".$cause;?><br />
						<?php 
						if($leave_approved=="Y")
							echo "Leave Status: <font color='green'>Approved</font> | ";
						elseif($leave_approved=="R")
						echo "Leave Status: <font color='red'>Rejected</font> | ";
						elseif($leave_approved=="N")
						echo "Leave Status: <font color='Blue'>Pending</font> | ";
						?>
					</td>
				</tr>
				<tr>
					<td align="center" colspan=5><?php if($paytype=="M"){ ?><input type="submit" class="btn save" value="UPDATE"
						name="B1" > <?php }?><input type=button value="CLOSE" class="btn close"
						onClick="parent.emailwindow.close()"> <input type='hidden'
						name='action' value="UPDATE" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $msg; ?>";
</script>
</html>

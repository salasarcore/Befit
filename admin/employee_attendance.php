<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 */
//include("modulemaster.php");
/*if(in_array(module_employee_attendance,$modules)){ 
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_employee_attendance))
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
?>
<script  language="javascript">

function getdiff(obj)
{

	var start=$("#time_from"+obj).val();
	var end=$("#time_to"+obj).val();


	var date=new Date();
	var curr_date = date.getDate();
	var curr_month = date.getMonth();
	curr_month = curr_month + 1;
	var curr_year = date.getFullYear();

	//Now i can get my required date in my desired format. For Example,
	dt= curr_date + '/'+ curr_month + '/'+ curr_year;
	      
	 
	    date1 = new Date(dt+" "+start);
	      date2 = new Date(dt+" "+end);
	
   var diffDays = date2.getTime() - date1.getTime();
   var hours=Math.floor(diffDays/(60*60*1000));
   var mins=(diffDays/(60*1000))-(60*hours);
   return hours+ "."+ mins;
}

function  populateEmployee(act)
{
	if(document.empattendance.adt.value!="" && document.empattendance.dept.value!=0)
	 {
    	var empnamefilter=document.empattendance.empname.value;
    	var att_statusfilter=document.empattendance.att_status.value;
    	document.getElementById("empid").value="";
		var url="ajax/session_employee_attendance_list.php?dept_id="+document.getElementById("dept").value+"&date="+document.getElementById("adt").value+"&empname="+empnamefilter+"&att_status="+att_statusfilter;
		makePOSTRequest(url,'','stuList');
		
		}
}
  
function checkstatus(frm)
{
	if($('#counterror').val()!='0')
	{
		alert('Attendance has not marked for employees lectures');

		return false;
	}
	else{
	var err="";
	$('input:radio').each(function() {

		var radiocheck=$(this).val().charAt(0);
		var radioval=$(this).val().substring(1);


	   if(radiocheck=='P' && $(this).is(':checked') && ($.trim($("#hrs"+radioval).val())=="" || parseFloat($("#hrs"+radioval).val())==0))
		   err="error";
	});
	if(err!=""){
		alert('Working hours should not be zero or blank');
		err="";
		return false;
	}
	}
}
function selectID(objChk)
{
	document.getElementById("empid").value=objChk;
	$('#employeelist tr').on('click', function() {
		$('#employeelist tr').removeClass('selected');
		$(this).toggleClass('selected');
		$(this).find('input[name=rdoID]').attr('checked', 'checked');
		});
}

function ActionScript(act)
{
	if(document.getElementById("empid").value=="" && act =="EDIT")
	{
	  	alert("Please select employee attendance entry.");
	}
	else
	{
		url="popups/emp_attendance.php?act="+act+"&empID="+document.getElementById("empid").value+"&date="+document.getElementById("adt").value;
		open_modal(url,600,320,"EMPLOYEE ATTENDANCE")
		return true;
	}
}

function checkfilter()
{
	var empname=document.empattendance.empname.value;
	var att_status=document.empattendance.att_status.value;
	var date=document.empattendance.adt.value;
	var dept=document.empattendance.dept.value;
if(dept!=0 && date!="")
{
	if(empname.trim()=="" && att_status=="")
	{
		document.empattendance.empname.value="";
		alert('Enter search criteria to filter ther records');
		return false;
	}
}
else
{
	alert('Please select department and attendance date to filter the records');
	return false;
}
populateEmployee('pop');
}

function checkhrs(objchk)
{
	if(objchk.value.charAt(0)=='P'){
		document.getElementById("hrs"+objchk.value.substr(1)).disabled = false;
		document.getElementById("hrs"+objchk.value.substr(1)).value="0";
	}
	else
	{
		document.getElementById("hrs"+objchk.value.substr(1)).value="0";
		document.getElementById("hrs"+objchk.value.substr(1)).disabled = true;
		
	}
}

</script>

<div class="page_head">
<div id="navigation">
	<a href="pages.php">Home</a><a> Payroll</a> <span style="color: #000000;"> Employees Attendance</span>
</div>
<h2>Employees Attendance</h2>
</div>
<br>
<?php 

$empname="";
$att_status="";

if(isset($_REQUEST['submit']))
{
if(@$_REQUEST['act']=="save")
{
	$count=0;
	$ids=makeSafe($_POST['txtMax']);
	$att_date=makeSafe($_POST['adt']);
	$deptid=makeSafe($_POST['dept']);
	if($deptid==0)
	{
		echo "<div class='error'>Please select department</div>";
	}
	elseif ($att_date=="")
	{
		echo "<div class='error'>Please select date.</div>";
	}
	else 
{
	$sql="SELECT eatt.* FROM emp_attendance_register as eatt
	join employee as e on eatt.empid=e.empid
	join employee_department as d on e.department_id=d.department_id and e.department_id='".$deptid."' join emp_sal_settings as es on es.empid=eatt.empid and es.pay_type='M'
	where att_date='". $att_date ."'";
	 

	$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	if(mysql_affected_rows($link)>0)
	{
		$count = mysql_num_rows($resC);
	}
	if($count>0)
	{
		echo "<div class='error'>Employee attendance is already marked for ".date("d-m-Y", strtotime($att_date)).". To update entry, select employee and update it.</div>";
	}
	else
	{
		$err="";
		$today=mysql_fetch_assoc(mysql_query("select curdate() as today"))['today'];
		if($today!=$att_date)
			if(($level!='Super Admin')&&($level!='Admin'))
				$err="error";
		
		if($err==""){


				for($i=1;$i<=$ids;$i++)
				{
					
					$att_type=substr(makeSafe($_POST[$i]),0,1);
					$stu_id=substr(makeSafe($_POST[$i]),1);
					
					$hrsw="hrs".$stu_id;
					
					 $workhrs=makeSafe(@$_POST[$hrsw]);
					
					if($att_type=='P' && (trim($workhrs)=="" || floatval(trim($workhrs))==0))
						$err.="Working hours should not be zero or blank";
	
				}
				if($err==""){
					for($i=1;$i<=$ids;$i++)
					{
					
					$att_type=substr(makeSafe($_POST[$i]),0,1);
					$stu_id=substr(makeSafe($_POST[$i]),1);
					$hrsw="hrs".$stu_id;
					$workhrs=makeSafe(@$_POST[$hrsw]);
					
					
					/**
					* code written below for the purpose, if user put value as 6.60 hrs, then it should be considered as 7hrs.
					* so here, i have exploded the value using (.) symbol and checked whether second index value is 60 or not.
					* If it is 60 then first index will be incremented by 1 to make it absolute working hours.
					*/
					$sql="select es.* from emp_sal_settings as es where es.empid=".$stu_id;
					$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
					$row=mysql_fetch_array($res);
					if($row['pay_type']=="M")
					{
						$sql="insert into emp_attendance_register(empid,att_date,att_type,updated_by,work_hours) values(".$stu_id.",'".$att_date."','".$att_type."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."','".$workhrs."')";
						$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
					}
					if($row['pay_type']=="H" && $att_type=="A")
					{
						$sql="insert into emp_attendance_register(empid,att_date,att_type,updated_by,work_hours) values(".$stu_id.",'".$att_date."','".$att_type."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."','".$workhrs."')";
						$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
					}
					}
					echo "<div class='success'>".($i-1)." Employees Attendance Data Saved</div>";
					}
				else
					echo "<div class='error'>$err</div>";
				}
				else
						echo "<div class='error'>You Are Only Authorised To Mark Today's Attendance. Please Contact Administrator.</div>";

	}
}
}
}

?>
<form action="pages.php?src=employee_attendance.php&act=save" method="post" name="empattendance" onsubmit="return checkstatus(this);">
<div style="padding-left: 10px;">
	<table width="100%" class="adminform1">
		<tr>
			<td valign="top"><span class="redstar">Select Department :</span> <select
				name="dept" id="dept" onchange="populateEmployee('pop')">
					<option value="0">--Select Department--</option>
					<?php

					$sql="select d.department_name,d.department_id from employee_department d  order by department_name";
					$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
					while($row=mysql_fetch_array($res))
						echo "<option value='".$row['department_id']."'>".$row['department_name']."</option>";
					?>
			</select> <span class="redstar">Date :</span> 
			<input name="adt" type="text" class="date" id="adt" value="<?php echo @$adt;?>"	size="11" onchange="populateEmployee('pop')" readonly/> 
			<script	type="text/javascript">
				  $(function() {
						$( "#adt" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
						});
					});

				  </script>
			</td>
			<td>
				<div id="option_menu">
					<a class="btn btn-info" href="javascript:void(0);"
						onClick="javascript:ActionScript('EDIT');">Edit</a>

				</div>
			</td>
		</tr>
		<tr>
			<td>Search Employee Name : <input type="text" name="empname" id="empname" value="" /> 
				<select name="att_status" id="att_status">
					<option value="">--Select Attendance Status--</option>
					<option value="P">Present</option>
					<option value="A">Absent</option>
					<option value="L">Leave</option>
					<option value="H">Holiday</option>
				</select>
				<input type="button" name="search" id="search" value="Go" class="btn btn-info" onclick="return checkfilter();"/>
			</td>
		</tr>
	</table>
	<br>
	<div id="stuList"></div>
</form>
</div>
<input type="hidden" name="empid" id="empid" value="" />

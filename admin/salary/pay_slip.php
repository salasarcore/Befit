<?php 

@$paID = makeSafe($_POST['paID']);
include_once("functions/common.php");

/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_pay_slip,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_pay_slip))
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
<script>
function populate_emp(sel)
{
	 var dept_id = sel.options[sel.selectedIndex].value;
	 if (dept_id!=0)
	 {
	 var url="salary/ajax/payslip_get_emp_list.php?dept_id="+dept_id;
	 makePOSTRequest(url,'','empList');
	 }
		else
			$("#empList").html("");
}

function checkMe(frm){

	
	if(frm.department.value==0)
	{
		alert('Please select department');
		frm.department.focus();
		return false;
	}
	 if(frm.year.value==0)
	{
		alert('Please select year');
		frm.year.focus();
		return false;
	}
	 if(frm.month.value==0)
	{
		alert('Please select month');
		frm.month.focus();
		return false;
	}
	var h = document.getElementById('hidd').value;

	 if(h>0)
	{
		var r = document.getElementsByName("rdoID");
		var c = -1;

		for(var i=0; i < r.length; i++){
		   if(r[i].checked) {
		      c = i;  } }
		if (c == -1) {  alert("please select employee"); 
		return false;
		}
		
	}
	 if(h==0)
	{
		alert('No employee in this department');
		return false;
	}
	
	else 

	{
		a="salary/reports/print_pay_slip.php?department="+document.getElementById("department").value+"&emp_id="+document.getElementById("paID").value+"&year="+document.getElementById("year").value+"&month="+document.getElementById("month").value;
	  	window.open(a,'PAY_SLIP','width=800,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,copyhistory=no,resizable=no');
	  	document.getElementById('department').value="0";
	}
		
	return true;
}

function selectID(objChk)
{
	document.getElementById("paID").value=objChk;
	
}
</script>
<div class="page_head">
<div id="navigation"><a href="admin.php"> Home</a><a> Payroll</a><a> Reports</a> <span style="color: #000000;"> Pay Slip</span></div>
<h2>Pay Slip</h2>
</div>
<br>
<form name="records" method="POST" onsubmit="return checkMe(this);">

	<table width="500px" class="shadow adminform" align="left">
	<tr>
		<td align="right"> Select Department :</td>
		<td>
			<?php 
				$department_id=@$_POST['department'];
				if(@$department_id=="") $department_id=0;
				echo "<select size='1' name='department' id='department'  onChange='populate_emp(this);'>";
				echo "<option value='0' selected>--Select Department--</option>";
				$sql="select department_id,department_name  from employee_department where br_id=".$_SESSION['br_id']." order by department_name";
				$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
					while($row1=mysql_fetch_array($res))
					{
						echo"<option value='".$row1['department_id']."'";
						if($department_id==$row1['department_id']) echo "selected";  echo">".$row1['department_name']."</option>";
					}
				echo"</select>";

				?>
		</td>

	</tr>
	<tr>
		
			<td align="right">Salary Year :</td>
			<td>
				<select name="year" id="year" >
				
				<option value="0">Select Year</option>
				<option value="2013">2013</option>
				</select>
			</td>
	</tr>
	<tr>
			<td align="right">Salary Month :</td>
			<td>
			<select name="month" id="month">
				
				<option value="0">Select Month</option>
				<option value="January">January</option>
				<option value="February">February</option>
				<option value="March">March</option>
				<option value="April">April</option>
				<option value="May">May</option>
				<option value="June">June</option>
				<option value="July">July</option>
				<option value="August">August</option>
				<option value="September">September</option>
				<option value="October">October</option>
				<option value="November">November</option>
				<option value="December">December</option>
				</select>
			
			</td>
	</tr>	
	<tr><td colspan="2" align="right"><input type="submit" value="PRINT PAY SLIP"  /></td></tr>
	</table>
	<table width="900px" class="adminform" align="center">
	<tr>
	<td> <div id="empList"></div></td>
	</tr>
	</table>
	<input type="hidden" name="paID" id="paID" value="" />
	
</form>	
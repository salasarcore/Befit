<?php 
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_scroll_for_next_month,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin'  && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_scroll_for_next_month))
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
$Errs="";
if(makeSafe(@$_GET['act'])=="GENERATE")
{
	$month=makeSafe(@$_POST['month']);
	$year=makeSafe(@$_POST['year']);
	$department_id=makeSafe(@$_POST['department']);

	if($department_id==0)
		$Errs="<div class='error'>Please select department</div>";
	elseif($year=="0")
		$Errs="<div class='error'>Please select year</div>";
	elseif($month=="0")
		$Errs="<div class='error'>Please select month</div>";
	else
	{
		$sqlWhere="";
		$sql="select m.empid from monthly_salary_earnings m,employee e,employee_department d,mst_branch b where d.department_id=e.department_id and m.empid=e.empid and e.br_id=b.br_id and m.month='".$month."' and m.year=".$year." and b.br_id=".$_SESSION['br_id']."";
		if($department_id>0) $sqlWhere =" and e.department_id=".$department_id." ";
		$sql=$sql." ".$sqlWhere;
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		if(mysql_affected_rows($link)>0)
			$Errs="<div class='error'>Scroll already generated for the Month & Year, Try Department wise</div>";
		else
		{
			$sql="select employee.empid from employee,mst_branch,employee_department where employee_department.department_id=employee.department_id and employee.br_id=mst_branch.br_id  and mst_branch.br_id=".$_SESSION['br_id']." and stop_salary='N'";
			if($department_id>0) $sqlWhere =" and employee.department_id=".$department_id." ";
			$sql=$sql." ".$sqlWhere;
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$i=mysql_affected_rows($link);
			while($row=mysql_fetch_array($res))
			{
				// adding earnings
				$sqlE="INSERT INTO monthly_salary_earnings(empid, year, month,  head_type, head_abvr, amount) ";
				$sqlE = $sqlE." select ".$row['empid'].",".$year.",'".$month."',head_name,head_abvr,amount from sal_salary_info where salary_type='E' and empid=".$row['empid'];
				$resE=mysql_query($sqlE) or die("Unable to connect to Server, We are sorry for inconvienent caused");
				// adding Deductions
				$sqlD="INSERT INTO monthly_salary_deductions(empid, year, month,  head_type, head_abvr, amount) ";
				$sqlD .=" select ".$row['empid'].",".$year.",'".$month."',head_name,head_abvr,amount from sal_salary_info where salary_type='D' and empid=".$row['empid'];
				$resD=mysql_query($sqlD) or die("Unable to connect to Server, We are sorry for inconvienent caused");
				// adding Loan
					
				$sqlL="select emp_loan_id, empid, loan_purpose, loan_abvr, total_amount, total_inst_no, monthly_inst,q_amount, current_inst_no from sal_salary_info_loan where empid=".$row['empid']." and total_inst_no >=current_inst_no";
				$resL=mysql_query($sqlL) or die("Unable to connect to Server, We are sorry for inconvienent caused");
				while($rowL=mysql_fetch_array($resL))
				{

					$emp_loan_id=$rowL['emp_loan_id'];
					$current_inst_no=$rowL['current_inst_no'];
					$total_inst_no=$rowL['total_inst_no'];
					$monthly_inst=$rowL['monthly_inst'];
					$q_amount=$rowL['q_amount'];
					$total_amount=$rowL['total_amount'];
					if(($total_amount-$q_amount)<$monthly_inst) $monthly_inst=($total_amount-$q_amount);
						
						
					$sqlE="INSERT INTO monthly_salary_loan_recovery(empid,emp_loan_id, year, month, inst_no, amount,q_amount)
						VALUES( ".$row['empid'].",".$emp_loan_id.",".$year.",'".$month."',".($current_inst_no+1).",".$monthly_inst.",".($q_amount+$monthly_inst).")";
					$resE=mysql_query($sqlE) or die("Unable to connect to Server, We are sorry for inconvienent caused");
					$sqlUpdateLoan="update sal_salary_info_loan set q_amount=q_amount+$monthly_inst,current_inst_no=current_inst_no+1 where emp_loan_id=".$emp_loan_id;
					$resE=mysql_query($sqlUpdateLoan) or die("5.Unable to connect to Server, We are sorry for inconvienent caused");

				}
				// end loan adding Loan
			}//where of employee selection
			if($i>0)
			$Errs="<div class='success'> $i employee scrolled</div>";
			else
			$Errs="<div class='error'> No employees in this department</div>";

		}//whether scroll generated earlier or not


	}//else checking blank fields



}

?>

<script type="text/javascript">
function populateemployee()
{

		document.getElementById("loading").innerHTML = "<br><img src='../images/loading.gif' alt='Loading..'><br> Please Wait ..";
		var value=$("#department option:selected").val();
		if(value!=0){
			    $.ajax({
			    	type: "GET",
			    	url:"salary/ajax/get_employee_list.php",
				    data: "dept_id="+$("#department option:selected").val(),
				    success: function(html){
				    	$("#loading").html(html);
					}
		    });
		}
		else
			$("#loading").html("");
	
}
function checkMe(frm){
	if(frm.department.value==0)
	{
		alert('Please select department');
		frm.department.focus();
		return false;
	}
	else if(frm.year.value==0)
	{
		alert('Please select year');
		frm.year.focus();
		return false;
	}
	else if(frm.month.value==0)
	{
		alert('Please select month');
		frm.month.focus();
		return false;
	}
}
</script>
<div class="page_head">
<div id="navigation"><a href="pages.php">Home</a><a> Payroll</a>  <span style="color: #000000;">Scroll for Next Month</span></div>
<h2>Scroll for Next Month</h2>
</div>
<br>
<span id="spErr"><?php echo $Errs;?> </span>
<form name="records" action="pages.php?src=salary/scroll_for_next_month.php&act=GENERATE" method="POST" onsubmit="return checkMe(this);">
<table >
<tr><td valign="top">
	<table width="500px" class="shadow adminform" align="center">
	<tr>
		<td align="right"> Select Department :</td>
		<td>
			<?php 
					echo "<select size='1' name='department' id='department' onchange='javascript:populateemployee();' >";
				echo "<option value='0' selected>--Select Department--</option>";
				$sql="select department_id,department_name  from employee_department where br_id=".$_SESSION['br_id']." order by department_name";
				$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
					while($row1=mysql_fetch_array($res))
					{
						echo"<option value='".$row1['department_id']."'";
						echo">".$row1['department_name']."</option>";
					}
				echo"</select>";

				?>
		</td>
	</tr>
	<tr>
		
			<td align="right">Salary Year :</td>
			<td>
				<select name="year">
				
				<option value="0">Select Year</option>
				<option value="2013">2013</option>
				</select>
			</td>
	
	</tr>
	<tr>
			<td align="right">Salary Month :</td>
			<td>
			<select name="month">
				
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
	<tr><td colspan="2" align="right"><input type="submit" value="GENERATE PAYMENT" /></td></tr>
	</table></td><td valign="top" style="padding-left: 10px;">
	<div id="loading" style="width: 500px;"></div>
	</td></tr>
	</table>
</form>	
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
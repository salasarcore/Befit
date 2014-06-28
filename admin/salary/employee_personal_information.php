<?php 
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_employee_personal_information,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin'  && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_employee_personal_information))
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
function selectID(objChk)
{
document.getElementById("empID").value=objChk;
}
function SubmitPage(Page)
{
document.getElementById("txtPage").value=Page;
document.myForm.submit();
}
<?php 
/**
 * Commented this code and can be used for further reference.
 */
?>
/*function ActionScript(act)
{
	if(document.getElementById("empID").value=="" && act !="add")
	  alert("Please select an Employee");
	else
	{
		url="popups/employee.php?act="+act+"&empID="+document.getElementById("empID").value;
		open_modal(url,730,550,"EMPLOYEE")
		return true;
	  }
}*/
function Earnings(act)
{
	if(document.getElementById("empID").value=="" )
	  alert("Please select an Employee");
	else
	{
		url="salary/popups/pay_employee_earnings.php?act="+act+"&empID="+document.getElementById("empID").value;
		open_modal(url,600,500,"EMPLOYEE EARNINGS")
		return true;
	  }
}
function Deduction(act)
{
	if(document.getElementById("empID").value=="" )
	  alert("Please select an Employee");
	else
	{
		url="salary/popups/pay_employee_deductions.php?act="+act+"&empID="+document.getElementById("empID").value;
		open_modal(url,400,200,"EMPLOYEE PAY DEDUCTION")
		return true;
	  }
}
function Loan(act)
{
	if(document.getElementById("empID").value=="" )
	  alert("Please select an Employee");
	else
	{
		url="salary/popups/pay_employee_loan.php?act="+act+"&empID="+document.getElementById("empID").value;
		open_modal(url,600,350,"EMPLOYEE LOAN/ADVANCE")
		return true;
	  }
}
function editearnings(act,id,head,empid)
{
	
		url="salary/popups/pay_employee_earnings.php?act="+act+"&empID="+empid+"&head_name="+head+"&sal_info_id="+id;
		open_modal(url,600,500,"EMPLOYEE EARNINGS")
		return true;
	 
}
function editdeduction(act,id,head,empid)
{
	if(document.getElementById("empID").value=="" )
	  alert("Please select an Employee");
	else
	{
		url="salary/popups/pay_employee_deductions.php?act="+act+"&empID="+empid+"&head_name="+head+"&sal_info_id="+id;
		open_modal(url,400,200,"EMPLOYEE PAY DEDUCTION")
		return true;
	  }
}
function editloan(act,id,head,empid)
{
	if(document.getElementById("empID").value=="" )
	  alert("Please select an Employee");
	else
	{
		url="salary/popups/pay_employee_loan.php?act="+act+"&empID="+empid+"&loan_purpose="+head+"&emp_loan_id="+id;
		open_modal(url,600,350,"EMPLOYEE LOAN/ADVANCE")
		return true;
	  }
}
function StopSalary(act)
{
	if(document.getElementById("empID").value=="" )
	  alert("Please select an Employee");
	else
	{
		url="salary/popups/stop_salary.php?act="+act+"&empID="+document.getElementById("empID").value;
		open_modal(url,500,300,"STOP SALARY")
		return true;
	  }
}
</script>
<div class="page_head">
<div id="navigation"><a href="admin.php">Home</a><a> Payroll</a>  <span style="color: #000000;">Employee Pay Structure</span></div>

<form name="myForm" action="admin.php?src=salary/employee_personal_information.php" method="POST">

<table width="100%" class="adminform1">
<tr>
	
	<td><h2>Employee Pay Structure</h2>
	</td>
	
	<td>
	<div id="option_menu">
	<?php 
	/**
	 * Commented this code and can be used for further reference.
	 */
	?>
	<!--  	<a  class="addnew" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
		<a  class="edit" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a> -->
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:Earnings('add');">Earnings</a>
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:Deduction('add');">Deductions</a>
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:Loan('add');">Loan & Advance</a>
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:StopSalary('edit');">Stop Salary</a>
	</div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">
Select Department : <?php 
$department_id=makeSafe(@$_POST['department']);
if(@$department_id=="") $department_id=0;
echo "<select size='1' name='department' id='department' onchange='javascript:SubmitPage(1);'>";
echo "<option value='0' selected>--Show All Department--</option>";
$sql="select department_id,department_name  from employee_department where br_id=".$_SESSION['br_id']." order by department_name";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['department_id']."'";
		if($department_id==$row1['department_id']) echo "selected"; echo">".$row1['department_name']."</option>";
	}
echo"</select>";

?>
</div>
<table width="100%" cellspacing="1" class="table table-bordered"" style="cursor: pointer;">
  <thead>		
   <tr>
    <th>#</th>
    <th>NAME</th>
    <th>PAY TYPE</th>
	<th>TOTAL HOURS</th>
    <th>EARNINGS</th>
    <th>DEDUCTIONS</th>
    <th>LOANS</th>
    <th>NET PAYABLE</th>
    <th>SALARY STATUS</th>
   </tr>
  <thead>
  <tbody>
   <?php
   $pageNum = 1;
$rowsPerPage=20;
$sqlWhere="";

	if(makeSafe(isset($_POST['txtPage'])))	    $pageNum = makeSafe($_POST['txtPage']);
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($offset<0) $offset=0;
	$i=0;
		$sql="select employee.empid,emp_id,activated,emp_name,designation_name,department_name,stop_salary,pay_stop_reason";
		$sql .=" from employee,mst_designations,mst_branch,employee_department where employee_department.department_id=employee.department_id and employee.br_id=mst_branch.br_id  and employee.br_id=".$_SESSION['br_id']." and mst_designations.designation_id=employee.designation_id";
	if($department_id>0)
			$sqlWhere =" and employee.department_id=".$department_id." ";
			
		$sqlOrder =" order by employee.empid desc";
		$limit=" LIMIT $offset, $rowsPerPage ";
	    $sql=$sql." ".$sqlWhere.$sqlOrder.$limit;
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
       
		
		while($row=mysql_fetch_array($res))
		{
		$i=$i+1;
		$total_earnings=0;
        $total_deductions=0;
        $total_loan=0;
        
        $sql="select * from emp_sal_settings where empid=".$row['empid']." group by empid";
        $result=mysql_query($sql,$link);
        $payres=mysql_fetch_assoc($result);
        $pay_type=$payres['pay_type'];
        
	?>

	<tr class=<?php if($i%2==0) echo "row0"; else echo "#row1"; ?> onclick="selectID('<?php echo $row['empid']; ?>');">
	 <td align='center'><input type="radio" name="rdoID" value="<?php echo $row['empid']; ?>" id="rdoID" onclick="selectID('<?php echo $row['empid']; ?>');"/></td>
		<td style="width: 150px;">
			<?php echo $row['emp_name']; ?>
			<?php echo $row['emp_id']; ?><br />
			<?php echo $row['designation_name']; ?><br />
			<?php echo $row['department_name']; ?>
		</td>
		<td align="center" style="width: 70px;"><?php echo ($pay_type=="H")?  "Hourly" : "Monthly"; ?></td>
		<td align='center'>
		<?php
			$sqlhrs=mysql_query("select IFNULL(TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(replace(work_hours, '.', ':') AS TIME)))),'%k.%i'),0) as work_hours from emp_attendance_register where empid=".$row['empid']." and MONTH(att_date) = MONTH(CURDATE()) AND YEAR(att_date) = YEAR(CURDATE())");
			echo $total_hours=mysql_fetch_assoc($sqlhrs)['work_hours']." Hrs";
		?>
		</td>
		<td  valign="top">
					<table width="100%" border="0">
						   <?php
						  	$sqlE="SELECT * from sal_salary_info where salary_type='E' and empid=".$row['empid'];
							$resE=mysql_query($sqlE) or die("Unable to connect to Server, We are sorry for inconvienent caused");
							while($rowE=mysql_fetch_array($resE))
								{
								?>
							  <tr>
								<td><?php echo $rowE['head_name'];?></td>
								<td><?php echo $rowE['head_abvr'];?></td>
								<td align="right"><?php echo $rowE['amount'];?></td>	
								<td align="center"><a href="javascript:void(0);"/><img alt="Edit" src="css/classic/edit.png" width="14px" onclick="editearnings('edit','<?php echo $rowE['sal_info_id'];?>','<?php echo $rowE['head_name'];?>','<?php echo $row['empid'];?>');" /></a><a href="javascript:void(0);"/><img alt="Delete" src="css/classic/close.png" style="border-left:1px solid black;padding-left:3px;" width="14px" onclick="editearnings('delete','<?php echo $rowE['sal_info_id'];?>','<?php echo $rowE['head_name'];?>','<?php echo $row['empid'];?>');" /></a>
								</td>	
											
							  </tr>
							 
							  <?php
							  $total_earnings=$total_earnings+$rowE['amount'];
							  }
							   
							   echo"<tr><td colspan=2>TOTAL:</td><td align='right'>".number_format($total_earnings,2)."</td></tr>";
							  ?>
					</table>		  
							  <!--END EARNINGS-->
		
		</td>
		<td valign="top">
				<table width="100%" border="0" class="adminform">
						   <?php
						  	$sqlE="SELECT * from sal_salary_info where salary_type='D' and empid=".$row['empid'];
							$resE=mysql_query($sqlE) or die("Unable to connect to Server, We are sorry for inconvienent caused");
							while($rowE=mysql_fetch_array($resE))
								{
								?>
							  <tr>
								<td><?php echo $rowE['head_name'];?></td>
								<td><?php echo $rowE['head_abvr'];?></td>
								<td align="right"><?php echo $rowE['amount'];?></td>
								<td align="center"><a href="javascript:void(0);"/><img alt="Edit" src="css/classic/edit.png"  width="14px" onclick="editdeduction('edit','<?php echo $rowE['sal_info_id'];?>','<?php echo $rowE['head_name'];?>','<?php echo $row['empid'];?>');" /></a><a href="javascript:void(0);"/><img alt="Delete" src="css/classic/close.png" style="border-left:1px solid black;padding-left:3px;" width="14px" onclick="editdeduction('delete','<?php echo $rowE['sal_info_id'];?>','<?php echo $rowE['head_name'];?>','<?php echo $row['empid'];?>');" /></a>
								</td>									
							  </tr>
							  <?php
							  $total_deductions=$total_deductions+$rowE['amount'];
							  }
							   echo"<tr><td colspan=2>TOTAL:</td><td align='right'>".number_format($total_deductions,2)."</td></tr>";
							  ?>
					</table>		
		
		</td>
		<td valign="top">
				<table width="100%" border="0">
						   <?php
						  	$sqlE="SELECT emp_loan_id, loan_purpose, total_amount,total_inst_no,monthly_inst,current_inst_no from sal_salary_info_loan where current_inst_no<=total_inst_no and empid=".$row['empid'];
							$resE=mysql_query($sqlE) or die("Unable to connect to Server, We are sorry for inconvienent caused");
							while($rowE=mysql_fetch_array($resE))
								{
								?>
							  <tr>
								<td><?php echo $rowE['loan_purpose'];?></td>
								<td><?php echo $rowE['monthly_inst'];?></td>
								<td><?php echo $rowE['current_inst_no'];?> of <?php echo $rowE['total_inst_no'];?></td>
								<td align="right"><?php echo $rowE['total_amount'];?></td>		
								<td align="center">
								<a href="javascript:void(0);"/><img alt="Edit" src="css/classic/edit.png"  width="14px" onclick="javascript:editloan('edit','<?php echo $rowE['emp_loan_id'];?>','<?php echo $rowE['loan_purpose'];?>','<?php echo $row['empid'];?>');" /></a>
								<a href="javascript:void(0);"/><img alt="Delete" src="css/classic/close.png" width="14px" style="border-left:1px solid black;padding-left:3px;" onclick="javascript:editloan('delete','<?php echo $rowE['emp_loan_id'];?>','<?php echo $rowE['loan_purpose'];?>','<?php echo $row['empid'];?>');" /></a>
								</td>
													
															
							  </tr>
							  <?php
							  $total_loan=$total_loan+$rowE['monthly_inst'];
							  }
							  if($total_loan>0)
							  echo"<tr><td>TOTAL:</td><td align='right'>".number_format($total_loan,2)."</td></tr>";
							  ?>
					</table>
		
		
		
		</td>
		<td>
		<?php echo"<font color='red'><b>".number_format(($total_earnings-$total_deductions-$total_loan),2)."</b></font>";?>
		</td>
		<td>
			<?php
			if($row['stop_salary']=="Y")
				echo "<font color='red'><b> Salary Stopped<br />".$row['pay_stop_reason']."</b></font>";
			else
			   echo "<font color='green'><b> RUNNING</b></font>";
			?>
		</td>
	</tr>
	<?php
	}
	?> 
  </tbody>
  <tfoot><tr>
<th colspan="9">
	<?php 
$sql="SELECT count(emp_id) as numrows";
$sql .=" from employee,mst_designations,mst_branch,employee_department where employee_department.department_id=employee.department_id and employee.br_id=mst_branch.br_id  and mst_branch.br_id=".$_SESSION['br_id']." and mst_designations.designation_id=employee.designation_id";
 $sql=$sql." ".$sqlWhere;		
$result  = mysql_query($sql) or die('Error, query failed');
$row     = mysql_fetch_array($result, MYSQL_ASSOC);
$numrows = $row['numrows'];
if($numrows>0){
$maxPage = ceil($numrows/$rowsPerPage);
echo "TOTAL : ".$numrows." | PAGE(S) : ".$maxPage;
$nav  = '';
for($page =$pageNum-4;  $page <= $pageNum+4 ; $page++)
{
if($page>0)
   if ($page == $pageNum)
      $nav .= " $page "; // no need to create a link to current page
    else
      $nav .= " <a class='mylink' href=\"javascript:SubmitPage('$page')\">$page</a> ";
    if($page>=$maxPage) break;
}

if ($pageNum > 1)
{
   $page  = $pageNum - 1;
   $prev  = " <a class='mylink' href=\"javascript:SubmitPage('$page')\" >[Prev]</a> ";

   $first = " <a class='mylink' href=\"javascript:SubmitPage('1')\" >[First Page]</a> ";
} 
else
{
   $prev  = '&nbsp;'; // we're on page one, don't print previous link
   $first = '&nbsp;'; // nor the first page link
}

if ($pageNum < $maxPage)
{
   $page = $pageNum + 1;
   $next = " <a class='mylink' href=\"javascript:SubmitPage('$page')\" >[Next]</a> ";

   $last = " <a class='mylink' href=\"javascript:SubmitPage('$maxPage')\" >[Last Page]</a> ";
} 
else
{
   $next = '&nbsp;'; // we're on the last page, don't print next link
   $last = '&nbsp;'; // nor the last page link
}

echo $first . $prev . $nav . $next . $last;
echo "";
}
else 
	echo "NO RECORDS FOUND";
?>

<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>'/>
</th>
  </tr>
</tfoot>
</th>
  </tr>
</tfoot>

</table>
<input type="hidden" name="empID" id="empID" value="" />
</form>
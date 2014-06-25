<?php
@session_start ();
include ('../../conn.php');
//include ('../../check_session.php');
include ("../../functions/common.php");
include ("../../functions/comm_functions.php");
$head_name = "";
$amount = "";
makeSafe ( extract ( $_REQUEST ) );
$head = "";
$Errs = "";
if (trim(@$action == "SAVE") || trim(@$action) == "UPDATE") {
	makeSafe ( extract ( $_POST ) );
	if ($head == "0")
		$Errs = "<div class='error'>Please Select Head</div>";
	elseif (trim ( $amount ) == "")
		$Errs = "<div class='error'>Please Enter Amount</div>";
	elseif (! is_numeric ( trim ( $amount ) ))
		$Errs = "<div class='error'>Amount Should Be Numeric</div>";
	else {
		$headval = explode ( "#", @$head );
		$head_name = @$headval[0];
		$head_abbvr = @$headval[1];
		if (trim($action) == "SAVE") {
			
			$sql = "select * from  sal_salary_info where head_name='" . $head_name . "' and empid=".$empID." and salary_type='E'";
			$res = mysql_query ( $sql, $link );
				if (mysql_affected_rows ( $link ) > 0)
					$Errs = "<div class='error'>Head already exist</div>";
				else {
					$sal_info_id=getNextMaxId("sal_salary_info","sal_info_id")+1;
					$sql = "insert into sal_salary_info(sal_info_id,empid,head_name,head_abvr,amount,salary_type,updated_by)";
					$sql = $sql . " values(".$sal_info_id." ,". $empID . ",'" . $head_name . "','" . $head_abbvr . "','" . $amount . "','E','" . $_SESSION ['emp_name'] . "')";
					$res = mysql_query ( $sql, $link );
					if(mysql_affected_rows($link)>0){
						$Errs= "<div class='success'>Record Saved Successfully: ".$head_name."</div>";
						$head_name="0";
						$amount="";
					}
				}
			
		}
		
		if (trim($action) == "UPDATE") {
			
			$sql = "select * from  sal_salary_info where head_name='" . $head_name . "' and empid=".$empID." and salary_type='E' and sal_info_id!=".$sal_info_id;
			$res = mysql_query ( $sql, $link );
			if (mysql_affected_rows ( $link ) > 0)
				$Errs = "<div class='error'>Head already exist</div>";
			else {
				$sqlearnings=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as earnings from sal_salary_info where salary_type='E' and sal_info_id!=".$sal_info_id." and empid=".$empID));
				$sqlded=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as deduction from sal_salary_info where salary_type='D' and empid=".$empID));
				$sqlloan=mysql_fetch_assoc(mysql_query("select IFNULL(sum(monthly_inst),0) as loan_amount from sal_salary_info_loan where total_inst_no>=current_inst_no and empid=".$empID));
				$balance=$sqlearnings['earnings']-($sqlded['deduction']+$sqlloan['loan_amount']);
				if($balance<0) $balance=$balance*(-1);
				
				if(intval($balance)<=intval($amount)){
				$sql = "update sal_salary_info set head_name='" . $head_name . "',head_abvr='" . $head_abbvr . "',amount='" . $amount . "',updated_by='" . $_SESSION ['emp_name'] . "' where sal_info_id=".$sal_info_id;
				$res = mysql_query ( $sql, $link );
				if (mysql_affected_rows ( $link ) == 0)
					$Errs = "<div class='success'>No Data Changed</div>";
				if (mysql_affected_rows ( $link ) > 0)
					$Errs = "<div class='success'>Record Updated successfully</div>";
				if (mysql_affected_rows ( $link ) < 0)
					$Errs = "<div class='error'>Record Not Updated successfully</div>";
			}
			else
				$Errs="<div class='error'>Unable To Update As Earning Amount Can Not Be Less Than Total Deduction Amount.</div>";
			}
			
		}
	}
}
if (trim(@$action) == "DELETE") {
	
	$sqlearnings=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as earnings from sal_salary_info where salary_type='E' and sal_info_id!=".$sal_info_id." and empid=".$empID));
	$sqlded=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as deduction from sal_salary_info where salary_type='D' and empid=".$empID));
	$sqlloan=mysql_fetch_assoc(mysql_query("select IFNULL(sum(monthly_inst),0) as loan_amount from sal_salary_info_loan where total_inst_no>=current_inst_no and empid=".$empID));
	$balance=$sqlearnings['earnings']-($sqlded['deduction']+$sqlloan['loan_amount']);
	if($balance>=0){
	$query   = "delete  FROM sal_salary_info  where sal_info_id=".$sal_info_id;
	$result = mysql_query ( $query ) or die ( 'Error, query failed' );
	if(mysql_affected_rows($link)>0){
		$Errs="<div class='success'>Record Deleted Successfully</div>";
		$head_name="0";
		}
}
else 
	$Errs="<div class='error'>Unable To Delete As Earning Amount Can Not Be Less Than Total Deduction Amount.</div>";
}

if (@$act == "edit" || @$act == "delete") {
	
		$query   = "SELECT * FROM sal_salary_info  where sal_info_id=".$sal_info_id;
		$result  = mysql_query($query,$link) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			{
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			 	
				$head_name=@$row['head_name'];
				$amount=@$row['amount'];
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
<link href="../../css/classic.css" type="text/css" rel="stylesheet">
<?php include('../../php/js_css_common.php');?>
<script type="text/javascript">
function validate(frm)
{
	if(frm.head.value=="0")
	{
		alert('Please Select Head');
		frm.head.focus();
		return false;
	}
	else if(frm.amount.value.trim()=="")
	{
		alert('Please Enter Amount');
		frm.amount.value="";
		frm.amount.focus();
		return false;
	}
}
function ClearField(frm){
	  frm.head.value = 0;
	  frm.amount.value = "";
	  }
	</script>
</head>
<body>
<form method="post" name="frmManu"
		action="pay_employee_earnings.php?head_name=<?php echo $head_name;?>&act=<?php echo $act; ?>&empID=<?php echo $empID; ?>&sal_info_id=<?php echo $sal_info_id; ?>"
		onsubmit="return validate(this);">
		<div id="middleWrap">
		<div class="head"><h2>EARNINGS</h2></div>

			<span id="spErr"></span>
			<?php 
			$empdetails=getDetailsById("employee","empid",$empID);
			?>
			<table width="100%">
			<tr>
			<td width="200px"  align="right">Employee Name : </td><td align="left">
			<?php echo $empdetails['emp_name']." (".$empdetails['emp_id'].")" ?>
			</td></tr>
			<tr>
			<td width="130px"  align="right">Payment Type : </td><td align="left">
			<?php 	
				$sql="select * from emp_sal_settings where empid=".$empID." group by empid";
				$result=mysql_query($sql,$link);
				$payres=mysql_fetch_assoc($result);
				$pay_type=$payres['pay_type'];
				$peramount=$payres['pay_amount'];
				echo ($pay_type=="H")?  "Hourly" : "Monthly";
				echo " basis";
				$actwrkhrs="";
			?>
			</td></tr>
			
			<?php 
			$sqlhrs=mysql_query("select IFNULL(TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(replace(work_hours, '.', ':') AS TIME)))),'%k.%i'),0) as work_hours from emp_attendance_register where empid=".$empID." and MONTH(att_date) = MONTH(CURDATE()) AND YEAR(att_date) = YEAR(CURDATE())");
			$total_hours=mysql_fetch_assoc($sqlhrs)['work_hours'];
			
			$minutes = 0;
			$hours=0;
			if (strpos($total_hours, '.') !== false)
			{
				list($hours, $minutes) = explode('.', $total_hours);
			}
			$total_minutes= $hours * 60 + $minutes;
			$totamount=0;
			if($pay_type=="M")
			{
			?>
			<tr>
			<td align="right">Total Hours Worked : </td>
			<td>
			<?php
			echo $total_hours." Hour(s) in ".date('F-Y')
			?>
			</td>
			</tr>
			<tr>
			<td width="130px"  align="right">Payable Amount : </td>
			<td align="left">
			<?php 
			echo number_format($peramount, 2);
			if($amount=="") $amount=$peramount;
			?>
			</td></tr>
			<?php 
			}
			else 
			{
			?>
			<tr>
			<td align="right">Total Hours Worked : </td><td>
			<?php
			echo $total_hours." Hour(s) in ".date('F-Y');
			?>
			</td></tr>
			<?php
		 	$sql="select IFNULL(TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(actual_outtime,actual_intime)))),'%k.%i'),0) as work_hours, sum(TIMESTAMPDIFF( MINUTE , actual_intime, actual_outtime )) as work_mins, department_name,
			sum(TIMESTAMPDIFF( MINUTE , actual_intime, actual_outtime ) * ( pay_amount / 60 )) as pay_amount
			from event as e, event_person_invites as ei, routine_class as r, emp_sal_settings as ess, mst_departments as md
			where e.event_id=ei.event_id and e.event_id=r.event_id and r.department_id=ess.study_dept_id and e.event_type='LECTURE' and md.department_id=r.department_id and MONTH(r.date) = MONTH(CURDATE()) AND YEAR(r.date) = YEAR(CURDATE())
			and ei.person_id=ess.empid  and ess.empid='$empID'  GROUP BY r.department_id";
			$resultset=mysql_query($sql,$link);
			if(mysql_num_rows($resultset)>0){?>
			<tr><td align="right" valign="top">Payable Details : </td>
			<td >
			<table width="100%" class="adminlist" style="cursor: pointer; width: 80%;">

			<tr><th>DEPARTMENT</th><th>WORK HOURS</th><th>AMOUNT</th></tr><tbody>
			<?php 
				
			while($details=mysql_fetch_assoc($resultset))
			{
				$actwrkhrs+=($details['work_mins']);
				$totamount +=$details['pay_amount'];
			?>
			<tr><td align="center"><?php echo $details['department_name']; ?></td><td align="center"><?php echo $details['work_hours']; ?></td><td align="center"><?php echo number_format($details['pay_amount'],2); ?></td></tr>
			<?php 
			}
			?>
			</tbody><tfoot>
			<tr><th style="text-align: right;">TOTAL : </th><th><?php echo date('H.i', mktime(0,$actwrkhrs)); ?></th><th><?php echo number_format($totamount, 2); ?></th></tr>
			</tfoot>
			</table>
			</td>
			</tr>
			<?php 
			}
			if($amount=="") $amount=$totamount;
			}
			?>
			<?php if((($total_minutes-$actwrkhrs)>0) && $pay_type=="H") {?><tr><td align="right">Additional Working Hours : </td><td><?php echo date('H.i', mktime(0,$total_minutes-$actwrkhrs))." Hrs";?></td></tr><?php }?>
			<tr>
			
			<td align="right" class="mandate">Head : </td>
			<td  style="padding-top: 10px;">
		 <?php
			echo "<select size='1' name='head' id='head'>";
			echo "<option value='0' selected>--Select Head--</option>";
			$sql = "select pa_abvr,pa_name  from sal_pay_allowances order by pa_name";
			$res = mysql_query ( $sql ) or die ( "Unable to connect to Server, We are sorry for inconvienent caused" );
			while ( $row1 = mysql_fetch_array ( $res ) ) {
				echo "<option value='" . $row1 ['pa_name'] . "#" . $row1 ['pa_abvr'] . "'";
				if (@$head_name == $row1 ['pa_name'])
					echo "selected";
				echo ">" . $row1 ['pa_name'] . "(" . $row1 ['pa_abvr'] . ")</option>";
			}
			echo "</select>";
			?>
					</td>
				</tr>
				<tr>
					<td align="right" class="mandate">Total Amount :</td>
					<td><input type="text" name="amount" id="amount" size="40"
						value='<?php if($amount!="") echo round($amount); ?>' maxlength="10"
						onkeypress="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')"
						onkeyup="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')" /></td>
				</tr>
				<tr>


					<td align="center" colspan=2><input type="submit" class="btn save"
						value='<?php
						if (@$act == "add")
							echo "SAVE";
						if (@$act == "edit")
							echo "UPDATE";
						if (@$act == "delete")
							echo "CONFIRM DELETE";
						?>'
						name="B1"> 
						 <?php if(@$act!="delete"){?> <input type="button" class="btn reset"	value="RESET" id="reset " name="reset"	onClick="ClearField(this.form)" /> <?php }	?>
						 <input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close();"> <input
						type='hidden' name='action'
						value='<?php
						if (@$act == "add")
							echo "SAVE";
						if (@$act == "edit")
							echo "UPDATE";
						if (@$act == "delete")
							echo "DELETE";
						?>' /></td>
				</tr>
			</table>
		</div>
	</form>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>

</body>
</html>
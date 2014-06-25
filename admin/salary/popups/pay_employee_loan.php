<?php

@session_start();
include('../../conn.php');
//include('../../check_session.php');
include ("../../functions/common.php");
$loan_purpose="0";
$total_amount="";
$total_inst_no="";
$monthly_inst="";
$current_inst_no="";
$total_amount="";
$loan_purpose_id="";
$Errs="";
makeSafe ( extract ( $_GET ) );
$action = makeSafe ( @$_POST ['action'] );

if (trim(@$action == "SAVE") || trim(@$action) == "UPDATE") 
{
	makeSafe ( extract ( $_POST ) );
	if ($head == "0")
		$Errs = "<div class='error'>Please Select Head</div>";
	elseif (trim ( $total_amount ) == "")
		$Errs = "<div class='error'>Please Enter Total Amount</div>";
	elseif (! is_numeric ( trim ( $total_amount ) ))
		$Errs = "<div class='error'>Total Amount Should Be Numeric</div>";
	elseif (trim ( $total_inst_no ) == "")
		$Errs = "<div class='error'>Please Enter Total Installment Number</div>";
	elseif (! is_numeric ( trim ( $total_inst_no ) ))
		$Errs = "<div class='error'>Total Installment Number Should Be Numeric</div>";
	elseif (trim ( $current_inst_no ) == "")
		$Errs = "<div class='error'>Please Enter Current Installment Number</div>";
	elseif (! is_numeric ( trim ( $current_inst_no ) ))
		$Errs = "<div class='error'>Current Installment Number Should Be Numeric</div>";
	elseif (trim ( $monthly_inst ) == "")
		$Errs = "<div class='error'>Please Enter Monthly Installment Amount</div>";
	elseif (! is_numeric ( trim ( $monthly_inst ) ))
		$Errs = "<div class='error'>Monthly Installment Amount Should Be Numeric</div>";
	elseif (intval($total_inst_no)<intval($current_inst_no))
		$Errs = "<div class='error'>Total Number Of Installment Can Not Be Less Than Current Installment Number</div>";
	else {
		
	
		$headval = explode ( "#", @$head );
		$loan_purpose = @$headval[0];
		$loan_abvr = @$headval[1];
		if (trim($action) == "SAVE")
		{
			
			$sql="select * from  sal_salary_info_loan where loan_purpose='".$loan_purpose."' and empid=".$empID;
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Head already exist</div>";
			else
			{
				$sqlearnings=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as earnings from sal_salary_info where salary_type='E' and empid=".$empID));
				$sqlded=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as deduction from sal_salary_info where salary_type='D' and empid=".$empID));
				$sqlloan=mysql_fetch_assoc(mysql_query("select IFNULL(sum(monthly_inst),0) as loan_amount from sal_salary_info_loan where total_inst_no>=current_inst_no and empid=".$empID));
				$balance=$sqlearnings['earnings']-$sqlded['deduction']-$sqlloan['loan_amount'];
				if($balance>=$monthly_inst){
				$emp_loan_id=getNextMaxId("sal_salary_info_loan","emp_loan_id")+1;
				$sql="insert into sal_salary_info_loan(emp_loan_id,empid,loan_purpose,loan_abvr,total_amount,total_inst_no,monthly_inst,current_inst_no,updated_by)";
				$sql = $sql ." values(".$emp_loan_id.",".$empID.",'".$loan_purpose."','".$loan_abvr."','".$total_amount."','".$total_inst_no."','".$monthly_inst."','".$current_inst_no."','".$_SESSION['emp_name']."')";
				$res=mysql_query($sql);
				if($res){
					$Errs= "<div class='success'>Record Saved Successfully</div>";
					$loan_purpose="0";
					$total_amount="";
					$head="";
					$total_inst_no="";
					$monthly_inst="";
					$current_inst_no="";
					$total_amount="";
					
				}
			}
			else
				$Errs="<div class='error'>Monthly Installment Amount is Greater Than Net Balance Amount.</div>";
			}
		}
		
		if (trim($action) == "UPDATE")
		{
			$sql="select * from  sal_salary_info_loan where loan_purpose='".$loan_purpose."' and empid=".$empID." and emp_loan_id!=".$emp_loan_id;
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>1)
				$Errs= "<div class='error'>Head already exist</div>";
			else
			{
				$sqlearnings=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as earnings from sal_salary_info where salary_type='E' and empid=".$empID));
				$sqlded=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as deduction from sal_salary_info where salary_type='D' and empid=".$empID));
				$sqlloan=mysql_fetch_assoc(mysql_query("select IFNULL(sum(monthly_inst),0) as loan_amount from sal_salary_info_loan where total_inst_no>=current_inst_no and emp_loan_id!=".$emp_loan_id." and empid=".$empID));
				$balance=$sqlearnings['earnings']-($sqlded['deduction']+$sqlloan['loan_amount']);
				if($balance>=$monthly_inst){
				$sql="update sal_salary_info_loan set loan_purpose='".$loan_purpose."',loan_abvr='".$loan_abvr."',total_amount='".$total_amount."',
				total_inst_no='".$total_inst_no."',monthly_inst='".$monthly_inst."',current_inst_no='".$current_inst_no."',updated_by='".$_SESSION['emp_name']."' where emp_loan_id=".$emp_loan_id;
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)==0)
					$Errs="<div class='success'>No Data Changed</div>";
				if(mysql_affected_rows($link)>0)
					$Errs="<div class='success'>Record Updated successfully</div>";
				if(mysql_affected_rows($link)<0)
					$Errs="<div class='error'>Record Not Updated successfully</div>";
			}
			else
				$Errs="<div class='error'>Monthly Installment Amount is Greater Than Net Payable Amount.</div>";
			}
		}
	}
}
if (trim(@$action) == "DELETE")
{
		
		$query   = "delete  FROM sal_salary_info_loan  where emp_loan_id=".$emp_loan_id;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0){
				$Errs="<div class='success'>Record Deleted Successfully</div>";
		$loan_purpose="0";
		}
}

if(@$act=="edit" || @$act=="delete" )
{

		$query   = "SELECT  * FROM sal_salary_info_loan  where emp_loan_id=".$emp_loan_id;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			{
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			 	
				$loan_purpose=@$row['loan_purpose'];
				$total_amount=@$row['total_amount'];
				$total_inst_no=@$row['total_inst_no'];
				$current_inst_no=@$row['current_inst_no'];
				$monthly_inst=@$row['monthly_inst'];
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
function populatemonthly(){
	var total_amount=document.frmManu.total_amount.value;
	var total_inst_no=document.frmManu.total_inst_no.value;

	if(total_amount.trim()!="" && total_inst_no.trim()!="")
		document.frmManu.monthly_inst.value=parseInt(parseInt(total_amount)/parseInt(total_inst_no));
}
function validate(frm)
{
	if(frm.head.value=="0")
	{
		alert('Please Select Head');
		frm.head.focus();
		return false;
	}
	else if(frm.total_amount.value.trim()=="")
	{
		alert('Please Enter Total Amount');
		frm.total_amount.value="";
		frm.total_amount.focus();
		return false;
	}
	else if(frm.total_inst_no.value.trim()=="")
	{
		alert('Please Enter Total Installment Number');
		frm.total_inst_no.value="";
		frm.total_inst_no.focus();
		return false;
	}
	else if(frm.current_inst_no.value.trim()=="")
	{
		alert('Please Enter Current Installment Number');
		frm.current_inst_no.value="";
		frm.current_inst_no.focus();
		return false;
	}
	else if(frm.monthly_inst.value.trim()=="")
	{
		alert('Please Enter Monthly Installment Amount');
		frm.monthly_inst.value="";
		frm.monthly_inst.focus();
		return false;
	}
	else if(parseInt(frm.total_inst_no.value.trim())<parseInt(frm.current_inst_no.value.trim()))
	{
		alert('Total Number Of Installment Can Not Be Less Than Current Installment Number');
		frm.current_inst_no.value="";
		frm.current_inst_no.focus();
		return false;
	}
}
function ClearField(frm){
	  frm.head.value = 0;
	  frm.total_amount.value = "";
	  frm.total_inst_no.value = "";
	  frm.current_inst_no.value = "";
	  frm.monthly_inst.value = "";
	  }
	</script>
</head>
<body>


<form method="post" name="frmManu" action="pay_employee_loan.php?loan_purpose=<?php echo $loan_purpose;?>&act=<?php echo $act; ?>&empID=<?php echo $empID; ?>&emp_loan_id=<?php echo $emp_loan_id; ?>" onsubmit="return validate(this);">
 <div id="middleWrap">
		<div class="head"><h2>LOAN/ADVANCE</h2></div>

 <span id="spErr"></span>
<table class="adminform">
 
  <tr>
    <td align="right" class="mandate">Loan Purpose :</td>
    <td>
		 <?php
 			echo "<select size='1' name='head' id='head'>";
			echo "<option value='0' selected>--Select Head--</option>";
			$sql="select loan_purpose_abvr,loan_purpose_name  from sal_loan_purpose order by loan_purpose_name";
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
				while($row1=mysql_fetch_array($res))
				{
					echo"<option value='".$row1['loan_purpose_name']."#".$row1['loan_purpose_abvr']."'";
					if(@$loan_purpose==$row1['loan_purpose_name']) echo "selected"; echo">".$row1['loan_purpose_name']."(".$row1['loan_purpose_abvr'].")</option>";
				}
				
			echo"</select>";
			?>
	</td>
   </tr>
	<tr>
		<td align="right">Amount :</td>
		<td><input type="text" name="total_amount" id="total_amount" size="40"  value ='<?php if($total_amount!="")  echo round($total_amount); ?>' maxlength="10"
						onkeypress="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')"
						onkeyup="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')" 
						onblur="populatemonthly()"
						/></td>
    </tr>
	<tr>
		<td align="right">Total Installment No:</td>
		<td><input type="text" name="total_inst_no" id="total_inst_no" size="40"  value ='<?php echo $total_inst_no; ?>' maxlength="5"
						onkeypress="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')"
						onkeyup="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')" 
						onblur="populatemonthly()"
						/></td>
    </tr>
	<tr>
		<td align="right">Current Installment No:</td>
		<td><input type="text" name="current_inst_no" id="current_inst_no" size="40"  value ='<?php echo $current_inst_no; ?>' maxlength="5"
						onkeypress="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')"
						onkeyup="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')" /></td>
    </tr>
	<tr>
		<td align="right">Monthly Installment(Amount) :</td>
		<td><input type="text" name="monthly_inst" id="monthly_inst" size="40"  value ='<?php if($monthly_inst!="") echo round($monthly_inst); ?>' maxlength="10" readonly /></td>
    </tr>
 <tr>
   
    
        <td align="center" colspan=2><input type="submit" class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
			 <?php if(@$act!="delete"){?> <input type="button" class="btn reset"	value="RESET" id="reset " name="reset"	onClick="ClearField(this.form)" /> <?php }	?>
			 <input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
          	<input type='hidden' name='action' value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' />
           </td>
    </tr>
</table>
</div>
</form>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
   
  </body>
  </html>
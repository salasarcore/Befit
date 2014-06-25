<?php

@session_start();
include('../../conn.php');
//include('../../check_session.php');
include ("../../functions/common.php");

$head_name="";
$amount="";
$head="";
$Errs="";
makeSafe ( extract ( $_GET ) );
$action = makeSafe ( @$_POST ['action'] );

if (trim(@$action == "SAVE") || trim(@$action) == "UPDATE") 
{
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
		
			if (trim($action) == "SAVE") 
			{
				$sql="select * from  sal_salary_info where head_name='".$head_name."'  and empid=".$empID." and salary_type='D'";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0)
					$Errs= "<div class='error'>Head already exist</div>";
				else
				{
					$sqlearnings=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as earnings from sal_salary_info where salary_type='E' and empid=".$empID));
					$sqlded=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as deduction from sal_salary_info where salary_type='D' and empid=".$empID));
					$sqlloan=mysql_fetch_assoc(mysql_query("select IFNULL(sum(monthly_inst),0) as loan_amount from sal_salary_info_loan where total_inst_no>=current_inst_no and empid=".$empID));
					$balance=$sqlearnings['earnings']-($sqlded['deduction']+$sqlloan['loan_amount']);
					if($balance>=$amount){
					$sal_info_id=getNextMaxId("sal_salary_info","sal_info_id")+1;
					$sql="insert into sal_salary_info(sal_info_id,empid,head_name,head_abvr,amount,salary_type,updated_by)";
					$sql = $sql ." values(".$sal_info_id." ,".$empID.",'".$head_name."','".$head_abbvr."','".$amount."','D','".$_SESSION['emp_name']."')";
					$res=mysql_query($sql,$link);
					if(mysql_affected_rows($link)>0){
						$Errs= "<div class='success'>Record Saved Successfully: ".$head_name."</div>";
						$head_name="0";
						$amount="";
						}
					}
					else
						$Errs="<div class='error'>Deduction Amount is Greater Than Net Balance Amount.</div>";
				}
			}
			
			if (trim($action) == "UPDATE") 
			{
				$sql="select * from  sal_salary_info where head_name='".$head_name."' and empid=".$empID." and salary_type='D' and sal_info_id!=".$sal_info_id;
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0)
					$Errs= "<div class='error'>Head already exist</div>";
				else
				{
					$sqlearnings=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as earnings from sal_salary_info where salary_type='E' and empid=".$empID));
					$sqlded=mysql_fetch_assoc(mysql_query("select IFNULL(sum(amount),0) as deduction from sal_salary_info where salary_type='D' and sal_info_id!=".$sal_info_id." and empid=".$empID));
					$sqlloan=mysql_fetch_assoc(mysql_query("select IFNULL(sum(monthly_inst),0) as loan_amount from sal_salary_info_loan where total_inst_no>=current_inst_no and empid=".$empID));
					$balance=$sqlearnings['earnings']-($sqlded['deduction']+$sqlloan['loan_amount']);
					if($balance>=$amount){
						
					
					$sql="update sal_salary_info set head_name='".$head_name."',head_abvr='".$head_abbvr."',amount='".$amount."',updated_by='".$_SESSION['emp_name']."' where sal_info_id=".$sal_info_id;
					$res=mysql_query($sql,$link);
					if(mysql_affected_rows($link)==0)
						$Errs="<div class='success'>No Data Changed</div>";
					if(mysql_affected_rows($link)>0)
						$Errs="<div class='success'>Record Updated Successfully</div>";
					if(mysql_affected_rows($link)<0)
						$Errs="<div class='error'>Record Not Updated Successfully</div>";
				
				}
				else
					$Errs="<div class='error'>Deduction Amount is Greater Than Net Payable Amount.</div>";
			}
		}
	}
}
if (trim(@$action) == "DELETE")
{
		
		$query   = "delete  FROM sal_salary_info  where sal_info_id=".$sal_info_id;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0){
				$Errs="<div class='success'>Record Deleted Successfully</div>";
				$head_name="0";
		}
}

if(@$act=="edit" || @$act=="delete" )
{

		$query   = "SELECT * FROM sal_salary_info  where sal_info_id=".$sal_info_id;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			{
				$row  = mysql_fetch_array($result, MYSQL_ASSOC);
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


<form method="post" name="frmManu" action="pay_employee_deductions.php?head_name=<?php echo $head_name;?>&act=<?php echo $act; ?>&empID=<?php echo $empID; ?>&sal_info_id=<?php echo $sal_info_id; ?>" onsubmit="return validate(this);">
 <div id="middleWrap">
		<div class="head"><h2>DEDUCTIONS</h2></div>

 <span id="spErr"></span>
<table class="adminform">
 
  <tr>
    <td align="right" class="mandate">Head :</td>
    <td>
		 <?php
 			echo "<select size='1' name='head' id='head'>";
			echo "<option value='0' selected>--Select Head--</option>";
			$sql="select deduc_abvr,deduc_name  from sal_deductions order by deduc_name";
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
				while($row1=mysql_fetch_array($res))
						{
					echo"<option value='".$row1['deduc_name']."#".$row1['deduc_abvr']."'";
					if(@$head_name==$row1['deduc_name']) echo "selected"; echo">".$row1['deduc_name']."(".$row1['deduc_abvr'].")</option>";
				}
				
			echo"</select>";
			?>
	
	
	</td>
   </tr>
  <tr>
    <td align="right">Amount :</td>
   <td><input type="text" name="amount" id="amount" size="40"
						value='<?php if($amount!="") echo round($amount); ?>' maxlength="10"
						onkeypress="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')"
						onkeyup="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')" /></td>
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
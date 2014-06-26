<?php
@session_start();
include('../conn.php');
include('../check_session.php');
include("../functions/common.php");

$bank_name="";
$account_number="";
$ifsc_code="";
$branch_name="";
$Errs="";
$action="";
makeSafe(extract($_REQUEST));
$bankID=base64_decode($bankID);
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
/*include("../modulemaster.php");
if($act=="add")
	$id=option_bank_list_add;
elseif($act=="edit")
	$id=option_bank_list_edit;
elseif($act=="delete")
	$id=option_bank_list_delete;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}
*/

if(@$action=="SAVE" || @$action=="UPDATE")
{
	if(trim(@$bank_name)=="")
		$Errs= "<div class='error'>Please Enter Bank Name</div>";
	elseif(trim(@$account_number)=="")
		$Errs= "<div class='error'>Please Enter Account Number</div>";
	elseif(trim(@$ifsc_code)=="")
		$Errs= "<div class='error'>Please Enter IFSC Code</div>";
	elseif(trim(@$branch_name)=="")
		$Errs= "<div class='error'>Please Enter Branch Name</div>";
	elseif(!is_numeric(trim(@$account_number)))
		$Errs= "<div class='error'>Account Number Should Be Numeric</div>";
	else
	{
		if(@$action=="SAVE"){
			$sql="select * from bank_master where bank_name='".trim(@$bank_name)."' and account_number=".$account_number;
			$res=mysql_query($sql,$link) or die(mysql_error($link));
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Bank Name And Account Number</div>";
			else
			{
				$newbankid=getNextMaxId("bank_master","bank_id")+1;
				$sql="insert into bank_master(bank_id,bank_name,account_number,ifsc_code,branch_name,created_on,updated_on,updated_by)";
				$sql = $sql ." values(".@$newbankid.",'".$bank_name."','".$account_number."','".@$ifsc_code."','".@$branch_name."',NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0){
					$Errs= "<div class='success'>Record Saved Successfully</div>";
					$bank_name="";
					$account_number="";
					$ifsc_code="";
					$branch_name="";
				}
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";			
			}
		
		}
		if(@$action=="UPDATE"){
			$sql="select * from bank_master where bank_name='".trim(@$bank_name)."' and account_number=".$account_number." and bank_id !=".@$bankID;
			$res=mysql_query($sql,$link) or die(mysql_error($link));
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Bank Name and Account Number Combination</div>";
			else
			{
				$sql="update bank_master set bank_name='".@$bank_name."', account_number='".@$account_number."', ifsc_code='".@$ifsc_code."', branch_name='".@$branch_name."', updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where bank_id=".@$bankID;
					
				$res=mysql_query($sql,$link) or die(mysql_error($link));
				if(mysql_affected_rows($link)==0)
					$Errs="<div class='success'>No Data Changed</div>";
				if(mysql_affected_rows($link)>0)
					$Errs="<div class='success'>Record Updated Successfully</div>";
				if(mysql_affected_rows($link)<0)
					$Errs="<div class='error'>Record Not Updated successfully</div>";
					
			}
		}
	}
}


if(@$action=="DELETE")
{
	$query   = "SELECT * FROM fee_transaction_details where to_bank=".$bankID;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$Errs= "<div class='error'>You can not delete this bank as it is already used in some transactions.</div>";
	}
	else
	{
		$query   = "delete FROM bank_master where bank_id=".$bankID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
		{
			$Errs= "<div class='success'>Bank Deleted Successfully</div>";
			$bank_name="";
			$account_number="";
			$ifsc_code="";
			$branch_name="";
		}
		else
		{
			$Errs= "<div class='error'>Bank Not Deleted Successfully</div>";
		}
	}
}

if(@$act=="edit" || @$act=="delete" )
{

	$query   = "SELECT  * FROM bank_master  where bank_id=".$bankID;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			
		$bank_name=@$row['bank_name'];
		$account_number=@$row['account_number'];
		$ifsc_code=@$row['ifsc_code'];
		$branch_name=@$row['branch_name'];
		$created_on=date("jS-M-Y g:iA",strtotime($row['created_on']));
		$updated_on=date("jS-M-Y g:iA",strtotime($row['updated_on']));
		$updated_by=@$row['updated_by'];
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#account_number').keyup(function(){
			var value = $('#this').val();
			if(this.value.match(/[^0-9]/g))
			{
				this.value = this.value.replace(/[^0-9]/g,'');
			}
		});

	});
	
function validatebank(frm)
{
	if(frm.bank_name.value.trim()=="")
	{
		alert('Bank name should not be blank');
		frm.bank_name.value="";
		frm.bank_name.focus();
		return false;
	}
	else if(frm.account_number.value.trim()=="")
	{
		alert('Account number should not be blank');
		frm.account_number.value="";
		frm.account_number.focus();
		return false;
	}
	else if(frm.ifsc_code.value.trim()=="")
	{
		alert('IFSC code should not be blank');
		frm.ifsc_code.value="";
		frm.ifsc_code.focus();
		return false;
	}
	else if(frm.branch_name.value.trim()=="")
	{
		alert('Branch name should not be blank');
		frm.branch_name.value="";
		frm.branch_name.focus();
		return false;
	}
	else
		return true;
}

function ClearField(frm){
	  frm.bank_name.value = "";
	  frm.account_number.value = "";
	  frm.ifsc_code.value = "";
	  frm.branch_name.value = "";
}
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?>	BANK</h2></div>
		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"	action="add_bank.php?bankID=<?php echo base64_encode(@$bankID);?>&act=<?php echo $act; ?>" onsubmit="return validatebank(this);" autocomplete="off">
			<table class="adminform">
				<tr>
					<td width="44%" align="right" class="redstar">Bank Name :</td>
					<td width="43%"><input type="text" name="bank_name"	id="bank_name" size="30" value='<?php echo $bank_name; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?>/></td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Account Number :</td>
					<td width="43%"><input type="text" name="account_number"	id="account_number" size="30" value='<?php echo $account_number; ?>' maxlength="50" <?php if(@$act=="delete") echo "readonly"; ?>/></td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">IFSC Code :</td>
					<td width="43%"><input type="text" name="ifsc_code"	id="ifsc_code" size="30" value='<?php echo $ifsc_code; ?>' maxlength="50" <?php if(@$act=="delete") echo "readonly"; ?>/></td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Branch Name :</td>
					<td width="43%"><input type="text" name="branch_name"	id="branch_name" size="30" value='<?php echo $branch_name; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?>/></td>
				</tr>
				<?php if(@$created_on!="") {?><tr><td align="right">Created On :</td><td><?php echo $created_on; ?></td></tr><?php } ?>
				<?php if(@$updated_on!="") {?><tr><td align="right">Updated On :</td><td><?php echo $updated_on; ?></td></tr><?php } ?>
				<?php if(@$updated_by!="") {?><tr><td align="right">Updated By :</td><td><?php echo @$updated_by; ?></td></tr><?php } ?>
				<tr>
					<td align="center" colspan=2>
					<input type="submit" class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
					if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
				 <?php if(@$act!="delete"){?> <input type="button" class="btn reset"	value="RESET" id="reset " name="reset"	onClick="ClearField(this.form)" /> <?php }	?>
				 <input type=button class="btn close"	value="CLOSE" onClick="parent.emailwindow.close();">
				 <input	type='hidden' name='action'	value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' />
					</td>
				</tr>
			</table>
		</form>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
	</div>
</body>
</html>

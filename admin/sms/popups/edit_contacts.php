<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include("../../functions/common.php");

$cntname="";
$cntnumber="";
$Errs="";
makeSafe(extract($_REQUEST));
$userID=base64_decode($userID);
if(@$action=="UPDATE")
{
	if(trim(@$cntname)=="")
		$Errs= "<div class='error'>Please Enter Contact Name</div>";
	elseif(trim(@$cntnumber)=="")
		$Errs= "<div class='error'>Please Enter Mobile Number</div>";
	elseif(strlen(trim($cntnumber))!=10)
		$Errs= "<div class='error'>Mobile Number should be 10 digits</div>";
	else
	{
		$sql="select * from sms_non_registered_users where mobile_num='".@$cntnumber."' and user_id !=".@$userID;
		$res=mysql_query($sql,$link);
		if(mysql_affected_rows($link)>0)
			$Errs= "<div class='error'>Duplicate Mobile Number</div>";
		else
		{
			$sql="update sms_non_registered_users set name='".@$cntname."', mobile_num='".@$cntnumber."', updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where user_id=".@$userID;
				
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)==0)
				$Errs="<div class='success'>No Data Changed</div>";
			if(mysql_affected_rows($link)>0)
				$Errs="<div class='success'>Record Updated Successfully</div>";
			if(mysql_affected_rows($link)<0)
				$Errs="<div class='error'>Record Not Updated successfully</div>";
				
		}
	}
}

if(@$action=="DELETE")
{
		$query  = "delete  FROM sms_non_registered_users where user_id=".@$userID;
		$result  = mysql_query($query,$link) or die('Error, query failed');
		if(mysql_affected_rows($link)>0){
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
			$cntname="";
			$cntnumber="";
		}
		else 
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
}

if(@$act=="edit" || @$act=="delete" )
{
	$query   = "SELECT * FROM sms_non_registered_users where user_id=".@$userID;
	$result  = mysql_query($query,$link) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$cntname=@$row['name'];
		$cntnumber=@$row['mobile_num'];
		$updated_on=date("jS-M-Y, g:iA",strtotime(@$row['updated_on']));
		$updated_by=@$row['updated_by'];
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../css/classic.css" type="text/css" rel="stylesheet">
<?php include('../../php/js_css_common.php');?>
<script type="text/javascript" src="../../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	
	
function validatecontacts(frm)
{
	if(frm.cntname.value.trim()=="")
	{
		alert('Contact name should not be blank');
		frm.cntname.value="";
		frm.cntname.focus();
		return false;
	}
	else if(frm.cntnumber.value.trim()=="")
	{
		alert('Mobile number should not be blank');
		frm.cntnumber.value="";
		frm.cntnumber.focus();
		return false;
	}
	else if(frm.cntnumber.value.length!=10)
	{
		alert('Mobile number should be 10 digits');
		frm.cntnumber.value="";
		frm.cntnumber.focus();
		return false;
	}
	else
		return true;
}

function ClearField(frm){
	  frm.cntname.value = "";
	  frm.cntnumber.value = "";
	  }
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?> CONTACT</h2></div>
		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"	action="edit_contacts.php?userID=<?php echo base64_encode(@$userID);?>&act=<?php echo $act; ?>" onsubmit="return validatecontacts(this);" autocomplete="off">
			<table class="adminform">
				<tr>
					<td width="44%" align="right" class="redstar">Contact Name : </td>
					<td width="43%"><input type="text" name="cntname"	id="cntname" size="30" value='<?php echo @$cntname; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Mobile Number : </td>
					<td width="43%"><input type="text" name="cntnumber"	id="cntnumber" size="30" value='<?php echo @$cntnumber; ?>' maxlength="10" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<?php if(@$updated_on!="") {?><tr><td align="right">Updated On : </td><td><?php echo @$updated_on; ?></td></tr><?php } ?>
				<?php if(@$updated_by!="") {?><tr><td align="right">Updated By : </td><td><?php echo @$updated_by; ?></td></tr><?php } ?>
				<tr>
					<td align="center" colspan=2>
					<input type="submit" class="btn save" value='<?php 
					if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
				 <?php if(@$act!="delete"){?> <input type="button" class="btn reset"	value="RESET" id="reset " name="reset"	onClick="ClearField(this.form)" /> <?php }	?>
				 <input type=button class="btn close"	value="CLOSE" onClick="parent.emailwindow.close();">
				 <input	type='hidden' name='action'	value='<?php 
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

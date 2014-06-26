<?php
@session_start();
include('../../conn.php');
//include('../../check_session.php');
include("../../functions/common.php");

$expensename="";
$action="";
$Errs="";
makeSafe(extract($_GET));
makeSafe(extract($_POST));
$exp_cat_id=base64_decode(@$exp_cat_id);

if(@$action=="SAVE" || @$action=="UPDATE")
{
	if(trim(@$expensename)=="")
		$Errs= "<div class='error'>Please Enter Expense Category Name</div>";
	else
	{
		if(@$action=="SAVE")
		{
			$sql="select * from expense_category where name='".trim(@$expensename)."' and br_id=".$_SESSION['br_id'];
			$res=mysql_query($sql,$link) or die('Error, query failed');
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Expense Category Name</div>";
			else
			{
				$newassetid=getNextMaxId("expense_category","exp_cat_id")+1;
				$sql="insert into expense_category(exp_cat_id,br_id,name,created_at,updated_at,updated_by)";
				$sql = $sql ." values(".@$newassetid.",".$_SESSION['br_id'].",'".@$expensename."',NOW(),NOW(),'".$_SESSION['emp_name']."')";
				$res=mysql_query($sql,$link) or die('Error, query failed');
				if(mysql_affected_rows($link)>0){
					$Errs= "<div class='success'>Record Saved Successfully</div>";
					$expensename="";
				}
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";
			}
		}
		elseif(@$action=="UPDATE")
		{
				
			$sql="select * from expense_category where name='".@$expensename."' and br_id=".$_SESSION['br_id']." and exp_cat_id !=".@$exp_cat_id;
			$res=mysql_query($sql,$link) or die('Error, query failed');
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Asset Name</div>";
			else
			{
				$sql="update expense_category set name='".@$expensename."',updated_by='".$_SESSION['emp_name']."' where exp_cat_id =".@$exp_cat_id;
				$res=mysql_query($sql,$link) or die('Error, query failed');
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

	$query   = "select * from expense_master where exp_cat_id=".@$exp_cat_id;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
		$Errs= "<div class='error'>Related Record Exists, Please Delete The Related Record</div>";
	else
	{
		$query  = "delete  FROM expense_category where exp_cat_id=".@$exp_cat_id;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0){
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
			$expensename="";
		}
		else 
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	}
}

if(@$act=="edit" || @$act=="delete" )
{

	$query   = "SELECT * FROM expense_category where exp_cat_id=".@$exp_cat_id;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$expensename=@$row['name'];
		$created_at=date("jS-M-Y, g:iA",strtotime(@$row['created_at']));
		$updated_at=date("jS-M-Y, g:iA",strtotime(@$row['updated_at']));
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
	$(document).ready(function(){
		$('#expensename').keyup(function(){
			var value = $('#this').val();
			if(this.value.match(/[^0-9a-zA-Z ]/g))
			{
				this.value = this.value.replace(/[^0-9a-zA-Z ]/g,'');
			}
		});

	});
	
function validateasset(frm)
{
	if(frm.expensename.value.trim()=="")
	{
		alert('Expense category name should not be blank');
		frm.expensename.value="";
		frm.expensename.focus();
		return false;
	}
	else
		return true;
}

function ClearField(frm){
	  frm.expensename.value = "";
	  frm.expensename.focus();
	  }
</script>
</head>
<body>
<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?> EXPENSE CATEGORY</h2></div>

<span id="spErr"><?php echo $Errs;?> </span>
<form method="post" name="frmManu"	action="expense_category.php?exp_cat_id=<?php echo base64_encode(@$exp_cat_id);?>&act=<?php echo $act; ?>" onsubmit="return validateasset(this);" autocomplete="off">
	<table class="adminform">
		<tr>
			<td width="30%" align="right" class="redstar">Expense Category Name :</td>
			<td width="43%"><input type="text" name="expensename"	id="expensename" size="25" value='<?php echo @$expensename; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
		</tr>
		<?php if(@$created_at!="") {?><tr><td align="right">Created At :</td><td><?php echo @$created_at; ?></td></tr><?php } ?>
		<?php if(@$updated_at!="") {?><tr><td align="right">Updated At :</td><td><?php echo @$updated_at; ?></td></tr><?php } ?>
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

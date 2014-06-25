<?php
@session_start();
include('../../../globalConfig.php');
include('../../check_session.php');
include("../../../functions/common.php");

$Errs="";$action="";
$expense_id=0;$exp_cat_id=0;
makeSafe(extract($_REQUEST));
$expense_id=base64_decode(@$expense_id);
$exp_cat_id=base64_decode(@$exp_cat_id);

if($action=="SAVE" || @$action=="UPDATE")
{

	if(trim(@$expense_name)=="")
		$Errs= "<div class='error'>Please Enter Expense Name</div>";
	if(trim($exptype)=="")
		$Errs= "<div class='error'>Please Select Expense Type</div>";
	else
	{
		if(@$action=="SAVE")
		{
			 $sql="select * from expense_master where name='".trim(@$expense_name)."' and type='".trim($exptype)."' and exp_cat_id in (select exp_cat_id from expense_category where exp_cat_id=".@$exp_cat_id." and br_id=".$_SESSION['br_id'].")"; 
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Expense Name</div>";
			else
			{
				$newexpense_id=getNextMaxId("expense_master","expense_id")+1;
				$sql="insert into expense_master(expense_id,exp_cat_id,name,type,created_at,updated_at,updated_by) values(".@$newexpense_id.",'".@$exp_cat_id."','".@$expense_name."','".$exptype."',NOW(),NOW(),'".$_SESSION['emp_name']."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0){
					$Errs= "<div class='success'>Record Saved Successfully</div>";
					$expense_name="";
				}
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";
				
			}
		}
		elseif(@$action=="UPDATE")
		{
				
			$sql="select * from expense_master where name='".@$expense_name."' and type='".trim($exptype)."'  and expense_id !=".@$expense_id." and exp_cat_id in (select exp_cat_id from expense_category where exp_cat_id=".@$exp_cat_id." and br_id=".$_SESSION['br_id'].")";
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Expense Name</div>";
			else
			{
					$sql="update expense_master set name='".@$expense_name."', type='".$exptype."', updated_by='".$_SESSION['emp_name']."' where expense_id=".@$expense_id;
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
}

if(@$action=="DELETE")
{
	$query   = "select expense_list_id from expense_list where expense_id=".@$expense_id;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
		$Errs= "<div class='error'>Related Record Exists, Please Delete The Related Record</div>";
	else
	{
		$query  = "delete  FROM expense_master where expense_id=".@$expense_id;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0){
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
			$expense_name="";
		}
		else 
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	}
}

if(@$act=="edit" || @$act=="delete" )
{

	$query   = "SELECT * FROM expense_master  where expense_id=".@$expense_id;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$expense_name=@$row['name'];
		$type=@$row['type'];
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
<?php include('../../modules/js_css_common.php');?>
<script type="text/javascript" src="../../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#expense_name').focus();
		$('#expense_name').keyup(function(){
			var value = $('#this').val();
			if(this.value.match(/[^0-9a-zA-Z ]/g))
			{
				this.value = this.value.replace(/[^0-9a-zA-Z ]/g,'');
			}
		});
	});
	
function validatefee(frm)
{
	if(frm.expense_name.value.trim()=="")
	{
		alert('Expense name should not be blank');
		frm.expense_name.value="";
		frm.expense_name.focus();
		return false;
	}
	else if(frm.exptype.value.trim()=="")
	{
		alert('Please select expense type');
		frm.exptype.focus();
		return false;
	}
	else
		return true;
}

function ClearField(frm){
	  frm.expense_name.value = "";
	  frm.exptype.value = "";
	  frm.expense_name.focus();
	  }
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?>	EXPENSE</h2></div>

		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"	action="expense_master.php?expense_id=<?php echo base64_encode(@$expense_id);?>&exp_cat_id=<?php echo base64_encode(@$exp_cat_id);?>&act=<?php echo $act; ?>" onsubmit="return validatefee(this);" autocomplete="off">
			<table class="adminform">
				<tr>
					<td width="44%" align="right" class="redstar">Expense Name :</td>
					<td width="43%"><input type="text" name="expense_name" id="expense_name" size="30" value='<?php echo @$expense_name; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Type :</td>
					<td width="43%">
					<select name="exptype" id="exptype" <?php if(@$act=="delete") echo "disabled"; ?>>
					<option value="">--Select Expense Type--</option>
					<option value="Fixed" <?php if(@$type=="Fixed") echo "selected"; ?> >Fixed</option>
					<option value="Floating" <?php if(@$type=="Floating") echo "selected"; ?> >Floating</option>
					</select>
					</td>
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
				 <input type=button	 class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
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

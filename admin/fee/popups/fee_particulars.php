<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include("../../functions/common.php");

$feeID=0;$catID=0;
$fee_cat_name="";
$fee_cat_desc="";
$act=makeSafe(@$_GET['act']);
?>
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../../modulemaster.php");

if($act=="add")
$id=option_fee_particulars_list_add;
elseif($act=="edit")
$id=option_fee_particulars_list_edit;
elseif($act=="delete")
$id=option_fee_particulars_list_delete;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
if(makeSafe(@$_GET['feeID'])!="") $feeID=makeSafe(base64_decode(@$_GET['feeID']));
if(makeSafe(@$_GET['catID'])!="") $catID=makeSafe(base64_decode(@$_GET['catID']));
$action=makeSafe(@$_POST['action']);
$Errs="";

if($action=="SAVE" || @$action=="UPDATE")
{
	$fee_name=makeSafe(@$_POST['txtfeename']);
	$fee_desc=makeSafe(@$_POST['feedesc']);
	$total_amount=makeSafe(@$_POST['txtfeeamount']);

	if(trim(@$fee_name)=="")
		$Errs= "<div class='error'>Please Enter Fee Particular Name</div>";
	elseif(trim(@$fee_desc)=="")
		$Errs= "<div class='error'>Please Enter Fee Desciption</div>";
	elseif(trim(@$total_amount)=="")
		$Errs= "<div class='error'>Please Enter Fee Amount</div>";
	elseif(intval(trim(@$total_amount))<=0)
	$Errs= "<div class='error'>Fee amount should not be less than or equal to Zero(0)</div>";
	else
	{
		if(@$action=="SAVE")
		{
			$sql="select * from fee_particulars where name='".trim(@$fee_name)."' and fee_catgories_id in (select fee_categories_id from fee_categories where fee_categories_id=".@$catID." and br_id=".$_SESSION['br_id'].")";
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Fee Particular Name</div>";
			else
			{
				$newfeeid=getNextMaxId("fee_particulars","fee_particulars_id")+1;
				$sql="insert into fee_particulars(fee_particulars_id,fee_categories_id,name,description,total_amount,created_at,updated_at,updated_by) values(".@$newfeeid.",'".@$catID."','".@$fee_name."','".@$fee_desc."','".@$total_amount."',NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0)
					$Errs= "<div class='success'>Record Saved Successfully</div>";
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";
				$fee_name="";
				$fee_desc="";
				$total_amount="";
			}
		}
		elseif(@$action=="UPDATE")
		{
				
			$sql="select * from fee_particulars where name='".@$fee_name."' and fee_particulars_id !=".@$feeID." and fee_catgories_id in (select fee_categories_id from fee_categories where fee_categories_id=".@$catID." and br_id=".$_SESSION['br_id'].")";
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Fee Particular Name</div>";
			else
			{
				$sqlres=mysql_query("select fee_expected_id as id from fee_expected where fee_particulars_id=".@$feeID." union all 
	select fee_discount_id as id from fee_discount where fee_particulars_id=".@$feeID);
				if(mysql_num_rows($sqlres)<=0){
					$sql="update fee_particulars set name='".@$fee_name."', description='".@$fee_desc."', total_amount='".@$total_amount."', updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where fee_particulars_id=".@$feeID;
						
					$res=mysql_query($sql,$link);
					if(mysql_affected_rows($link)==0)
						$Errs="<div class='success'>No Data Changed</div>";
					if(mysql_affected_rows($link)>0)
						$Errs="<div class='success'>Record Updated Successfully</div>";
					if(mysql_affected_rows($link)<0)
						$Errs="<div class='error'>Record Not Updated successfully</div>";
					}
					else
						$Errs="<div class='error'>To edit this record, please delete it's related records from fees expected and discount.</div>";
			}
		}
	}
}

if(@$action=="DELETE")
{

	$query   = "select fee_expected_id as id from fee_expected where fee_particulars_id=".@$feeID." union all 
	select fee_discount_id as id from fee_discount where fee_particulars_id=".@$feeID."";
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
		$Errs= "<div class='error'>Related record exists, Please delete the related record</div>";
	else
	{
		$query  = "delete  FROM fee_particulars where fee_particulars_id=".@$feeID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
		else 
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	}
}

if(@$act=="edit" || @$act=="delete" )
{

	$query   = "SELECT * FROM fee_particulars  where fee_particulars_id=".@$feeID;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$fee_name=@$row['name'];
		$fee_desc=@$row['description'];
		$total_amount=@$row['total_amount'];
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
<script type="text/javascript" src="../../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#txtfeename').focus();
		$('#txtfeename').keyup(function(){
			var value = $('#this').val();
			if(this.value.match(/[^0-9a-zA-Z ]/g))
			{
				this.value = this.value.replace(/[^0-9a-zA-Z ]/g,'');
			}
		});

		$('#txtfeeamount').keyup(function(){
			var value = $('#this').val();
			if(this.value.match(/[^0-9.]/g))
			{
				this.value = this.value.replace(/[^0-9.]/g,'');
			}
		});

		$('#feedesc').keypress(function(e){
			   if (e.keyCode == 13) return false
			});
	});
	
function validatefee(frm)
{
	if(frm.txtfeename.value.trim()=="")
	{
		alert('Fee particular name should not be blank');
		frm.txtfeename.value="";
		frm.txtfeename.focus();
		return false;
	}
	else if(frm.feedesc.value.trim()=="")
	{
		alert('Fee description should not be blank');
		frm.feedesc.value="";
		frm.feedesc.focus();
		return false;
	}
	else if(frm.txtfeeamount.value.trim()=="")
	{
		alert('Fee amount should not be blank');
		frm.txtfeeamount.value="";
		frm.txtfeeamount.focus();
		return false;
	}
	else if(parseInt(frm.txtfeeamount.value.trim())<=0)
	{
		alert('Fee amount should not be less than or equal to Zero(0)');
		frm.txtfeeamount.value="";
		frm.txtfeeamount.focus();
		return false;
	}
	else
		return true;
}

function ClearField(frm){
	  frm.txtfeename.value = "";
	  frm.feedesc.value = "";
	  frm.txtfeeamount.value = "";
	  frm.txtfeename.focus();
	  }
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?>	FEE PARTICULAR</h2></div>
		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"	action="fee_particulars.php?feeID=<?php echo base64_encode(@$feeID);?>&catID=<?php echo base64_encode(@$catID);?>&act=<?php echo $act; ?>" onsubmit="return validatefee(this);" autocomplete="off">
			<table class="adminform">
				<tr>
					<td width="44%" align="right" class="redstar">Fee Particular Name :</td>
					<td width="43%"><input type="text" name="txtfeename" id="txtfeename" size="30" value='<?php echo @$fee_name; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Description :</td>
					<td width="43%"><textarea name="feedesc" id="feedesc"	rows="5" cols="35" maxlength="500" <?php if(@$act=="delete") echo "readonly"; ?> ><?php echo @$fee_desc; ?></textarea></td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Fees Amount :</td>
					<td width="43%"><input type="text" name="txtfeeamount"	id="txtfeeamount" size="30" value='<?php echo @$total_amount; ?>' maxlength="10" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
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
				 <?php if(@$act!="delete"){?> <input type="button"	class="btn reset" value="RESET" id="reset " name="reset"	onClick="ClearField(this.form)" /> <?php }	?>
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

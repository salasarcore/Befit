<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include("../../functions/common.php");

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
$id=option_fee_category_list_add;
elseif($act=="edit")
$id=option_fee_category_list_edit;
elseif($act=="delete")
$id=option_fee_category_list_delete;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$feeID=makeSafe(base64_decode(@$_GET['feeID']));
$action=makeSafe(@$_POST['action']);
$Errs="";

if(@$action=="SAVE" || @$action=="UPDATE")
{
	$fee_cat_name=makeSafe(@$_POST['txtfeecatname']);
	$fee_cat_desc=makeSafe(@$_POST['feecatdesc']);

	if(trim(@$fee_cat_name)=="")
		$Errs= "<div class='error'>Please Enter Fee Category Name</div>";
	elseif(trim(@$fee_cat_desc)=="")
		$Errs= "<div class='error'>Please Enter Fee Category Desciption</div>";
	else
	{
		if(@$action=="SAVE")
		{
			$sql="select * from fee_categories where name='".trim(@$fee_cat_name)."' and br_id=".$_SESSION['br_id'];
			$res=mysql_query($sql,$link) or die(mysql_error($link));
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Fee Category Name</div>";
			else
			{
				$newfeeid=getNextMaxId("fee_categories","fee_categories_id")+1;
				$sql="insert into fee_categories(fee_categories_id,br_id,name,description,created_at,updated_at,updated_by)";
				$sql = $sql ." values(".@$newfeeid.",".$_SESSION['br_id'].",'".@$fee_cat_name."','".@$fee_cat_desc."',NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				$res=mysql_query($sql,$link) or die(mysql_error($link));
				if(mysql_affected_rows($link)>0)
					$Errs= "<div class='success'>Record Saved Successfully</div>";
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";
				$fee_cat_name="";
				$fee_cat_desc="";				
			}
		}
		elseif(@$action=="UPDATE")
		{
				
			$sql="select * from fee_categories where name='".@$fee_cat_name."' and br_id=".$_SESSION['br_id']." and fee_categories_id !=".@$feeID;
			$res=mysql_query($sql,$link) or die(mysql_error($link));
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Fee Category Name</div>";
			else
			{
				$sql="update fee_categories set name='".@$fee_cat_name."', description='".@$fee_cat_desc."', updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where fee_categories_id=".@$feeID;
					
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

	$query   = "select * from fee_particulars where fee_categories_id in (select fee_categories_id from fee_categories where fee_categories_id=".@$feeID." and br_id=".$_SESSION['br_id'].")";
	
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
		$Errs= "<div class='error'>Related record exists, Please delete the related record</div>";
	else
	{
		$query  = "delete  FROM fee_categories where fee_categories_id=".@$feeID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
		else 
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	}
}

if(@$act=="edit" || @$act=="delete" )
{

	$query   = "SELECT * FROM fee_categories  where fee_categories_id=".@$feeID;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$fee_cat_name=@$row['name'];
		$fee_cat_desc=@$row['description'];
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
		$('#txtfeecatname').keyup(function(){
			var value = $('#this').val();
			if(this.value.match(/[^0-9a-zA-Z ]/g))
			{
				this.value = this.value.replace(/[^0-9a-zA-Z ]/g,'');
			}
		});

		$('#feecatdesc').keypress(function(e){
			   if (e.keyCode == 13) return false
			});
	});
	
function validatefeecat(frm)
{
	if(frm.txtfeecatname.value.trim()=="")
	{
		alert('Fee category name should not be blank');
		frm.txtfeecatname.value="";
		frm.txtfeecatname.focus();
		return false;
	}
	else if(frm.feecatdesc.value.trim()=="")
	{
		alert('Fee category description should not be blank');
		frm.feecatdesc.value="";
		frm.feecatdesc.focus();
		return false;
	}
	else
		return true;
}

function ClearField(frm){
	  frm.txtfeecatname.value = "";
	  frm.feecatdesc.value = "";
	  }
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?>	FEE CATEGORY</h2></div>
		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"	action="fee_category.php?feeID=<?php echo base64_encode(@$feeID);?>&act=<?php echo $act; ?>" onsubmit="return validatefeecat(this);" autocomplete="off">
			<table class="adminform">
				<tr>
					<td width="44%" align="right" class="redstar">Fee Category Name :</td>
					<td width="43%"><input type="text" name="txtfeecatname"	id="txtfeecatname" size="30" value='<?php echo @$fee_cat_name; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Description :</td>
					<td width="43%"><textarea name="feecatdesc" id="feecatdesc"	rows="5" cols="35" maxlength="500" <?php if(@$act=="delete") echo "readonly"; ?> ><?php echo @$fee_cat_desc; ?></textarea></td>
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

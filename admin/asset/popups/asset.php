<?php
@session_start();
include('../../conn.php');
//include('../../check_session.php');
include("../../functions/common.php");

$assetname="";
$assetdesc="";
$assettype="";
makeSafe(extract($_GET));
makeSafe(extract($_POST));
$assetID=base64_decode(@$assetID);

$Errs="";

if(@$action=="SAVE" || @$action=="UPDATE")
{
	if(trim(@$assetname)=="")
		$Errs= "<div class='error'>Please Enter Asset Name</div>";
	elseif(trim(@$assetdesc)=="")
		$Errs= "<div class='error'>Please Enter Asset Description</div>";
	elseif(trim(@$assettype)=="")
		$Errs= "<div class='error'>Please Select Asset Type</div>";
	else
	{
		if(@$action=="SAVE")
		{
			$sql="select * from asset_master where name='".trim(@$assetname)."'  and br_id=".$_SESSION['br_id']." and type='".$assettype."'" ;
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Asset Name</div>";
			else
			{
				$newassetid=getNextMaxId("asset_master","asset_id")+1;
				$sql="insert into asset_master(asset_id,br_id,name,description,type,created_at,updated_at,updated_by)";
				$sql = $sql ." values(".@$newassetid.",".$_SESSION['br_id'].",'".@$assetname."','".@$assetdesc."','".@$assettype."',NOW(),NOW(),'".$_SESSION['emp_name']."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0)
					$Errs= "<div class='success'>Record Saved Successfully</div>";
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";
			}
		}
		elseif(@$action=="UPDATE")
		{
				
			$sql="select * from asset_master where name='".@$assetname."' and br_id=".$_SESSION['br_id']." and type='".$assettype."' and asset_id !=".@$assetID;
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Asset Name</div>";
			else
			{
				$sql="update asset_master set name='".@$assetname."', description='".@$assetdesc."', type='".@$assettype."',updated_by='".$_SESSION['emp_name']."' where asset_id =".@$assetID;
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

	$query   = "select * from asset_record where asset_id=".@$assetID;
	$result  = mysql_query($query,$link) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
		$Errs= "<div class='error'>Related record exists, Please delete the related record</div>";
	else
	{
		$query  = "delete  FROM asset_master where asset_id=".@$assetID;
		$result  = mysql_query($query,$link) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
		else 
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	}
}

if(@$act=="edit" || @$act=="delete" )
{

	$query   = "SELECT * FROM asset_master where asset_id=".@$assetID;
	$result  = mysql_query($query,$link) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$assetname=@$row['name'];
		$assetdesc=@$row['description'];
		$assettype=@$row['type'];
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
<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#assetname').keyup(function(){
			var value = $('#this').val();
			if(this.value.match(/[^0-9a-zA-Z ]/g))
			{
				this.value = this.value.replace(/[^0-9a-zA-Z ]/g,'');
			}
		});

		$('#assetdesc').keypress(function(e){
			   if (e.keyCode == 13) return false
			});
	});
	
function validateasset(frm)
{
	if(frm.assetname.value.trim()=="")
	{
		alert('Asset name should not be blank');
		frm.assetname.value="";
		frm.assetname.focus();
		return false;
	}
	else if(frm.assetdesc.value.trim()=="")
	{
		alert('Asset description should not be blank');
		frm.assetdesc.value="";
		frm.assetdesc.focus();
		return false;
	}
	else if(frm.assettype.value.trim()=="")
	{
		alert('Please select asset type');
		frm.assettype.focus();
		return false;
	}
	else
		return true;
}

function ClearField(frm){
	  frm.assetname.value = "";
	  frm.assetdesc.value = "";
	  frm.assettype.value = "";
	  }
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?>	ASSET</h2></div>

		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"	action="asset.php?assetID=<?php echo base64_encode(@$assetID);?>&act=<?php echo $act; ?>" onsubmit="return validateasset(this);" autocomplete="off">
			<table class="adminform">
				<tr>
					<td width="30%" align="right" class="redstar">Asset Name :</td>
					<td width="43%"><input type="text" name="assetname"	id="assetname" size="25" value='<?php echo @$assetname; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Description :</td>
					<td width="43%"><textarea name="assetdesc" id="assetdesc"	rows="5" cols="35" maxlength="500" <?php if(@$act=="delete") echo "readonly"; ?> ><?php echo @$assetdesc; ?></textarea></td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Type :</td>
					<td width="43%">
					<select name="assettype" id="assettype" <?php if(@$act=="delete") echo "disabled"; ?>>
					<option value="">--Select Asset Type--</option>
					<option value="Fixed" <?php if(@$assettype=="Fixed") echo "selected"; ?> >Fixed</option>
					<option value="Floating" <?php if(@$assettype=="Floating") echo "selected"; ?> >Floating</option>
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
				 <?php if(@$act!="delete"){?> <input type="button"	class="btn reset" value="RESET" id="reset " name="reset"	onClick="ClearField(this.form)" /> <?php }	?>
				 <input type=button	class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
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

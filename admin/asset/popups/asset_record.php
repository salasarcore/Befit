<?php
@session_start();
include('../../conn.php');
//include('../../check_session.php');
include("../../functions/common.php");
include("../../functions/comm_functions.php");

makeSafe(extract($_GET));
makeSafe(extract($_POST));

$assetrecID=base64_decode($assetrecID);

$Errs="";
$uploaddir = "../../../site_img/assets/";
if(@$action=="SAVE" || @$action=="UPDATE")
{
	$newassetrecid=getNextMaxId("asset_record","asset_record_id")+1;
	if(@$received_date!="")
		list($iyear,$imonth,$iday) = explode('-',@$received_date);
	
	if(trim(@$assetid)=="")
		$Errs= "<div class='error'>Please Select Asset Name</div>";
	elseif(trim(@$quantity)=="")
		$Errs= "<div class='error'>Please Enter Quantity</div>";
	elseif(!is_numeric($quantity))
		$Errs= "<div class='error'>Quantity Should Be In Digits</div>";
	elseif(trim(@$price)=="")
		$Errs= "<div class='error'>Please Enter Price Per Unit</div>";
	elseif(trim(@$on_demand)=="")
		$Errs= "<div class='error'>Please Select On Demand</div>";
	elseif(trim(@$received_date)=="")
		$Errs= "<div class='error'>Please Enter Received Date</div>";
	elseif(checkdate( $imonth , $iday , $iyear )==false)
		$Errs= "<div class='error'>Please Enter Valid Received Date</div>";
	else{
		
	
		if(@$action=="SAVE")
		{
			if(@$_FILES["attachfile"]["name"]!="")
			{
				$allowed = array('application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain','application/vnd.ms-excel','application/pdf','text/html','image/gif','image/jpeg','image/jpg','image/pjpeg','image/x-png','image/png');
				$upload="";
				$attachfile="";
				if(!in_array($_FILES['attachfile']['type'], $allowed))
					$Errs= "<div class='error'>Only RTF, DOC, DOCX, HTML, JPG, JPEG, PNG, GIF, PDF, XLS, XLSX and TEXT files are allowed</div>";
				else
				{
					$ext=explode('.', $_FILES["attachfile"]["name"]);
					$tmp_name = $_FILES["attachfile"]["tmp_name"];
					if(@$action=="SAVE") $name=base64_encode($newassetrecid); else $name=base64_encode($assetrecID);
					$filepath=$uploaddir.DOMAIN_IDENTIFIER."_".$name.".".$ext[1];
					if (move_uploaded_file($tmp_name,$filepath ))
						$attachfile=$name.".".$ext[1];
					else
						$Errs= "<div class='error'>File upload failed</div>";
						
				}
			}
				
			if($Errs=="")
			{
				$sql="insert into asset_record(asset_record_id,asset_id,quantity,price,on_demand,received_date,attachment,created_at,updated_at,updated_by)";
				$sql = $sql ." values(".@$newassetrecid.",".@$assetid.",".@$quantity.",'".@$price."','".@$on_demand."','".@$received_date."','".@$attachfile."',NOW(),NOW(),'".$_SESSION['emp_name']."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0)
					$Errs= "<div class='success'>Record Saved Successfully</div>";
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";
			}
		}
		elseif(@$action=="UPDATE")
		{
			$check="";
			if(@$deletefile=="remove")
			{
				$assetrecorddetails=getDetailsById("asset_record","asset_record_id",$assetrecID);
				@unlink($uploaddir.DOMAIN_IDENTIFIER."_".$assetrecorddetails[attachment]);
				$attachfile="";
				$check="removed";
			}
			if(@$_FILES["attachfile"]["name"]!="")
			{
				$assetrecorddetails=getDetailsById("asset_record","asset_record_id",$assetrecID);
				@unlink($uploaddir.DOMAIN_IDENTIFIER."_".$assetrecorddetails[attachment]);
				
				$allowed = array('application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain','application/vnd.ms-excel','application/pdf','text/html','image/gif','image/jpeg','image/jpg','image/pjpeg','image/x-png','image/png');
				$upload="";
				$check="updated";
				if(!in_array($_FILES['attachfile']['type'], $allowed))
					$Errs= "<div class='error'>Only RTF, DOC, DOCX, HTML, JPG, JPEG, PNG, GIF, PDF, XLS, XLSX and TEXT files are allowed</div>";
				else
				{
					$ext=explode('.', $_FILES["attachfile"]["name"]);
					$tmp_name = $_FILES["attachfile"]["tmp_name"];
					if(@$action=="SAVE") $name=base64_encode($newassetrecid); else $name=base64_encode($assetrecID);
					$filepath=$uploaddir.DOMAIN_IDENTIFIER."_".$name.".".$ext[1];
					if (move_uploaded_file($tmp_name,$filepath ))
						$attachfile=$name.".".$ext[1];
					else
						$Errs= "<div class='error'>File upload failed</div>";
			
				}
			}
			
			if($Errs==""){

				$sql="update asset_record set quantity='".@$quantity."', price='".@$price."', on_demand='".@$on_demand."',received_date='".@$received_date."'";
				if(@$check!="") $sql.=	",attachment='".@$attachfile."'";
				
				$sql.=",updated_by='".$_SESSION['emp_name']."' where asset_record_id =".@$assetrecID;
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
		$assetrecorddetails=getDetailsById("asset_record","asset_record_id",$assetrecID);
		@unlink($uploaddir.DOMAIN_IDENTIFIER."_".$assetrecorddetails[attachment]);
		$query  = "delete  FROM asset_record where asset_record_id=".@$assetrecID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
		else 
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	
}

if(@$act=="edit" || @$act=="delete" )
{

	$query   = "SELECT * FROM asset_record where asset_record_id=".@$assetrecID;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$assetid=@$row['asset_id'];
		$quantity=@$row['quantity'];
		$price=@$row['price'];
		$on_demand=@$row['on_demand'];
		$received_date=@$row['received_date'];
		$attachfile=@$row['attachment'];
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
<script type="text/javascript" src="../../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<link rel="Stylesheet" type="text/css" href="../../../css/jquery-ui.css" />
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>

<?php include('../../php/js_css_common.php');?>
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

		$('#quantity').keyup(function(){
			var value = $('#this').val();
			if(this.value.match(/[^0-9]/g))
			{
				this.value = this.value.replace(/[^0-9]/g,'');
			}
		});

		
		$("#cancelimage").hide();
		$("#cancelimage").click(function(){
		$("#attachfile").val("");
		$("#cancelimage").hide();
		});

		$("#delimage").click(function(){
			$("#fileattached").hide();
			$("#delimage").hide();
			$("#deletefile").val('remove');
			});

		
		$("#attachfile").change(function(){
			if($("#attachfile").val()!="")
				$("#cancelimage").show();
			else
				$("#cancelimage").hide();
			});
	});
	
function validateasset(frm)
{
	if(frm.assetid.value=="")
	{
		alert('Please select asset name');
		frm.assetid.value="";
		frm.assetid.focus();
		return false;
	}
	else if(frm.quantity.value.trim()=="")
	{
		alert('Please enter quantity');
		frm.quantity.value="";
		frm.quantity.focus();
		return false;
	}
	else if(frm.price.value.trim()=="")
	{
		alert('Please enter price per unit');
		frm.price.value="";
		frm.price.focus();
		return false;
	}
	else if(frm.on_demand.value.trim()=="")
	{
		alert('Please select on demand');
		frm.on_demand.value="";
		frm.on_demand.focus();
		return false;
	}
	else if(frm.received_date.value.trim()=="")
	{
		alert('Please enter received date');
		frm.received_date.value="";
		frm.received_date.focus();
		return false;
	}
	else if(frm.attachfile.value!="")
	{
		return validateattachfile(frm.attachfile.value);
	}
	else
		return true;
}

function ClearField(frm){
	  frm.assetid.value = "";
	  frm.quantity.value = "";
	  frm.price.value = "";
	  frm.on_demand.value = "";
	  frm.received_date.value = "";
	  frm.attachfile.value = "";
	  frm.assetid.focus();
	  }

function validateattachfile(filename)
{
	var validationStatus = true;
	var parts = filename.split('.');
	
	var exts = ['rtf', 'doc', 'txt', 'html','docx','jpg', 'jpeg', 'png', 'gif', 'pdf', 'xls', 'xlsx'];
     if(exts.indexOf(parts[parts.length-1]) == -1) 
       {
    	 alert('Only RTF, DOC, DOCX, HTML, JPG, JPEG, PNG, GIF, PDF, XLS, XLSX and TEXT files are allowed');
         validationStatus = false;
        }

     return validationStatus;

}
</script>
</head>
<body>
<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?> ASSET RECORD</h2></div>

		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"	action="asset_record.php?assetrecID=<?php echo base64_encode(@$assetrecID);?>&act=<?php echo $act; ?>" onsubmit="return validateasset(this);" autocomplete="off" enctype="multipart/form-data">
			<table class="adminform">
				
				<tr>
					<td width="30%" align="right" class="redstar">Select Asset :</td>
					<td width="43%">
					<select name="assetid" id="assetid" <?php if(@$act=="delete") echo "disabled"; ?>>
					<option value="">--Select Asset Name--</option>
					
					<?php 
					$sql="select * from asset_master where br_id=".$_SESSION['br_id'];
					$res=mysql_query($sql);
					while ($row = mysql_fetch_array($res)) {
						echo "<option value='".$row['asset_id']."'";
						if(@$assetid==$row['asset_id']) echo "selected";
						 echo ">".$row['name']."</option>";
					}
					?>
					</select>
					</td>
				</tr>
				
				<tr>
					<td width="30%" align="right" class="redstar">Quantity :</td>
					<td width="43%"><input type="text" name="quantity"	id="quantity" size="25" value='<?php echo @$quantity; ?>' maxlength="10" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Price Per Unit:</td>
					<td width="43%"><input type="text" name="price"	id="price" onkeyup="if (/[^(\d*?)(\.\d{1,2})?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)(\.\d{1,2})?$]/g,'')" size="25" value='<?php echo @$price; ?>' maxlength="10" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">On Demand :</td>
					<td width="43%">
					<select name="on_demand" id="on_demand" <?php if(@$act=="delete") echo "disabled"; ?>>
					<option value="">--Select On Demand--</option>
					<option value="Y" <?php if(@$on_demand=="Y") echo "selected"; ?> >Yes</option>
					<option value="N" <?php if(@$on_demand=="N") echo "selected"; ?> >No</option>
					</select>
					</td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Received Date:</td>
					<td width="43%">
					<input name="received_date" id="received_date" type="text" class="date" value="<?php echo @$received_date;?>" maxlength="10"	<?php if(@$act=="delete") echo "readonly"; ?>  size="25"/>
					</td>
					<script type="text/javascript">
					  $(function() {
							$( "#received_date" ).datepicker({
								numberOfMonths: [1,2],
								dateFormat: 'yy-mm-dd',
								maxDate: new Date()
							});
						});
				  </script>
					</td>
				</tr>
				<tr>
				<?php if(@$attachfile!=""){?><tr><td align="right">Attached File :</td><td>
				<a href="<?php echo "../../../site_img/assets/".DOMAIN_IDENTIFIER."_".$attachfile; ?>" target="_blank"><img src="../../css/classic/message_attachment.png" id="fileattached" height="20px" width="20px"/></a>
				<img name="image" id ="delimage" alt="cancel" src="../../css/classic/close.png" width="15px" height="15px" title="Delete the file" style="cursor: pointer; vertical-align: top;">
				<input type="hidden" name="deletefile" id="deletefile" value="">
				</td></tr><?php }?>
				
				<tr>
				<td width="30%" align="right">Attach File :</td>
				<td width="30%"><strong><input type="file" name="attachfile" id="attachfile"  <?php if(@$act=="delete") echo "disabled"; ?> /></strong>
				<img name="image" id ="cancelimage" alt="cancel" src="../../css/classic/close.png" width="15px" height="15px" title="De-select the file" style="cursor: pointer; vertical-align: middle;">
				
				</td></tr>
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

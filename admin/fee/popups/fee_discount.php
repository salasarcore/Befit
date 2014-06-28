<?php
@session_start();
include("../../conn.php");
include('../../check_session.php');
include("../../functions/common.php");
include("../../functions/comm_functions.php");

$type="";
$name="";
$act=makeSafe(@$_GET['act']);
?>
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../../modulemaster.php");

if($act=="add")
$id=option_fee_discount_list_add;
elseif($act=="edit")
$id=option_fee_discount_list_edit;
elseif($act=="delete")
$id=option_fee_discount_list_delete;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$name=makeSafe(@$_GET['name']);
$action=makeSafe(@$_POST['action']);
$discountID=makeSafe(@$_GET['discountID']);
$Errs="";
if(@$action=="SAVE" || @$action=="UPDATE")
{
	$particularid=makesafe(@$_POST['fee_particular']);
	$type=makeSafe(@$_POST['txttype']);
	$name=makeSafe(@$_POST['txtname']);
	$description=makeSafe(@$_POST['txtDescription']);
	$discount=makeSafe(@$_POST['txtdiscount']);
	$rdoamount=makeSafe(@$_POST['rdoamount']);
	$getparticulardet=getDetailsById("fee_particulars","fee_particulars_id",$particularid);
	if(trim(@$type)=="")
		$Errs= "<div class='error'>Please Enter Type</div>";
	elseif(trim(@$name)=="")
		$Errs= "<div class='error'>Please Enter Name</div>";
	elseif(trim(@$particularid)=="")
		$Errs= "<div class='error'>Please select Particular Name</div>";
	elseif(trim(@$description)=="")
		$Errs= "<div class='error'>Please Enter Description</div>";
	elseif(trim(@$discount)=="")
		$Errs= "<div class='error'>Please Enter Discount Amount</div>";
	elseif($rdoamount=="amount" && floatval($discount)>floatval($getparticulardet['total_amount']))
		$Errs= "<div class='error'>Discount Can Not Be Greater Than Total Amount Of Fees Particular</div>";
	elseif($rdoamount=="percent" && $discount>100)
		$Errs= "<div class='error'>Discount Percentage Can Not Be Greater Than 100%</div>";
	else
	{
		if($rdoamount=="percent")
			$discount=$getparticulardet['total_amount']*($discount/100);
		
		if($action=="SAVE")
		{
			$newfeediscountid=getNextMaxId("fee_discount","fee_discount_id")+1;
			$sql="select * from fee_discount where name='".trim(@$name)."' and fee_particulars_id=".$particularid;
			$res=mysql_query($sql,$link) or die(mysql_error($link));
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Fee Discount.</div>";
			else
			{
				$sql="insert into fee_discount(fee_discount_id,fee_particulars_id,type,name,description,discount_amount,created_at,updated_at,updated_by)";
				$sql = $sql ." values('". @$newfeediscountid. "','".@$particularid."','".@$type."','".@$name."','".@$description."','".@$discount."',NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				$res=mysql_query($sql,$link) or die(mysql_error($link));
				if(mysql_affected_rows($link)>0){
					$Errs= "<div class='success'>Record Saved Successfully</div>";
					$particularid="";
					$type="";
					$name="";
					$description="";
					$discount="";
					}
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";
					
			}
		}
		else if($action=="UPDATE")
		{
				
			$sql="select * from fee_discount where name='".trim(@$name)."' and fee_particulars_id=".$particularid." and fee_discount_id!=".$discountID;
			$res=mysql_query($sql,$link) or die(mysql_error($link));
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Fee Discount.</div>";
			else
			{
					$sql="update fee_discount set fee_particulars_id=".$particularid.", type='".@$type."',name='".@$name."', description='".@$description."', discount_amount='".@$discount."', updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where fee_discount_id=".@$discountID;			
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
	$getdiscountcolldet=getDetailsById("fee_discount_collection","fee_discount_id",@$discountID);
	if(!empty($getdiscountcolldet))
		$Errs= "<div class='error'>Related record exists, Please delete the related record</div>";
	else
	{
		$query  = "delete FROM fee_discount where fee_discount_id=".@$discountID;
		$result  = mysql_query($query) or die('Error, query failed'.mysql_error());
		if(mysql_affected_rows($link)>0)
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
		else
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	}
}

if(@$act=="edit" || @$act=="delete" )
{
	$row=getDetailsById("fee_discount","fee_discount_id",@$discountID);
	if(!empty($row))
	{
		$particularid=@$row['fee_particulars_id'];
		$getparticulardet=getDetailsById("fee_particulars","fee_particulars_id",$particularid);
		$totalamount=$getparticulardet['total_amount'];
		$type=@$row['type'];
		$name=@$row['name'];
		$description=@$row['description'];
		$discount=@$row['discount_amount'];
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
<title></title>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<SCRIPT>

$(document).ready(function(){
	$("#feeDtls").empty();
	$("#feeDtls").hide();
	$("#fee_particular").focus();
	$('#txtdiscount').keyup(function(){
		var value = $(this).val();
		if(this.value.match(/[^0-9.]/g))
		{
			this.value = this.value.replace(/[^0-9.]/g,'');
		}
	});
});
function validatediscount(frm)
{
	
	if(frm.fee_particular.value==""){
		alert("Please select fee particular name");
		frm.fee_particular.focus();
		return false;
	}
	if(frm.txttype.value.trim()=="")
	{
		alert('Type should not be blank');
		frm.txttype.value="";
		frm.txttype.focus();
		return false;
	}
	if(frm.txtname.value.trim()=="")
	{
		alert('Name should not be blank');
		frm.txtname.value="";
		frm.txtname.focus();
		return false;
	}

	if(frm.txtDescription.value.trim()=="")
	{
		alert('Description should not be blank');
		frm.txtDescription.value="";
		frm.txtDescription.focus();
		return false;
	}
	
	if(frm.txtdiscount.value.trim()=="")
	{
		alert('Discount should not be blank');
		frm.txtdiscount.value="";
		frm.txtdiscount.focus();
		return false;
	}
	if($('input[name="rdoamount"]:checked').val()=="percent" && parseInt(frm.txtdiscount.value)>100)
	{
		alert('Discount percentage can not be greater than 100%');
		frm.txtdiscount.value="";
		frm.txtdiscount.focus();
		return false;
	}
	if($("#txtaction").val()!="delete"){
			
		var totalamt=$("#txtamount").val();
		var discount=frm.txtdiscount.value;
		if($('input[name="rdoamount"]:checked').val()=="percent")
			discount=parseFloat(totalamt)*(parseFloat(discount)/100);
		
		if(!confirm("Discount Amount : "+discount+"\n\nAre you sure to proceed further?")){
			frm.txtdiscount.focus();
			return false;
			}
	}
}

function populateparticular()
{
	if($("#fee_particular").val()!=''){
		var id= $("#fee_particular").val();
    	if($("#fee_particular").val()!=""){
    		$("#txtamount").val($("#fee_particular").children(":selected").attr("id"));
    		$("#feeDtls").show();
    		document.getElementById("feeDtls").innerHTML = "<br><img src='../../../images/loading.gif' alt='Loading..'><br> Please Wait ..";
    		$.ajax({
    	    	type: "GET",
    	    	url:"../ajax/get_particular_details.php",
    		    data: "act=particular&particularid="+id,
    		    success: function(html){
    		    	$("#feeDtls").html(html);
    			}
        	});
		}
		else
		{
			$("#feeDtls").empty();
			$("#feeDtls").hide();
		}
	}
}

function ClearField(frm){
	  frm.txttype.value = "";
	  frm.txtname.value = "";
	  frm.txtDescription.value = "";
	  frm.txtdiscount.value = "";
	  frm.fee_particular.value = "";
	  
	}
</SCRIPT>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2>		<?php echo strtoupper($act); ?>
				FEE DISCOUNT</h2>
		
		</div>

		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frm" action="fee_discount.php?discountID=<?php echo makeSafe(@$discountID);?>&act=<?php echo makeSafe(@$act); ?>" onsubmit="return validatediscount(this);" autocomplete="off">
			<table class="adminform">
				<tr>
					<td width="44%" align="right" class="redstar">Fee Particular Name :</td>
					<td width="43%"><select name="fee_particular" id="fee_particular" <?php if(@$act=="delete") echo "disabled"; ?> onchange="populateparticular();">
					<?php
						$sqlquery=mysql_query("select * from fee_particulars where fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")");
						$res=array();
						while($row=mysql_fetch_array($sqlquery))
							$res[]=$row;
						echo "<option value='' selected>--Select fee Particular--</option>";
						foreach($res as $row1)
						{
							echo"<option value='".$row1['fee_particulars_id']."' id='".$row1['total_amount']."'";
							if(@$particularid==$row1['fee_particulars_id']) echo "selected";
							echo ">".$row1['name']."</option>";
						}
					?>
					</select></td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Type :</td>
					<td><input type="text" name="txttype" id="txttype" size="25" value='<?php echo @$type; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> />
					</td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Name :</td>
					<td><input type="text" name="txtname" id="txtname" size="25" value='<?php echo @$name; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> />
					</td>
				</tr>
				
				<tr>
					<td width="44%" align="right" class="redstar">Description :</td>
					<td><textarea name="txtDescription" cols="20" rows="3" maxlength="200"  <?php if(@$act=="delete") echo "readonly"; ?> ><?php echo @$description; ?></textarea> 
					</td>
				</tr>
				
				<tr>
					<td width="44%" align="right" class="redstar">Discount Amount:</td>
					<td><input type="radio" name="rdoamount" id="rdoamount" checked="checked" value="amount"> Amount
					<input type="radio" name="rdoamount" id="rdoamount" value="percent">Percent(%)
					</td><tr><td></td><td>
					<input type="text" name="txtdiscount" id="txtdiscount" size="25" maxlength="10" value='<?php echo @$discount; ?>' <?php if(@$act=="delete") echo "readonly"; ?> />
					</td>
				</tr>
					  
				<?php if(@$created_at!="") {?><tr><td align="right">Created At :</td><td><?php echo @$created_at; ?></td></tr><?php } ?>
				<?php if(@$updated_at!="") {?><tr><td align="right">Updated At :</td><td><?php echo @$updated_at; ?></td></tr><?php } ?>
				<?php if(@$updated_by!="") {?><tr><td align="right">Updated By :</td><td><?php echo @$updated_by; ?></td></tr><?php } ?>
				<tr>
					<td align="center" colspan=2><input type="submit" class="btn save"
						value='<?php 
	                if(@$act=="add") echo "SAVE";
					if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>'
						name="B1"> <?php if(@$act!="delete"){?> 
						<input type="button" class="btn reset" value="RESET" id="reset " name="reset"	onClick="ClearField(this.form)" /> 
						<?php }	?> 
						<input type=button class="btn close"	value="CLOSE" onClick="parent.emailwindow.close();">
						<input type='hidden' name='action' value='<?php 
			                if(@$act=="add") echo "SAVE";
			                if(@$act=="edit") echo "UPDATE";
			                if(@$act=="delete") echo "DELETE";
						 ?>' />
					</td>
				</tr>
				<tr><td align="center" width="100%" colspan="2"><div id="feeDtls" style="float: left; width: 100%;"></div></td></tr>
			</table>
			<input type="hidden" name="txtamount" id="txtamount" value="<?php echo @$totalamount; ?>" />
			<input type="hidden" id="txtaction" name="txtaction" value="<?php echo @$act; ?>"/>
		</form> 
	</div>
	<script language=javascript>
		document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
	</script>
</body>
</html>

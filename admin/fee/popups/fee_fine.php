<?php
@session_start();
include("../../conn.php");
include('../../check_session.php');
include("../../functions/common.php");
include("../../functions/comm_functions.php");

$action="";
makeSafe(extract($_GET));
?>
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../../modulemaster.php");

if($act=="add")
$id=option_fee_fine_list_add;
elseif($act=="edit")
$id=option_fee_fine_list_edit;
elseif($act=="delete")
$id=option_fee_fine_list_delete;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
makeSafe(extract($_POST));
$Errs="";
if(@$action=="SAVE" || @$action=="UPDATE")
{

	$getparticulardet=getDetailsById("fee_particulars","fee_particulars_id",$fee_particular_id);
	if(trim(@finename)=="")
		$Errs= "<div class='error'>Please Enter Name</div>";
	elseif(trim(@$fee_particular_id)=="")
		$Errs= "<div class='error'>Please select Particular Name</div>";
	elseif(trim(@$fine_amount)=="")
		$Errs= "<div class='error'>Please Enter Fine Amount</div>";
	elseif($rdoamount=="amount" && floatval($fine_amount)>floatval($getparticulardet['total_amount']))
		$Errs= "<div class='error'>Fine Can Not Be Greater Than Total Amount Of Fees Particular</div>";
	elseif($rdoamount=="percent" && $fine_amount>100)
		$Errs= "<div class='error'>Fine Percentage Can Not Be Greater Than 100%</div>";
	elseif(intval(trim(@$fine_amount))<=0)
		$Errs= "<div class='error'>Fine amount should not be less than or equal to Zero(0)</div>";
	else
	{
		if($rdoamount=="percent")
			$fine_amount=$getparticulardet['total_amount']*($fine_amount/100);
		
		if($action=="SAVE")
		{
			$newfeefineid=getNextMaxId("fee_fine_master","fine_id")+1;
			$sql="select * from fee_fine_master where fee_particulars_id=".$fee_particular_id;
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Fee Fine.</div>";
			else
			{
				$sql="insert into fee_fine_master(fine_id,fee_particulars_id,fine_name,fine_amount,created_at,updated_at,updated_by)";
				$sql = $sql ." values('". @$newfeefineid. "','".@$fee_particular_id."','".@$finename."','".@$fine_amount."',NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0){
					$Errs= "<div class='success'>Record Saved Successfully</div>";
					$fee_particular_id="";
					$finename="";
					$fine_amount="";
					}
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";
					
			}
		}
		else if($action=="UPDATE")
		{
				
			$sql="select * from fee_fine_master where fee_particulars_id=".$fee_particular_id." and fine_id!=".$fineID;
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$Errs= "<div class='error'>Duplicate Fee Fine.</div>";
			else
			{
					$sql="update fee_fine_master set fee_particulars_id=".$fee_particular_id.",fine_name='".@$finename."',  fine_amount='".@$fine_amount."', updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where fine_id=".@$fineID;			
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
	$getfinecolldet=getDetailsById("fee_fine_collection","fine_id",@$fineID);
	if(!empty($getfinecolldet))
		$Errs= "<div class='error'>Related record exists, Please delete the related record</div>";
	else
	{
		$query  = "delete FROM fee_fine_master where fine_id=".@$fineID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0){
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
		$fee_particular_id="";
					$finename="";
					$fine_amount="";}
		else
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	}
}

if(@$act=="edit" || @$act=="delete" )
{
	$row=getDetailsById("fee_fine_master","fine_id",@$fineID);
	if(!empty($row))
	{
		$fee_particular_id=@$row['fee_particulars_id'];
		$getparticulardet=getDetailsById("fee_particulars","fee_particulars_id",$fee_particular_id);
		$totalamount=$getparticulardet['total_amount'];
		$finename=@$row['fine_name'];
		$fine_amount=@$row['fine_amount'];
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
<link href="../../css/classic.css" type="text/css" rel="stylesheet">
<?php include('../../php/js_css_common.php');?>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<SCRIPT>

$(document).ready(function(){
	$("#feeDtls").empty();
	$("#feeDtls").hide();
	$("#finename").focus();
	$('#fine_amount').keyup(function(){
		var value = $(this).val();
		if(this.value.match(/[^0-9.]/g))
		{
			this.value = this.value.replace(/[^0-9.]/g,'');
		}
	});
});
function validatefine(frm)
{
	
	
	if(frm.finename.value.trim()=="")
	{
		alert('Fine name should not be blank');
		frm.finename.value="";
		frm.finename.focus();
		return false;
	}
	else if(frm.fee_particular_id.value==""){
		alert("Please select fee particular name");
		frm.fee_particular_id.focus();
		return false;
	}
	else if(frm.fine_amount.value.trim()=="")
	{
		alert('Fine amount should not be blank');
		frm.fine_amount.value="";
		frm.fine_amount.focus();
		return false;
	}
	else if($('input[name="rdoamount"]:checked').val()=="percent" && parseInt(frm.fine_amount.value)>100)
	{
		alert('Fine percentage can not be greater than 100%');
		frm.fine_amount.value="";
		frm.fine_amount.focus();
		return false;
	}
	else if(parseInt(frm.fine_amount.value.trim())<=0)
	{
		alert('Fine amount should not be less than or equal to Zero(0)');
		frm.fine_amount.value="";
		frm.fine_amount.focus();
		return false;
	}
	if($("#txtaction").val()!="delete"){
			
		var totalamt=$("#txtamount").val();
		var fine=frm.fine_amount.value;
		if($('input[name="rdoamount"]:checked').val()=="percent")
			fine=parseFloat(totalamt)*(parseFloat(fine)/100);
		
		if(!confirm("Fine Amount : "+fine+"\n\nAre you sure to proceed further?")){
			frm.fine_amount.focus();
			return false;
			}
	}
}

function populateparticular()
{
	if($("#fee_particular_id").val()!=''){

		var id= $("#fee_particular_id").val();
    	if($("#fee_particular_id").val()!=""){
    		$("#txtamount").val($("#fee_particular_id").children(":selected").attr("id"));
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
		
	}
	else
	{
		$("#feeDtls").empty();
		$("#feeDtls").hide();
	}
}

function ClearField(frm){
	  frm.finename.value = "";
	  frm.fine_amount.value = "";
	  frm.fee_particular_id.value = "";
	}
</SCRIPT>
</head>
<body>
<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?>
				FEE FINE
			</h2>
		</div>

		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frm" action="fee_fine.php?fineID=<?php echo makeSafe(@$fineID);?>&act=<?php echo makeSafe(@$act); ?>" onsubmit="return validatefine(this);" autocomplete="off">
			<?php 
			$sqlquery=mysql_query("select * from fee_particulars where fee_particulars_id not in (select fee_particulars_id from fee_fine_master) and fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")");
			if($act=="add" && $action!="SAVE" && mysql_num_rows($sqlquery)<=0)
				$Errs=  "<div class='error'>Fine to all fee particulars is already assigned.</div>";
				else{
			?>
			<table class="adminform">
				<tr>
					<td width="44%" align="right" class="redstar">Fine Name :</td>
					<td><input type="text" name="finename" id="finename" size="25" value='<?php echo @$finename; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> />
					</td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Fee Particular Name :</td>
					<td width="43%"><select name="fee_particular_id" id="fee_particular_id" <?php if(@$act=="delete") echo "disabled"; ?> onchange="populateparticular();">
					<?php
					if($act=="add")	
					$sqlquery=mysql_query("select * from fee_particulars where fee_particulars_id not in (select fee_particulars_id from fee_fine_master) and fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")");
					else
					$sqlquery=mysql_query("select * from fee_particulars where fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")");
					$res=array();
						while($row=mysql_fetch_array($sqlquery))
							$res[]=$row;
						echo "<option value='' selected>--Select fee Particular--</option>";
						foreach($res as $row1)
						{
							echo"<option value='".$row1['fee_particulars_id']."' id='".$row1['total_amount']."'";
							if(@$fee_particular_id==$row1['fee_particulars_id']) echo "selected";
							echo ">".$row1['name']."</option>";
						}
					?>
					</select></td>
				</tr>
				
							
				<tr>
					<td width="44%" align="right" class="redstar">Fixed Fine Amount:</td>
					<td><input type="radio" name="rdoamount" id="rdoamount" checked="checked" value="amount"> Amount
					<input type="radio" name="rdoamount" id="rdoamount" value="percent">Percent(%)
					</td><tr><td></td><td>
					<input type="text" name="fine_amount" id=fine_amount size="25" maxlength="10" value='<?php echo @$fine_amount; ?>' <?php if(@$act=="delete") echo "readonly"; ?> />
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
			<?php 
			}
			
			
			?>
		</form> 
	</div>
	<script language=javascript>
		document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
	</script>
</body>
</html>

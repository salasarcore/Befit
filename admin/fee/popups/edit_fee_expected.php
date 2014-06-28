<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include("../../functions/common.php");
include("../../functions/comm_functions.php");

$act=makeSafe(@$_GET['act']);
?>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<link rel="Stylesheet" type="text/css" href="../../../css/jquery-ui.css" />
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../../modulemaster.php");


$id=option_fee_expected_list_edit;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$action=makeSafe(@$_POST['action']);
$Errs="";
if(makeSafe(@$_GET['expectedID'])!="") $expectedID=makeSafe(base64_decode(@$_GET['expectedID']));

if(@$action=="UPDATE")
{
	$feeexpectedname=makeSafe($_POST['txtfeeexpectedname']);
	$start=makeSafe($_POST['adtstart']);
	$last=makeSafe($_POST['adtlast']);
	$due=makeSafe($_POST['adtdue']);
	$startdate=strtotime(makeSafe($_POST['adtstart']));
	$lastdate=strtotime(makeSafe($_POST['adtlast']));
	$duedate=strtotime(makeSafe($_POST['adtdue']));

	if(trim(@$feeexpectedname)=="")
		$Errs= "<div class='error'>Please Enter Fee Expected Name</div>";
	else if($lastdate <= $startdate)
		$Errs="<div class='error'>Last Date should be greater than start date.</div>";
	else if($duedate < $lastdate)
		$Errs="<div class='error'>Due Date should be greater than last date.</div>";
	else if($duedate <= $startdate)
		$Errs="<div class='error'>Due Date should be greater than start date.</div>";
	else
	{
		$sql="select * from fee_expected where fee_expected_id!=".@$expectedID." and name='".@$feeexpectedname."'";
		$res=mysql_query($sql,$link) or die("asdas".mysql_error($link));
		if(mysql_affected_rows($link)>0)
			$Errs= "<div class='error'>Duplicate Fee Expected Name.</div>";
		else
		{
			$sql="update fee_expected set name='".$feeexpectedname."', start_date='".@$start."',last_date='".@$last."', due_date='".@$due."', updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where fee_expected_id=".@$expectedID;
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
if(@$act=="edit" || @$act=="delete" )
{
	$row=getDetailsById("fee_expected","fee_expected_id",@$expectedID);
	if(!empty($row))
	{
		$particular_id=@$row['fee_particulars_id'];
		$expected_name=@$row['name'];
		$start_date=@$row['start_date'];
		$last_date=@$row['last_date'];
		$due_date=@$row['due_date'];
		$sections=@$row['section_id'];
		$created_at=date("jS-M-Y, g:iA",strtotime(@$row['created_at']));
		$updated_at=date("jS-M-Y, g:iA",strtotime(@$row['updated_at']));
		$updated_by=@$row['updated_by'];

		$secstr="";
		$secids=explode(",",$row['section_id']);
		foreach($secids as $sec)
		{
			$getdet=getDetailsById("session_section","session_id",$sec);
			$getdeptdet=getDetailsById("mst_departments","department_id",$getdet['department_id']);
			$secstr.=$getdeptdet['department_name']." - ".$getdet['section'].", ";
		}
		$secstr=rtrim($secstr,", ");
	}
}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">


<script>
function validatefeeexpected(frm)
{
	if(frm.txtfeeexpectedname.value.trim()=="")
	{
		alert('Fee expected name should not be blank');
		frm.txtfeeexpectedname.value="";
		frm.txtfeeexpectedname.focus();
		return false;
	}
	if(frm.fee_particular.value==""){
		alert("Please select fee particular name");
		frm.fee_particular.focus();
		return false;
	}
	if(frm.adtstart.value.trim()==""){
		alert("Please select start date");
		frm.adtstart.focus();
		return false;
	}
	if(frm.adtlast.value.trim()==""){
		alert("Please select last date");
		frm.adtlast.focus();
		return false;
	}
	if(frm.adtdue.value.trim()==""){
		alert("Please select due date");
		frm.adtdue.focus();
		return false;
	}
	if (Date.parse($.trim($('#adtlast').val())) <= Date.parse($.trim($('#adtstart').val())))
	{
        alert('Last date should be greater than Start date  ');
        frm.adtlast.focus();
        return false;
	}
	if (Date.parse($.trim($('#adtdue').val())) < Date.parse($.trim($('#adtlast').val())))
	{
        alert('Due date can not be less than last date  ');
        frm.adtdue.focus();
        return false;
	}
}
function ClearField(frm)
{
	  frm.txtfeeexpectedname.value = "";
	  frm.adtstart.value="";
	  frm.adtlast.value="";
	  frm.adtdue.value="";
	  frm.txtfeeexpectedname.focus();
}
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2>EDIT FEE EXPECTED</h2></div>
		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frm"	action="edit_fee_expected.php?act=<?php echo $act; ?>&expectedID=<?php echo base64_encode(@$expectedID);?>"	onsubmit="return validatefeeexpected(this);" autocomplete="off">
			<table class="adminform">
				<tr>
					<td width="44%" align="right" class="redstar">Fee Expected Name :</td>
					<td width="43%">
					<input type="text" name="txtfeeexpectedname" id="txtfeeexpectedname" size="30"	value='<?php echo @$expected_name; ?>' maxlength="200"/>
					</td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Fee Particular Name :</td>
					<td width="43%">
					<select name="fee_particular" id="fee_particular" disabled >
					<?php
					$sqlquery=mysql_query("select * from fee_particulars where fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")");
						$res=array();
						while($row=mysql_fetch_array($sqlquery))
							$res[]=$row;
					echo "<option value='' selected>--Select fee Particular--</option>";
					foreach($res as $row1)
					{
						echo"<option value='".$row1['fee_particulars_id']."' id='".$row1['total_amount']."'";
						if(@$particular_id==$row1['fee_particulars_id']) echo "selected";
						echo ">".$row1['name']."</option>";
					}
						 ?>
					</select>
					</td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Start Date:</td>
					<td width="43%">
					<input name="adtstart" type="text" class="date"	id="adtstart" size="11" value='<?php echo @$start_date; ?>'	readonly />
					<script	type="text/javascript">
					  $(function() {
						$( "#adtstart" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});
					});
				  </script>
				  </td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Last Date:</td>
					<td width="43%">
					<input name="adtlast" type="text" class="date" id="adtlast" size="11" value='<?php echo @$last_date; ?>' readonly />
					<script	type="text/javascript">
				  		$(function() {
						$( "#adtlast" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});
					});
				  </script>
				</td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Due Date:</td>
					<td width="43%">
					<input name="adtdue" type="text" class="date"	id="adtdue" size="11" readonly value='<?php echo @$due_date; ?>' />
					<script	type="text/javascript">
						  $(function() {
							$( "#adtdue" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});
					});
				  </script>
				 </td>
				</tr>
				<tr><td align="right">Selected Sections :</td><td><?php echo @$secstr; ?></td></tr>
				<?php if(@$created_at!="") {?><tr><td align="right">Created At :</td><td><?php echo @$created_at; ?></td></tr><?php } ?>
				<?php if(@$updated_at!="") {?><tr><td align="right">Updated At :</td><td><?php echo @$updated_at; ?></td></tr><?php } ?>
				<?php if(@$updated_by!="") {?><tr><td align="right">Updated By :</td><td><?php echo @$updated_by; ?></td></tr><?php } ?>
				<tr>
					<td align="center" colspan=2>
					<input type="submit" class="btn save" value="UPDATE" name="B1">
					<input type="button" class="btn reset" value="RESET" id="reset " name="reset"	onClick="ClearField(this.form)" />
				 	<input type="button" class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
				 	<input type="hidden" name="action" id="action"	value="UPDATE" />
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

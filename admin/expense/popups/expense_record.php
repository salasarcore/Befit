<?php
@session_start();
include('../../conn.php');
//include('../../check_session.php');
include("../../functions/common.php");
include("../../functions/comm_functions.php");

makeSafe(extract($_REQUEST));

if(@$expense_list_id!="") $expense_list_id=base64_decode($expense_list_id);

$Errs="";
$uploaddir = "../../../site_img/expense/";
if(@$action=="SAVE" || @$action=="UPDATE")
{
	$attachfile="";
	$newexpense_list_id=getNextMaxId("expense_list","expense_list_id")+1;
	if(@$date!="")
		list($iyear,$imonth,$iday) = explode('-',@$date);
	
	if(trim(@$exp_cat_id)=="")
		$Errs= "<div class='error'>Please Select Expense Category Name</div>";
	else if(trim(@$expense_id)=="")
		$Errs= "<div class='error'>Please Select Expense Name</div>";
	elseif(trim(@$date)=="")
		$Errs= "<div class='error'>Please Enter Date</div>";
	elseif(trim(@$amount)=="")
		$Errs= "<div class='error'>Please Enter Amount</div>";
	elseif(trim(@$amount)=="0")
	$Errs= "<div class='error'>Amount can not be 0</div>";
	elseif(trim(@$amount_currency)=="")
		$Errs= "<div class='error'>Please Select Currency Type</div>";
	elseif(trim(@$credited_to)=="")
		$Errs= "<div class='error'>Please Enter Credited To</div>";
	elseif(checkdate( $imonth , $iday , $iyear )==false)
		$Errs= "<div class='error'>Please Enter Valid Received Date</div>";
	else{
		
	
		if(@$action=="SAVE")
		{
			if(@$_FILES["attachfile"]["name"]!="")
			{
				$allowed = array('application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain','application/vnd.ms-excel','application/pdf','image/gif','image/jpeg','image/jpg','image/pjpeg','image/x-png','image/png');
				$upload="";
				
				if(!in_array($_FILES['attachfile']['type'], $allowed))
					$Errs= "<div class='error'>Only RTF, DOC, DOCX, JPG, JPEG, PNG, GIF, PDF, XLS, XLSX and TEXT files are allowed</div>";
				else
				{
					$ext=explode('.', $_FILES["attachfile"]["name"]);
					$tmp_name = $_FILES["attachfile"]["tmp_name"];
					if(@$action=="SAVE") $name=base64_encode($newexpense_list_id); else $name=base64_encode($expense_list_id);
					$filepath=$uploaddir.$name.".".$ext[1];
					if (move_uploaded_file($tmp_name,$filepath ))
						$attachfile=$ext[1];
					else
						$Errs= "<div class='error'>File upload failed</div>";
						
				}
			}
				
			if($Errs=="")
			{
				 $sql="insert into expense_list(expense_list_id,expense_id,date,amount_currency,amount,credited_to,remark,mime,created_at,updated_at,updated_by)";
				 $sql = $sql ." values(".@$newexpense_list_id.",".@$expense_id.",'".@$date."','".@$amount_currency."','".@$amount."','".@$credited_to."','".@$remark."','".@$attachfile."',NOW(),NOW(),'".$_SESSION['emp_name']."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0){
					$Errs= "<div class='success'>Record Saved Successfully</div>";
					$expense_id = "";
					$date = "";
					$amount = "";
					$amount_currency = "";
					$credited_to = "";
					$remark = "";
					$attachfile = "";
				}
				else
					$Errs= "<div class='error'>Record Not Saved Successfully</div>";
			}
		}
		elseif(@$action=="UPDATE")
		{
			$check="";
			if(@$deletefile=="remove")
			{
				$expenserecorddetails=getDetailsById("expense_list","expense_list_id",$expense_list_id);
				@unlink($uploaddir.base64_encode(@$expense_list_id).".".$expenserecorddetails['mime']);
				$attachfile="";
				$check="removed";
			}
			if(@$_FILES["attachfile"]["name"]!="")
			{
				$expenserecorddetails=getDetailsById("expense_list","expense_list_id",$expense_list_id);
				@unlink($uploaddir.base64_encode(@$expense_list_id).".".$expenserecorddetails['mime']);
				
				$allowed = array('application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain','application/vnd.ms-excel','application/pdf','image/gif','image/jpeg','image/jpg','image/pjpeg','image/x-png','image/png');
				$upload="";
				$check="updated";
				if(!in_array($_FILES['attachfile']['type'], $allowed))
					$Errs= "<div class='error'>Only RTF, DOC, DOCX, JPG, JPEG, PNG, GIF, PDF, XLS, XLSX and TEXT files are allowed</div>";
				else
				{
					$ext=explode('.', $_FILES["attachfile"]["name"]);
					$tmp_name = $_FILES["attachfile"]["tmp_name"];
					if(@$action=="SAVE") $name=base64_encode($newexpense_list_id); else $name=base64_encode($expense_list_id);
					$filepath=$uploaddir.$name.".".$ext[1];
					if (move_uploaded_file($tmp_name,$filepath ))
						$attachfile=$ext[1];
					else
						$Errs= "<div class='error'>File upload failed</div>";
			
				}
			}
			
			if($Errs==""){

				$sql="update expense_list set expense_id=".$expense_id.", amount_currency='".@$amount_currency."', amount='".@$amount."',remark='".@$remark."', credited_to='".@$credited_to."',date='".@$date."'";
				if(@$check!="") $sql.=	",mime='".@$attachfile."'";
				
				$sql.=",updated_by='".$_SESSION['emp_name']."' where expense_list_id =".@$expense_list_id;
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
		$expenserecorddetails=getDetailsById("expense_list","expense_list_id",$expense_list_id);
		@unlink($uploaddir.base64_encode(@$expense_list_id).".".$expenserecorddetails['mime']);
		$query  = "delete  FROM expense_list where expense_list_id=".@$expense_list_id;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0){
			$Errs=  "<div class='success'>Record Deleted Successfully</div>";
			$expense_id = "0";
			$date = "";
			$amount = "";
			$amount_currency = "";
			$credited_to = "";
			$remark = "";
			$attachfile = "";
		}
		else 
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	
}

if(@$act=="edit" || @$act=="delete" )
{

	$query="select m.*,l.*,c.name as cat_name from expense_list as l, expense_master as m, expense_category as c where c.exp_cat_id=m.exp_cat_id and c.br_id='".$_SESSION['br_id']."' and l.expense_id=m.expense_id and expense_list_id=".@$expense_list_id;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$exp_cat_id=@$row['exp_cat_id'];
		$expense_id=@$row['expense_id'];
		$date=@$row['date'];
		$amount=@$row['amount'];
		$amount_currency=@$row['amount_currency'];
		$remark=@$row['remark'];
		$credited_to=@$row['credited_to'];
		$attachfile=@$row['mime'];
		$created_at=date("jS-M-Y, g:iA",strtotime(@$row['created_at']));
		$updated_at=date("jS-M-Y, g:iA",strtotime(@$row['updated_at']));
		$updated_by=@$row['updated_by'];
		
	}

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head



<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../css/classic.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="../../../js/ajax.js"></script>
<script type="text/javascript" src="../../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<link rel="Stylesheet" type="text/css" href="../../../css/jquery-ui.css" />
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#credited_to,#amount_currency').keyup(function(){
		var value = $('#this').val();
		if(this.value.match(/[^0-9a-zA-Z ]/g))
		{
			this.value = this.value.replace(/[^0-9a-zA-Z ]/g,'');
		}
		
	});

	$('#remark').keypress(function(e){
		   if (e.keyCode == 13) return false
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
		
		
		$('#exp_cat_id').change(function(){
			if($('#exp_cat_id').val()!=0)
			{
		       $.post("../ajax/get_expense_list.php?action=expns&catid="+$('#exp_cat_id').val(),function(data){
			   $('.expense_id').html(data);
			    });
			} else {

				$('#expense_id')
				   .find('option')
				    .remove()
				    .end()
				    .append('<option value="0">--Select Expense Name--</option>')
				    .val('0');
			  }
	});
});
	
function validateexpense(frm)
{
	if(frm.exp_cat_id.value==0)
	{
		alert('Please select expense category name');
		frm.exp_cat_id.value="";
		frm.exp_cat_id.focus();
		return false;
	}
	else if(frm.expense_id.value=="")
	{
		alert('Please select expense name');
		frm.expense_id.value="";
		frm.expense_id.focus();
		return false;
	}
	else if(frm.date.value.trim()=="")
	{
		alert('Please select date');
		frm.date.value="";
		frm.date.focus();
		return false;
	}
	else if(frm.amount.value.trim()=="")
	{
		alert('Please enter amount');
		frm.amount.value="";
		frm.amount.focus();
		return false;
	}
	else if(frm.amount.value.trim()=='0') 
	{
		alert('Amount can not be 0');
		frm.amount.value="";
		frm.amount.focus();
		return false;
	}
	else if(frm.amount_currency.value.trim()=="")
	{
		alert('Please select currency type');
		frm.amount_currency.value="";
		frm.amount_currency.focus();
		return false;
	}
	else if(frm.credited_to.value.trim()=="")
	{
		alert('Please enter credited to');
		frm.credited_to.value="";
		frm.credited_to.focus();
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
	
	  frm.exp_cat_id.value = "";
	  frm.expense_id.value = "";
	  frm.date.value = "";
	  frm.amount.value = "";
	  frm.amount_currency.value = "";
	  frm.credited_to.value = "";
	  frm.remark.value = "";
	  frm.attachfile.value = "";
	  frm.expense_id.focus();
}

function validateattachfile(filename)
{
	var validationStatus = true;
	var parts = filename.split('.');
	
	var exts = ['rtf', 'doc', 'txt','docx','jpg', 'jpeg', 'png', 'gif', 'pdf', 'xls', 'xlsx'];
     if(exts.indexOf(parts[parts.length-1].toLowerCase()) == -1) 
       {
    	 alert('Only RTF, DOC, DOCX , JPG, JPEG, PNG, GIF, PDF, XLS, XLSX and TEXT files are allowed');
         validationStatus = false;
       }
     return validationStatus;
}
</script>
<?php include('../../php/js_css_common.php');?>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act); ?> EXPENSE RECORD</h2></div>
		<hr class="sidebar" />
		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"	action="expense_record.php?expense_list_id=<?php echo base64_encode(@$expense_list_id);?>&act=<?php echo $act; ?>" onsubmit="return validateexpense(this);" autocomplete="off" enctype="multipart/form-data">
			<table class="adminform">
				<tr>
				<td width="30%" align="right" class="redstar">Select Expense Category : </td>
				<td width="43%"><select name="exp_cat_id" id="exp_cat_id" <?php if(@$act=="delete") echo "disabled";?>>
					<option value=0>--Select Expense Category--</option>
					<?php 
					$sql="select * from expense_category  where br_id=".$_SESSION['br_id'];
					$res=mysql_query($sql);
					while ($row = mysql_fetch_array($res)) {
						echo "<option value='".$row['exp_cat_id']."'";
						if ($act=="edit" || $act=="delete") { if(@$exp_cat_id==$row['exp_cat_id']) echo "selected"; }
						echo ">".$row['name']."</option>";
					}
					?>
					</select></td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Select Expense :</td>
					<td width="43%">  
					<select name="expense_id" class="expense_id" id="expense_id" <?php if(@$act=="delete") echo "disabled"; ?>>
					<option value="">--Select Expense Name--</option>
					<?php if ($act=="edit" || $act=="delete") { 
					$sql="select m.*,c.* from expense_master as m, expense_list as c where m.expense_id=c.expense_id and c.expense_id='".$expense_id."' ";
                	$res=mysql_query($sql);
					$rows = mysql_fetch_array($res); ?>
					<option value="<?php echo$rows['expense_id']; ?>" <?php if($rows['expense_id']==$expense_id) echo "selected";?> > <?php echo $rows['name'];?>(<?php echo $rows['type'];?> ) </option>
					<?php } ?>
   					</select> 
		      	</td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Date :</td>
					<td width="43%">
					<input name="date" id="date" type="text" class="date" value="<?php echo @$date;?>" maxlength="10"	<?php if(@$act=="delete") echo "readonly"; ?>  size="25"/>
					</td>
					<script type="text/javascript">
					  $(function() {
							$( "#date" ).datepicker({
								numberOfMonths: [1,2],
								dateFormat: 'yy-mm-dd',
								maxDate: new Date()
							});
						});
				  </script>
					</td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Amount :</td>
					<td width="43%"><input type="text" name="amount"	id="amount" onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" size="25" value='<?php echo @$amount; ?>' maxlength="10" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Currency Type :</td>
					<td width="43%">
					<select name="amount_currency" id="amount_currency" <?php if(@$act=="delete") echo "disabled"; ?>>
					<option value="">--Select Currency Type--</option>
					<option value="INR" <?php if(@$amount_currency=="INR") echo "selected"; ?> >INR</option>
					<option value="USD" <?php if(@$amount_currency=="USD") echo "selected"; ?> >USD</option>
					</select>
					</td>
				</tr>
				<tr>
					<td width="30%" align="right" class="redstar">Credited To :</td>
					<td width="43%"><input type="text" name="credited_to"	id="credited_to" size="25" value='<?php echo @$credited_to; ?>' maxlength="200" <?php if(@$act=="delete") echo "readonly"; ?> /></td>
				</tr>
				<tr>
					<td width="30%" align="right">Remark :</td>
					<td width="43%"><textarea name="remark" id="remark"	rows="5" cols="20" maxlength="500" <?php if(@$act=="delete") echo "readonly"; ?> ><?php echo @$remark; ?></textarea></td>
				</tr>
				<tr>
				<?php if(@$attachfile!=""){?><tr><td align="right">Attached File :</td><td>
				<a href="<?php echo $uploaddir.base64_encode(@$expense_list_id).".".$attachfile; ?>" target="_blank"><img src="../../css/classic/message_attachment.png" id="fileattached" height="20px" width="20px"/></a>
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

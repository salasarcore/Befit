<?php 
@session_start();
include("../../../globalConfig.php");
include("../../../functions/employee/dropdown.php");
include("../../../functions/dropdown.php");
include("../../../functions/common.php");
include("../../check_session.php");
?>
<?php
$msg = "";
makeSafe(extract($_REQUEST));

?>
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../../modules/js_css_common.php');
include("../../modulemaster.php");

if($act=="add")
$id=option_sms_settings_add;
elseif($act=="edit")
$id=option_sms_settings_edit;
elseif($act=="delete")
$id=option_sms_settings_delete;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}
if(@$action=="ADD" || @$action=="UPDATE")
{
	if($approved_status == "APPROVED")
		$approved_status = 'Y';
	else
		$approved_status = 'N';
	if($template_type == "TRANSACTIONAL")
		$template_type = 'T';
	else
		$template_type = 'P';
	if($modulename == "select")
	{
		$msg = "<div class='error'>Please select a module name.</div>";
	}
	else if($availableid == "select")
	{
		$msg = "<div class='error'>Please select a Template ID.</div>";
	}
	else if($send_type == "")
	{
		$msg = "<div class='error'>Please select send type.</div>";
	}
	else
	{
		$nextId = getNextMaxId("sms_settings","sms_type_id")+1;
		if(@$action=="ADD")
		{
			if($template_id=="")
			$query = "INSERT INTO sms_settings (sms_type_id,module_name,send_type,template,date_updated,updated_by,approved_status,created_on,template_type)
				VALUES($nextId,'$modulename','$send_type','$template',NOW(),'".$_SESSION['emp_name']."','$approved_status',NOW(),'$template_type')";
			else
				$query = "INSERT INTO sms_settings VALUES($nextId,'$modulename','$send_type','$template',NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."','$approved_status',NOW(),$template_id,'$template_type')";
		}
		else if(@$action=="UPDATE")
		{
			if($template_id=="")
				$query = "UPDATE sms_settings SET module_name='$modulename',send_type='$send_type',template='$template',date_updated=NOW(),updated_by='".$_SESSION['emp_name']."[".$_SESSION['emp_id']."]',
				approved_status='$approved_status',template_type='$template_type' WHERE sms_type_id=$sms_type_id";
			else
				$query = "UPDATE sms_settings SET module_name='$modulename',send_type='$send_type',template='$template',date_updated=NOW(),updated_by='".$_SESSION['emp_name']."[".$_SESSION['emp_id']."]',
				approved_status='$approved_status',template_id=$template_id,template_type='$template_type' WHERE sms_type_id=$sms_type_id";		
		}
		$res = mysql_query($query,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused.");
		if(mysql_affected_rows($link) > 0)
		{
			if(@$action=="ADD")
				$msg = "<div class='success'>Record Added Successfully.</div>";
			else if(@$action=="UPDATE")
				$msg = "<div class='success'>Record Updated Successfully.</div>";
		}
		else if(mysql_affected_rows($link) == 0)
		{
			$msg = "<div class='success'>No Data Changed</div>";
		}
		else
		{
			$msg = "<div class='error'>Error occurred.</div>";
		}
	}
}
if(@$action=='DELETE')
{
	$query = "DELETE FROM sms_settings WHERE sms_type_id=$sms_type_id";
	$res = mysql_query($query,$link) or die('Query failed. We are sorry for the inconvenience caused.');
	if(mysql_affected_rows($link) > 0)
	{
		$msg = "<div class='success'>Record deleted successfully.</div>";
	}
	else
	{
		$msg = "<div class='error'>Unable to delete record. Please try again later..</div>";
	}
}
if(@$act == 'edit' || @$act == 'delete')
{
$query = "SELECT * FROM sms_settings WHERE sms_type_id = $sms_type_id";
$run = mysql_query($query,$link);
@$row = mysql_fetch_assoc($run);
}
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>SMS Settings</title>
<link rel="shortcut icon" href="../favicon.ico">
<script type="text/javascript"   src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<link rel="Stylesheet" type="text/css" href="../../../css/jquery-ui.css" />
<script language="javascript" type="text/javascript" src="../../../js/date_time_currency_number_email.js"></script>
<script>
$(document).ready(function(){	
	 var request = <?php echo json_encode(makeSafe(($_REQUEST['act']))); ?>;
	 
	 if(request == 'add')
	 {
		var url="../ajax/getModuleList.php?act=add";
		makePOSTRequest(url,'','modulename');
	 }	
	$('#modulename').change(function(){
		document.frm.approved_status.value = "";
		document.frm.send_type.value = "select";
		document.frm.template.value = "";
		document.frm.template_type.value = "";
		document.frm.template_id.value = "";
		$.post("../ajax/getAvailableTemplateList.php?moduleid="+$('#modulename').val(),function(data){
			$('#availableid').html(data);
		});
	});
	$('#availableid').change(function(){
		if($('#availableid').val()=='select')
		{
			document.frm.approved_status.value = "";
			document.frm.send_type.value = "select";
			document.frm.template.value = "";
			document.frm.template_type.value = "";
			document.frm.template_id.value = "";
		}
		else
		{
			var datastring="moduleid="+$('#availableid').val();
			$.ajax({
		  		type: "GET",
				url:"../ajax/get_template_data.php",
				data: datastring,
				 dataType:"JSON",
				success: function(data)
				{
				   var values=data;
			       document.frm.template.value=values.template_format;
			       document.frm.approved_status.value=values.approved;
			       if(values.approved == "Y")
			    	   document.frm.approved_status.value = "APPROVED";
			       else
			    	   document.frm.approved_status.value = "NOT APPROVED";
			       if(values.template_type == "T")
			    	   document.frm.template_type.value= "TRANSACTIONAL";
			       else
			    	   document.frm.template_type.value= "PROMOTIONAL";
				   document.frm.template_id.value=values.template_id;
				}
		  	});
		}
	});
});

function check()
{
	if($('#modulename').val() == "select")
	{
		alert('Please select a module name.');
		$('#modulename').focus();
		return false;
	}
	if($('#availableid').val() == "select")
	{
		alert('Please select Template ID.');
		$('#availableid').focus();
		return false;
	}
	if($('#send_type').val() == "select")
	{
		alert('Please select send type.');
		$('#send_type').focus();
		return false;		
	}
}

</script>
</head>
<body leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0" style="text-align: left">

<div id="middleWrap">
		<div class="head"><h2><?php echo ucfirst(@$act).' SMS Settings';?></h2></div>

   
   <span id="spErr"><?php echo @$msg;?> </span>
	<form action="sms_settings_func.php?act=<?php echo @$act;?>&sms_type_id=<?php echo @$sms_type_id;?>" method="post" name="frm">
	<table border="0" cellspacing="0" cellpadding="0" align="center" class="adminlist">	
	  <tr>
		<td align="right" class="redstar">Module Name :</td>
		<td>
		<?php if(@$act == "edit" || @$act == "delete"){?>
		<label for="modulename"><?php echo @$row['module_name'];?></label>
		<input type="hidden" value="<?php echo @$row['module_name'];?>" name="modulename" />				
		<?php }
		else{?>
		<select name="modulename" id="modulename" <?php if(@$act=="delete") echo "disabled";?>></select>
		<?php }?>
		</td>
	  </tr>
	  <tr>
		<td align="right" class="redstar"><?php if(@$act=="add") echo "Available Template IDs :"; else echo "Selected Template ID :";?></td>
		<td>
		<?php if(@$act == "edit" || @$act == "delete"){?>
		<label for="availableid"><?php echo @$row['template_id'];?></label>
		<input type="hidden" value="<?php echo @$row['template_id'];?>" name="availableid" />
		<?php } 
		else {?>
		<select name="availableid" id="availableid" <?php if(@$act=="delete") echo "disabled";?>></select>
		<?php }?>
		</td>
	  </tr>
	  <tr>
		<td align="right" class="redstar">Send Type :</td>
		<td colspan=3>
		<select name="send_type" id="send_type" <?php if(@$act=="delete") echo "disabled"; ?>>
		<option name="select" value="select">SELECT</option>
		<option name="A" value="A" <?php if(@$row['send_type']=='A'){?>selected="selected"<?php }?>>AUTO</option>
		<option name="M" value="M" <?php if(@$row['send_type']=='M'){?>selected="selected"<?php }?>>MANUAL</option>
		</select>
		</td>			
	  </tr>
	  <tr>
		<td align="right" class="redstar">Approved Status :</td>
		<td colspan=3><input type="text" name="approved_status" id="approved_status" size="50" 
		value="<?php if(@$act=="edit" || @$act=="delete"){if(@$row['approved_status']=="Y") echo "APPROVED"; else if(@$row['approved_status']=="N") echo "NOT APPROVED"; else echo '';}?>"<?php if(@$act=="delete") echo "disabled"; else echo "readonly";?>/></td>
	  </tr>
	  <tr>
		<td align="right" class="redstar">Template :</td>
		<td colspan=3>
		<textarea type="text" id="template" name="template" rows="5" cols="37"<?php if(@$act=="delete") echo "disabled"; else echo "readonly";?>><?php if(@$act=="edit" || @$act=="delete") echo trim(@$row['template']);?></textarea>
		</td>		
	  </tr>
	  <tr>
		<td align="right" class="redstar">Template Type :</td>
		<td colspan=3><input type="text" name="template_type" id="template_type" size="50" 
		value="<?php if(@$act=="delete" || @$act=="edit") {if(@$row['template_type']=="T") echo "TRANSACTIONAL"; else if(@$row['template_type']=="P") echo "PROMOTIONAL"; else echo '';}?>"
		<?php if(@$act=="delete") echo "disabled"; else echo "readonly";?>/></td>
	  </tr>
	  
	<input type="hidden" name="template_id" id="template_id" size="50" value="<?php echo @$row['template_id'];?>"<?php if(@$act=="delete") echo "disabled"; else echo "readonly";?>/>
	  <tr><td colspan="2" align="center"><input type="submit" class="btn save" onclick="return check();" value='<?php if(@$act=="edit") echo "UPDATE"; else if(@$act=="add") echo "ADD"; else echo "DELETE";?>' name="B1">
		<input type="button" class="btn close" value="CLOSE" name="close" onClick="parent.emailwindow.close();"/>
		<input type="hidden" name='action' value='<?php if(@$act=="edit") echo "UPDATE"; else if(@$act=="add") echo "ADD"; else echo "DELETE";?>' />
		<input type="hidden" name='sms_type_id' value='<?php echo @$sms_type_id; ?>'/>
		</td>
</tr>
</table>
</form>
</div>
</body>
</html>
<?php
@session_start ();
include ('../../../globalConfig.php');
include ('../../check_session.php');
include ('../../../functions/functions.php');
include ('../../../functions/common.php');
include("../../functions/comm_functions.php");
$action = "";
$act = "";
$Errs="";
$stuids = array ();
makeSafe ( extract ( $_REQUEST ) );
if (trim($action) == "SAVE") {
	if(trim($message_text)=="")
		$Errs=  "<div class='error'>Please enter message text.</div>";
	else{
	preg_match_all('/((#\w+\b#))/i', $message_text, $matches);
   for ($i = 0; $i < count($matches[1]); $i++) {
    $key = $matches[1][$i];
    $value = $matches[2][$i];
    $$key = $value;
    $message_text=str_replace($key,$hashvalues[$i],$message_text);
   }
	$stuids = explode ( ",", $values );

	if (! empty ( $stuids )) {
		foreach ( $stuids as $stuid ) {
			$studetails=getDetailsById("mst_students","stu_id",$stuid);
			$logid = getNextMaxId ( "sms_transaction_log", "log_id" ) + 1;
			mysql_query("insert into sms_transaction_log values(".$logid.",'".$studetails['stu_fname']."','".trim($message_text)."','".$studetails['mob']."','PENDING','$send_to','transaction_id',NOW())");
		}
		$Errs= "<div class='success'>Messages sent successfully.</div>";
	}
	}
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../css/classic.css" type="text/css" rel="stylesheet" />
<link rel="icon" href="images/icon.ico" />
<link href="css/classic.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../../css/ddsmoothmenu.css" />
<link href="../../../css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="../../../css/dhtmlwindow.css" rel="stylesheet" type="text/css" />
<link href="../../../css/modal.css" rel="stylesheet" type="text/css" />
<link rel="Stylesheet" type="text/css" href="../../../css/jquery-ui.css" />
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/ddsmoothmenu.js"></script>
<script type="text/javascript" src="../../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/dhtmlwindow.js"></script>
<script type="text/javascript" src="../../../js/modal.js"></script>
<style type="text/css">
textarea#message_text{
color:#666;
font-size:13px;
margin:5px 0px 5px 0px;
padding:5px;
height:100px;
width:300px;
border:#999 1px solid;
font-family:"Lucida Sans Unicode", "Lucida Grande", sans-serif;
transition: all 0.25s ease-in-out;
-webkit-transition: all 0.25s ease-in-out;
-moz-transition: all 0.25s ease-in-out;
box-shadow: 0 0 5px rgba(81, 203, 238, 0);
-webkit-box-shadow: 0 0 5px rgba(81, 203, 238, 0);
-moz-box-shadow: 0 0 5px rgba(81, 203, 238, 0);
}
</style>
<script>
function showMsg()
{
	var templateid = $('#template_id').val(); 
	$('#message_text').empty();
	var datastring = "templateid="+templateid;

	$.ajax({
  		type: "GET",
		url:"../ajax/getTemplateMessage.php",
		data: datastring,
		 dataType:"JSON",
		success: function(data)
		{
		   document.frm.message_text.value = data.value;
		   var count = data.count;
		   document.frm.hidden.value = count;
		   $('#textboxes').html("");
		   for(var i=1; i<=count;i++)
		   {
		   $('#textboxes').append('VALUE '+i+' : <input type="text" name="hashvalues[]" class="hashvalues" id="hashvalues'+i+'"/><br/>');
		   }
		}
  	});	
}
function check()
{
	if($('#send_to').val() == "select")
	{
		alert('Please select who you want to send message');
		$('#send_to').focus();
		return false;
	}
	else if($('#template_id').val() == "select")
	{
		alert('Please select a template');
		$('#template_id').focus();
		return false;
	}
	var empties = $('.hashvalues').filter(function () {
	     return $.trim($(this).val()) == '';
	 });
	 if (empties.length){
	  alert("Please enter all values");
	  return false;
	  }
}

function limitText(limitField, update){
	sms=1;
    limit = 144;
    var count=Math.ceil(limitField.value.length/limit);
	if(count==0) count=1;
    document.getElementById(update).innerHTML = limitField.value.length;
    document.getElementById("smscount").innerHTML = count;
  }
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2>STUDENT TRANSACTIONAL SMS</h2></div>

	<span id="spErr"><?php echo $Errs;?> </span>
		<form id="frm" name="frm" method="post"	action="send_student_tran_sms.php?act=<?php echo $act; ?>&values=<?php echo $values; ?>" onsubmit="return check();">
			<table class="adminform" border="0" align="center">
			<tr>
					<td align="right" class="redstar">SEND TO :</td>
					<td>
					<select style="width: 110px;" name="send_to" size="1" id="send_to">
							<option value="select" selected>-----Select------</option>
							<option value="STUDENT">STUDENT</option>
							<option value="PARENT">PARENT</option>
					</select>
					</td>
				</tr>
				<tr>
					<td align="right" class="redstar">TEMPLATE ID :</td>
					<td>
					<select style="width: 110px;" name="template_id" size="1" id="template_id" onchange="showMsg()">
							<option value="select" selected>-----Select------</option>
					<?php 
					$sql = "SELECT * FROM global_sms_templates WHERE template_type='T' AND approved='Y'";
					$res = mysql_query($sql) or die('Sorry.');
					while($row = mysql_fetch_assoc($res))
					{
					?>
					<option value="<?php echo $row['template_id'];?>"><?php echo $row['template_id']?></option>
					<?php }?>
					</select>
					</td>
				</tr>
				<tr>
					<td align="right" class="redstar"><label for="message">MESSAGE :</label></td>
					<td>
					<textarea name='message_text' id='message_text' rows='7' cols='30' readonly></textarea>
					</td>
				</tr>
				<tr> 	
				<td></td>
				<td align="left" id="textboxes"></td>			
				</tr>
				<tr>
					<td colspan="2" align="center" style="padding-top: 5px;"><input type="submit" class="btn save" value='SEND'>
					<input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close();"></br></br>
					<input type='hidden' name='action'	value='
					<?php		
						if (@$act == "add")
							echo "SAVE";
						if (@$act == "edit")
							echo "UPDATE";
						if (@$act == "delete")
							echo "DELETE";
						?>' /></td>
				</tr>
			</table>
		</form>
	</div>
		<script language=javascript>
		document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
	</script>
</body>
</html>
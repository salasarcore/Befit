<?php
@session_start ();
include ('../../../globalConfig.php');
include ('../../check_session.php');
include ('../../../functions/functions.php');
include ('../../../functions/common.php');
include("../../functions/comm_functions.php");
require '../SmsSystem.class.php';

$action = "";
$act = "";
$Errs="";
$stuids = array();
$smssend = new SMS(); //create object instance of SMS class
makeSafe ( extract ( $_REQUEST ) );
if (trim($action) == "SAVE") {
	if(trim($message_text)=="")
		$Errs=  "<div class='error'>Please enter message text.</div>";
	else if($send_to=='select')
		$Errs=  "<div class='error'>Please select who you want to send message to..</div>";
	else{
	$stuids = explode ( ",", $values );

	if (! empty ( $stuids )) {
		foreach ( $stuids as $stuid ) {
			$studetails=getDetailsById("mst_students","stu_id",$stuid);
			$result = $smssend->sendSMS($studetails['mob'],trim($message_text));
			if($send_to=="STUDENT") $send_to="MEMBER";
			$query = $smssend->insertLog($result,$studetails['stu_fname'],trim($message_text),$studetails['mob'],$send_to);
			if($query)
			{
				$Errs= "<div class='success'>Data inserted into transaction log successfully.</div>";
			}
			else
			{
				$Errs=  "<div class='error'>Error occurred. Please try again later.</div>";
			}
		}
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
function check()
{
	if($('#send_to').val() == "select")
	{
		alert('Please select who you want to send message to.');
		$('#send_to').focus();
		return false;
	}
	if($.trim($('#message_text').val()) == "")
	{
		alert("Please enter message text.");
		$('#message_text').val('');
		$('#message_text').focus();
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
		<div class="head"><h2>STUDENT SMS</h2></div>

	<span id="spErr"><?php echo $Errs;?> </span>
		<form id="frm" name="frm" method="post"	action="send_student_sms.php?act=<?php echo $act; ?>&values=<?php echo $values; ?>" onsubmit="return check();">
			<table class="adminform" border="0" align="center">
			<tr>
					<td align="right" class="redstar">SEND TO :</td>
					<td>
					<select name="send_to" size="1" id="send_to">
							<option value="select" selected>---Select---</option>
							<option value="STUDENT">STUDENT</option>
							<option value="PARENT">PARENT</option>
					</select>
					</td>
				</tr>
				<tr>
					<td align="right" class="redstar"><label for="message">MESSAGE :</label></td>
					<td>
					<textarea name="message_text" id="message_text" rows="7" cols="30" onKeyDown="limitText(this, 'countdownArea')" onKeyUp="limitText(this, 'countdownArea')"></textarea>
					</td>
				</tr>
				<tr><td style="color: #FF0000;" align="right" colspan="2">SMS Count : <span id="smscount" >1</span> Character Count : <span id="countdownArea" >0</span></td></tr>
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
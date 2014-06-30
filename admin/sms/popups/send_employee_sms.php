<?php
@session_start ();
include ('../../conn.php');
include ('../../check_session.php');
include ('../../functions/functions.php');
include ('../../functions/common.php');
include("../../functions/comm_functions.php");
require '../SmsSystem.class.php';
$action = "";
$act = "";
$Errs="";
makeSafe(extract($_REQUEST));
?>
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../../php/js_css_common.php');
/*include("../../modulemaster.php");

$id=option_manual_employee_sms_sendsms;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
<link rel="Stylesheet" href="../../../js/jquery.ui.timepicker.css" rel="stylesheet"/>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script src="../../../js/jquery.ui.timepicker.js"></script>
<style type="text/css">
textarea#message_text{
color:#666;
font-size:13px;
-moz-border-radius: 8px; -webkit-border-radius: 8px;
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
String.prototype.replaceAt=function(index, character) {
	return this.substr(0, index) + character + this.substr(index+character.length);
}
function check()
{
	var schedule_date = $('#schedule_date').val();
	var schedule_time = $('#schedule_time').val();
	var lowerlimit = Date.parse('01/01/2011 9:10:00');
	var upperlimit = Date.parse('01/01/2011 20:45:00');
	var time = Date.parse('01/01/2011 '+schedule_time);
	var checktime1 = Date.parse('01/01/2011 12:00:00');
	var checktime2 = Date.parse('01/01/2011 12:59:00');
	if($('#schedulecheck').attr('checked'))
    {
        if($('#schedule_date').val()=="")
        {
            alert('Please enter schedule date');
            $('#schedule_date').focus();
            return false;
        }
        else if(schedule_time=="")
        {
            alert('Please select schedule time');
            $('#schedule_time').focus();
            return false;
        }
        else if(time < lowerlimit || time > upperlimit)
        {
            alert("You can't schedule mesages in between 8:45pm - 9:10am.");
            $('#schedule_time').focus();
            return false;
        }
    }
	if(time >= checktime1 && time <= checktime2)
    {
    	schedule_time = schedule_time.replaceAt(0,"2");
    	schedule_time = schedule_time.replaceAt(1,"4");
    	$('#schedule_time').val(schedule_time);
    }
	var smscount=<?php echo json_encode($_SESSION['sms_p_count']); ?>;
	if(smscount <= 0)
	{
		alert('You have exceeded your maximum SMS sending limit. Please contact the system administrator for more details.');
    	return false;
	}
	else if($.trim($('#message_text').val()) == "")
	{
		alert("Please enter message text.");
		$('#message_text').val('');
		$('#message_text').focus();
		return false;
	}
	else
	{
		$('#spErr').html('<img id="loadingImg" src="../../../css/classic/loading.gif" />Sending Message(s)...');
		var url = "../ajax/smsSubmitForm.php?act=search&type=P&sendtype=E";
		$.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            data: $("#frm").serialize(),
            success: function(data)
            {
            	$("#spErr").empty();
            	$("#spErr").html(data.msg);
            	$("#count").html(data.count);
            }
          });
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
<script>
$(document).ready(function(){
	$("#count").html(<?php if(@$_SESSION['sms_p_count'] < 0) echo json_encode("0"); else echo json_encode(@$_SESSION['sms_p_count']);?>);
	$('#schedulecheck').change(function(){
		if($('#schedulecheck').attr('checked'))
			$('#datetimebox').fadeIn('slow');
		else
			$('#datetimebox').fadeOut('slow');
	});
	if($('#schedulecheck').attr('checked'))
    {
        if($('#schedule_date').val()=="")
        {
            alert('Please enter schedule date');
            $('#schedule_date').focus();
        }
        else if($('#schedule_time').val()=="")
        {
            alert('Please select schedule time');
            $('#schedule_time').focus();
        }
    }
});
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2>MANUAL EMPLOYEE SMS</h2></div>

	<span id="spErr"></span>
		<form id="frm" name="frm" method="post"	action="send_employee_sms.php?act=<?php echo $act; ?>&values=<?php echo $values; ?>" onsubmit="return check();">
		<div style="padding-left: 10px;">
		<span style="color: #FF0000;">Total Remaining Promotional SMS : <label style="display: inline; font-size: 12px;" id="count"></label></span>
			<table class="adminform" border="0" align="center">
				<tr>
					<td align="right" class="redstar">MESSAGE :</td>
					<td>
					<textarea name="message_text" id="message_text" rows="7" cols="30" onKeyDown="limitText(this, 'countdownArea')" onKeyUp="limitText(this, 'countdownArea')"></textarea>
					</td>
				</tr>
				<tr><td style="color: #FF0000;" align="right" colspan="2">SMS Count : <span id="smscount" >1</span> Character Count : <span id="countdownArea" >0</span></td></tr>
				</tr>
				<tr>
				<td></td>
				
				<!-- <td><input type="checkbox" id="schedulecheck" name="schedulecheck"/> <span style="font-weight: bold; color: green;"> CHECK TO SCHEDULE MESSAGE</span>
				<div id="datetimebox" style="display: none;">
				<input name="schedule_date" placeholder="Date" type="text" class="date" id="schedule_date" size="11" maxlength="10" readonly /> 
				<script type="text/javascript">
				  $(function() {
						$( "#schedule_date" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'dd-mm-yy',
							minDate: new Date()
						});
					});
				</script> <br/>
				<input name="schedule_time" placeholder="Time" type="text" class="time" id="schedule_time" size="11" maxlength="10" readonly /> 
				<script>
        	$('#schedule_time').timepicker({
            	'minutes': { interval: 1 },
            	'timeFormat': "HH:mm",
            	minTime: new Date().getTime(),
                'showDuration': true,
            	rows:5
            	});
        	</script>				
								
				</div>
				</td>-->
				</tr>
				<tr>
				<input type="hidden" name="count" id="count" value="<?php echo @$_SESSION['sms_p_count'];?>"/>
				<input type="hidden" name="values" id="values" value="<?php echo @$values;?>"/>
					<td colspan="2" align="center" style="padding-top: 5px;"><input type="button" value='SEND' onclick="return check();">
					<input type=button value="CLOSE" onClick="parent.emailwindow.close();"></br></br>
					<input type='hidden' name='action'	value='<?php if (@$act == "add") echo "SAVE"; if (@$act == "edit") echo "UPDATE"; if (@$act == "delete") echo "DELETE";?>' /></td>
				</tr>
			</table>
			</div>
		</form>
	</div>
		<script language=javascript>
		document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
	</script>
</body>
</html>
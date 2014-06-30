<?php 
require_once 'conn.php';
include_once 'SmsSystem.class.php';
include_once("functions/common.php");
include('check_session.php');
include_once("functions/comm_functions.php");
error_reporting( E_ALL ^ E_DEPRECATED ^ E_NOTICE);
require('ExcelUpload/phpExcelReader/Excel/reader.php');
$msg="";
$statusresult = array();
makeSafe(extract($_REQUEST));
$br_id=makeSafe(@$_POST['branches']);
$Errs="";
$smssend = new SMS(); //create object instance of SMS class
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_nonregistered_sms,$modules))
{
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_nonregistered_sms))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
}
else
{
	echo "<script>location.href='pages.php';</script>";
	exit;
}*/
if(makeSafe(isset($_REQUEST['insert'])))
{
	$excelarray = array();
	$filename= $_FILES["file"]["name"];
	if(($_FILES["file"]["type"] == "application/vnd.ms-excel"))
	{
		$extn=explode('.',$_FILES["file"]["name"]);
		$upath="ExcelUpload/phpExcelReader/temp/".$filename;
		move_uploaded_file($_FILES['file']['tmp_name'],$upath);
	}
	function  add_person($name,$mobile)
	{
		global $ExcelData;
		{
			$ExcelData[] = array('name' => $name,'mobile' => $mobile);
		}
	}
	$ExcelData = array();
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('CP1251');
	$file = "ExcelUpload/phpExcelReader/temp/".$filename;
	$data->read($file);
	for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++)
	{
		for($j = 1; $j <= $data->sheets[0]['numCols']; $j++)
		{
			$name='';
			$mobile='';
			$name = $data->sheets[0]['cells'][$i][1];
			$mobile=$data->sheets[0]['cells'][$i][2];
		}
		add_person($name,$mobile);
	}
	$i=2;
	$counter = 0;
	$length = count($ExcelData);
	foreach($ExcelData as $rowData)
	{
		if($rowData['name']=='')
			$msg.= "<div class='error'>Name should not be blank at line ".$i."</div><br>";
		if($rowData['mobile']=='')
			$msg.= "<div class='error'>Mobile number should not be blank at line ".$i."</div><br>";
		if(!is_numeric($rowData['mobile']))
			$msg.= "<div class='error'>Mobile number should be numbers only at line ".$i."</div><br>";
		if(in_array($rowData['mobile'],$excelarray))
			$msg.= "<div class='error'>Duplicate mobile number on line ".$i."</div><br>";		
		if(!preg_match('/^\d{10}$/', $rowData['mobile']))
		{
			$msg.= "<div class='error'>Mobile number should be 10 digits only at line ".$i."</div><br>";
		}
		if(trim($msg)=="") //indicates no errors in the uploaded excel file
		{
			$statusresult['status'] = 'true';
			$statusresult['msg'] = "Excel Data Uploaded Successfully! Proceed To Sending SMS.";
			$statusresult['name'.$counter] = $rowData['name'];
			$statusresult['mobile'.$counter] = $rowData['mobile'];
		}
		else //indicates that there are errors in the uploaded excel file
		{
			$statusresult['status'] = 'false';
			$statusresult[$counter] = $msg;
		}
		$i++;
		$counter++;
		array_push($excelarray,$rowData['mobile']);
	}
}
?>
<link rel="Stylesheet" href="../js/jquery.ui.timepicker.css" rel="stylesheet"/>
<script src="../js/jquery.ui.timepicker.js"></script>
<script>
String.prototype.replaceAt=function(index, character) {
	return this.substr(0, index) + character + this.substr(index+character.length);
}
function getLineNumber()
{
	var lines = $('#mobilenos').val().split('\n');
	for(var i = 0;i < lines.length;i++)
	{
		var t = $("#mobilenos")[0];
	    var number = t.value.substr(0, t.value.length).split("\n")[i];
	    if((number.length) != 10)
	    {
	    	alert('Mobile number should be 10 digits only. Invalid mobile number "'+number+'" entered on line '+(i+1));
	    	document.frm.mobilenos.focus();
	    	break;
	    }
	}
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
	
	var isset = <?php echo json_encode($statusresult['status']);?>;
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
        if(time >= checktime1 && time <= checktime2)
        {
        	schedule_time = schedule_time.replaceAt(0,"2");
        	schedule_time = schedule_time.replaceAt(1,"4");
        	$('#schedule_time').val(schedule_time);
        }
    }
	if(($.trim($('#message_text').val())==""))
	{
		alert('Please enter message text');
		return false;
	}
	else if(($.trim($('#message_text').val())!="") && (($.trim($('#mobilenos').val())=="") && !($('#senddatabase').is(':checked')) && !($('#uploaddatabase').is(':checked')) && ($.trim($('#mobile').val())=="")))
	{
		alert('Please enter Mobile Numbers or Select from Database or Upload Excel.');
		return false;
	}
	else if(($('#uploaddatabase').is(':checked')) && (isset == "" || isset == null))
	{
		alert('Please upload the selected excel file.');
		return false;
	}
	else
	{
		$('#spErr').html('<img id="loadingImg" src="../../css/classic/loading.gif" />Sending Message(s)...'); //Sending Message(s)...
	 	var url = "sms/ajax/nonregisteredSubmitForm.php";
	 	$.ajax({
        type: "GET",
        cache: false,
        async: true,
        url: url,
        dataType: 'json',
        data: $("#frm").serialize(),
        success: function(data)
        {
        	$('#excelmsg').hide();
        	$('#excelmsg').empty();
        	$("#spErr").empty();
        	$("#spErr").html(data.msg);
        }
      });
 		return false;
	}
}
</script>
<script>
$(document).ready(function()
{
	$('#insert').click(function(){
		if($('#file').val()=="")
		{
			alert('Please select a file to upload.');
			return false;
		}
	});
	if($('#uploaddatabase').change(function(){
		if(this.checked)
		{
			$('#uploadexcel').fadeIn('slow');
		}
		else
			$('#uploadexcel').fadeOut('slow');
	}));
	$('#mobilenos').keyup(function(){
		if(this.value.match(/[^0-9-\n]/g))
			{
	 	  		this.value=this.value.replace(/[^0-9-\n]/g,'');
			}
	});
	$("#mobilenos").blur(function() {
		var mobilenos = $('#mobilenos').val();
		mobilenos = mobilenos.replace(/^\s*[\r\n]/gm,'');
		mobilenos = mobilenos.replace(/\n*$/,'');
		$('#mobilenos').val(mobilenos);
		if(($('#mobilenos').val())!="")
			getLineNumber();
	});

	$('#schedulecheck').change(function(){
		if($('#schedulecheck').attr('checked'))
			$('#datetimebox').fadeIn('slow');
		else
			$('#datetimebox').fadeOut('slow');
	});
	
});
function limitText(limitField, update)
{
	sms = 1;
    limit = 144;
    var count=Math.ceil(limitField.value.length/limit);
	if(count==0) count=1;
    document.getElementById(update).innerHTML = limitField.value.length;
    document.getElementById("smscount").innerHTML = count;
}
</script>
<div class="page_head">
	<div id="navigation">
		<a href="pages.php">Home</a><a> Utilities</a><a> SMS</a><a> Non
			Registered Users</a> <span style="color: #000000;">Send SMS</span>
	</div>
	<table width="100%">
		<tr>
			<td><h2>Non Registered Users send SMS</h2></td>
		</tr>
	</table>
</div>
<br />
<form name="frm" id="frm" action="pages.php?src=sms/nonregistered_sendsms.php" method="POST" enctype="multipart/form-data">
	<div style="padding-left: 10px;">
		<table class="adminform" border="0" align="left">
		<table>
		<tr><td>
		<table>
				<tr>
					<td colspan="2"><span style="color: #FF0000;">Note : Please enter multiple mobile numbers in different lines</span></td>
				</tr>
				<tr>
					<td align="right"><span class="redstar">Mobile Numbers :</span></td>
					<td><textarea name="mobilenos" id="mobilenos" rows='5' cols='40'><?php echo @$_REQUEST['mobilenos'];?></textarea></td>
				</tr>
				<tr>
					<td align="right"><span class="redstar">Message :</span></td>
					<td><textarea name='message_text' id='message_text' rows='5' cols='40' onKeyDown="limitText(this, 'countdownArea')" onKeyUp="limitText(this, 'countdownArea')"><?php echo @$_REQUEST['message_text'];?></textarea></td>
				</tr>
				<tr>
					<td></td>
					<td style="color: #FF0000;" align="left" colspan="2">SMS Count : <span id="smscount">1</span> Character Count : <span id="countdownArea">0</span></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="checkbox" name="senddatabase" id="senddatabase" <?php if(@$_REQUEST['senddatabase'] == 'on'){ echo 'checked';}?>/> Send message to all unregistered users from database</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="checkbox" name="uploaddatabase" id="uploaddatabase"<?php if(@$statusresult['status']=='true') echo 'checked';?> /> Send message using import excel</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<div id="uploadexcel" name="uploadexcel" style="display: none;">
							<table>
								<tr>
									<td><input type="file" name="file" id="file" /></td>
								</tr>
								<tr>
									<td><input type="submit" name="insert" id="insert" value="Upload" class="btn save"/></td>
								</tr>
							</table>
						</div>
					</td>
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
                'showDuration': true,
            	rows:5
            	});
        	</script>				
								
				</div>
				</td>-->
				</tr>
				<tr>
					<td></td>
					<td align="left"><input type="button" name="search" id="search" onclick="return check();" value="SEND SMS" style="background: url('css/classic/new.png') left center no-repeat; padding: 5px; padding-left: 25px;" /></td>
				</tr>
				</table></td><td valign="top" width="70%">
				<table align="right" width="100%">
					<tr>
						<td width="100%" valign="top">
							<div id="messageDiv">
								<span id='spErr'></span>
								<?php
								if(makeSafe(isset($_REQUEST['insert'])))
								{
									echo "<script>$('#spErr').empty();</script>";
									if($statusresult['status'] == 'true')
									{
										echo "<span id='excelmsg'><div class='success'>$statusresult[msg]</div></span><br>";
										for($i=0;$i<(count($statusresult)-2)/2;$i++)
										{
											?>
								<input type="hidden" value="<?php echo $statusresult['name'.$i];?>" name="name[]" />
								<input type="hidden" id="mobile" value="<?php echo $statusresult['mobile'.$i];?>" name="mobile[]" />
								<?php 
										}
									}
									else
									{
										for($i=0;$i<count($statusresult);$i++)
										{
											echo '<span id="excelmsg">'.$statusresult[$i].'</span>';
										}
									}
								}
								?>
							</div>
						</td>
					</tr>
				</table>
				</td></tr>
				</table>
				</table>
	</div>
</form>
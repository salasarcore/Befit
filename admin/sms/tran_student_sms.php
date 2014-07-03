<?php 
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
require_once '../globalConfig.php';
if(in_array(module_transactional_student_sms,$modules))
{
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_transactional_student_sms))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}*/

$br_id=makeSafe(@$_POST['branches']);
$Errs="";
?>
<link rel="Stylesheet" href="../js/jquery.ui.timepicker.css" rel="stylesheet"/>
<script src="../js/jquery.ui.timepicker.js"></script>
<script>
function populateList(act)
{
	if(act=="sectionlist")
	{
		 $("#section").empty();
		var url="sms/ajax/getList.php?act="+act+"&department="+$("#department").val();
		makePOSTRequest(url,'','section');
	}
}
function getStudentList(dept,section,sendto,template)
{
	$('#stuList').html('<img id="loadingImg" src="../../css/classic/loading.gif" />Loading Student List...'); //Sending Message(s)...
	$.ajax({
  		type: "POST",
		url:"sms/ajax/getStudentSMSList.php?department_id="+dept+"&section="+section+"&sendto="+sendto+"&template="+template,
		success: function(list)
		{
			$('#stuList').empty();
			$('#stuList').html(list);					
		}
  	});
}
</script>
<script>
var online_application_template = 13826;
var application_reject = 14790;

function check()
{
	String.prototype.replaceAt=function(index, character) {
		return this.substr(0, index) + character + this.substr(index+character.length);
	}
	var schedule_date = $('#schedule_date').val();
	var schedule_time = $('#schedule_time').val();
	var time = Date.parse('01/01/2011 '+schedule_time);
	var checktime1 = Date.parse('01/01/2011 12:00:00');
	var checktime2 = Date.parse('01/01/2011 12:59:00');
	
	var smscount=<?php echo json_encode($_SESSION['sms_t_count']); ?>;
	if(time >= checktime1 && time <= checktime2)
    {
    	schedule_time = schedule_time.replaceAt(0,"2");
    	schedule_time = schedule_time.replaceAt(1,"4");
    	$('#schedule_time').val(schedule_time);
    }
	if(smscount <= 0)
	{
		alert('You have exceeded your maximum SMS sending limit. Please contact the system administrator for more details.');
    	return false;
	}
	/* var checkValues = $('input[name=rdoID[]]:checked').map(function()
	            {
	                return $(this).val();
	            }).get();
     if(checkValues=="")
     {
    	alert("Please select a student to send message.");
 		return false;
     }*/
      if($('#template_id').val() == "select")
 	{
 		alert('Please select a template');
 		$('#template_id').focus();
 		return false;
 	}
     else if($('#send_to').val() == "select")
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
   /* var checkValues = $('input[name=rdoID[]]:checked').map(function()
            {
                return $(this).val();
            }).get();
 	if(checkValues!="" && checkValues=="on")
 	{
		alert("Please select a student to send message.");
		return false;
 	}*/
 	if($('#template_id').val() == "select")
	{
		alert('Please select a template');
		$('#template_id').focus();
		return false;
	}
  	var empties = $('.hashvalues').filter(function()
  	{
  	  	return $.trim($(this).val()) == '';
	});
	 if (empties.length)
	{
		alert("Please enter all values");
		return false;
	}
    else
    {
    	$('#spErr').html('<img id="loadingImg" src="../../css/classic/loading.gif" />Sending Message(s)...');
     	var err="";
   	 	var url = "sms/ajax/smsSubmitForm.php?act=search&type=T&sendtype=S";
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
function selectallid()
{

	var elms=document.getElementsByName("rdoID[]");
	if($('#selectall').is(':checked')) {
		for (i=0;i<elms.length;i++){
			if (elms[i].type="checkbox" ){
				elms[i].checked = true;
				}
			}
	}
	else{
		for (i=0;i<elms.length;i++){
			if (elms[i].type="checkbox" ){
				elms[i].checked = false;
				
				}
			}
	}
}

function getSelected()
{
	
	var elms=document.getElementsByName("rdoID");
	for (i=0;i<elms.length;i++){
		if (elms[i].type="checkbox" ){
			if(!$('#'+elms[i].value).is(':checked')) {
				$('#selectall').attr('checked',false);
			}
		}
}
}
function showMsg()
{
	var dept = $('#department').val();
	var section = $('#section').val();
	var sendto = $('#send_to').val();
	var template = $('#template_id').val();
	if(template != 'select')
	{	
		getStudentList(dept,section,sendto,template);
	}	

	var templateid = $('#template_id').val(); 
	$('#message_text').empty();
	var datastring = "templateid="+templateid;
	$.ajax({
  		type: "GET",
		url:"sms/ajax/getTemplateMessage.php",
		data: datastring,
		dataType:"JSON",
		success: function(data)
		{
			   document.frm.message_text.value = data.value;
			   var count = data.count;
			   document.frm.hidden.value = count;
			if(template != online_application_template)
			{
				$('#textboxes').html("");
				   for(var i=2; i<=data.matches.length-1;i++)
				   {
					   $('#textboxes').append('<input type="text" placeholder="'+data.matches[i].replace(/#/g,'')+'" style="width: 150px;" name="hashvalues[]" class="hashvalues" id="hashvalues'+i+'"/><br/>');
				   }	
			}
		}
  	});	
}

</script>
<script>
$(document).ready(function(){
	$("#count").html(<?php if(@$_SESSION['sms_t_count'] < 0) echo json_encode("0"); else echo json_encode(@$_SESSION['sms_t_count']);?>);
	$('#department').change(function(){
		var dept = $('#department').val();
		var section = $('#section').val();
		var sendto = $('#send_to').val();
		var template = $('#template_id').val();
		if(section==null)
			var section = "";
		if(send_to=="select")
			var sendto = "";
		if(dept=="select")
		{
			$('#stuList').empty();
			return false;
		}
		else
			getStudentList(dept,section,sendto,template);
	});
	$('#section').change(function(){
		var dept = $('#department').val();
		var section = $('#section').val();
		var sendto = $('#send_to').val();
		var template = $('#template_id').val();
		if(sendto=="select")
			var sendto = "";
		getStudentList(dept,section,sendto,template);
	});
	$('#send_to').change(function(){
		var dept = $('#department').val();
		var section = $('#section').val();
		var sendto = $('#send_to').val();
		var template = $('#template_id').val();
		if(dept!='select')
		{
			if(section==null)
				var section = "";
			getStudentList(dept,section,sendto,template);
		}
	});
	$('#template_id').change(function(){
		var dept = $('#department').val();
		var section = $('#section').val();
		var sendto = $('#send_to').val();
		var template = $('#template_id').val();
		if(template == online_application_template || template == application_reject)
		{
			$('#section').attr('disabled',true);
		}
		else
			$('#section').attr('disabled',false);
		if(dept!='select')
		{
			$('#message_text').empty();
			$('#textboxes').empty();
			$('#stuList').empty();
			if(section==null)
				var section = "";
			getStudentList(dept,section,sendto,template);
		}		
	});	
	var url="sms/ajax/getTemplateList.php";
	makePOSTRequest(url,'','template_id');

	$('#schedulecheck').change(function(){
		if($('#schedulecheck').attr('checked'))
			$('#datetimebox').fadeIn('slow');
		else
			$('#datetimebox').fadeOut('slow');
	});
});
</script>

<div class="page_head">
	<div id="navigation">
		<a href="pages.php">Home</a><a> Utilities</a><a> SMS</a> <span style="color: #000000;">Member Transactional SMS</span>
	</div>
	<table width="100%">
		<tr>
			<td><h2>Member Transactional SMS</h2></td>
		</tr>
	</table>
</div>
<br />
<form name="frm" id="frm" action="pages.php?src=sms/tran_student_sms.php" method="POST">
<div style="padding-left: 10px;">
<span id="spErr"></span>
		<table class="adminform" border="0" align="left">
		<table>
		<tr><td>
			<table>
				<tr>
					<td colspan="2"><span style="color: #FF0000;">Total Remaining Transactional SMS : <label style="font-size: 12px; display: inline;" id="count"></label></span></td>
				</tr>
				<tr>
					<td align="right"><span class="redstar">Send To :</span></td>
					<td><select style="width: 150px;" name="send_to" size="1" id="send_to">
					<option value="select" selected>----SEND TO----</option>
					<option value="STUDENT">MEMBER</option>
					<!-- <option value="PARENT">PARENT</option>-->
					</select>
					</td>
				</tr>
				<tr>
					<td align="right"><span class="redstar">Select Course :</span>
					</td>
					<td><select style="width: 150px;" name="department" id="department" onchange="populateList('sectionlist')">
					<?php
							$sql="select d.* from mst_departments d where d.br_id=".@$_SESSION['br_id']." order by department_name";
							$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
							echo "<option value='select'>-COURSE-</option>";
							echo "<option value='0'>ALL COURSE</option>";
							while($row=mysql_fetch_array($res))
							{
								echo "<option value='".$row['department_id']."'";
								if(@$department_id==$row['department_id']) echo " Selected"; echo ">".$row['department_name']."</option>";
							}
							?>
					</select></td>
				</tr>
				<tr>
					<td align="right"><span class="redstar">Select BATCH :</span></td>
					<td><select style="width: 150px;" name="section" id="section"><option value=''>----BATCH-----</option></select></td>
				</tr>
				<tr>
					<td align="right" class="redstar">Template ID :</td>
					<td><select style="width: 150px;" name="template_id" size="1" id="template_id" onchange="showMsg()" id="template_id"
						<?php if(@$act=="delete") echo "disabled";?>></select>
					</td>
				</tr>
				<tr>
					<td align="right"><span class="redstar">Message :</span></td>
					<td><textarea name='message_text' id='message_text' rows='7' cols='28' readonly></textarea></td>
				</tr>
				<tr>
					<td></td>
					<td align="left" id="textboxes"></td>
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
			</table></td><td valign="top" width="76%">
		<table width="100%">
			<tr>
				<td width="100%" valign="top"><div id="stuList"></div></td>
			</tr>
		</table>
		</td></tr>
		</table>
		</table>
	</div>
</form>
<?php 
@session_start();
include("../conn.php");
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/common.php");
include("../functions/event_master.php");
include('../check_session.php');
include("../../fckeditor/fckeditor.php") ;

makeSafe(extract($_REQUEST));
$Errs="";
$act=makeSafe(@$_GET['act']);
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../js/ajax.js"></script>

<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<?php
include('../php/js_css_common.php');
/*include '../modulemaster.php';
$act=makeSafe(@$_GET['act']);
if($act=="add")
$id=option_academic_calender_add;
elseif($act=="edit")
$id=option_academic_calender_edit;
elseif($act=="delete")
$id=option_academic_calender_delete;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$EventID=makeSafe(@$_GET['EventID']);
$action=makeSafe(@$_POST['action']);
$sql="";

if(@$action=="SAVE" || @$action=="UPDATE")
{
	makeSafe(extract($_REQUEST));
	$brid=$_SESSION['br_id'];
	$query_str="select * from session where session_name='".$_SESSION['d_session']."'";
	$sessionid=mysql_fetch_array(mysql_query($query_str));
	$sid=$sessionid['session_id'];
	$start_date = date('Y-m-d',strtotime($start_date));
	$end_date = date('Y-m-d',strtotime($end_date));	
	if(trim($event_name)=="")
		$Errs= "<div class='error'>Please Enter Event Name</div>";
	elseif($event_type=="select")
	$Errs= "<div class='error'>Please Select Event Type</div>";
	elseif(trim($start_date)=="")
	$Errs= "<div class='error'>Please Enter Event Start Date</div>";
	elseif(trim($end_date)=="")
	$Errs= "<div class='error'>Please Enter Event End Date</div>";
	elseif(trim($start_date) > trim($end_date))
	$Errs= "<div class='error'>Start date cannot be greater than end date.</div>";
	elseif((trim($start_date) == trim($end_date)) && (trim($start_time) > trim($end_time)))
	$Errs= "<div class='error'>Start time cannot be greater than end time for same date.</div>";
	elseif(trim($description)=="")
	$Errs= "<div class='error'>Please Enter Event Description.</div>";
	else
	{
		$start_date = $start_date.' '.$start_time.':00';
		$end_date = $end_date.' '.$end_time.':00';
		if(@$action=="SAVE")
		{
			$eventid=getNextMaxId("event","event_id")+1;	
			$sql1="insert into event(`event_id`,`br_id`,`session_id`,`event_type`, `event_name`, `event_description`, `event_start_date`, `event_end_date`, `event_creation_datetime`, `event_created_by`)
			values('".$eventid."','".$brid."','".$sid."','".$event_type."','".$event_name."','".$description."','".$start_date."','".$end_date."',NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
			$res1=mysql_query($sql1);
			$event_organizer_id = getNextMaxId('event_organizer','event_organizer_id') + 1;
			$sql3 = "INSERT INTO event_organizer(event_organizer_id, event_id,person_id, person_type) VALUES (".$event_organizer_id.",".$eventid.",".$_SESSION['empid'].",'E')";
			$res3 = mysql_query($sql3);
			if($res1)
			{
				foreach(@$empteam as $pid)
				{
					if($pid=="")
						continue;
					else
					{
					$event_organizer_id = getNextMaxId('event_organizer','event_organizer_id') + 1;
					$sql2="INSERT INTO event_organizer(event_organizer_id, event_id,person_id, person_type) VALUES (".$event_organizer_id.",".$eventid.",".$pid.",'E')";
					@$msg="Saved";
					$res2=mysql_query($sql2,$link);
					}
				}
				foreach(@$stuteam as $pid)
				{
					if($pid=="")
						continue;
					else
					{
					$event_organizer_id = getNextMaxId('event_organizer','event_organizer_id') + 1;
					$sql2="INSERT INTO event_organizer(event_organizer_id, event_id,person_id, person_type) VALUES (".$event_organizer_id.",".$eventid.",".$pid.",'S')";
					@$msg="Saved";
					$res2=mysql_query($sql2,$link);
					}
				}
			}
			if(mysql_affected_rows($link)==0)
				$Errs="<div class='success'>No Data Changed</div>";
			if(mysql_affected_rows($link)>0)
				$Errs="<div class='success'>Event Added Successfully</div>";
			if(mysql_affected_rows($link)<0)
				$Errs="<div class='error'>Event Not Added Successfully</div>";
		}
		if($action=="UPDATE")
		{								
			$sql1="update event set event_name='".$event_name."', event_description='".trim($description)."',event_start_date='".$start_date."', event_end_date='".$end_date."',
						event_type='".$event_type."' where event_id=".$EventID;
			$res1=mysql_query($sql1,$link);
			if($res1)
			{
				if(@$empteam != "")
				{
				foreach(@$empteam as $pid)
				{
					if($pid=="")
						continue;
					else
					{
					$event_organizer_id = getNextMaxId('event_organizer','event_organizer_id') + 1;
					$sql2="INSERT INTO event_organizer(event_organizer_id, event_id,person_id, person_type) VALUES (".$event_organizer_id.",".$EventID.",".$pid.",'E')";
					@$msg="Updated";				
					$res2=mysql_query($sql2,$link);
					}
				}
				}
				if(@$stuteam!="")
				{
				foreach(@$stuteam as $pid)
				{
					if($pid=="")
						continue;
					else
					{
					$event_organizer_id = getNextMaxId('event_organizer','event_organizer_id') + 1;
					$sql2="INSERT INTO event_organizer(event_organizer_id, event_id,person_id, person_type) VALUES (".$event_organizer_id.",".$EventID.",".$pid.",'S')";
					@$msg="Updated";
					$res2=mysql_query($sql2,$link);
					}
				}
				}			
			}
			if(mysql_affected_rows($link)==0)
				$Errs="<div class='success'>No Data Changed</div>";
			if(mysql_affected_rows($link)>0)
				$Errs="<div class='success'>Event Updated Successfully</div>";
			if(mysql_affected_rows($link)<0)
				$Errs="<div class='error'>Event Not Updated Successfully</div>";
		}			
	}
}
if(@$act=="add")
	body();


if(@$action=="DELETE")
{
	@$msg="Deleted";
	$query4 = mysql_query("delete from routine_class where event_id=".$EventID);
	$query3 = mysql_query("delete from event_person_invites where event_id=".$EventID);
	$query2 = mysql_query("delete from event_organizer where event_id=".$EventID);
	if($query2)
	{
		$query = mysql_query("delete from event where event_id=".$EventID);
		if(mysql_affected_rows($link)>0)
		{
			$Errs="<div class='success'>Event Deleted Successfully </div>";
		}
		else
			$Errs="<div class='error'>Event Not Deleted Sucessfully1</div>";
	}
	else
		$Errs="<div class='error'>Event Not Deleted Sucessfully2</div>";
}
if(@$act=="edit" || @$act=="delete" )
{
$check1 = "select * from event_organizer where event_id=".$EventID." and person_type='E'";
$check2 = "select * from event_organizer where event_id=".$EventID." and person_type='S'";
	
	/**
	 * Below variables will be used separately in the body of the file. mysql_fetch_assoc() does not work on a single variable more than once.
	 * These variables need to be declared in the body functions as global variables. Without declaring them global, these variables cannot be implemented. For eg:
	 * @param $run1 will be used in mysql_fetch_assoc in the body to fetch the data
	 */
	
	$run1 = mysql_query($check1);
	$run2 = mysql_query($check1);
	$run5 = mysql_query($check1);
	$run3 = mysql_query($check2);
	$run4 = mysql_query($check2);
	$run6 = mysql_query($check2);
	
	 $no_emp = mysql_num_rows($run1);
	 $no_stu = mysql_num_rows($run3);

	$query   = "SELECT * from event where event_id=".$EventID;
	$result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
		$row  = mysql_fetch_array($result, MYSQL_ASSOC);
		$EventID=$row['event_id'];
		$start_date=$row['event_start_date'];
		$end_date=$row['event_end_date'];
		$event_name=$row['event_name'];
		$event_description=$row['event_description'];
		$event_created_by=$row['event_created_by'];
		$eventtype = $row['event_type'];
	}
	body();
}
function body()
{
	global $link;
	global $act;
	global $start_date;
	global $end_date;
	global $event_name;
	global $EventID;
	global $event_description;
	global $event_created_by;
	global $eventtype;
	global $run1;
	global $run2;
	global $run3;
	global $run4;
	global $run5;
	global $run6;
	global $no_stu;
	global $no_emp;
	?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>BRANCHES</title>
<link rel="shortcut icon" href="../saplimg/favicon.ico">

<link rel="Stylesheet" href="../../js/jquery.ui.timepicker.css" rel="stylesheet"/>
<script src="../../js/jquery.ui.timepicker.js"></script>
<style>
.ui-timepicker-table .ui-timepicker-title
{
line-height:0.9em;
}
.ui-timepicker-table td, .ui-timepicker-table th.periods {
 font-size:10px;
}

.timestyle
{
width:100px;
height:22px;}

</style>
<script type="text/javascript">
function deleteRow(id)
{
	var r=confirm("Are you sure you want remove this organizer?");
	if(r==true)
	{
		var datastring = "personid="+id+"&eventid=<?php echo @$EventID?>";
		$.ajax({
	  		type: "GET",
	  		url:"../ajax/deleteEventPerson.php",
			data: datastring,
			success: function(data){
				if(data=="true")
				{
					alert('Organizer deleted successfully.');
					window.location.reload();
				}
				else if(data=='failed')
				{
					alert("Unable to delete.");
					return false;
				}
				else
				{
					alert('Cannot delete as there should be atleast one employee organizer.');
					return false;
				}
		}
	  	});
	}
}
function validateevent()
{
	if($('#event_name').val()=="")
	{
		alert("Please enter event name.");
		return false;
	}
	else if($('#event_type').val()=="select")
	{
		alert("Please select event type");
		return false;
	}
	else if($('#start_date').val()=="")
	{
		alert("Please enter event date");
		return false;
	}
	else if($('#end_date').val()=="")
	{
		alert("Please enter event end date.");
		return false;
	}
	else if($('#description').val()=="")
	{
		alert("Please enter event description.");
		return false;
	}
	else if($('#start_date').val() > $('#end_date').val())
	{
		alert('Start date cannot be greater than end date.')
		return false;
	}
	else if($('#start_date').val() == $('#end_date').val())
	{
		var start_time = $('#start_time').val();
		var end_time = $('#end_time').val();
		if(start_time == end_time)
		{
			alert('Start time and End time of event for same date cannot be same.');
			return false;
		}
		else if(start_time > end_time)
		{
			alert('Start time cannot be greater than end time.');
			return false;
		}
	}
	
	var emparray = document.getElementsByName('empteam[]');
	if($('#emp_1').val()!="")
	{
		for (var i=0; i < emparray.length; i++)
		{
			if(emparray[i].value=="")
			{
				alert('Please select an employee name from the drop down list only.');
				return false;
			}
		}
	}
	if($('#stu_1').val()!="")
	{
		var stuarray = document.getElementsByName('stuteam[]');
		for (var i=0; i < stuarray.length; i++)
		{
			if(stuarray[i].value=="")
			{
				alert('Please select a student name from the drop down list only.');
				return false;
			}
		}
	}
	if(($('#stu_1').val()=="") && ($('#emp_1').val()==""))
	{
		alert('Please enter atleast one student or employee organizer');
		return false;
	}
	return true;
}

var add_stu_mem_count = <?php echo ($act=='edit')? $no_stu : 2; ?>;
var stu_counter = <?php echo ($act=='edit')? $no_stu : 2; ?>;
function add_stu_mem()
{
	if(stu_counter < 5)
	{
	stu_counter++;
	$("#stuname").append("<span class='add_team' id='stuadd_team"+add_stu_mem_count+"' style='margin-top:5px;display:block;'><input type='text' class='team' id='stu_"+add_stu_mem_count+"' onkeyup=\"autoCompleteList('stu_"+add_stu_mem_count+"','S');\"/><input type='hidden' name='stuteam[]' id='stuid_stu_"+add_stu_mem_count+"' value='' /><img title='Delete Row' style='cursor: hand;' src='../css/classic/cross1.png' class='deleteteam_text' width='20' style='vertical-align: middle;' onclick='$(\"#stuadd_team"+add_stu_mem_count+"\").remove(); remMember(\"S\")'/></span>");
	add_stu_mem_count++;
	}
	else
	{
		alert('Cannot add more than 5 student organizing members.');
		return false;
	}
}

var add_emp_mem_count = <?php echo ($act=='edit')? $no_emp : 2; ?>;
var emp_counter = <?php echo ($act=='edit')? $no_emp : 2; ?>;
function add_emp_mem()
{
	if(emp_counter < 5)
	{
	emp_counter++;
	$("#empname").append("<span class='add_team' id='empadd_team"+add_emp_mem_count+"' style='margin-top:5px;display:block;'><input type='text' class='team' id='emp_"+add_emp_mem_count+"' onkeyup=\"autoCompleteList('emp_"+add_emp_mem_count+"','E');\"/><input type='hidden' name='empteam[]' id='empid_emp_"+add_emp_mem_count+"' value='' /><img style='cursor: hand;' title='Delete Row' src='../css/classic/cross1.png' class='deleteteam_text' width='20' style='vertical-align: middle;' onclick='$(\"#empadd_team"+add_emp_mem_count+"\").remove(); remMember(\"E\")'/></span>");
	add_emp_mem_count++;
	}
	else
	{
		alert('Cannot add more than 5 employee organizing members.');
		return false;
	}
}
function remMember(value)
{
	if(value=='E')
		emp_counter--;
	else
		stu_counter--;
}
</script>
<script>
function autoCompleteList(myId,type)
{
	if($("#"+myId).val()!="")
	{
		var eventid="<?php echo $EventID;?>";
		if(eventid=="")
			var event="";
		else
			var event="<?php echo $EventID;?>";
		var datastring="ptype="+type+"&pName="+$("#"+myId).val()+"&event="+event;
  	 $.ajax({
  		type: "GET",
  		url:"../ajax/getEventPersonList.php",
		data: datastring,
		 dataType:"JSON",
		success: function(data){
			
			 var values=data;
			 			 			 
			 $("#"+myId).autocomplete({
			       minlength:0,
		    		source: function (request,response){
		          		var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
		              	var matching = $.grep(values, function (value){
		                 var value1 = value.value;
		                 var personID = value.personID;
		               return matcher.test(value1);
		              });
			        response(matching);		       
		          },	
		          select:function( event, ui ){		        		
		            	$("#"+myId).val(ui.item.value1);
		            	if(type=='E')
		            		$("#empid_"+myId).val(ui.item.personID);
		            	else
		            		$("#stuid_"+myId).val(ui.item.personID);
		          }
		       })
	}
  	});
	}
}
</script>
</head>

<body leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0"	style="text-align: left">
<div id="middleWrap">
		<div class="head"><h2>
			<?php echo strtoupper(@$act);?>
			EVENT</h2>
		</div>
		
		<span id="spErr"><?php echo $Errs;?> </span>
		<form name="records" method="POST" action="event.php?act=<?php echo makeSafe(@$_GET['act']);?>&EventID=<?php echo makeSafe(@$_GET['EventID']);?>" onsubmit="return validateevent();">
			<table border="0" width="100%" id="table1" class="adminform">
				<tr>
					<td align="right" class="redstar">Event Name :</td>
					<td><input type="text" name="event_name" id="event_name" size="40" value="<?php if($act=="edit" || $act=="delete") echo @$event_name; ?>" maxlength="500" <?php if(@$act=="delete") echo 'disabled';?>></td>
				</tr>
				<tr>
					<td align="right" class="redstar">Event Type :</td>
					<td><select name="event_type" id="event_type" <?php if(@$act=="delete") echo 'disabled';?>>
							<option value="select" name="select">SELECT</option>
							<?php 
							$event_type = event_type();
							foreach($event_type as $event)
							{
								echo "<option value='".$event."'";
								if($event===@$eventtype) echo "selected";
								echo " name='".$event."'>$event</option>";
							}
							?>
					</select>
					</td>
				</tr>
				<tr>
					<td align="right" class="redstar">Event Start Date :</td>
					<td><input name="start_date" type="text" style="max-width: 8em;" class="date" id="start_date" <?php if(@$act=="delete") echo 'disabled';?> value="<?php if($act=="edit" || $act=="delete") echo date('Y-m-d', strtotime(@$start_date));?>"
						size="11" maxlength="10" /> 
				<script type="text/javascript">
				  $(function() {
						$( "#start_date" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});

					});
				</script> 
				Event Start Time : <input name="start_time"  style="max-width: 6em;" type="text" <?php if(@$act=="delete") echo 'disabled';?> id="start_time" size="11" maxlength="10" value="<?php if($act=="edit" || $act=="delete") echo date('H:i',strtotime(@$start_date));?>" />
		<script>
        	$('#start_time').timepicker({
            	minutes: { interval: 1 },
            	timeFormat: "HH:mm",
            	rows:5
            	});
        </script>
					</td>
				</tr>
				<tr>
					<td align="right" class="redstar">Event End Date:</td>
					<td><input name="end_date" type="text" style="max-width: 8em;" class="date" id="end_date" <?php if(@$act=="delete") echo 'disabled';?> value="<?php if($act=="edit" || $act=="delete") echo date('Y-m-d', strtotime(@$end_date));?>" size="11" maxlength="10" /> 
			<script type="text/javascript">
				  $(function() {
						$( "#end_date" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});
					});
			</script> 
			Event End Time : <input style="margin-left: 8px;max-width: 6em;" <?php if(@$act=="delete") echo 'disabled';?> name="end_time" type="text" id="end_time" value="<?php if($act=="edit" || $act=="delete") echo date('H:i', strtotime(@$end_date));?>"
						size="11" maxlength="10" /> 
		<script>
        	$('#end_time').timepicker({
            	minutes: { interval: 1 },
            	timeFormat: "HH:mm",
            	rows:5
            	});
        </script>
					</td>
				</tr>

				<tr>
					<td align="right" class="redstar">Description :</td>
					<td><textarea name="description" id="description" cols="30" rows="5" <?php if(@$act=="delete") echo 'disabled';?>><?php if($act=="edit" || $act=="delete") echo @$event_description;?></textarea></td>
				</tr>				
				<tr>
					<td align="right" valign="top">Employee Organizers : <?php if(@$act=="edit" || @$act=="delete"){?><img style="cursor: hand;" src="../css/classic/Add.png" onclick="add_emp_mem()" /><?php }?></td>
					<td>
					<table border='0'>
					<?php 
					if(@$act=='edit' || @$act=='delete')
					{
						if(@mysql_fetch_assoc($run5)=="")
						{?>
					<tr>
					<td id="emppname">
					<input type='text' class='team' id='emp_1' onkeyup="autoCompleteList('emp_1','E');" <?php if(@$act=="delete") echo 'disabled';?>/> 
					<input type='hidden' name='empteam[]' id='empid_emp_1' value='' /> 
					
					</td>
					</tr>
						<?php }
						else
						{
						$i = 1;
						while(@$return1 = mysql_fetch_assoc($run2))
						{
							$getname = mysql_fetch_assoc(mysql_query("select emp_name from employee where empid=".$return1['person_id']));
						?>
					<tr>
					<td id="empname">
					<img style="cursor: hand;" width="15" height="15" src='../css/classic/cross1.png' onclick='deleteRow(<?php echo $return1['person_id'];?>)'/>
					<label style="font-size:11px; padding-bottom: 20px;" id="employeename"><?php echo $getname['emp_name'];?></label>					 
					</td>
					</tr>
					<?php 
						$i += 1;
						}
						}
					}
			else{
				?>
					<tr>
					<td id="empname">
					<input type='text' class='team' id='emp_1' onkeyup="autoCompleteList('emp_1','E');" />
					<input type='hidden' name='empteam[]' id='empid_emp_1' value='' /> 
					<img style="cursor: hand;" src="../css/classic/Add.png" onclick="add_emp_mem()" />
					</td>
					</tr>
					<?php }?>					
					</table>
					</td>
				</tr>
				<tr>
					<td align="right" valign="top">Student Organizers : <?php if(@$act=="edit" || @$act=="delete"){?><img style="cursor: hand;" src="../css/classic/Add.png" onclick="add_stu_mem()" /><?php }?></td><td>
					<table border='0'>
					<?php 
					if(@$act=='edit' || @$act=='delete')
					{
						if(@mysql_fetch_assoc($run6)=="")
						{?>
					<tr>
					<td id="stuname">
					<input type='text' class='team' id='stu_1' onkeyup="autoCompleteList('stu_1','S');" <?php if(@$act=="delete") echo 'disabled';?>/>
					<input type='hidden' name='stuteam[]' id='stuid_stu_1' value='' />
					</tr>
						<?php }
						else
						{
						$i = 1;
						while(@$return2 = mysql_fetch_assoc($run4))
						{
							$getname = mysql_fetch_assoc(mysql_query("select concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name from mst_students where stu_id=".$return2['person_id']));
							?>
					<tr>
					<td id="stuname">
					<img style="cursor: hand;" width="15" height="15" src='../css/classic/cross1.png' onclick='deleteRow(<?php echo $return2['person_id'];?>)'/>
					<label style="font-size:11px;" id="studentname"><?php echo $getname['stu_name'];?></label>
					</td>
					</tr>
					<?php 
						$i += 1;
						}
						}
					}
			else{
				?>
					<tr>
					<td id="stuname">
					<input type='text' class='team' id='stu_1' onkeyup="autoCompleteList('stu_1','S');" /> <input type='hidden' name='stuteam[]' id='stuid_stu_1' value='' />
					<img style="cursor: hand;" src="../css/classic/Add.png" onclick="add_stu_mem()" /> 
					</td>
					</tr>
					<?php }?>					
					</table>
					</td>
				</tr>
				<tr>
					<td width="70%" align="right" colspan="2">
						<p align="center">
							<input type="submit" class="btn save"
								value='<?php
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">&nbsp;&nbsp; <input type=button value="CLOSE" class="btn close" name="close" id="close" onClick="parent.emailwindow.close()">
					</td>
				</tr>
			</table>
			<INPUT type='HIDDEN' name='EventID' value='<?php echo @$EventID; ?>'>
			<INPUT type='HIDDEN' name='action'
				value='<?php
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>'>
			</table>
		</form>
		
		<?php }
?>
</div>
</body>
</html>
<script language="javascript">
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
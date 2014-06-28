<style>
.message {
	font-size: 15px;
	font-weight: bold;
}
</style>

<?php 
@session_start();
include("../conn.php");
include('../check_session.php');
include_once("../functions/common.php");
include_once("../functions/dropdown.php");

makeSafe(extract($_REQUEST));
$i=0;
if($att_status=="" )
	$sql="select e.* from employee e  where  department_id=".$dept_id." and br_id=".$_SESSION['br_id']." and emp_name like '%".trim($empname)."%' ";
else		
	$sql="select eatt.*,e.* from employee as e join emp_attendance_register as eatt on e.empid=eatt.empid and eatt.att_type like '%".$att_status."%' and eatt.att_date='".$date."' and department_id=".makeSafe($_REQUEST['dept_id'])." and br_id=".$_SESSION['br_id']." and emp_name like '%".trim($empname)."%' ";

$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
$count=mysql_num_rows($res);

if($count>0)
{	
	?>

<div style="float:right;margin-right: 100px;font-weight: bold;font-size:12px;" ><li>Blue color indicates working hours for <font color="#0000FF">lecturers</font>. </li><li>Red color indicates working hours for <font color="#FF0000">others</font>.</li></div>

<br><br><br>
<table class="table table-bordered"" width="auto" style="cursor: pointer;" name="employeelist" id="employeelist">
	<thead>
		<tr>
			<th align="center" width="50px;">#</th>
			<th>EMPLOYEE CODE</th>
			<th>EMPLOYEE NAME</th>
			<th>PAY TYPE</th>
			<th colspan="4">ATTENDANCE STATUS</th>
			<th width="100px;">WORK HOURS</th>
			<th width="100px;">RECOMMENDED INTIME</th>
			<th width="100px;">RECOMMENDED OUTTIME</th>
			<th>CAUSE(If Any)</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$counterror=0;
	
		while($row=mysql_fetch_array($res))
		{
			$stat="";
			$leave_approved="";
			$cause="";
			$l=false;
			$holiday="";
			$atttype="";
			$work_hours="";
			$workhours="";
			$work="";
			$difftime="";
			$intime="";
			$outtime="";
			$paytype="";
			$type="";
			$nolect="";
			$sql="SELECT TIME_FORMAT(SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(reco_outtime,reco_intime))),'%k.%i') as work,pay_type,reco_intime,reco_outtime FROM emp_sal_settings where empid=".$row['empid'];
			$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			if(mysql_affected_rows($link)>0)
			{
				$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
				$work_hours=$rowL['work'];
			$paytype=$rowL['pay_type'];
			$intime=$rowL['reco_intime'];
			$outtime=$rowL['reco_outtime'];
			$type="red";
			}
			
			$sql="SELECT leave_approved,cause FROM emp_leave_applications where empid=".$row['empid']."  and '". $date ."' between date_from and date_to";
			$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			if(mysql_affected_rows($link)>0)
			{
				$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
				$leave_approved=$rowL['leave_approved'];
				$cause=$rowL['cause'];
				$l=true;
			}
			
			$sql="SELECT event_type FROM event as e,event_person_invites as ep  where e.event_id=ep.event_id and person_type='E' and person_id=".$row['empid']." and '". $date ."' between date(event_start_date) and date(event_end_date)";
			$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			if(mysql_affected_rows($link)>0)
			{
				$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
				$holiday=$rowL['event_type'];
			}
			
			$sqlatt="SELECT ep.* from event as e,event_person_invites as ep where e.event_id=ep.event_id and e.event_type='LECTURE' and ep.person_type='E' and ep.person_id=".$row['empid']." and ep.show_up_status=' ' and date(invited_on)= '". $date ."'";
			$resatt=mysql_query($sqlatt) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			if(mysql_affected_rows($link)>0)
			{
				$stat=mysql_num_rows($resatt);
			}
	
			if($paytype=="H"){
			$sql="SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(ep.actual_outtime,ep.actual_intime)))),'%k.%i')  as total_work_hours from event as e,event_person_invites as ep where e.event_id=ep.event_id and e.event_type='LECTURE' and ep.person_type='E' and ep.person_id=".$row['empid']."  and date(invited_on)= '". $date ."'";
			
			$resE=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$rowE= mysql_fetch_assoc($resE);
			$work_hours=$rowE['total_work_hours'];
			$type="blue";
			if($work_hours!="") $nolect="assigned";
			$sql="SELECT min(e.event_start_date) as min,max(e.event_end_date) as max from event as e,event_person_invites as ep where e.event_id=ep.event_id and e.event_type='LECTURE' and ep.person_type='E' and ep.person_id=".$row['empid']."  and date(invited_on)= '". $date ."'";
			
			$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			if(mysql_affected_rows($link)>0)
			{
				$rowH     = mysql_fetch_array($resC,MYSQL_ASSOC);

				$intime=date("H:i:s",strtotime($rowH['min']));
				$outtime=date("H:i:s",strtotime($rowH['max']));
			}
			}
			$sql="SELECT att_type,work_hours FROM emp_attendance_register where empid=".$row['empid']."  and att_date='". $date ."' ";
			$resC=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			if(mysql_affected_rows($link)>0)
			{
				$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
				$atttype=$rowL['att_type'];
				$work_hours=$rowL['work_hours'];
				
			}
			
			$i=$i+1;
			?>
		<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo $row['empid']; ?>');">
		<td align="center"><input type="radio" name="rdoID" value="<?php echo $row['empid']; ?>" id="rdoID" onclick="selectID('<?php echo $row['empid']; ?>');"/></td>
			<td align="center"><?php echo $row['emp_id'];?></td>
			<td><?php echo $row['emp_name'];?></td>
			<td><?php if($paytype=="H")echo "Hourly"; else echo "Monthly";?></td>
			<td><input type='radio' id='P<?php echo $i;?>' name='<?php echo $i;?>' value="P<?php echo $row['empid'];?>"	<?php 
			if($l==false)
				echo "checked";
			
			?> 
			onchange="checkhrs(this);"/>Present</td>
			<td><input type='radio' id='A<?php echo $i;?>'	name='<?php echo $i;?>' value="A<?php echo $row['empid'];?>" 
			<?php 
			if(@$atttype=="A" ||($paytype=="H" && $work_hours==""))
				echo "checked";
			?>
			onchange="checkhrs(this);"  onloadeddata="checkhrs(this);"/>Absent</td>
			<td><input type='radio' id='L<?php echo $i;?>'	name='<?php echo $i;?>' value="L<?php echo $row['empid'];?>" <?php 
			if($l==true) 
				echo "checked";
			elseif(@$atttype=="L")
				echo "checked";
			?>
			
			onchange="checkhrs(this);"/>Leave</td>
			<td><input type='radio' id='H<?php echo $i;?>'	name='<?php echo $i;?>' value="H<?php echo $row['empid'];?>" <?php 
			if($holiday=='HOLIDAY')
				echo "checked";
			elseif(@$atttype=="H")
				echo "checked";
			?>
			onchange="checkhrs(this);"/>Holiday</td>
			<?php if($paytype=="H"){?>
			<td>
			
			<input type="hidden" id="hrs<?php echo $row['empid'];?>" name="hrs<?php echo $row['empid'];?>" value="<?php if($work_hours=="") echo "0.00"; else echo $work_hours;?>" /><label style="color: <?php echo $type;?>"><?php if($work_hours=="")echo "0.00"; else echo $work_hours;?></label>
			</td>
			<td colspan="3" style="color:red;text-align: center;">Mark attendance from event for this employee</td>
			 <?php } else {?>
			<td>
			
			<input type="text" id="hrs<?php echo $row['empid'];?>" name="hrs<?php echo $row['empid'];?>" <?php if($nolect!="" || $paytype=="M"|| $paytype==""|| $holiday!="")  echo "readonly=readonly";?> <?php if($l==true || $holiday=='Holiday' ) echo "disabled=disabled"; ?> style="color: <?php echo $type;?>;max-width: 4em;" maxlength="5" size="3"  value="<?php if($work_hours=="") echo "0.00"; else echo $work_hours;?>" /> Hrs
			</td>
			<td><select name="time_from<?php echo $row['empid'];?>" id="time_from<?php echo $row['empid'];?>" size="1" <?php if($paytype=="H"|| $atttype!="") echo "disabled";?>  onchange="javascript:document.getElementById('hrs<?php echo $row['empid'];?>').value=getdiff('<?php echo $row['empid'];?>');" >
			<?php time_selected(@$intime); ?>
						</select>
				  		</td><td><select name="time_to<?php echo $row['empid'];?>" id="time_to<?php echo $row['empid'];?>" size="1" <?php if($paytype=="H"|| $atttype!="") echo "disabled";?>  onchange="javascript:document.getElementById('hrs<?php echo $row['empid'];?>').value=getdiff('<?php echo $row['empid'];?>');">
						<?php time_selected(@$outtime); ?>
						</select>
						
				  		</td>
			<td><?php echo $cause==""?"":"Cause: ".$cause; 
			if($leave_approved=="Y")
				echo "Leave Status: <font color='green'> Approved</font> | ";
			elseif($leave_approved=="R")
				echo "Leave Status: <font color='red'> Rejected</font> | ";
			elseif($leave_approved=="N")
				echo "Leave Status: <font color='Blue'> Pending</font> | ";

			echo $holiday==""?"":" ".$holiday;?><?php if($paytype=="H" && $stat>0) {echo "<font color='red'> ".$stat. " Lecture's attendance has not marked yet .</font>"; $counterror++;}if($work_hours=="" && $paytype!="" && $holiday=="" &&  $l==false)echo "<font color='red'> No Lecture is assigned to this employee.</font>";?></td>
			<?php }?>
		</tr>
		<tr></tr>
				<?php 
		}
		?>
	</tbody>
	<tfoot>
			<?php if(trim($empname)=="" & trim($att_status)==""){?>	
		<tr>

			<td colspan="11">
			<input type="hidden" value='<?php echo $counterror;?>' name="counterror" id="counterror"/>
			<input type="submit" value="SUBMIT" name="submit" class="btn save" id="submit"/>
			</td>
			
		</tr>
		<?php }?>
	</tfoot>

</table>
<b>Total: <?php echo $i;?>
</b>
<input
	type="hidden" id="txtMax" name="txtMax" value='<?php echo $i;?>'>
<?php 

}

else
	echo "<div class='message'>No employees in this department</div>";
?>

<?php 
@session_start();
include("../conn.php");
include('../check_session.php');
include_once("../functions/common.php");

makeSafe(extract($_REQUEST));
$query="";
$query="select * from student_class where session_id=".$session_id ;

$getatt=mysql_num_rows(mysql_query($query,$link));
if($getatt!=0)
{
?>
<table class="table table-boredered" width="auto" style="cursor: pointer;" name="studentlist" id="studentlist">
<thead>
	<tr>
	  <th align="center" width="50px;">#</th>
		<th>REG NO</th>
		<th>MEMBER NAME</th>
		<th colspan="4">ATTENDANCE STATUS</th>
	  </tr>
</thead>
<tbody>
<?php 
$i=0;
$sql="SELECT student_class.stu_id, mst_students.reg_no,concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name FROM student_class,mst_students WHERE student_class.stu_id=mst_students.stu_id and session_id='".$session_id."' and mst_students.reg_no = '".$filter."'
	  union all
	  SELECT student_class.stu_id, mst_students.reg_no,concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name FROM student_class,mst_students WHERE student_class.stu_id=mst_students.stu_id and session_id='".$session_id."' and (concat_ws(' ',stu_fname,stu_mname,stu_lname) like '%".$filter."%' OR mst_students.reg_no='".$filter."') order by reg_no";
//echo $sql;
$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
while($row=mysql_fetch_array($res))
{
	$i=$i+1;
    $sql="SELECT stu_id,att_type FROM student_attendance_register where stu_id=".$row['stu_id']."  and att_date='".$date."'"; 
	$resC=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
	if(mysql_affected_rows($link) > 0)
		{
			$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
			$atttype=$rowL['att_type'];
		}

		$leave_approved="";
		$cause="";
		$l=false;
		$holiday="";
		/*$sql="SELECT leave_approved,cause FROM student_leave_applications where stu_id=".$row['stu_id']."  and '". $date ."' between date_from and date_to"; 
		$resC=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
		if(mysql_affected_rows($link)>0)
		{
			$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
			$leave_approved=$rowL['leave_approved'];
			$cause=$rowL['cause'];
			$l=true;
		}
		$sql="SELECT event_type FROM event as e,event_person_invites as ep  where e.event_id=ep.event_id and person_type='S' and person_id=".$row['stu_id']." and '". $date ."' between date(event_start_date) and date(event_end_date)";
		$resC=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
		if(mysql_affected_rows($link)>0)
		{
			$rowL     = mysql_fetch_array($resC,MYSQL_ASSOC);
			$holiday=$rowL['event_type'];
			
		}*/
				
?>
	<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>  onclick="selectID('<?php echo $row['stu_id']; ?>');">
	<td align="center"><input type="radio" name="rdoID" value="<?php echo $row['stu_id']; ?>" id="rdoID" onclick="selectID('<?php echo $row['stu_id']; ?>');"/></td>
	<td><?php echo $row['reg_no'];?></td>
	<td><?php echo $row['stu_name'];?></td>
	<td><input type='radio' id='P<?php echo $i;?>' name='<?php echo $i;?>' value="P<?php echo $row['stu_id'];?>" <?php if($l==false) echo "checked";?>/>Present</td>
	<td><input type='radio' id='A<?php echo $i;?>' name='<?php echo $i;?>' value="A<?php echo $row['stu_id'];?>"
	<?php 
	if(@$atttype=="A")
		echo "checked";
	?>	/>Absent</td>
	<td><input type='radio' id='L<?php echo $i;?>' name='<?php echo $i;?>' value="L<?php echo $row['stu_id'];?>" <?php 
	if($l==true) 
		echo "checked";
	elseif(@$atttype=="L")
		echo "checked";?> />Leave</td>
	<td><input type='radio' id='H<?php echo $i;?>' name='<?php echo $i;?>' value="H<?php echo $row['stu_id'];?>" <?php
	if($holiday=='HOLIDAY')
		echo "checked";
	elseif(@$atttype=="H")
		echo "checked";
	?> />Holiday</td>
	</tr>
<?php 
}
?>
</tbody>
<tfoot>
<tr><td colspan="9">
<?php
if($i==0)
	echo "<h3>NO RECORDS FOUND.</h3>";
else
{ 
if(trim($filter)==""){?><input type="submit" value="SUBMIT" class="btn save" name="submit" id="submit"/><?php }?>
</td>
</tr>
</tfoot>
</table>
<b>Total: <?php echo $i;?></b>
<?php }?>
<input type="hidden" id="txtMax" name="txtMax" value='<?php echo $i;?>'>
<?php 
}
else
  echo "<center><h3>Students Are Not Available For These Department</h3></center>"; 	  
  ?>
<?php 
@session_start();
include("../conn.php");
include("../functions/common.php");

if(isset($_REQUEST['Eventdate']))
{
$EventDate=makeSafe(@$_REQUEST['Eventdate']);

$sql="SELECT event.*,event_organizer.* FROM event LEFT JOIN event_organizer on event.event_id=event_organizer.event_id WHERE event.br_id=".$_SESSION['br_id']." and `event_start_date` LIKE '$EventDate%' GROUP BY event.`event_id` order by event_start_date desc";
}
else
{
$sql="SELECT event.*,event_organizer.* FROM event LEFT JOIN event_organizer on event.event_id=event_organizer.event_id where event.br_id=".$_SESSION['br_id']." GROUP BY event.`event_id`  order by event_start_date desc";
}
$result=mysql_query($sql);

?>
<style>
.selected {
    background-color: 'blue';
}	
</style>

<table width="100%" border="0" class="table table-bordered"" align="center" id="tabone" style="cursor:pointer;" >
  <thead>
  <tr>
    <th>#</th>
    <th>EVENT NAME</th>
    <th>DESCRIPTION</th>
    <th>START DATE</th>
    <th>END DATE</th>
    <th>TYPE</th>
    <th>ORGANIZED BY</th>    
    <th>CREATED ON</th>    
    <th>UPDATED BY <br>Employee Name[Emp_Code]</th>    
  </tr>
  </thead>
  <tbody>
  <?php
  		if(mysql_num_rows($result)>0)
  		{
  		$i=0;
		while($row=mysql_fetch_array($result))
		{
		$i=$i+1;
		
		$sqlorganizers=mysql_query("select * from event_organizer where event_id=".$row['event_id']);
		$organizername="";
		   if(mysql_num_rows($sqlorganizers)>0)
			{
		 	
		while ($organizer = mysql_fetch_array($sqlorganizers)) 
		 {
		 	
			if($organizer['person_type'] == "E") 
			{
		   	  $sqldetails = mysql_fetch_array(mysql_query("select emp_name as name from employee where empid=" . $organizer ['person_id']));		
		      $organizername.="&bull; ".$sqldetails['name']."(EMPLOYEE)<br>";
		    }
            else if($organizer['person_type'] == "S")
			{
				$sqldetails = mysql_fetch_array(mysql_query("select concat_ws(' ',stu_fname,stu_mname,stu_lname) as name from mst_students where stu_id=" . $organizer ['person_id']));
				 $organizername.="&bull; ".$sqldetails['name']."(STUDENT)<br>";
            }
		}	
        $organizername=rtrim($organizername,", ");
		 }
		 
	?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo $row['event_id']; ?>')">
	    <td><input type="radio" name="rdoID" value="<?php echo $row['event_id']; ?>" id="rdoID"/></td>
	    <td><a href ="javascript:open_modal('popups/read_event.php?event_id=<?php echo base64_encode($row['event_id']);?>',600,400,'EVENT');" class='mylink'">&nbsp;<?php echo $row['event_name'];?></a></td>
	    
	    <td width="18%" style="word-break: break-all;">&nbsp;<?php echo $row['event_description'];?></td>
		<td>&nbsp;<?php echo date("jS-M-Y g:iA",strtotime($row['event_start_date']));?></td>
		<td>&nbsp;<?php echo date("jS-M-Y g:iA",strtotime($row['event_end_date']));?></td>
		<td>&nbsp;<?php echo $row['event_type'];?></td>
		<td><?php echo $organizername ?></td>
		<td>&nbsp;<?php echo date("jS-M-Y g:iA",strtotime($row['event_creation_datetime']));?></td>
		<td>&nbsp;<?php echo ucfirst($row['event_created_by']);?></td>
		
	  </tr>
	  <?php
	  }
	  ?>
	    <tr><th colspan="18"> <b center>Total: <?php echo $i;?></b></th></tr>
	<?php
	  }
	  else
	  {
	  	?>
	  	<tr><th align="center" colspan="9" style="font-weight:bold">NO RECORDS FOUND</th></tr>
	<?php 
	   }	  
	  ?>
	  </tbody>	  
	
	  </table>
	  <input type="hidden" name="EventID" id="EventID" value="" />
<input type="hidden" name="txtAction" id="txtAction" value="" />
<input type="hidden" name="txtUpdateStr" id="txtUpdateStr" value="" />

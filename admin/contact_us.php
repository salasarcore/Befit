<?php 
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_contact_us,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_contact_us))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
	if(isAccessModule($id_admin, export_excel_enquiries))
		$hasexcelpermission = true;
	else
		$hasexcelpermission = false;
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}*/
?>
<script>
function ExportExcel()
{
	url = 'enquiryListToExcel.php';
	window.open(url, '_blank');
}
</script>
<div class="page_head">
<div id="navigation"><a href="pages.php"> Home</a><a> Administration</a><span style="color: #000000;">List of Enquirers</span> </div>
<table width="100%" class="adminform1">
<tr>
	<td><h2>List of Enquirers</h2></td>
	<td>
	<div id="option_menu">

  <a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ExportExcel();">Export Excel</a>

  </div>
	</td>
</tr>	
</table>
</div>
<form name="frm" id="frm" method="POST" action="pages.php?src=contact_us.php">
<div class="search_bar">
<?php  $strfilter1= makeSafe(@$_POST['txtFilter1']); 
$department= makeSafe(@$_POST['department']);
?>

Enter Name: <input type="text" name="txtFilter1" value="<?php echo $strfilter1; ?>" /> 

<input type="submit" name="submit" value="Go" class="btn btn-info">
</div>
<br/>


<div align="center" style="font-size:15px;color:red;">Note:Kindly contact the enquirer by mail or contact number as listed below.</div>
<br/>
<table cellspacing="0" class="table table-bordered" style="cursor: pointer;">

  <thead>		
   <tr>
    <th>NAME</th>
    <th>HEIGHT</th>
    <th>WEIGHT</th>
    <th>AGE</th>
    <th>HEALTH PROBLEMS</th>
    <th>CATEGORY</th>
    <th>PHONE NO.</th>
    <th>PROGRAM RECOMMENDED</th>
    <th>CONSULTANT</th>
    <th>FOLLOW NO.</th>
    <th>ENQUIRY FOR</th>
    
    <th>DATE</th>
  </tr>
  </thead>
  <tbody>
   <?php
	$i=0;
	$sql="";
	$sqlWhere="";
	
	// $sql.="select c.*, d.department_id, d.department_name from  contact_us left JOIN mst_departments ON  c.dept_id=d.department_id where c.br_id=".$_SESSION['br_id']." and c.name LIKE '%".$strfilter1."%' ";
	//$sql.="select c.*, d.department_id, d.department_name from contact_us c, mst_departments d where c.br_id=".$_SESSION['br_id']." and c.dept_id=d.department_id and c.name LIKE '%".$strfilter1."%' ";
       $sql.="select * from contact_us where name LIKE '%".$strfilter1."%'";
	   
	     		$sqlOrder =" order by enquiry_no desc";
			
      		$sql=$sql." ".$sqlWhere.$sqlOrder." ";

		$res=mysql_query($sql);
	
	while($row=mysql_fetch_array($res))
	{
		$i=$i+1;
		?>
				 <tr>
					<td align="center" width="10%"><?php echo $row['name'];?>	</td>
					<td align="center" width="5%"><?php echo $row['height'];?>	</td>
					<td align="center" width="5%"><?php echo $row['weight'];?>	</td>
					<td align="center" width="5%"><?php echo $row['age'];?></td>
					<td align="center" width="20%" style="word-break:break-all;"><?php echo $row['health_problems'];?></td>
					<td align="center"><?php echo $row['category'];?></td>
					<td align="center"><?php echo $row['phone_no'];?></td>
					<td align="center" width="20%" style="word-break:break-all;"><?php echo $row['program_recommended'];?></td>
					<td align="center"><?php echo $row['consultant'];?></td>
					<td align="center"><?php echo $row['follow_no'];?></td>
					<td align="center"><?php echo $row['enquiry_for'];?></td>
					<td align="center"><?php echo date("jS-M-Y",strtotime($row['enquiry_date']));?></td>
						
				</tr>
			<?php
			}
			?> 
		  </tbody>
		  
		    <tfoot>
			  <th colspan='18'>
			<?php // how many rows we have in database
 	$sql1="select  count(enquiry_no) as numrows  from contact_us where name LIKE '%".$strfilter1."%'";
     	
      		$sqlOrder =" order by id desc";
      		$sql1=$sql1." ".$sqlWhere;
	
			$result  = mysql_query($sql1);
			$row1     = mysql_fetch_array($result, MYSQL_ASSOC);
			$numrows = $row1['numrows'];
						
			if($numrows>0)
				echo "TOTAL RECORDS : ".$numrows;
			else
				echo "NO RECORDS FOUND";

?>
			</th>
			  </tfoot>
			  
		</table>
		</form>

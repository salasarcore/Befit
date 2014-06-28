<?php 
@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
$act=makeSafe($_GET['act']);
$dept_id=makeSafe(@$_REQUEST['department']);
$section_id=makeSafe(@$_REQUEST['section']);
$expected_id=makeSafe(@$_REQUEST['expected']);

		if($act=="sectionlist")
		{
			$sql="select distinct(s.session_id), s.section from mst_departments d, session_section s,student_expected_fees as exp	where s.session_id=exp.section_id and d.department_id=s.department_id and s.freeze='N' and s.session='".makesafe($_SESSION['d_session'])."' and s.department_id='".$dept_id."' and d.br_id=".$_SESSION['br_id']." order by department_name,session_id desc";
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			echo "<option value=''>--Select Batch--</option>";
			while($row=mysql_fetch_array($res))
			{
				echo "<option value='".$row['session_id']."'";
				echo ">".$row['section']."</option>";
			}
		}
		elseif($act=="feeexpectedlist")
		{

			$sql="select distinct(fee_expected_id) as id from student_expected_fees where section_id=".$section_id;
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			if(mysql_num_rows($res)>0)
			{				echo "<option value=''>--Select Fees--</option>";
				if(mysql_num_rows($res)>1)
					echo "<option value=''>All</option>";
			}
			else
				echo "<option value=''>Fees not yet assigned to this section.</option>";
			while ($row = mysql_fetch_array($res))
			{
				$sql1="select name from fee_expected where fee_expected_id=".$row['id'];
				$res1=mysql_query($sql1) or die("Unable to connect to Server, We are sorry for inconvienent caused");
				$results=mysql_fetch_assoc($res1);
				echo "<option value='".$row['id']."'";
				echo ">".$results['name']."</option>";
			}
		}
		elseif ($act=="all" || $act=="search")
		{
		$i=0;
?>
<br />
<style>
	.selected {
	 background-color: 'blue';
	 }
</style>

<table id="tab" class="table table-bordered" align="center" style="width: 100%; table-layout:fixed;cursor: pointer;">
	  <thead>
		  <tr>
			<th width="30px">#</th>
			<th>FEE NAME</th>
			<th>COLLECTION DATE</th>
			<th>COURSE</th>
			<th>BATCH</th>
			<th>NAME</th> 
			<th width="150px">TOTAL AMOUNT</th> 
			<th width="150px">COLLECTED AMOUNT</th> 
		  </tr>
	  </thead>
	  <tbody>
			<?php 
		
			$sql="select sum(collected_amount)+IFNULL(sum(discount_amount),0) as collected_amount,fc.stu_id,fc.updated_at, fc.fee_expected_id, department_id, session_id,concat_ws(' ',ms.stu_fname,ms.stu_mname,ms.stu_lname) as stu_name,rollno 
					from fee_collection as fc 
					LEFT JOIN fee_discount_collection ON fc.fee_collection_id=fee_discount_collection.fee_collection_id
					Inner join mst_students as ms on
					ms.stu_id=fc.stu_id and ms.br_id=".$_SESSION['br_id']."
					Inner join student_class as sc on
					ms.stu_id=sc.stu_id";
			if(trim(@$dept_id)!="" && @$dept_id!='null') $sql.=" where department_id=".@$dept_id;
			if(trim(@$section_id)!="" && @$section_id!='null') $sql.=" and session_id=".@$section_id;
			if(trim(@$expected_id)!="" && @$expected_id!='null') $sql.=" and fc.fee_expected_id=".@$expected_id;
			
			$sql.=" GROUP BY fc.stu_id, fc.fee_expected_id order by updated_at desc";
			$collection=mysql_query($sql);
if(mysql_num_rows($collection)>0){
	while ($row = mysql_fetch_array($collection)) {
		$deptdetails=getDetailsById("mst_departments","department_id",$row['department_id']);
		$secdetails=getDetailsById("session_section","session_id",$row['session_id']);
		$exptdetails=getDetailsById("fee_expected","fee_expected_id",$row['fee_expected_id']);
		$pertdetails=getDetailsById("fee_particulars","fee_particulars_id",$exptdetails['fee_particulars_id']);
	$i++;
?>
  					 <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo $row['stu_id']; ?>','<?php echo $row['fee_expected_id']; ?>')" >
  					 <td  align="center"><input type="radio" name="rdoID" value="<?php echo $row['stu_id']; ?>" id="rdoID" /></td>
  						<td  align="center"><?php echo $exptdetails['name'];?></td>
  						<td  align="center"><?php echo date("jS-M-Y",strtotime($row['updated_at']));?></td>
  						<td style="word-wrap: break-word;" align="center"><?php echo $deptdetails['department_name'];?></td>
  						<td style="word-wrap: break-word;" align="center"><?php echo $secdetails['section'];?></td>
  						<td  align="center"><?php echo $row['stu_name'];?></td>
  						<td  align="center"><?php echo round($pertdetails['total_amount']);?></td>
  						<td  align="center"><?php echo round($row['collected_amount']);?></td>
  					</tr>
<?php 		
	}
	echo "<tr><th colspan='8' align='center'><b> Total Records : ".mysql_num_rows($collection)."</b></th></tr>";
}
else 
	echo "<tr><th colspan='8'>NO RECORDS FOUND</th></tr>"
				  		 ?>
				  		 </tbody>
	  </table>
				  <?php }?>

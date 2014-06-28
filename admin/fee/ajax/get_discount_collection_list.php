<?php 
@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
makeSafe(extract($_REQUEST));
		$i=0;
?>
<br />
<style>
.selected {
	 background-color: 'blue';
}
</style>
<?php 
if(trim(@$dept_id)=="")
{
?>
<table id="tab" class="table table-bordered" align="center" style="width: 100%; table-layout:fixed;">
	  <thead>
		  <tr>
			<th>COURSE NAME</th>
			<th>TOTAL AMOUNT</th>
			<th>COLLECTED AMOUNT</th> 
			<th>DISCOUNT AMOUNT</th>
			<th>DISCOUNT DETAILS</th>
		  </tr>
	  </thead>
	  <tbody>
			<?php 
		
			  $sql="SELECT md.department_id,department_name, sum(collected_amount) as collected,IFNULL(sum(discount_amount),0) as discount
					from fee_collection as fc
					INNER JOIN student_expected_fees as sef ON fc.stu_id=sef.stu_id and sef.fee_expected_id=fc.fee_expected_id
					INNER JOIN fee_expected as fe ON fe.fee_expected_id=sef.fee_expected_id
					INNER JOIN session_section as ss ON ss.session_id=sef.section_id
					INNER JOIN mst_departments as md ON md.department_id=ss.department_id
					LEFT JOIN fee_discount_collection as fdc ON fc.fee_collection_id=fdc.fee_collection_id
					LEFT JOIN fee_particulars as fp ON fp.fee_particulars_id=fe.fee_particulars_id where md.br_id=".$_SESSION['br_id'];
			 	 if(trim(@$collection_date)!="") $sql.="  and date(fc.created_at)='".$collection_date."'";
			$sql.=" GROUP BY md.department_id";
			$collection=mysql_query($sql);
if(mysql_num_rows($collection)>0){
	while ($row = mysql_fetch_array($collection)) {
		$sqlparticulars=mysql_fetch_assoc(mysql_query("select sum(total_particulars_amount) as collected from (select (count(sef.stu_id))*total_amount as total_particulars_amount, sef.fee_expected_id,sef.section_id
						from student_expected_fees as sef,mst_departments as md,session_section as ss,fee_expected as fe,fee_particulars as fp
						where md.department_id=ss.department_id and ss.session_id=sef.section_id
						and fe.fee_expected_id=sef.fee_expected_id and fe.fee_particulars_id=fp.fee_particulars_id and md.br_id=".$_SESSION['br_id']." and md.department_id=$row[department_id]
						group by md.department_id,sef.fee_expected_id) as collected"));
	$i++;
?>
  					 <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> >
  					 <td style="word-wrap: break-word;" align="center"><?php echo $row['department_name'];?></td>
  						<td  align="center"><?php echo round($sqlparticulars['collected']); ?></td>
  						<td  align="center"><?php echo round($row['collected']);?></td>
  						<td  align="center"><?php echo round($row['discount']);?></td>
  						
  						<td  align="center"><?php if(round($row['discount'])>0){?>
  						<a class="mylink" href="javascript:discountdetails('dept','<?php echo $row['department_id'];?>');">Discount Details</a>
  						<?php } else echo "No Discount";?></td>
  					</tr>
<?php 		
	}
	echo "<tr><td colspan='5' align='center'><b> Total Records : ".mysql_num_rows($collection)."</b></td></tr>";
}
else 
	echo "<tr><th colspan='5'>NO RECORDS FOUND</th></tr>"
				  		 ?>
				  		 </tbody>
	  </table>
<?php 
}
else 
{
?>
<table id="tab" class="adminlist" align="center" style="width: 100%; table-layout:fixed;">
	  <thead>
		  <tr>
			<th>ROLL NO</th>
			<th>STUDENT NAME</th>
			<th>SECTION</th>
			<th>TOTAL AMOUNT</th>
			<th>COLLECTED AMOUNT</th> 
			<th>DISCOUNT AMOUNT</th>
			<th>DISCOUNT DETAILS</th>
		  </tr>
	  </thead>
	  <tbody>
			<?php 
		
			  $sql="SELECT sef.stu_id,concat_ws(' ',ms.stu_fname,ms.stu_mname,ms.stu_lname) as stu_name,rollno,section, sum(collected_amount) as collected,IFNULL(sum(discount_amount),0) as discount 
					from fee_collection as fc 
					INNER JOIN student_expected_fees as sef ON fc.stu_id=sef.stu_id and sef.fee_expected_id=fc.fee_expected_id 
					INNER JOIN mst_students as ms ON ms.stu_id=sef.stu_id 
					INNER JOIN student_class as sc ON sc.stu_id=sef.stu_id 
					INNER JOIN fee_expected as fe ON fe.fee_expected_id=sef.fee_expected_id 
					INNER JOIN session_section as ss ON ss.session_id=sef.section_id 
					INNER JOIN mst_departments as md ON md.department_id=ss.department_id 
					LEFT JOIN fee_discount_collection as fdc ON fc.fee_collection_id=fdc.fee_collection_id 
					LEFT JOIN fee_particulars as fp ON fp.fee_particulars_id=fe.fee_particulars_id 
					where md.department_id=$dept_id and md.br_id=".$_SESSION['br_id'];
			 	 if(trim(@$collection_date)!="") $sql.="  and date(fc.created_at)='".$collection_date."'";
			  $sql.=" group by sef.stu_id";
			$collection=mysql_query($sql);
if(mysql_num_rows($collection)>0){
	while ($row = mysql_fetch_array($collection)) {
		$sqlparticulars=mysql_fetch_assoc(mysql_query("select sum(total_amount) as collected, sef.fee_expected_id,sef.section_id
						from student_expected_fees as sef,mst_departments as md,session_section as ss,fee_expected as fe,fee_particulars as fp
						where md.department_id=ss.department_id and ss.session_id=sef.section_id
						and fe.fee_expected_id=sef.fee_expected_id and fe.fee_particulars_id=fp.fee_particulars_id 
						group by sef.stu_id"));
			$i++;
?>
  					 <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>>
  					 <td align="center"><?php echo $row['rollno'];?></td>
  					 <td align="center"><?php echo $row['stu_name'];?></td>
  					  <td align="center"><?php echo $row['section'];?></td>
  						<td  align="center"><?php echo round($sqlparticulars['collected']); ?></td>
  						<td  align="center"><?php echo round($row['collected']);?></td>
  						<td  align="center"><?php echo round($row['discount']);?></td>
  						
  						<td  align="center"><?php if(round($row['discount'])>0){?>
  						<a class="mylink" href="javascript:discountdetails('student','<?php echo $row['stu_id'];?>');">Discount Details</a>
  						<?php } else echo "No Discount";?>
  						</td>
  					</tr>
<?php 		
	}
	echo "<tr><td colspan='7' align='center'><b> Total Records : ".mysql_num_rows($collection)."</b></td></tr>";
}
else 
	echo "<tr><th colspan='7'>NO RECORDS FOUND</th></tr>"
				  		 ?>
				  		 </tbody>
	  </table>
<?php
}
?>

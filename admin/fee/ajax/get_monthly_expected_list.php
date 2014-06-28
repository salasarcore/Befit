<?php 
@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
$act=makeSafe($_GET['act']);
$dept_id=makeSafe(@$_REQUEST['department']);
$monthyear=makeSafe(@$_REQUEST['monthyear']);

$curmonth="";
if ($act=="all" || $act=="search")
{
$i=0;
?>
<table id="tab" class="table table-bordered" align="center" style="width: 100%; table-layout:fixed;cursor: pointer;">
	  <thead>
		  <tr>
			<th>NAME</th>
			<th>DEPARTMENT</th>
			<th>FEE EXPECTED NAME</th>
			<th>INSTALLMENT AMOUNT</th>
			<th>DUE DATE</th>
			<th>TRANSACTION STATUS</th>
		  </tr>
	  </thead>
	  <tbody>
<?php 
			$sql="select fpt.*,md.*,sc.*,concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name from fee_payment_type as fpt, mst_departments as md, student_class as sc,mst_students as ms where fpt.stu_id=sc.stu_id and ms.stu_id=fpt.stu_id and sc.department_id=md.department_id and ms.br_id=".$_SESSION['br_id'];
		
			if(trim(@$dept_id)!="" && @$dept_id!='0') $sql.=" and md.department_id=".@$dept_id;
			
			$paymentdetails=mysql_query($sql);
			$collection=array();
			$unrealised=array();
			$total_collected=0;
			$total_amount=0;
			$count_extended=0;
			$expectedamt=0;
			
			$sqldate="select curdate() as today";
			$resdate=mysql_query($sqldate);
			$today=mysql_fetch_assoc($resdate);
			$seldate=date("m-Y",strtotime($today['today']));
				
			if(trim($monthyear)!="")
				$seldate=$monthyear;
			
			
	if(mysql_num_rows($paymentdetails)>0){
		while ($row = mysql_fetch_array($paymentdetails)) {
			unset($collection);
			unset($unrealised);
			$unrealised=array();
			$due_date="";
			
			$total_collected=0;
			$expected=getDetailsById("fee_expected","fee_expected_id",$row['fee_expected_id']);
			$particular=getDetailsById("fee_particulars","fee_particulars_id",$expected['fee_particulars_id']);
			$total_amount=$particular['total_amount'];
			
			$sqlcoll="select fc.* from fee_collection as fc, fee_transaction_details as ft where fc.fee_collection_id=ft.fee_collection_id and fc.fee_expected_id=".$expected['fee_expected_id']." and fc.stu_id=".$row['stu_id']." and cast(ft.realisation_datetime as datetime) <> 0";
			$result=mysql_query($sqlcoll);
			while ($row2 = mysql_fetch_assoc($result))
				$collection[]=$row2;
			if(!empty($collection)){
				foreach($collection as $rows){
					$sqlcoll="select discount_amount from fee_discount_collection where fee_collection_id=".$rows['fee_collection_id'];
					$result3=mysql_query($sqlcoll);
					$row3 = mysql_fetch_assoc($result3);
						
					$total_collected+=($rows['collected_amount']+$row3['discount_amount']);
				}
			}
			
			$count=0;

			$no_of_inst=$row['no_of_installments'];
			$inst_amt=$row['installment_amount'];
			$total_amount=$particular['total_amount'];
			
			for($i=1;$i<=$no_of_inst;$i++)
			if($total_collected>=($i*$inst_amt)) $count++;
			
			$calc=0;
			$remaining=0;
			

			for($i=$count;$i<$no_of_inst;$i++){
				if(date("m-Y",strtotime("+".($i*$row['interval_in_days'])." days", strtotime($row['start_date'])))==$seldate)
					$calc++;
			}

			$sqlcoll="select fc.*,ft.* from fee_collection as fc, fee_transaction_details as ft where fc.fee_collection_id=ft.fee_collection_id and fc.fee_expected_id=".$expected['fee_expected_id']." and fc.stu_id=".$row['stu_id'];
			$results=mysql_query($sqlcoll);
			while ($row2 = mysql_fetch_assoc($results))
				$unrealised[]=$row2;

			for($i=$count;$i<$no_of_inst;$i++){
				if(date("m-Y",strtotime("+".($i*$row['interval_in_days'])." days", strtotime($row['start_date'])))==$seldate){
					$due_date=date("jS-M-Y",strtotime("+".($i*$row['interval_in_days'])." days", strtotime($row['start_date'])));
					$curmonth=date("F-Y",strtotime($due_date));

		?>
  			<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> >
  			<?php if($remaining==0){?><td  align="center" rowspan="<?php echo $calc;?>"><?php echo $row['stu_name']; $count_extended++;?></td><?php }?>
  			<?php if($remaining==0){?><td style="word-wrap: break-word;" align="center" rowspan="<?php echo $calc;?>"><?php echo $row['department_name'];?></td><?php }?>
  			<?php if($remaining==0){?><td style="word-wrap: break-word;" align="center" rowspan="<?php echo $calc;?>"><?php echo $expected['name'];?></td><?php }?>
  			<td  align="center" style="border-left: 1px solid #CCCCCC;"><?php echo round($row['installment_amount']);?></td>
  			<td  align="center"><?php echo $due_date; ?></td>
  			<td  align="center"><?php if(array_key_exists($i, $unrealised)){
  			if($unrealised[$i]['realisation_datetime']=="0000-00-00 00:00:00")	
  					echo "<font color='blue'>In progress<font>"; 
  			}
  				else 
  				{
  					echo "<font color='red'>Unpaid<font>"; 
  					$expectedamt+=round($row['installment_amount']);
  				}
  				?></td>
  			</tr> 
<?php 	
	$remaining++;
	

		}
	}
}
	if($count_extended>0)
		echo "<tr><th colspan='6' align='center'><b> Total Records : $count_extended</b></th></tr>";
	else 
		echo "<tr><th colspan='6'>NO RECORDS FOUND</th></tr>";
}
else 
	echo "<tr><th colspan='6'>NO RECORDS FOUND</th></tr>";
?>
</tbody>
</table>
<br>
<?php 
	 $total_expected=0;
	 $sql="select sum(collected_amount)+IFNULL(sum(discount_amount),0) as collected_amount 
			from fee_collection as fc 
			LEFT JOIN student_class as sc ON sc.stu_id=fc.stu_id
			LEFT JOIN mst_students as ms ON ms.stu_id=sc.stu_id
			LEFT JOIN mst_departments as md ON sc.department_id=md.department_id
			LEFT JOIN fee_discount_collection ON fc.fee_collection_id=fee_discount_collection.fee_collection_id 
			where ms.br_id=".$_SESSION['br_id']." and ";
	 
	if(trim($monthyear)!="") 
		$sql.="MONTH(fc.updated_at)='".explode("-",$monthyear)[0]."' and YEAR(fc.updated_at)='".explode("-",$monthyear)[1]."'";
	else 
		$sql.="MONTH(fc.updated_at)=MONTH(CURDATE()) and YEAR(fc.updated_at)=YEAR(CURDATE())";

	 if(trim(@$dept_id)!="" && @$dept_id!='0') $sql.=" and md.department_id=".@$dept_id;
	 $resultcollected=mysql_fetch_array(mysql_query($sql));
	 
	 
	 $sql="select sum(collected_amount)+IFNULL(sum(discount_amount),0) as collected_amount
			from fee_collection as fc
			LEFT JOIN student_class as sc ON sc.stu_id=fc.stu_id
			LEFT JOIN fee_transaction_details as ftd ON ftd.fee_collection_id=fc.fee_collection_id
			LEFT JOIN mst_students as ms ON ms.stu_id=sc.stu_id
			LEFT JOIN mst_departments as md ON sc.department_id=md.department_id
			LEFT JOIN fee_discount_collection ON fc.fee_collection_id=fee_discount_collection.fee_collection_id
			where ms.br_id=".$_SESSION['br_id']." and cast(ftd.realisation_datetime as datetime) = 0 and ";
	 
	 if(trim($monthyear)!="")
	 	$sql.="MONTH(fc.updated_at)='".explode("-",$monthyear)[0]."' and YEAR(fc.updated_at)='".explode("-",$monthyear)[1]."'";
	 else
	 	$sql.="MONTH(fc.updated_at)=MONTH(CURDATE()) and YEAR(fc.updated_at)=YEAR(CURDATE())";

	 if(trim(@$dept_id)!="" && @$dept_id!='0') $sql.=" and md.department_id=".@$dept_id;
	 $resultcollectedrealised=mysql_fetch_array(mysql_query($sql));
	 
	 
	 $total_expected=$resultcollected['collected_amount']+$expectedamt;
	 
	 $total_realised=$resultcollectedrealised['collected_amount'];
?>
<table style="margin-left: 15px;">
<tr>
  <th width="200px">Total Expected Amount</th><th width="200px">Total Transaction Amount</th><th width="200px">Total Unrealised Amount</th><th width="200px">Total Pending Dues</th>
</tr>
<tr>
<?php 
if($curmonth=="") $curmonth= date("F-Y",strtotime("1-".$monthyear));
?>
  <td align="center"><a class="feesbtn" style="width: 100px;color: orange;" title="Total Expected Amount For <?php echo $curmonth;?>"><?php echo round($total_expected);?>/-</a></td>
  <td align="center"><a class="feesbtn" style="width: 100px;color: red;" title="Total Transaction Amount For <?php echo $curmonth;?>"><?php echo round($resultcollected['collected_amount']);?>/-</a></td>
  <td align="center"><a class="feesbtn" style="width: 100px;color: green;" title="Total Unrealised Amount For <?php echo $curmonth;?>"><?php echo round($total_realised);?>/-</a></td>
  <td align="center"><a class="feesbtn" style="width: 100px;color: blue;" title="Total Pending Dues For <?php echo $curmonth;?>"><?php echo round($expectedamt+$total_realised);?>/-</a></td>
</tr>
</table>
<?php } ?>
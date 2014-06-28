<script language="javascript">
$(document).ready(function()
{
	$("table tr[group]").hide();

	$("input[type=checkbox]").change(function() {
	    var $this = $(this);
	    if ($this.is(':checked')) {
	    	$("table tr[group='"+$this.val()+"']").show(1000);
	    } else {
	    	$("table tr[group='"+$this.val()+"']").hide(1000);
	    }
	});	
});
</script>
<?php 
@session_start();
include('../../conn.php');
include('../../check_session.php');
include('../../functions/comm_functions.php');
include_once("../../functions/common.php");
$regno=makeSafe($_REQUEST['regno']);
$student_details=array();
$unrealised=array();
$sqlstu=mysql_query("select * from mst_students where reg_no='".$regno."' and br_id=".$_SESSION['br_id']);
while ($row = mysql_fetch_array($sqlstu)) 
	$student_details=$row;
if(!empty($student_details)){

	$expected_details=array();
	$stuid=$student_details['stu_id'];
	
	$sql=mysql_query("select * from student_expected_fees where stu_id=".$stuid);
	while ($row = mysql_fetch_array($sql)) {
		$expected_details[]=$row;
	}
	
	if(!empty($expected_details)){
		$i=0;
		echo "<table class='table table-bordered'>";

			foreach($expected_details as $row)
			{
				$expected=getDetailsById("fee_expected","fee_expected_id",$row['fee_expected_id']);
				$particular=getDetailsById("fee_particulars","fee_particulars_id",$expected['fee_particulars_id']);
				$instsql="select * from fee_payment_type where fee_expected_id=".$expected['fee_expected_id']." and stu_id=".$stuid;
				$res= mysql_query($instsql) or die('Error, query failed');
?>
<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> >
	<td align="center"><input type="checkbox" name="checkbox"	value="<?php echo $expected['fee_expected_id']; ?>" id="rdoID" /></td>
	<td align="center" colspan="<?php if(makeSafe(isset($_GET['type']))) echo "2"; else echo "3"; ?>"><a id="expthead" href="#" title="Fee Expected Name (Total Amount)"><b><?php echo $expected['name']." (".$particular['total_amount'].")";?></b></a></td>
	<?php if(makeSafe(isset($_GET['type']))) echo "<td align='center'><b>Delete<b></td>";?>
	
</tr>
<?php 
if(mysql_num_rows($res)>0){
$instdetails=mysql_fetch_assoc($res);
$date=date('jS-M-Y',strtotime($instdetails['start_date']));
?>
<tr group="<?php echo $expected['fee_expected_id']; ?>">
	<td align="center"><b>#</b></td>
	<td align="center"><b>Due Date</b></td>
	<td align="center"><b>Amount</b></td>
	<?php if(makeSafe(!isset($_GET['type']))){?><td align="center"><b>Pay Status</b></td><?php }?>
	<?php if(makeSafe(isset($_GET['type']))){ ?>
	<td align='center'><a href ="javascript:void(0);"><img src="../../../images/publish_x.png" onclick="Deleteinst('<?php echo $instdetails['fee_payment_type_id']; ?>');"/></a></td><?php }?>
	</tr>
<?php 
$collectiondtls="select sum(fc.collected_amount+IFNULL(fd.discount_amount,0)) as collected from fee_collection as fc left join fee_discount_collection as fd on fc.fee_collection_id=fd.fee_collection_id where fc.fee_expected_id=".$expected['fee_expected_id']." and stu_id=".$stuid;
$res1= mysql_query($collectiondtls) or die('Error, query failed');
$collection=mysql_fetch_assoc($res1);
$unrealised=array();
$sqlcoll="select fc.*,ft.* from fee_collection as fc, fee_transaction_details as ft where fc.fee_collection_id=ft.fee_collection_id and fc.fee_expected_id=".$expected['fee_expected_id']." and fc.stu_id=".$stuid;
$results=mysql_query($sqlcoll);
while ($row2 = mysql_fetch_assoc($results))
	$unrealised[]=$row2;


$count=0;
$strstatus="";
for($loop=0;$loop<$instdetails['no_of_installments'];$loop++)
{
?>
<tr	group="<?php echo $expected['fee_expected_id']; ?>">
	<td align="center"><?php echo $loop+1;?></td>
	<td align="center"><?php echo $date;?></td>
	<td align="center"><?php echo $instdetails['installment_amount'];?></td>
	<?php if(makeSafe(!isset($_GET['type']))){
	?><td align="center">
	<?php 
	if($collection['collected']>=(($loop+1)*$instdetails['installment_amount']))
		$strstatus= "<b><font color='green'>PAID</font></b>"; 
	else { 
		if($count==0){
			$strstatus= "<b><font color='#D2691E'>NEXT</font></b>"; 
			$count++;
			} 
		else 
			$strstatus= "<b><font color='red'>UNPAID</font></b>"; 
		
}
if(array_key_exists($loop, $unrealised)){
			if($unrealised[$loop]['realisation_datetime']=="0000-00-00 00:00:00")
				$strstatus= "<b><font color='blue'>IN PROGRESS<font></b>";
		}
		echo $strstatus;
		?>
			</td><?php }?>
			<?php if(makeSafe(isset($_GET['type']))) echo "<td></td>"; ?>
<?php 		
	$date=date('jS-M-Y',strtotime($date."+".$instdetails['interval_in_days']." days"));
}
}
else
	echo "<tr group='".$expected['fee_expected_id']."'><td colspan='4' align='center'>Define installment types for ".$expected['name']."</td>";
	echo "</tr>";
			}
	echo  "</table>";
	}
}
?>

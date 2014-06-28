<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include('../../functions/comm_functions.php');
include_once("../../functions/common.php");

if(makeSafe(isset($_REQUEST['act']))) $act=makeSafe($_REQUEST['act']);
if($act=="submit")
{
	$stuid=makeSafe($_REQUEST['stuid']);
	$expectedid=makeSafe($_REQUEST['expecteddropdown']);
	$interval=makeSafe($_REQUEST['interval']);
	if(trim($expectedid)=="")
		echo "<font color='#FF0000'>Please Select Fees Expected.</font>";
	elseif (trim($interval)=="")
		echo "<font color='#FF0000'>Please Days Extended in Interval.</font>";
	elseif (!is_numeric($interval))
		echo "<font color='#FF0000'>Days Extended Should Be in Digits.</font>";
	else
	{
		$collection=array();
		$sqlcoll="select * from fee_collection where fee_expected_id=".$expectedid." and stu_id=".$stuid;
		$result=mysql_query($sqlcoll);
		while ($rows = mysql_fetch_assoc($result))
			$collection[]=$rows;


		$expected=getDetailsById("fee_expected","fee_expected_id",$expectedid);
		$particular=getDetailsById("fee_particulars","fee_particulars_id",$expected['fee_particulars_id']);

		$total_collected=0;
		foreach($collection as $row)
		{
			$total_collected_amt=0;
			$sqlcoll="select discount_amount from fee_discount_collection where fee_collection_id=".$row['fee_collection_id'];
			$result3=mysql_query($sqlcoll);
			$row3 = mysql_fetch_assoc($result3);
			$total_collected_amt=$row['collected_amount']+$row3['discount_amount'];

			$total_collected+=$total_collected_amt;
		}
		$total_amount=$particular['total_amount'];

		if(round($total_amount-$total_collected)>0){

			$sql="update fee_payment_type set extended_days='".$interval."', updated_by='".$_SESSION['emp_name']."'";
			$result=mysql_query($sql) or die('Error, query failed');
				
			if($result)
				echo "<font color='#268C3C;'>Due Date Extended Successfully.</font>";
			else
				echo "<font color='#FF0000'>Transaction Failed. Please Try Again.</font>";
		}
		else
			echo "<font color='#FF0000'>Cannot Extend Days As There Are No Pending Dues.</font>";
	}
}
?>
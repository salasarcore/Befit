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
	$sql="select ss.duration_days from session_section as ss,student_class as sc where ss.session_id=sc.session_id and sc.stu_id=".$stuid;
	$res=mysql_query($sql);
	$rowduration=mysql_fetch_array($res);
	$duration=$rowduration['duration_days'];
	$expectedid=makeSafe($_REQUEST['expecteddropdown']);
	$no_of_inst=makeSafe($_REQUEST['no_of_inst']);
	$interval=makeSafe($_REQUEST['interval']);
	if(trim($expectedid)=="")
	echo "<font color='#FF0000'>Please Select Fees Expected.</font>";
	elseif (trim($no_of_inst)=="")
	echo "<font color='#FF0000'>Please Enter Number of Installments.</font>";
	elseif (!is_numeric($no_of_inst))
	echo "<font color='#FF0000'>Number of Installments Should Be in Digits.</font>";
	elseif (trim($interval)=="")
	echo "<font color='#FF0000'>Please Enter Day in Interval.</font>";
	elseif (!is_numeric($interval))
	echo "<font color='#FF0000'>Day in Interval Should Be in Digits.</font>";
	elseif (intval($interval)>365)
	echo "<font color='#FF0000'>Day in interval can nat be greater than a year.</font>";
	elseif (intval($interval)<=0)
	echo "<font color='#FF0000'>Day in interval can nat be less than or equal to zero.</font>";
	elseif (intval($interval*$no_of_inst)>$duration)
	echo "<font color='#FF0000'>Days in interval can nat be greater than duration of course .</font>";
	else
	{
		$query="select * from fee_payment_type where fee_expected_id=".$expectedid." and stu_id=".$stuid;
		$res=mysql_query($query);
		if(mysql_num_rows($res)>0){
			echo "<font color='#FF0000'>Fee Installment Type For This Fee Expected is Already Defined.</font>";
		}
		else
		{
			$extendeddays=0;
			$getexpecteddetails=getDetailsById("fee_expected","fee_expected_id",$expectedid);
			$getparticularsdetails=getDetailsById("fee_particulars","fee_particulars_id",$getexpecteddetails['fee_particulars_id']);
				
			$sql="select CURDATE() as today";
			$result1=mysql_query($sql);
			$row=mysql_fetch_assoc($result1);
			if(date('Y-m-d',strtotime($row['today']))>date('Y-m-d',strtotime($getexpecteddetails['due_date'])))
			$startdate=date('Y-m-d',strtotime($getexpecteddetails['due_date']));
			else
			$startdate=date('Y-m-d',strtotime($row['today']));
				
			$enddate= date('Y-m-d', strtotime($startdate. ' + '.($interval*($no_of_inst-1)).' days'));
			$intallmentamount=ceil($getparticularsdetails['total_amount']/$no_of_inst);
				
			$fee_payment_type_id=getNextMaxId("fee_payment_type","fee_payment_type_id")+1;
				
			$sql="insert into fee_payment_type (fee_payment_type_id,fee_expected_id,stu_id,no_of_installments,interval_in_days,installment_amount,start_date,end_date,extended_days,created_at,updated_at,updated_by) values
			(".$fee_payment_type_id.",".$expectedid.",".$stuid.",".$no_of_inst.",".$interval.",".$intallmentamount.",'".$startdate."','".$enddate."',".$extendeddays.",NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				
			$result=mysql_query($sql) or die('Error, query failed');
				
			if($result){
				$str = "<font color='#315829;'>Installment type defined successfully.</font>";
				$str .="<table class='shadow adminlist' style='width:50%;'>";
				$str .="<tr><th>SR NO</th><th>DUE DATE</th><th>INSTALLMENT AMOUNT</th></tr>";
				for($i=0;$i<$no_of_inst;$i++){

					$str .="<tr><td align='center'>".($i+1)."</td>
				<td align='center'>".date('jS-M-Y', strtotime($startdate. ' + '.($interval*$i).' days'))."</td>
				<td align='center'>".$intallmentamount."</td></tr>";
				}
				$str .="</table>";
				echo $str;
			}
			else
			echo "<font color='#FF0000'>Transaction Failed. Please Try Again.</font>";
		}
	}
}
elseif($act=="delete"){
	
	$payid=makeSafe($_POST['pay_id']);
	$getpaydetails=getDetailsById("fee_payment_type","fee_payment_type_id",$payid);
	$expectedid = $getpaydetails['fee_expected_id'];
	$stuid = $getpaydetails['stu_id'];
	
	$sqlcoll=mysql_query("select * from fee_collection where stu_id=".$stuid." and fee_expected_id=".$expectedid);
	
	if(mysql_num_rows($sqlcoll)>0)
		echo "Unable to delete as collection for this fees has been started already.";
	else 
	{
		$sqldelete=mysql_query("delete from fee_payment_type where fee_payment_type_id=".$payid);
		if($sqldelete)
			echo "Installment type deleted successfully.";
		else 
			echo "Unable to delete installment type. Please try again.";
	}
}
?>
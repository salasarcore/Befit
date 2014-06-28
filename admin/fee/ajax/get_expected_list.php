<?php
@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
$regno=makeSafe($_REQUEST['regno']);
$value="";
$getstudentdetails=array();
$sqlstu=mysql_query("select * from mst_students where reg_no='".$regno."' and br_id=".$_SESSION['br_id']);
while ($row = mysql_fetch_array($sqlstu)) 
	$getstudentdetails=$row;
if(!empty($getstudentdetails)){


	$getexpected=getDetailsById("student_expected_fees","stu_id",$getstudentdetails['stu_id']);
	if(!empty($getexpected)){
		if(makeSafe(isset($_REQUEST['duedate'])))
		{
		$sql="SELECT * FROM student_expected_fees WHERE fee_expected_id IN ( SELECT fee_expected_id FROM fee_payment_type where stu_id=".$getstudentdetails['stu_id'].") and stu_id=".$getstudentdetails['stu_id'];
		$value="To give due date extension";
		}
		else{
		$sql="SELECT * FROM student_expected_fees WHERE fee_expected_id NOT IN ( SELECT fee_expected_id FROM fee_payment_type where stu_id=".$getstudentdetails['stu_id'].") and stu_id=".$getstudentdetails['stu_id'];
		$value="To define installment";
		}
		$result=mysql_query($sql);
		while ($row = mysql_fetch_array($result))
		$getexpectedlist[]=$row;
		if(!empty($getexpectedlist)){
			echo "<label style='font-size:13;color:#4f4f4f;'><b>Note:</b> $value, please select fee expected from list.</label><br /><br />";
			echo "<label style='font-size:12;color:#575433;font-weight: normal !important;'>SELECT FEE EXPECTED :</label> <select name='expecteddropdown' id='expecteddropdown' onclick='populatedetails();'>
					<option value=''>--Select Fee Expected--</option>";
			foreach($getexpectedlist as $value){
				$getexpecteddetails=getDetailsById("fee_expected","fee_expected_id",$value['fee_expected_id']);
				echo "<option value='".$value['fee_expected_id']."'>".$getexpecteddetails['name']."</option>";
			}
			echo "<input type='hidden' name='stuid' id='stuid' value='".$getstudentdetails['stu_id']."' />";
		}
		else{
			if(makeSafe(isset($_REQUEST['duedate'])))
				echo "no:Please Define Installment Type To Extend Due Dates";
			else
				echo "no:Payment Type To All Fees Expected is Already Assigned.";
		}
	}
	else
	echo "no:No Fees Expected Details Found";
}
?>

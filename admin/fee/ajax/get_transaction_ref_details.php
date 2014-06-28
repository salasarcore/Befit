<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include('../../functions/comm_functions.php');
include_once("../../functions/common.php");

$trnsrefno=makeSafe($_REQUEST['trnsrefno']);

$sql="select * from fee_transaction_details where trans_ref_no='".$trnsrefno."'";
$results=mysql_query($sql);
if(mysql_num_rows($results)>0){
	$sqldata="select reg_no,fee_expected_id from fee_collection as fc, fee_transaction_details as t, mst_students as ms where fc.fee_collection_id=t.fee_collection_id and fc.stu_id=ms.stu_id and trans_ref_no='".$trnsrefno."'";
	$resultset= mysql_fetch_assoc(mysql_query($sqldata));
	echo $resultset['reg_no'].",".$resultset['fee_expected_id'];
}
else 
	echo "no:<font color='#FF0000'>Record Not Found</font>";

?>

<?php
@session_start();
include("../../conn.php");
include_once("../../functions/common.php");
include('../../functions/comm_functions.php');

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
		echo "You are not authorised to delete the fee collection data.";
		exit;
}

makeSafe(extract($_REQUEST));

$sql="delete from fee_fine_collection where fee_collection_id=".$fee_collection_id;
$res = mysql_query($sql);

$sql="delete from fee_collection_id where fee_collection_id=".$fee_collection_id;
$res = mysql_query($sql);

$sql="delete from fee_transaction_details where fee_collection_id=".$fee_collection_id;
$res = mysql_query($sql);
	if($res){
	$sql="delete from fee_collection where fee_collection_id=".$fee_collection_id;
	$res = mysql_query($sql);
		if($res)
			echo "Fee collection data deleted successfully.";
		else
			echo "Fee collection data not deleted successfully.";
	}
?>

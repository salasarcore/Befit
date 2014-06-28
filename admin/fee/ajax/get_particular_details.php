<?php 
@session_start();
include('../../conn.php');
include('../../check_session.php');
include('../../functions/comm_functions.php');
include_once("../../functions/common.php");

if(makeSafe(isset($_REQUEST['act']))) $act=makeSafe($_REQUEST['act']);
if($act=="particular")
{

	$getparticularsdetails=getDetailsById("fee_particulars","fee_particulars_id",makeSafe($_REQUEST['particularid']));
		
	if(!empty($getparticularsdetails)){
		$str="";
		$str .="<table class='shadow adminlist' style='width:100%;'>";
		$str .="<tr><td align='center'><b>PARTICULAR NAME</b></td><td align='center'><b>TOTAL AMOUNT</b></td></tr>";
		$str .="<tr><td align='center'>".$getparticularsdetails['name']."</td>
			<td align='center' id='paramount'>".$getparticularsdetails['total_amount']."</td></tr>";
		
		$str .="</table>";
		echo $str;
	}
	else
		echo "<font color='#FF0000'>Invalid Particular Selection.</font>";
}

?>
<?php 
@session_start();
include('../../conn.php');
include('../../check_session.php');
include('../../functions/comm_functions.php');
include_once("../../functions/common.php");
//require '../../sms/SmsSystem.class.php';
//include('../../email_settings.php');

//$smssend = new SMS(); //create object instance of SMS class

$fetch = array();
$getdata = array();
$feedata = array();
/*$querysetting="select * from notification_setting where module_id=".FEE_COLLECTION." and notification_type='S' and sending_type='A'";
$resquerysetting=mysql_query($querysetting,$link);
$numrows1 = mysql_num_rows($resquerysetting);
if($numrows1>0)
{
	$q  = "SELECT e.*, g.* FROM notification_module_master e, global_sms_templates g WHERE e.module_id=g.module_id  AND  e.module_name='Fee Collection' and available_for_school='Y' ";
	$query = mysql_query($q,$scslink);
	$fetch = mysql_fetch_array($query);
	$num = mysql_num_rows($query);
}
else $num = 0;
//email template

$query_local = "select * from notification_setting where module_id = ".FEE_COLLECTION." and  notification_type='E' and sending_type='A' " ; // module_id = 9 for fee collection
$res_local = mysql_query($query_local,$link);
$num_local = mysql_num_rows($res_local);
if($num_local>0) {
	
$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id and available_for_school='Y'  AND  e.module_name = 'Fee Collection'";
$sql_t = mysql_query($query_t,$scslink) or die('Error. Query failed.');
$nums = mysql_num_rows($sql_t);
$gettemp = mysql_fetch_assoc($sql_t);
$template = $gettemp['email_temp_format'];
}
*/
if(makeSafe(isset($_REQUEST['act']))) $act=makeSafe($_REQUEST['act']);
if(!isset($_REQUEST['paytype'])) 
	$type="expected";
else 
	$type=makeSafe($_REQUEST['paytype']);
$result=false;
if($act=="submit")
{
	if($type=="all"){
		$stuid=makeSafe($_REQUEST['stuid']);

		if(makeSafe(isset($_REQUEST['realised'])))
		{
				$realised=$_REQUEST['realised'];
				$realiseddate=$_REQUEST['realiseddate'];
				$realisedtime=$_REQUEST['realisedtime'];

				foreach ($realised as $realise)
				{
					$datetime=$realiseddate[$realise]." ".$realisedtime[$realise];
					$sqlupdate="update fee_transaction_details set realisation_datetime='".$datetime."' where trans_id=".$realise;
					mysql_query($sqlupdate);
				}
				echo "<font color='#268C3C;'>Fees Records Updated Successfully.</font>";
		}
		else{
		if(makeSafe(isset($_REQUEST['discount'])) && makeSafe(isset($_REQUEST['seldiscount']))){
			$discounts=$_REQUEST['discount'];
			$seldiscount=$_REQUEST['seldiscount'];
			$exptfine=$_REQUEST['fine'];
			$remarks=$_REQUEST['remark'];
			$paydet=$_REQUEST['paydet'];
			$tobank=$_REQUEST['tobank'];
			$frombank=$_REQUEST['frombank'];
			$accountno=$_REQUEST['accountno'];
			$trnsrefno=$_REQUEST['trnsrefno'];
			$paymode=$_REQUEST['paymode'];
			
			$err="";
			$received_amt=0;
			
			$sqlcoll="select * from student_expected_fees where stu_id=".$stuid;
			$result=mysql_query($sqlcoll);
			while ($row3 = mysql_fetch_assoc($result))
				$expected_details[]=$row3;
		
				
			$sql="select sum(total_amount) as total_amount from fee_particulars
			where fee_particulars_id in(
			select fee_particulars_id from fee_expected where
			fee_expected_id in(
			select fee_expected_id from student_expected_fees where stu_id=$stuid))";
			$sqlresult=mysql_query($sql);
			$totalamt=mysql_fetch_assoc($sqlresult);

			$sql1="select sum(collected_amount) as total_collected_amount from fee_collection where stu_id=".$stuid;
			$sqlresult=mysql_query($sql1);
			$total_collected=mysql_fetch_assoc($sqlresult);

			$remaining_amount=$totalamt['total_amount']-$total_collected['total_collected_amount'];
			
			foreach ($expected_details as $expected)
			{
				unset($collection);
				unset($total_collected);
				unset($particulardet);
				$total_collected_amt=0;
				if (array_key_exists($expected['fee_expected_id'],$seldiscount)){
					if($seldiscount[$expected['fee_expected_id']]==0)
						$discount=0;
					else
						$discount= makeSafe($discounts[$expected['fee_expected_id']]);

					$sql="select total_amount,fp.fee_particulars_id from fee_particulars as fp,fee_expected as fe
					where fp.fee_particulars_id=fe.fee_particulars_id and fe.fee_expected_id=".$expected['fee_expected_id'];
					$sqlres=mysql_query($sql);
					$particulardet=mysql_fetch_assoc($sqlres);

					$sqlcoll="select * from fee_collection where fee_expected_id=".$expected['fee_expected_id']." and stu_id=".$stuid;
						
					$result=mysql_query($sqlcoll);
					while ($row2 = mysql_fetch_assoc($result))
						$collection[]=$row2;

					if(!empty($collection)){
						foreach($collection as $rows){
							$sqlcoll="select discount_amount from fee_discount_collection where fee_collection_id=".$rows['fee_collection_id'];
							$result3=mysql_query($sqlcoll);
							$row3 = mysql_fetch_assoc($result3);
							$total_collected_amt+=($rows['collected_amount']+$row3['discount_amount']);
						}
					}

					if(($particulardet['total_amount']-$total_collected_amt)>=$discount){
						$paidamount=$particulardet['total_amount']-$total_collected_amt-$discount;

						$collection_id=getNextMaxId("fee_collection","fee_collection_id")+1;
						$sql="insert into fee_collection (fee_collection_id,fee_expected_id,stu_id,collected_amount,extended_days,remark,created_at,updated_at,updated_by) values
						(".$collection_id.",'".$expected['fee_expected_id']."',".$stuid.",".$paidamount.",0,'".makeSafe($remarks[$expected['fee_expected_id']])."',NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
						$result=mysql_query($sql);
						$realisedate="";
						if(makeSafe($paymode[$expected['fee_expected_id']])=="CASH")
							$realisedate=date("Y-m-d H:i:s");
						else
							$realisedate="";
						
						$trans_id=getNextMaxId("fee_transaction_details","trans_id")+1;
						$sqltrns="insert into fee_transaction_details (trans_id,fee_collection_id,payment_mode,trans_ref_no,transaction_remark,from_bank,from_bank_acc_no,to_bank,trans_datetime,realisation_datetime,updated_on,updated_by) values
						(".$trans_id.",'".$collection_id."','".makeSafe($paymode[$expected['fee_expected_id']])."','".makeSafe($trnsrefno[$expected['fee_expected_id']])."','".makeSafe($paydet[$expected['fee_expected_id']])."','".makeSafe($frombank[$expected['fee_expected_id']])."','".makeSafe($accountno[$expected['fee_expected_id']])."','".makeSafe($tobank[$expected['fee_expected_id']])."',NOW(),'".$realisedate."',NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
						$result=mysql_query($sqltrns);
						
						if($discounts[$expected['fee_expected_id']]!=0 && $seldiscount[$expected['fee_expected_id']]!=0)
						{
							$disc_coll_id=getNextMaxId("fee_discount_collection","fee_discount_collection_id")+1;
							$sql="insert into fee_discount_collection (fee_discount_collection_id,fee_collection_id,fee_discount_id,discount_amount,created_at,updated_at,updated_by) values
							(".$disc_coll_id.",".$collection_id.",".makeSafe($seldiscount[$expected['fee_expected_id']]).",".$discount.",NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
							$result=mysql_query($sql);
						}
						if($exptfine[$expected['fee_expected_id']]=="") $exptfine[$expected['fee_expected_id']]=0;
						if($exptfine[$expected['fee_expected_id']]!=0)
						{
							$fine_coll_id=getNextMaxId("fee_fine_collection","fee_fine_collection_id")+1;
							$finedet=getDetailsById("fee_fine_master","fee_particulars_id",$particulardet['fee_particulars_id']);
							$sql="insert into fee_fine_collection (fee_fine_collection_id,fee_collection_id,fine_id,fine_amount,created_at,updated_at,updated_by) values
													(".$fine_coll_id.",".$collection_id.",".makeSafe($finedet['fine_id']).",".makeSafe($exptfine[$expected['fee_expected_id']]).",NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
							$result=mysql_query($sql);
							$paidamount+=round(makeSafe($exptfine[$expected['fee_expected_id']]));
						}
						$received_amt+=$paidamount;
						
						$sqlextend="update fee_payment_type set extended_days='0' where stu_id=".$stuid." and fee_expected_id=".$expected['fee_expected_id'];
						mysql_query($sqlextend);
					}
					else
						$err.="error";
				}
			}
			if($result)
			{
				$str = "<font color='#268C3C;'>Fee Accepted Successfully.</font>";
				$str .="<table class='adminlist' style='width:50%;'>";
				$str .="<tr><td>Total Fees :</td><td>".$totalamt['total_amount']."</td></tr>";
				$str .="<tr><td>Paid Amount :</td><td>".$received_amt."</td></tr>";
				if($err!="") $str .="<tr><td colspan='2'><font color='#FF0000'>
				Discounted amount can not be greater than fee expected balance amount.</font></td></tr>";
				$str .="</table>";
				echo $str;
				$getdata = mysql_fetch_assoc(mysql_query("select * from mst_students where stu_id=".@$stuid));
			
				$date = date("jS M,Y");   
				$emailto =  $getdata['p_email'];
				//email
							
			/*	if(@$nums>0 && $emailto!="")
				{
					$subject= "Fee Collection";
					$path = FOLDER_POPUP;
					$url = SCHOOL_URL;
					 $sqlcol2="select x.name from student_expected_fees s, fee_expected x where  s.fee_expected_id=x.fee_expected_id and stu_id=".$stuid;
					$result2=mysql_query($sqlcol2);
					while ($row4 = mysql_fetch_assoc($result2))
						$expected_ar[]=$row4['name'];
					 $exp_name = implode(' & ',$expected_ar);
					
									
					$receipt = $_SESSION['br_name'].'-'.$collection_id;		
					$hashvalue = array(@$getdata['father_name'],$exp_name,'INR '.@$received_amt,$date,$receipt,$url,$url);
					$temp_value = getEmailMessage($template,$hashvalue);
					$sendEmail = Sending_EMail($temp_value,$emailto,$subject,$path);
				}
				//sms
				if($_SESSION['sms_t_count'] > 0 && $num > 0)
				{
					$receipt = $_SESSION['br_name'].'-'.$collection_id;
					$hashvalues = array(SCHOOL_NAME,'Mr.'.@$getdata['father_name'],'INR '.@$received_amt,$date,$receipt);//parentname, amount, date, school
					$message = $smssend->getSmsMessage($fetch['template_format'],@$hashvalues);
					$mobile = $getdata['mob'];
					$send_to = 'PARENT';
					$name = @$getdata['father_name'];
					$sendMessage = $smssend->sendTransactionalSMS($fetch['template_id'],$mobile,$message,trim($sms_sender_id));
					$logInsert = $smssend->insertLog($sendMessage,$name,trim($message),$mobile,$send_to,'T');
				}*/
			}
			else
			{
				echo "<font color='#FF0000'>Transaction Failed. Please Try Again.";
				if($err!="") echo " <br /> Discounted amount can not be greater than fee expected balance amount.";
				echo "</font>";
			}
		}
		else
			echo "<font color='#FF0000'>You have no pending dues.</font>";
		}
	}
	elseif($type=="expected"){
$str="";
		if(makeSafe(isset($_REQUEST['realised'])))
		{
			$realised=$_REQUEST['realised'];
			$realiseddate=$_REQUEST['realiseddate'];
			$realisedtime=$_REQUEST['realisedtime'];
				
			foreach ($realised as $realise)
			{
				$datetime=$realiseddate[$realise]." ".$realisedtime[$realise];
				$sqlupdate="update fee_transaction_details set realisation_datetime='".$datetime."' where trans_id=".$realise;
				mysql_query($sqlupdate);
			}
			$str= "<font color='#268C3C;'>Fees Records Updated Successfully.</font>";
		}
		
		if(isset($_REQUEST['checkedinst'])){
			
			$amount_collected=0;$err="";
			$stuid=makeSafe($_REQUEST['stuid']);
			$discounts=$_REQUEST['discount'];
			$seldiscount=$_REQUEST['seldiscount'];
			$exptfine=$_REQUEST['fine'];
			$expectedids=$_REQUEST['expected'];
			$exptamt=$_REQUEST['expectedids'];
			$remarks=$_REQUEST['remark'];
			$paydet=$_REQUEST['paydet'];
			$tobank=$_REQUEST['tobank'];
			$frombank=$_REQUEST['frombank'];
			$accountno=$_REQUEST['accountno'];
			$trnsrefno=$_REQUEST['trnsrefno'];
			$paymode=$_REQUEST['paymode'];
			
			foreach($expectedids as $expectedid){
				if (array_key_exists($expectedid,$_REQUEST['checkedinst'])){
					foreach($_REQUEST['checkedinst'][$expectedid] as $row)
					{
						$paid_amount=makeSafe($exptamt[$expectedid][$row]);
						if($paid_amount>=$discounts[$expectedid][$row]){
							if($discounts[$expectedid][$row]!=0 && $seldiscount[$expectedid][$row]!=0)
								$paid_amount-=makeSafe($discounts[$expectedid][$row]);
							$collection_id=getNextMaxId("fee_collection","fee_collection_id")+1;
							 $sql="insert into fee_collection (fee_collection_id,fee_expected_id,stu_id,collected_amount,extended_days,remark,created_at,updated_at,updated_by) values
							(".$collection_id.",'".$expectedid."',".$stuid.",".$paid_amount.",0,'".makeSafe($remarks[$expectedid][$row])."',NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
							$result=mysql_query($sql);
							$realisedate="";
							$trans_id=getNextMaxId("fee_transaction_details","trans_id")+1;
							if(makeSafe($paymode[$expectedid][$row])=="CASH")
								$realisedate=date("Y-m-d H:i:s");
							else 
								$realisedate="";
							
							$sqltrns="insert into fee_transaction_details (trans_id,fee_collection_id,payment_mode,trans_ref_no,transaction_remark,from_bank,from_bank_acc_no,to_bank,trans_datetime,realisation_datetime,updated_on,updated_by) values
						(".$trans_id.",'".$collection_id."','".makeSafe($paymode[$expectedid][$row])."','".makeSafe($trnsrefno[$expectedid][$row])."','".makeSafe($paydet[$expectedid][$row])."','".makeSafe($frombank[$expectedid][$row])."','".makeSafe($accountno[$expectedid][$row])."','".makeSafe($tobank[$expectedid][$row])."',NOW(),'".$realisedate."',NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
							$result=mysql_query($sqltrns);
							
							
							if($discounts[$expectedid][$row]!=0 && $seldiscount[$expectedid][$row]!=0)
							{
								$disc_coll_id=getNextMaxId("fee_discount_collection","fee_discount_collection_id")+1;
								$sql="insert into fee_discount_collection (fee_discount_collection_id,fee_collection_id,fee_discount_id,discount_amount,created_at,updated_at,updated_by) values
								(".$disc_coll_id.",".$collection_id.",".makeSafe($seldiscount[$expectedid][$row]).",".makeSafe($discounts[$expectedid][$row]).",NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
								$result=mysql_query($sql);
							}
							if($exptfine[$expectedid][$row]=="") $exptfine[$expectedid][$row]==0;
							if($exptfine[$expectedid][$row]!=0)
							{
								$fine_coll_id=getNextMaxId("fee_fine_collection","fee_fine_collection_id")+1;
								$particulardet=getDetailsById("fee_expected","fee_expected_id",$expectedid);
								$finedet=getDetailsById("fee_fine_master","fee_particulars_id",$particulardet['fee_particulars_id']);
								$sql="insert into fee_fine_collection (fee_fine_collection_id,fee_collection_id,fine_id,fine_amount,created_at,updated_at,updated_by) values
															(".$fine_coll_id.",".$collection_id.",".makeSafe($finedet['fine_id']).",".makeSafe($exptfine[$expectedid][$row]).",NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
								$result=mysql_query($sql);
								$paid_amount+=round(makeSafe($exptfine[$expectedid][$row]));
							}
							
							$amount_collected+=$paid_amount;
						}
						else
							$err.="error";
					}
				}
				$sqlextend="update fee_payment_type set extended_days='0' where stu_id=".$stuid." and fee_expected_id=".$expectedid;
				mysql_query($sqlextend);
			}
			if($result){
				$str = "<font color='#268C3C;'>Fee Accepted Successfully.</font>";
				$str .="<table class='adminlist' style='width:50%;'>";
				$str .="<tr><td>Paid Amount :</td><td>".$amount_collected."</td></tr>";
				if($err!="") $str .="<tr><td colspan='2'><font color='#FF0000'>
				Discounted amount can not be greater than fee expected balance amount.</font></td></tr>";
				$str .="</table>";
				echo $str;
				/*$getdata = mysql_fetch_assoc(mysql_query("select * from mst_students where stu_id=".@$stuid));
				$date = date("jS M,Y");
				//email
				$emailto =  $getdata['p_email'];
				if(@$nums>0 && $emailto!="")
				{
					$subject= "Fee Collection";
				    $path = FOLDER_POPUP;
				 
									
					foreach($expectedids as $expectedid){
					if (array_key_exists($expectedid,$_REQUEST['checkedinst'])){
					$sql2="select x.name  from fee_collection c, fee_expected x where c.fee_expected_id=".$expectedid." and x.fee_expected_id=".$expectedid."  and stu_id=".$stuid;
					$results=mysql_query($sql2);
					while ($rows3 = mysql_fetch_assoc($results))
					$ids[]=$rows3['name'];
				    } }
					$array_name = array_unique($ids); 
				    $exp_name = implode (' & ',$array_name); 
				    $receipt = $_SESSION['br_name'].'-'.$collection_id;
				    $url = SCHOOL_URL;
					$hashvalue = array(@$getdata['father_name'],$exp_name,'INR '.@$amount_collected,$date,$receipt,$url,$url);
					$temp_value = getEmailMessage($template,$hashvalue); 
					$sendEmail = Sending_EMail($temp_value,$emailto,$subject,$path);
				}
				//sms 
				if($_SESSION['sms_t_count'] > 0 && $num > 0)
				{
					$receipt = $_SESSION['br_name'].'-'.$collection_id;
					$hashvalues = array(SCHOOL_NAME,'Mr.'.@$getdata['father_name'],'INR '.@$amount_collected,$date,$receipt);//parentname, amount, date, school
					$message = $smssend->getSmsMessage($fetch['template_format'],$hashvalues);
					$mobile = $getdata['mob'];
					$send_to = 'PARENT';
					$name = @$getdata['father_name'];
					$sendMessage = $smssend->sendTransactionalSMS($fetch['template_id'],$mobile,$message,trim($sms_sender_id));
					$logInsert = $smssend->insertLog($sendMessage,$name,trim($message),$mobile,$send_to,'T');
				}*/
				
			}
			else{
				echo "<font color='#FF0000'>Transaction Failed. Please Try Again.";
				if($err!="") echo " <br/> Discounted amount can not be greater than fee expected balance amount.";
				echo "</font>";
			}
		}
		else
			if($str=="") echo "<font color='#FF0000'>No installments selected.</font>"; else echo $str;
	}
}
?>
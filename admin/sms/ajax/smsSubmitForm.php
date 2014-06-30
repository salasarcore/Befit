<?php

/**
 * This is a common ajax call which will be used to call the sms functions to send the message. This ajax call will be utilized for both manual as well as transactional sms sending.
 */

@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
include_once '../SmsSystem.class.php';

$smssend = new SMS(); //create object instance of SMS class
$br_id=makeSafe(@$_POST['branches']);
$Errs="";
$statusresult = array();
$schedulecheck="";
makeSafe(extract($_REQUEST));
if($act=="search")
{
	$temparray = array();
	$index = 0;
	$temp = 0;
	$sent = 0;
	$action = "";
	$act = "";
	$count = 0;
	$stuids = array();
	if(trim($message_text)=="")
		$Errs=  "<div class='error'>Please enter message text.</div>";
	else if(@$sendtype == "S")
	{
		if(@$send_to=='select')
			$Errs=  "<div class='error'>Please select Send To.</div>";
		else if (!empty($rdoID))
		{
			foreach ($rdoID as $stuid)
			{
				$studetails=getDetailsById("mst_students","stu_id",$stuid);
				 if(@$send_to == 'STUDENT')
					$send_to=='MEMBER';
					
						$mobile = $studetails['mob'];
			
				if(@$type=="P")
				{
					if(($sent > $_SESSION['sms_p_count']) || ($_SESSION['sms_p_count'] == 0))
					{
						$Errs= "<div class='success'>".$sent." Message(s) sent as you have exceeded your SMS sending limit. Please contact system administrator for more details.</div>";
						break;
					}
					else
					{
						/*if(@$schedulecheck=='on')
						{
							$datetime = str_replace(':','-',$schedule_date.'-'.$schedule_time);
							$result = $smssend->schedulePromotionalSMS(trim($mobile),trim($message_text),$datetime);
							$query = $smssend->insertLog($result,$studetails['stu_fname'],trim($message_text),trim($mobile),$send_to,'P');
						}
						else
						{*/
							$result = $smssend->sendSMS($mobile,trim($message_text));
							$query = $smssend->insertLog($result,$studetails['stu_fname'],trim($message_text),$mobile,$send_to,'P');
					//	}
					}
					if($_SESSION['sms_p_count'] < 0)
						$count = 0;
					else
						$count = $_SESSION['sms_p_count'];
				}
				else if(@$type=="T")
				{
					$check = "SELECT e.*, g.* FROM notification_module_master e, global_sms_templates g  WHERE e.module_id=g.module_id and  template_id=".$template_id;
					$run = mysql_query($check, $scslink) or die('Unable to connect to server. We are sorry for the inconvenience caused.');
					$fetch = mysql_fetch_assoc($run);
					if($fetch['module_name'] == 'Online Application' || $fetch['module_name'] == 'Change Password')
					{
						if($fetch['module_name'] == 'Online Application')
						{
							$studetails=getDetailsById("admission_application","adm_form_no",$stuid);
							$temparray = array(SCHOOL_NAME,$studetails['stu_fname'], $stuid);
						}
						else
						{
							$studetails=getDetailsById("mst_students","stu_id",$stuid);
							$temparray = array(SCHOOL_NAME,$studetails['stu_fname']);
						}
						
							$mobile = $studetails['mob'];
						
					}
					else
					{
						if($fetch['module_name'] == 'Application Rejected')
						{
							$studetails=getDetailsById("admission_application","adm_form_no",$stuid);
							
								$mobile = $studetails['mob'];
					
						}
						$temparray[$index] = SCHOOL_NAME;
						$index++;
						$temparray[$index] = $studetails['stu_fname'];
						$index++;
						$length = count($hashvalues);
						while($index <= $length+1)
						{
							$temparray[$index] = $hashvalues[$temp];
							$temp++;
							$index++;
						}
						$index = 0;
						$temp = 0;
					}
					if(($sent > $_SESSION['sms_t_count']) || ($_SESSION['sms_t_count'] == 0))
					{
						$Errs= "<div class='success'>".$sent." Message(s) sent as you have exceeded your SMS sending limit. Please contact system administrator for more details.</div>";
						break;
					}
					else
					{
						$message = $smssend->getSmsMessage($message_text,$temparray);
						/*if(@$schedulecheck=='on')
						{
							$datetime = str_replace(':','-',$schedule_date.'-'.$schedule_time);
							$result = $smssend->scheduleTransactionalSMS($template_id,trim($mobile),trim($message),$datetime,$sms_sender_id);
							$query = $smssend->insertLog($result,$studetails['stu_fname'],trim($message),trim($mobile),$send_to,'T');
						}
						else
						{*/
							$result = $smssend->sendTransactionalSMS($template_id,trim($mobile),trim($message),trim($sms_sender_id));
							$query = $smssend->insertLog($result,$studetails['stu_fname'],trim($message),trim($mobile),$send_to,'T');
						//}
					}
					if($_SESSION['sms_t_count'] < 0)
						$count = 0;
					else
						$count = $_SESSION['sms_t_count'];
				}
				if($query=="Y")
				{
					$sent++;
				/*	if(@$schedulecheck=='on')
						$Errs= "<div class='success'>".$sent." Message(s) Scheduled. Please Check SMS Transaction Log For More Details.</div>";
					else*/
						$Errs= "<div class='success'>".$sent." Message(s) Sent. Please Check SMS Transaction Log For More Details.</div>";
				}
				else
				{
					$Errs= "<div class='error'>Message(s) Sending Failed. Please Check SMS Transaction Log For More Details.</div>";
				}
			}
			$statusresult['count'] = $count;
			$statusresult['msg'] = $Errs;
			echo json_encode($statusresult);
		}
	}
	else if(@$sendtype=="E")
	{
		$empids = explode ( ",", $values );
		if (! empty ( $empids ))
		{
			foreach ( $empids as $empid )
			{
				$empdetails=getDetailsById("employee","empid",$empid);
				$flag = 'EMPLOYEE';
				if(@$type=="P")
				{
					if(($sent > $_SESSION['sms_p_count']) || ($_SESSION['sms_p_count'] == 0))
					{
						$Errs= "<div class='success'>".$sent." Message(s) sent as you have exceeded your SMS sending limit. Please contact system administrator for more details.</div>";
						break;
					}
					else
					{
						/*if(@$schedulecheck=='on')
						{
							$datetime = str_replace(':','-',$schedule_date.'-'.$schedule_time);
							$result = $smssend->schedulePromotionalSMS($empdetails['emp_mob'],trim($message_text),$datetime);
							$query = $smssend->insertLog($result,$empdetails['emp_name'],trim($message_text),$empdetails['emp_mob'],$flag,'P');
						}
						else
						{*/
							$result = $smssend->sendSMS($empdetails['emp_mob'],trim($message_text));
							$query = $smssend->insertLog($result,$empdetails['emp_name'],trim($message_text),$empdetails['emp_mob'],$flag,'P');
						//}
					}
					if($_SESSION['sms_p_count'] < 0)
						$count = 0;
					else
						$count = $_SESSION['sms_p_count'];
				}
				else if(@$type=="T")
				{
					$temparray[$index] = SCHOOL_NAME;
					$index++;
					$temparray[$index] = $empdetails['emp_name'];
					$index++;
					$length = count($hashvalues);
					while($index <= $length+1)
					{
						$temparray[$index] = $hashvalues[$temp];
						$temp++;
						$index++;
					}
					$index = 0;
					$temp = 0;
					if(($sent > $_SESSION['sms_t_count']) || ($_SESSION['sms_t_count'] == 0))
					{
						$Errs= "<div class='success'>".$sent." Message(s) sent as you have exceeded your SMS sending limit. Please contact system administrator for more details.</div>";
						break;
					}
					else
					{
						$message = $smssend->getSmsMessage($message_text,$temparray);
						/*if(@$schedulecheck=='on')
						{
							$datetime = str_replace(':','-',$schedule_date.'-'.$schedule_time);
							$result = $smssend->scheduleTransactionalSMS($template_id,$empdetails['emp_mob'],trim($message),$datetime,trim($sms_sender_id));
							$query = $smssend->insertLog($result,$empdetails['emp_name'],trim($message),$empdetails['emp_mob'],$flag,'T');
						}
						else
						{*/
							$result = $smssend->sendTransactionalSMS($template_id,$empdetails['emp_mob'],trim($message),trim($sms_sender_id));
							$query = $smssend->insertLog($result,$empdetails['emp_name'],trim($message),$empdetails['emp_mob'],$flag,'T');
						//}
					}
					if($_SESSION['sms_t_count'] < 0)
						$count = 0;
					else
						$count = $_SESSION['sms_t_count'];
				}
				if($query=="Y")
				{
					$sent++;
					if(@$schedulecheck=='on')
						$Errs= "<div class='success'>".$sent." Message(s) Scheduled. Please Check SMS Transaction Log For More Details.</div>";
					else
						$Errs= "<div class='success'>".$sent." Message(s) Sent. Please Check SMS Transaction Log For More Details.</div>";
				}
				else
				{
					$Errs= "<div class='error'>Message(s) Sending Failed. Please Check SMS Transaction Log For More Details.</div>";
				}
			}
			$statusresult['count'] = $count;
			$statusresult['msg'] = $Errs;
			echo json_encode($statusresult);
		}
	}
}
?>
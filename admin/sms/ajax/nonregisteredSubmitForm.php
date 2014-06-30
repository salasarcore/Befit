<?php 
@session_start();
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
include_once '../SmsSystem.class.php';

$smssend = new SMS(); //create object instance of SMS class
$Errs="";
$senddatabase="";
$uploaddatabase="";
$act = "";
$schedulecheck="";
$statusresult = array();
$mobilearray = array();
$databasearray = array();
makeSafe(extract($_REQUEST));
$sent = 0;
$count = 0;
if(trim($message_text)=="")
	echo $Errs=  "<div class='error'>Please enter message text.</div>";
if($mobilenos != "" || $mobilenos != null)
{
	$text = trim($mobilenos);
	$textArray = explode("\n", $text);
	$textArray = array_filter($textArray, 'trim'); // remove any extra \r characters left behind
	foreach($textArray as $mob)
	{
		if(in_array(trim($mob),$mobilearray)) //dont send sms to duplicate nos
			continue;
		else //continue to sending sms and insert the mobile number in the array
		{
			if(($sent > $_SESSION['sms_p_count']) || ($_SESSION['sms_p_count'] == 0))
			{
				$Errs= "<div class='success'>".$sent." Message(s) sent as you have exceeded your SMS sending limit. Please contact system administrator for more details.</div>";
				break;
			}
			else
			{
				if(@$schedulecheck=='on')
				{
					$datetime = str_replace(':','-',$schedule_date.'-'.$schedule_time);
					$result = $smssend->schedulePromotionalSMS(trim($mob),trim($message_text),$datetime);
				}
				else
				{
					$result = $smssend->sendSMS($mob,trim($message_text));
				}
				$send_to = "OTHERS";
				$query = $smssend->insertLog($result,'',trim($message_text),$mob,$send_to,'P');
				if($query=="Y")
				{
					$sent++;
					if(@$schedulecheck=='on')
						$Errs= "<div class='success'>".$sent." Message(s) Scheduled. Please Check SMS Transaction Log For More Details.</div>";
					else
						$Errs = "<div class='success'>".$sent." Message(s) Sent. Please Check SMS Transaction Log For More Details.</div>";
				}
				array_push($mobilearray,trim($mob));
				$count = $_SESSION['sms_p_count'];
			}
		}
	}
}
if($senddatabase == 'on')
{
	$query = "select * from sms_non_registered_users";
	$res = mysql_query($query,$link);
	while($row = mysql_fetch_assoc($res))
	{
		if(in_array(trim($row['mobile_num']),$mobilearray))
			continue;
		else
		{
			if(($sent > $_SESSION['sms_p_count']) || ($_SESSION['sms_p_count'] == 0))
			{
				$Errs= "<div class='success'>".$sent." Message(s) sent as you have exceeded your SMS sending limit. Please contact system administrator for more details.</div>";
				break;
			}
			else
			{
				if(@$schedulecheck=='on')
				{
					$datetime = str_replace(':','-',$schedule_date.'-'.$schedule_time);
					$result = $smssend->schedulePromotionalSMS($row['mobile_num'],trim($message_text),$datetime);
				}
				else
				{
					$result = $smssend->sendSMS($row['mobile_num'],trim($message_text));
				}
				$send_to = "OTHERS";
				$query = $smssend->insertLog($result,$row['name'],trim($message_text),$row['mobile_num'],$send_to,'P');
				if($query=="Y")
				{
					$sent++;
					if(@$schedulecheck=='on')
						$Errs= "<div class='success'>".$sent." Message(s) Scheduled. Please Check SMS Transaction Log For More Details.</div>";
					else
						$Errs = "<div class='success'>".$sent." Message(s) Sent. Please Check SMS Transaction Log For More Details.</div>";
				}
				array_push($databasearray,$row['mobile_num']);
				$count = $_SESSION['sms_p_count'];
			}
		}
	}
}
if($uploaddatabase == 'on')
{
	for($i = 0; $i < count($name); $i++)
	{
		if(in_array(trim($mobile[$i]),$mobilearray))
			continue;
		else if(in_array(trim($mobile[$i]),$databasearray))
			continue;
		else
		{
			if(($sent > $_SESSION['sms_p_count']) || ($_SESSION['sms_p_count'] == 0))
			{
				$Errs= "<div class='success'>".$sent." Message(s) sent as you have exceeded your SMS sending limit. Please contact system administrator for more details.</div>";
				break;
			}
			else
			{
				if(@$schedulecheck=='on')
				{
					$datetime = str_replace(':','-',$schedule_date.'-'.$schedule_time);
					$result = $smssend->schedulePromotionalSMS($mobile[$i],trim($message_text),$datetime);
				}
				else
				{
					$result = $smssend->sendSMS($mobile[$i],trim($message_text));
				}
				$send_to = "OTHERS";
				$query = $smssend->insertLog($result,$name[$i],trim($message_text),$mobile[$i],$send_to,'P');
				if($query=="Y")
				{
					$sent++;
					if(@$schedulecheck=='on')
						$Errs= "<div class='success'>".$sent." Message(s) Scheduled. Please Check SMS Transaction Log For More Details.</div>";
					else
						$Errs = "<div class='success'>".$sent." Message(s) Sent. Please Check SMS Transaction Log For More Details.</div>";
				}
				$count = $_SESSION['sms_p_count'];
			}
		}
	}
}
$statusresult['count'] = $count;
$statusresult['msg'] = $Errs;
echo json_encode($statusresult);
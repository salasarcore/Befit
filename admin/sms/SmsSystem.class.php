<?php 

class SMS
{
	/**
	 * Variables have been defined protected to make them visible in all classes that extend SMS class including the parent class
	 */
	protected $uid,
	$pin,
	$sender,
	$domain,
	$route,
	$return_val;

	/**
	 * Constructor containing global SMS sending settings. These settings include the uid, pin, senderid, domain and route
	 */
	function SMS()
	{
		$this->return_val="";
		$this->uid="73616c61736172"; //your uid 73616c61736172
		$this->pin="5320164e378eb"; //your api pin 5320164e378eb
		$this->sender="SCSAUC"; // approved sender id SCSAUC
		$this->domain="smsalertbox.com"; // connecting url
		$this->promotional_route="4";// 0-Normal,1-Priority,4-Promotional,5-Transactional
		$this->transactional_route="5";
	}

	/**
	 * sendSMS function will contain code to send an SMS using settings defined in the SMS constructor
	 * @param $mobile contains the mobile number to which the message is to be sent
	 * @param $message contains the message text
	 * @return function will return a value which will be either a transaction_id if sending of sms is successfull or it will return a message displaying the error
	 */
	function sendSMS($mobile,$message)
	{
		$uid=urlencode($this->uid);
		$pin=urlencode($this->pin);
		$sender=urlencode($this->sender);
		$route=$this->promotional_route;
		$message=urlencode($message);
		$mobile=urlencode($mobile);
		$domain=$this->domain;
		$parameters="uid=$uid&pin=$pin&sender=$sender&rtype=json&route=$route&mobile=".$mobile."&message=$message";
		$url="http://$domain/api/sms.php";
		$get_url=$url."?".$parameters;
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_POST,0);
		curl_setopt($ch, CURLOPT_URL, $get_url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  // RETURN THE CONTENTS OF THE CALL
		$return_val = curl_exec($ch);
		return $return_val;
	}

	/**
	 * sendTransactionalSMS function will be used to send messages via the transactional route. Though most of the variables may seem similar, this function has the following additions:
	 * Template ID: A predefined template will be passed. These id's are obtained from TRAI once the message template is approved.
	 * @param $tempid contains the template ID of the message template selected
	 * @param $mobile contains the mobile number to which the message is to be sent
	 * @param $message contains the message text. This message will include the dynamically replaced values.
	 * @return function will return a value which will either a transaction_id if sms is sent successfully or it will return a error message
	 * Note: The transaction_id which is returned on successful sending of the message will be different from the template id of the selected message.
	 */

	function sendTransactionalSMS($tempid,$mobile,$message)
	{
	
		$uid=urlencode($this->uid);
		$pin=urlencode($this->pin);
		$route=$this->transactional_route;
		$tempid=urlencode($tempid);
		$message=urlencode($message);
		$mobile=urlencode($mobile);
		$domain=$this->domain;
		$parameters="uid=$uid&pin=$pin&rtype=json&route=$route&tempid=$tempid&mobile=$mobile&message=$message";
		$url="http://$domain/api/sms.php";
		$get_url=$url."?".$parameters;
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_POST,0);
		curl_setopt($ch, CURLOPT_URL, $get_url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  // RETURN THE CONTENTS OF THE CALL
		$return_val = curl_exec($ch);
		return $return_val;
	}

	/**
	 * schedulePromotionalSMS function will schedule a message to be sent via promotional route.
	 * @param $tempid contains the template ID of the message template selected
	 * @param $mobile contains the mobile number to which the message is to be sent
	 * @param $message contains the message text. This message will include the dynamically replaced values.
	 * @param $time contains the time at which the message is scheduled to be sent. The time will be in the format 'dd-mm-yyyy-24h- min'
	 * @return function will return a value which will either a transaction_id if sms is sent successfully or it will return a error message
	 */

	function schedulePromotionalSMS($mobile,$message,$time)
	{
		//http://yourdomain.com/api/sms.php?uid=your_uid&pin=your_pin&sender=SENDER&route=0&mobile=MOBILE1&message=MESSAGE&time=dd-mm-yyyy-24h- min
		$uid=urlencode($this->uid);
		$pin=urlencode($this->pin);
		$sender=urlencode($this->sender);
		$route=$this->promotional_route;
		$message=urlencode($message);
		$mobile=urlencode($mobile);
		$domain=$this->domain;
		$parameters="uid=$uid&pin=$pin&sender=$sender&rtype=json&route=$route&mobile=$mobile&message=$message&time=$time";
		$url="http://$domain/api/sms.php";
		$get_url=$url."?".$parameters;
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_POST,0);
		curl_setopt($ch, CURLOPT_URL, $get_url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  // RETURN THE CONTENTS OF THE CALL
		$return_val = curl_exec($ch);
		return $return_val;
	}

	/**
	 * scheduleTransactionalSMS function will schedule a message to be sent via transactional route.
	 * @param $tempid contains the template ID of the message template selected
	 * @param $mobile contains the mobile number to which the message is to be sent
	 * @param $message contains the message text. This message will include the dynamically replaced values.
	 * @param $time contains the time at which the message is scheduled to be sent. The time will be in the format 'dd-mm-yyyy-24h- min'
	 * @return function will return a value which will either a transaction_id if sms is sent successfully or it will return a error message
	 */

	function scheduleTransactionalSMS($tempid,$mobile,$message,$time)
	{
		
		$uid=urlencode($this->uid);
		$pin=urlencode($this->pin);
		$route=$this->transactional_route;
		$tempid=urlencode($tempid);
		$message=urlencode($message);
		$mobile=urlencode($mobile);
		$domain=$this->domain;
		$parameters="uid=$uid&pin=$pin&sender=$sender&rtype=json&route=$route&tempid=$tempid&mobile=$mobile&message=$message&time=$time";
		$url="http://$domain/api/sms.php";
		$get_url=$url."?".$parameters;
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_POST,0);
		curl_setopt($ch, CURLOPT_URL, $get_url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  // RETURN THE CONTENTS OF THE CALL
		$return_val = curl_exec($ch);
		return $return_val;
	}

	/**
	 * getDeliveryStatus function will get the message delivery status of a sent message.
	 * @param $msgid contains the message ID of the sent message. The delivery status can tracked through this ID only.
	 * @return function will return a sample delivery status response like Sent, Delivered, DND etc.
	 */

	function getDeliveryStatus($msgid)
	{
		$uid=urlencode($this->uid);
		$pin=urlencode($this->pin);
		$messageID = urlencode($msgid);
		$domain=$this->domain;
		$parameters="uid=$uid&pin=$pin&msgid=$messageID";
		$url="http://$domain/api/dlr.php";
		$get_url=$url."?".$parameters;
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_POST,0);
		curl_setopt($ch, CURLOPT_URL, $get_url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  // RETURN THE CONTENTS OF THE CALL
		$return_val = curl_exec($ch);
		return $return_val;
	}

	/**
	 * insertLog function will insert the sms transaction data into the sms transaction log.
	 * @param $value contain either the transaction ID of the transaction or error message
	 * @param $name will contain name of the employee/student
	 * @param $message will contain the message text
	 * @param $mob will contain the employees/student mobile number
	 * @param $flag will be either employee, student or parent
	 * @return true or false depending on the successful execution of the query
	 */
	function insertLog($value,$name,$message,$mob,$flag,$type)
	{
		if(is_numeric($value))
		{
			$status = 'Y';
			$query = mysql_query("INSERT INTO sms_transaction_log(sms_type, name, message, mobile_no, status, flag, transaction_id, log_text, date_sent) values('".$type."','".$name."','".$message."',".$mob.",'".$status."','$flag','$value','',NOW())");
			if($type=='P')
				$_SESSION['sms_p_count']--;
			else
				$_SESSION['sms_t_count']--;
			return $status;
		}
		else
		{
			$status = 'N';
			$query = mysql_query('INSERT INTO sms_transaction_log(sms_type, name, message, mobile_no, status, flag, transaction_id, log_text, date_sent) values("'.$type.'","'.$name.'","'.$message.'","'.$mob.'","'.$status.'","'.$flag.'","0","'.$value.'",NOW())');
			return $status;
		}
	}

	/**
	 * insertSCSLog function will insert the sms transaction data into the sms transaction log for SCS Admin.
	 * @param $value contain either the transaction ID of the transaction or error message
	 * @param $name will contain name of the employee/student
	 * @param $message will contain the message text
	 * @param $mob will contain the employees/student mobile number
	 * @param $flag will be either employee, student or parent
	 * @return true or false depending on the successful execution of the query
	 */
	function insertSCSLog($value,$name,$message,$mob,$flag,$type)
	{
		if(is_numeric($value))
		{
			$status = 'Y';
			$query = mysql_query("INSERT INTO scs_sms_transaction_log(sms_type, name, message, mobile_no, status, flag, transaction_id, log_text, date_sent)
					values('".$type."','".$name."','".$message."',".$mob.",'".$status."','$flag','$value','',NOW())");
			return $status;
		}
		else
		{
			$status = 'N';
			$query = mysql_query("INSERT INTO scs_sms_transaction_log(sms_type, name, message, mobile_no, status, flag, transaction_id, log_text, date_sent)
					values('".$type."','".$name."','".$message."',".$mob.",'".$status."','$flag','0','$value',NOW())");
			return $status;
		}
	}

	/**
	 * getSmsMessage function will be used wherever the auto sms sending setting has been defined. It will replace all the hash values in the message template.
	 * @param $message_text contains the message template defined in global SMS templates table.
	 * @param $hashvalues is an array containing the values which will be used to replace the hash values in the message template.
	 * @return will return the formatted message text with all hash values replaced by user defined values.
	 */
	function getSmsMessage($message_text,$hashvalues)
	{
		preg_match_all('/((#\w+\b#))/i', $message_text, $matches);
		for ($i = 0; $i < count($matches[1]); $i++)
		{
			$key = $matches[1][$i];
			$value = $matches[2][$i];
			$$key = $value;
			$message_text=str_replace($key,$hashvalues[$i],$message_text);
		}
		return $message_text;
	}
}
?>
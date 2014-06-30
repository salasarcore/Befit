
<?php

require_once('email_template.php');
require_once('PHPMailer-master/class.phpmailer.php');
include("PHPMailer-master/class.smtp.php");

/* 
 From http://www.html-form-guide.com
This is the simplest emailer one can have in PHP.
If this does not work, then the PHP email configuration is bad!
*/

// optional, gets called from within class.phpmailer.php if not already loaded

/**

 * a sendmail function is sending mail.
 * @param $mailparam is an array with following indexes
 * $mailparam['to'] - To
 * $mailparam['toname'] - To name
 * $mailparam['cc'] - Carbon Copy
 * $mailparam['bcc'] - bcc
 * $mailparam['replyto'] - reply to
 * $mailparam['replytoname'] - reply to name
 * $mailparam['subject'] - subject
 * $mailparam['message'] - message
 * $mailparam['fromemail'] - from email
 * $mailparam['fromname'] - from name
 * $mailparam['attachment'] - attachment
 *
 * @return true if mail sent successfully or will return false.
*/

function sendmail($mailparam)
{
	if($mailparam['fromemail']=="")
	{
		$mailparam['fromemail'] = "no-reply@salasaredu.com";
		$mailparam['fromname'] = "No Reply Salasar Edu";
	}
	if($mailparam['toname'] =="")
	{
		$mailparam['toname'] = $mailparam['to'];
	}

	
	$mail  = new PHPMailer();
    if ($mailparam['cc']!="")
    {
    	$mail->AddCC($mailparam['cc']);
    }
	$mail->SetFrom($mailparam['fromemail'], $mailparam['fromname']);
	$mail->Subject    = $mailparam['subject'];

	$mail->MsgHTML($mailparam['message']);
	$mail->AddAddress($mailparam['to'],$mailparam['toname']);

	if($mailparam['replyto']!="")
	{
		if($mailparam['replytoname']=="")
		{
			$mailparam['replytoname'] = $mailparam['replyto'];
		}
		$mail->AddReplyTo($mailparam['replyto'], $mailparam['replytoname']);
	}

	if ($mailparam['attachment'] != "")
	{
		$mail->AddAttachment($mailparam['attachment']);      // attachment
	}

	// Do not change
	$mail->IsSMTP(); 						// telling the class to use SMTP
	$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
	// 1 = errors and messages
	// 2 = messages only
	$mail->SMTPAuth   = true;                  // enable SMTP authentication

	/**
	 * Starttls should be enabled first and port no should be 587 for gmail conf and also change php.ini file
	 */
	$mail->SMTPSecure = "tls";
	$mail->Port = 587;

	//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
	$mail->Host       = "email-smtp.us-east-1.amazonaws.com"; // sets the SMTP server
	$mail->Port       = 587;                    // set the SMTP port for the GMAIL server
	$mail->Username   = "AKIAIQRBZBJDHUVMCXNA"; // SMTP account username
	$mail->Password   = "AvVufZngvPMakhUjhVeublOc4grXY9MBRj5HoZ6u0lra";        // SMTP account password
	$error = $mail->ErrorInfo;


	if(!$mail->Send()) {

		sendEmailErrors($mailparam,$error);
		return false;
	}
	else {

		return true;
	}
}

/** 

 * a sendEmailErrors function will save the unsent mails
 * @param $mailparam is an array with following indexes
 * $mailparam['to'] - To
 * $mailparam['toname'] - To name
 * $mailparam['cc'] - Carbon Copy
 * $mailparam['bcc'] - bcc
 * $mailparam['replyto'] - reply to
 * $mailparam['replytoname'] - reply to name
 * $mailparam['subject'] - subject
 * $mailparam['message'] - message
 * $mailparam['fromemail'] - from email
 * $mailparam['fromname'] - from name
 * $mailparam['attachment'] - attachment
 *
 * @return void
 */

function sendEmailErrors($mailparam,$errormsg)
{

	$textFile = "temp/email_errors.txt";
	
	$stringData="\r\n\r\n ----------------------------------";
	$stringData .= "\r\n\r\n      Email ID: ".$mailparam['to']." \r\n ".$mailparam['message']."\r\n\r\n";

	foreach ($mailparam as $key => $value) {
		$stringData .= "Key: $key; Value: $value\r\n";
	}
	$stringData .="Mailer Error: " . $errormsg;
	$file = fopen($textFile, 'a') or die("can't open file");

	fwrite($file,$stringData);
	fclose($file);

}

/**
 * getEmailMessage function will be used wherever the auto email sending setting has been defined. It will replace all the hash values in the message template.
 * @param $message_text contains the message template defined in global email templates table.
 * @param $hashvalues is an array containing the values which will be used to replace the hash values in the message template.
 * @return will return the formatted message text with all hash values replaced by user defined values.
 */
function getEmailMessage($message_text,$hashvalues)
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


/**
* a Sending_EMail function is for sending mail.
* @param to is email to whom the mail is to be sent
* @param subject is subject line of mail
* @return Mail sent if mail sent successfully or will return Mail failed.
* @param temp_value is template value
* @param path is a define image path
*/
function Sending_EMail($temp_value,$to,$subject,$path)
{
	
	$mailmsg=createTemplate($temp_value,$path);
	$mailparam=array("to"=>$to,"toname"=>"","cc"=>"", "bcc"=>"", "replyto" => "", "replytoname" => "", "subject" =>$subject, "message"=>$mailmsg, "fromemail"=>"", "fromname"=>"", "attachment"=>"");

	if(sendmail($mailparam))
	{
		$statusresult[0] = true;
		return $statusresult;
	}
	else
	{
		$statusresult[0] = false;
		$statusresult['msg'] = "Unable to send mail";
		$statusresult['errorcode']=-1;
		return $statusresult;
	}
}

/**
* a Sending_MultyEMail function is for sending mail includeing into cc.
* @param to is email to whom the mail is to be sent
* @param subject is subject line of mail
* @return Mail sent if mail sent successfully or will return Mail failed.
* @param temp_value is template value
* @param path is a define image path
*/
function Sending_MultyEMail($temp_value,$to,$cc,$subject,$path)
{
   	$mailmsg=createTemplate($temp_value,$path);
	$mailparam=array("to"=>$to,"toname"=>"", "cc"=>$cc,  "bcc"=>"", "replyto" => "", "replytoname" => "", "subject" =>$subject, "message"=>$mailmsg, "fromemail"=>"", "fromname"=>"", "attachment"=>"");
	
	if(sendmail($mailparam))
	{
		$statusresult[0] = true;
		return $statusresult;
	}
	else
	{
		$statusresult[0] = false;
		$statusresult['msg'] = "Unable to send mail";
		$statusresult['errorcode']=-1;
		return $statusresult;
	}
}
?>
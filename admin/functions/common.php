<?php

//CONSTANTS

define("MESSAGE_UPLOAD_DIR", "site_img/message_attachments/");
define("DEFAULT_IMAGE", "My_School_no.jpg");
define("REGEX_EMAIL","/^(([\w-\.]{1,20})+@(([\w-]{3,20})+\.(com|net|org|info|coop|int|com\.au|co\.uk|org\.uk|ac\.uk|co\.in|[a-z]{2,4})))?$/");
define("MAX_GRADE_LIMIT", "5");

/**
 * THIS CONSTANT IS DEFINE FOR CUSTOMIZED APPLY ONLINE FORM FEATURE RELEASE DATE
 */

define("CUSTOMIZED_APPLY_ONLINE_FORM_RELEASE_DATE", "2014-06-11");

/**
 * THESE CONSTANTS ARE USED FOR CUSTOMIZED APPLY ONLINE SETTINGS
 */

define("NUMERIC", "/^[0-9]+$/");
define("ALPHABATES", "/^[a-zA-Z ]+$/");
define("ALPHANUM","/^[a-zA-Z0-9 ]+$/");



/**
 * THIS CONSTANT IS DEFINE FOR NOTIFICATION(EMAIL/SMS) MODULE 
 */
define("ONLINE_APPLICATION","1");
define("APPLCATION_ACCEPTED","2");
define("APPLCATION_REJECTED","3");
define("EMPLOYEE_REG","4");
define("CHANGE_PASSWORD","5");
function redirect($url,$msg)
{
	echo "<script>location.href='$url?msg=$msg'</script>";
}


function module($filename)
{
	$query ="select filename,module_display from modules where filename ='".$filename ."'";
	$result =mysql_query($query);
	$row = mysql_fetch_assoc($result);
		if($row['module_display'] ==1)
		{	
			return true; 
		}
		elseif($row['module_display'] ==0)
		{
			return false;
			
		}	
}



function my_money_format($value) {
  if (function_exists('money_format')) { 
        return money_format('%!i', $value); 
    } 
else
   return number_format($value,2,'.',',');
}



function get_code($seed_length=8) {
    $seed = "ABCDEFGHJKLMNPQRSTUVWXYZ234567892345678923456789";
    $str = '';
    srand((double)microtime()*1000000);
    for ($i=0;$i<$seed_length;$i++) {
        $str .= substr ($seed, rand() % 48, 1);
    }
    return $str;
}

function file_size($file_name)   {
   
	if(is_file($file_name))
		return filesize($file_name);
	  else
	    return 0;
   }
   
function makeSafe($string){
	$string=trim($string);
		$string=str_replace("'","",$string);
		$string=str_replace("\\","",$string);
		$string=(get_magic_quotes_gpc() ? stripslashes($string) : $string);
		
			
			
			return str_replace("'","",$string);
	
	}


function create_session(){
global $link;
$cid="0";
$full_name="";
$day = date('d', time());
$month = date('m', time());
$year = date('Y', time());
$hour = date('H', time());
$min = date('i', time());
$sec = date('s', time());
$remote_addr=@$_SERVER['REMOTE_ADDR'];
$sid=$year.$month.$day.$hour.$min.$sec.rand(99,9999);
$_SESSION['sid']=$sid;
;
}

function last_click(){
	@session_start();
	global $link;
	$cid="0";
	$full_name="";
	$day = date('d', time());
	$month = date('m', time());
	$year = date('Y', time());
	$hour = date('H', time());
	$min = date('i', time());
	$sec = date('s', time());
	@$_SESSION['lastUrlClicked']=str_replace("'","",$_SESSION['lastUrlClicked']);
	$sql="update whos_online set time_last_click='".$hour.$min.$sec."',last_page_url='".@$_SESSION['lastUrlClicked']."' where session_id='".$_SESSION['sid']."'";
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
	//echo $sql;
}
//=======================
function site_hits(){
	global $link;
	$sql="update site_counters set hits=hits+1";
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());

}


function replaceChar($string){
		$string=(get_magic_quotes_gpc() ? stripslashes($string) : $string);
		$string=str_replace("'","",$string);
		if(function_exists('mysql_real_escape_string')){
			return mysql_real_escape_string($string);
		}else{
			return mysql_escape_string($string);
		}
	}
	
	function getClientIP() {
   $ip = ""; 
   if (getenv("HTTP_CLIENT_IP"))            $ip = getenv("HTTP_CLIENT_IP"); 
   else if(getenv("HTTP_X_FORWARDED_FOR"))  $ip = getenv("HTTP_X_FORWARDED_FOR"); 
   else if(getenv("REMOTE_ADDR"))           $ip = getenv("REMOTE_ADDR"); 
   else                                     $ip = "UNKNOWN";
   
   return $ip; 
 }

 
 function encode($string) {
 return base64_encode($string);
 }
 
 
 function decode($string) {
 return base64_decode($string);
 }
 
 function getNextMaxId($table_name,$col_name)
 {
      $query="Select max($col_name) from $table_name";
      $result=mysql_query($query);
      $row=mysql_fetch_array($result);
      return $row[0];
 }
 
if(!isset($_SESSION['sid'])) create_session();

/**
 * this fuction is used for getting browser's information
 */
function getBrowser()
{
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$bname = 'Unknown';
	$platform = 'Unknown';
	$version= "";

	//First get the platform?
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'linux';
	}
	elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'mac';
	}
	elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'windows';
	}

	// Next get the name of the useragent yes seperately and for good reason
	if(preg_match('/MSIE/i',$u_agent) )
	{
		$bname = 'Internet Explorer';
		$ub = "MSIE";
	}
	
	
	 else if (preg_match('/Trident\/[0-9\.]+/', $u_agent) && preg_match('/rv/i', $u_agent)) 
	 {
		$bname = 'Internet Explorer';
		$ub = "MSIE";
		
	 }	
	
	elseif(preg_match('/Firefox/i',$u_agent))
	{
		$bname = 'Mozilla Firefox';
		$ub = "Firefox";
	}
	elseif(preg_match('/Chrome/i',$u_agent))
	{
		$bname = 'Google Chrome';
		$ub = "Chrome";
	}
	elseif(preg_match('/Safari/i',$u_agent))
	{
		$bname = 'Apple Safari';
		$ub = "Safari";
	}
	elseif(preg_match('/Opera/i',$u_agent))
	{
		$bname = 'Opera';
		$ub = "Opera";
	}
	elseif(preg_match('/Netscape/i',$u_agent))
	{
		$bname = 'Netscape';
		$ub = "Netscape";
	}


	return array(
			'name'      => $bname,
	);
}
 
?>
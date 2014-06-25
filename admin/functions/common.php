<?php

//CONSTANTS

define("MESSAGE_UPLOAD_DIR", "site_img/message_attachments/");
define("DEFAULT_IMAGE", "My_School_no.jpg");
define("REGEX_EMAIL","/^(([\w-\.]{1,20})+@(([\w-]{3,20})+\.(com|net|org|info|coop|int|com\.au|co\.uk|org\.uk|ac\.uk|co\.in|[a-z]{2,4})))?$/");



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


?>
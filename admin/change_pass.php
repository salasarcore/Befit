<?php

@session_start();

include_once("functions/common.php");
include('check_session.php');
include('../email_settings.php');
//require 'sms/SmsSystem.class.php';

//$smssend = new SMS(); //create object instance of SMS class

$msg="";
$oldpass="";
$newpass="";
$repass="";
$nickname="";
$regno="";

if(isset($_POST['submit']))
{
	$oldpass=makeSafe($_POST['oldpass']);
	$newpass=makeSafe($_POST['newpass']);
	$repass=makeSafe($_POST['repass']);
	$id = $_SESSION['empid'];
		$sql="";
	if($oldpass=="")
	{
		$msg="<div class='error'>Enter Current Password</div>";
	}
	elseif($newpass=="")
	{
		$msg="<div class='error'>Enter New Password</div>";
	}
	elseif($repass=="")
	{
		$msg="<div class='error'>Enter Re-type Password</div>";
	}
	elseif($oldpass==$newpass)
	{
		$msg="<div class='error'>Current And New Password Should Not Be Same</div>";
	}
	elseif($repass!=$newpass)
	{
		$msg="<div class='error'>New and Re-type Password Should Be Same</div>";
	}
	else 
		
	{
	 $query="SELECT * FROM employee WHERE empid='".$id."'";  
		$resultset=mysql_query($query) or die(mysql_error());
		$rows=mysql_fetch_array($resultset);
			
			if($oldpass==$rows['password'])
				{
					$sql="UPDATE employee SET password ='".$newpass."' WHERE empid='".$id."'"; 
					
				}
				else 
				{
					$msg="<div class='error'>Invalid Current Password.</div>";
				}
			}
			if($sql!="")
			{
				//$msg="<div class='error'>Process Failed. Please Try Again Later</div>";
				
			$result  = mysql_query($sql)or die(mysql_error());
			if($result)
			{
			
				// email query 
				$query_local = "select * from notification_setting where module_id = ".CHANGE_PASSWORD." and  notification_type='E' and sending_type='A' " ; // module_id = 14 for Change Password
				$res_local = mysql_query($query_local,$link);
				$num_local = mysql_num_rows($res_local);
				if($num_local>0) {
				
				$query_t = "SELECT e.*, g.* FROM notification_module_master e, global_email_template g  WHERE e.module_id=g.email_module_id and available_for_school='Y'  AND  e.module_name = 'Change Password'";
				$sql_t = mysql_query($query_t) or die('Error. Query failed.');
				$nums = mysql_num_rows($sql_t);
				$gettemp = mysql_fetch_assoc($sql_t);
				$template = $gettemp['email_temp_format'];
				if($nums>0) {
				$subject="Password Changed";
				$to=$rows['email'];
				$name=$rows['emp_name'];
				$path = "../logo.png";
				
				$hashvalue = array($name);
				$temp_value = getEmailMessage($template,$hashvalue);
				$sendEmail = Sending_EMail($temp_value,$to,$subject,$path);
				 }  } 
				$msg= "<div class='success'>Password Successfully Changed</div>";
				$oldpass="";
				$newpass="";
				$repass="";
				

			/*	$querysetting="select * from notification_setting where module_id=".CHANGE_PASSWORD." and notification_type='S' and sending_type='A'";
				$resquerysetting=mysql_query($querysetting,$link);
				$numrows1 = mysql_num_rows($resquerysetting);
				if($numrows1>0)
				{
					$query = "SELECT e.*, g.* FROM notification_module_master e, global_sms_templates g	WHERE e.module_id=g.module_id  AND  e.module_name='Change Password' and available_for_school='Y' ";
					$sql = mysql_query($query,$scslink) or die('Error. Query failed.');
					$numrows = mysql_num_rows($sql);
					$getresult = mysql_fetch_assoc($sql);
					if($_SESSION['sms_t_count'] > 0 && $numrows>0)
					{
						$hashvalues =array(SCHOOL_NAME,$rows['emp_name']);
						$send_to='EMPLOYEE';
						$message =$smssend->getSmsMessage($getresult['template_format'],$hashvalues);
						$sendMessage =$smssend->sendTransactionalSMS($getresult['template_id'],$rows['emp_mob'],$message,trim($sms_sender_id));
						$logInsert =$smssend->insertLog($sendMessage,$rows['emp_name'],trim($message),$rows['emp_mob'],$send_to,'T');
					}
				}*/
			}//result 
			else 
			{
				$msg="<div class='error'>Process Failed. Please Try Again Later</div>";
				
			}
			
	}
}
?>
</script>
<script type="text/javascript">
$(document).ready(function(){
	$('#regno').focus();
	
	<?php 
			/**
			 * On blur of newpass textbox it will check if the value length is greater than 6 and if not will display error msg.
			 */  
		?>
	 	  $('#newpass').blur(function(e) {
		      if($('#newpass').val().length<6 && $('#newpass').val()!="")
		      {
		    	  alert("Password Must Be Minimum 6 Characters Long");
		      		    	  $('#newpass').focus().select();
		    	 
		      }
		      
			  if(($('#newpass').val()!="" && $('#oldpass').val()!="") && ($('#newpass').val()==$('#oldpass').val())) 
			  {
				  alert("New Password Is Same as Old Password. Please Try Different Password.");
				  $('#oldpass').val('');
				  $('#newpass').val('');
			  	  $('#oldpass').focus().select();
			  }
		      
			      
		  });
		  
	 	 <?php 
	 			/**
	 			 * On blur of confpassword textbox it will check the new password n confirm password are equal or not.
	 			 * if not then will display error message.
	 			 */  
	 	 ?>
		  $('#repass').blur(function(e) {
			 
			  if($('#newpass').val()!=$('#repass').val() && $('#repass').val()!="" && $('#newpass').val()!="")
			  {
				  alert('New Password And Re-Type Password Should Be Same.');
			  				  $('#newpass').val('');
				  $('#repass').val('');
				  $('#newpass').focus().select();				
			  }
			  else if($('#newpass').val()=="" && $('#repass').val()!="")
		      {
				  alert('New Password Should Not Be Blank.');
		      		    	  $('#newpass').focus();
		    	 
		    	  $('#repass').val('');
		      }
			  
			  
		  });
});
</script>
<style>
.success{margin-left:0px;margin-top:0px; margin-bottom:10px;text-align:center;width:400px;border-radius:5px;height:20px;color:black;}
.error{margin-left:0px;margin-top:0px; margin-bottom:10px; text-align:center;width:400px;border-radius:5px;height:20px;color:red}	
</style>
<div class="page_head">
<div id="navigation"><a href="pages.php">Home</a><a> My Account</a>  <span style="color: #000000;">Change password</span></div>
<h2>Change Password</h2>
</div>
<table  STYLE="width:auto;margin-top:10px;" class="adminform1">
<form name="changepass" action="" method="post" autocomplete="off">
<tr><td colspan="2" style="text-align: center;"><?php echo @$msg;?>
</td></tr>
<tr>
<td style="padding: 5px 0 10px 15px;" align="right">Current :</td>
<td style="padding: 5px 0 10px 10px;"><input type="password" name="oldpass" id="oldpass" maxlength="45" size="25"  required="required"/></td>
</tr>
<tr>
<td style="padding: 0 0 10px 15px;" align="right">New :</td>
<td style="padding: 0 0 10px 10px;"><input type="password" name="newpass" id="newpass" maxlength="45" size="25"  required="required"/></td>
</tr>
<tr>
<td style="padding: 0 0 10px 15px;" align="right">Re-type :</td>
<td style="padding: 0 0 10px 10px;"><input type="password" name="repass" id="repass" maxlength="45" size="25" required="required"/>
</td>
</tr>
<tr>
<td align="center"></td>
<td><input type="submit" name="submit" value="SUBMIT" class="btn save" style="padding: 5px 15px 5px 15px; margin: 0 20px 0 10px;"/>
<input type="reset" value="RESET" class="btn reset" style="padding: 5px 15px 5px 15px;"/></td></tr>
</form></table>
				 

	
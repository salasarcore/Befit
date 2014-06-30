<?php 
@session_start();
include("../conn.php");
include('../check_session.php');
include("../functions/employee/dropdown.php");
include("../functions/common.php");
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../js/ajax.js"></script>
<script type="text/javascript" src="../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<?php include('../php/js_css_common.php');?>
<?php

$act=makeSafe(@$_GET['act']);
/*include("../modulemaster.php");
if($act=="add")
	$id=option_session_list_add;
elseif($act=="edit")
$id=option_session_list_edit;
elseif($act=="delete")
$id=option_session_list_delete;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Session</title>
	
	


</head>
<body leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0" style="text-align: left">

 <div id="middleWrap">
		<div class="head"><h2>SESSION</h2></div>
   
<?php 
$session_name="";
$sessionID=makeSafe(@$_GET['sessionID']);
$action=makeSafe(@$_POST['action']);
$msg="";

if(@$action=="SAVE" || @$action=="UPDATE")
{
	$session_name=makeSafe(@$_POST['txtSession']);
	$sdate=makeSafe(@$_POST['adt']);
	
if($action=="SAVE")
	{
		if($sdate=="")
		{
			$msg= "<div class='error'>Please Enter Date</div>";
		}
		else
			if($session_name=="")
			{
				$msg= "<div class='error'>Please Enter Session Name</div>";
			}
		else 
		{
			$sql=" select * from session where session_name='".$session_name."'";
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
		   		$msg= "<div class='error'>Duplicate Entry</div>";
		     else
		     {
			 $newsessionid=getNextMaxId("session","session_id")+1;
		     	$sql="insert into session(session_id,session_name,start_date,updated_by) values(".$newsessionid.",'".$session_name."','".$sdate."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
			     $res=mysql_query($sql,$link);
			     if(mysql_affected_rows($link)>0)
			    
			     	$msg= "<div class='success'>Record Saved Successfully</div>";
			     
			     else
			    
			     	$msg="<div class='error'>Record Not Saved Successfully</div>";
			     
		     }
		     
		}
		
		}
	else
	{
		if($sdate=="")
		{
			$msg="<div class='error'>Please Enter Date</div>";
		}
		else
			if($session_name=="")
			{
				$msg= "<div class='error'>Please Enter Session Name</div>";
			}
		else 
		{
			$sql=" select * from session where session_name='".$session_name."' and session_id!=".$sessionID;
			$res=mysql_query($sql,$link);
			if(mysql_affected_rows($link)>0)
				$msg= "<div class='error'>Duplicate Entry</div>";
			else 
			{
				$sql="update session set session_name='".$session_name."',start_date='".$sdate."',updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where session_id=".$sessionID;
		
				
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)==0)
					$msg="<div class='success'>No Data Changed</div>";
				if(mysql_affected_rows($link)>0)
					$msg="<div class='success'>Record Updated successfully</div>";
				if(mysql_affected_rows($link)<0)
					$msg="<div class='error'>Record Not Updated successfully</div>";
			}
		}
	}
	
	
	
 if($action=="SAVE")
 {
		$act=="add";
 }
	else
	$act=="edit";
}
if(@$action=="DELETE")
{
	$query   = "SELECT  stu_id FROM student_class  where session_id=".$sessionID;
	 $result  = mysql_query($query) or die('Error, query failed');
	if(mysql_affected_rows($link)>0)
	{
	$msg= "<div class='error'>Can not delete as student(s) already registered in this session</div>";
	
	}
	else {
		$query   = "delete  FROM session where session_id=".$sessionID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
				$msg='<div class=\'success\'>Session Deleted Successfully</div>';
	}
}

if(@$act=="edit" || @$act=="delete" )
{
        
		$query   = "SELECT  session_id, session_name, start_date, freeze, updated_by, date_updated FROM session  where session_id=".$sessionID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			{
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			 	
				$session_name=@$row['session_name'];
				$sdate=@$row['start_date'];
				$last_updated=@$row['date_updated'];
				$updated_by=@$row['updated_by'];
			}

}
?>
<SCRIPT>
function ClearField(frm){
	  frm.txtSession.value = "";
	  frm.adt.value = "";
	  
	}
</SCRIPT>


<span id="spErr"><?php echo @$msg;?></span>
<form method="post" name="frmManu"   id="frmManu" action="session.php?sessionID=<?php echo @$sessionID;?>&act=<?php echo $act; ?>" >
<table border="0" cellspacing="0" cellpadding="0" align="center" class="adminform">
<tr>
    <td align="right"><span class="redstar" >Session Name :</span></td>

    <td> <input type="text" name="txtSession" id="txtSession" size="25"  value ="<?php if($act=="add") echo ""; else echo @$session_name; ?>"/></td>
    </tr>
    <tr><td></td><td><p class="hint_text"> Ex:2008-2009, 2009-2010, </p> </td></tr>
    <td  align="right" class="redstar">Start Date :</td>
	
    <td>
	
	<input name="adt" type="text" class="date"	id="adt" value="<?php if($act=="add") echo ""; else echo @$sdate;?>" size="11"  <?php if(@$act=="delete") echo "disabled"; ?>/>	
	
   <!--     <script type="text/javascript">
				  $(function() {
						$( "#adt" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
						});
					});

				  </script>  -->  </td>
	<tr>
    <td align="right">last Updated : </td>
    <td><?php echo @$last_updated; ?></td>
    </tr><tr>
    <td align="right">Updated By : </td>
    <td><?php echo @$updated_by; ?></td>
    </tr>
  <tr>
    
  <tr>
   
    
        <td align="center" colspan=2>     
	  
        <input type="submit"  class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1" />
				
			<?php if(@$act!="delete"){?>			
			<input type="button" class="btn reset" value="RESET" name="reset" id="reset" onClick="ClearField(this.form)"/>			
			<?php }?>				
			<input type="button" class="btn close" value="CLOSE" onClick="parent.emailwindow.close();" />
          	
          	<input type='hidden' name='action' value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' />
	
           </td>
    </tr>
</table>
</form>

</div>
<script language=javascript>

document.getElementById("spErr").innerHTML= "<?php echo $msg; ?>";

document.ready(function(){
$(function() {
	$( "#adt" ).datepicker({
		numberOfMonths: [1,2],
		dateFormat: 'yy-mm-dd',
		maxDate: new Date()
	});
});
});

   </script>

    </body>
  </html>
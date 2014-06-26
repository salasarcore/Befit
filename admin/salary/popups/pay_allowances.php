<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include("../../functions/common.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title></title>
	<link href="../../css/classic.css" type="text/css" rel="stylesheet">
	<?php include('../../php/js_css_common.php');?>
	<script>
function chkMe()
{
	var txtPayName=document.frm.txtPayName.value;
	if(txtPayName.trim()=="")
	{
		alert("Please Enter Your Pay / Allowances Name ");
		document.frm.txtPayName.value="";
		document.frm.txtPayName.focus();
	    return false;
	 }
	var txtPayName=document.frm.txtPayName.value.length;
	 
	if(txtPayName < 2){
		alert(" Pay / Allowances Name Should Be Atleast 2 characters");
		document.frm.txtPayName.focus();
		return false;
	}
	 var  txtAbvr=document.frm.txtAbvr.value;	
	if(txtAbvr.trim()==""){
		alert("Please Enter Your ABBR. ");
		document.frm.txtAbvr.value="";
		document.frm.txtAbvr.focus();
		return false;
	}
	 var  txtAbvr=document.frm.txtAbvr.value.length;
	if(document.frm.txtAbvr.value!=0){
		 if(txtAbvr > 5){
		 	alert("Not Allow More Than 5 digits.");
		 	document.frm.txtAbvr.focus();
		 	return false;
		} } 
	
	
return true;	   
} 

function ClearField(frm){
	  frm.txtPayName.value = "";
	  frm.txtAbvr.value = "";
	  
	}
</script>
</head>
<body>
<?php

 makesafe(extract($_GET));  
 makesafe(extract($_POST));

$msg="";
$Errs="";

if(@$action=="SAVE" || @$action=="UPDATE")
{
	if($Errs==""){
		if($txtPayName=='')
			$Errs= "<div class='error'>Please Enter Your PAY & ALLOWANCES NAME</div>";
		elseif($txtAbvr=='')
		   $Errs= "<div class='error'>Please Enter ABBR.</div>";
	}

 if($action=="SAVE")
	{
		$query   = "SELECT pa_name from sal_pay_allowances where pa_name='".trim($txtPayName)."'";
		$result  = mysql_query($query) or die('Error1, query failed');
		if(mysql_affected_rows($link)>0)
			$Errs="<DIV class='error'>Duplicate Pay & Allownces Name</DIV>";
		else
		{
			if($Errs!='') { 
				
				$msg=$Errs;
			 }
			else 
			{	
				 $ap_id=getNextMaxId("sal_pay_allowances","ap_id")+1;
				 $sql="insert into sal_pay_allowances(ap_id,pa_name,pa_abvr,updated_by)";
			     $sql = $sql ." values('".$ap_id."','".$txtPayName."','".$txtAbvr."','".$_SESSION['emp_name']."')";
			     $res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			     if(mysql_affected_rows($link)>0) 
						$Errs= "<div class='success'>Record Saved successfully</div>";
						if($action=="SAVE")
							$act=="add";
							else
							$act=="edit";
			     }

			  }
	}//save
	
	if($action=="UPDATE")
	{		
	    $query   = "SELECT pa_name, ap_id from sal_pay_allowances where pa_name='".trim($txtPayName)."' and ap_id !=".$paID;
		$result  = mysql_query($query,$link) or die('Error, query failed');
	      if(mysql_affected_rows($link)>0)
				 $Errs="<DIV class='error'>Duplicate Pay & Allownces Name</DIV>";
		
		else
		{
			if($Errs!='') { 
				$msg=$Errs; }
			else 
			{  		  	
				$sql="update sal_pay_allowances set pa_name='$txtPayName',  pa_abvr='$txtAbvr',  updated_by='".$_SESSION['emp_name']."' where ap_id =".$paID;
			    
			     $res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			     if(mysql_affected_rows($link)>0) {
						$Errs= "<div class='success'>Record Updated successfully</div>";
						if($action=="SAVE")
							$act=="add";
							else
							$act=="edit"; }
				if(mysql_affected_rows($link)==0)
					$Errs= "<div class='success'>No Data Changed.</div>";
			
				if(mysql_affected_rows($link)<0)
					$Errs="<div class='error'>Record Not Updated successfully</div>";
					
		   }
	}//else
  }//update
}//save-update
		

if(@$action=="DELETE")
{
		
		$query   = "delete  FROM sal_pay_allowances where ap_id=".$paID;
		$result  = mysql_query($query) or die('Error, query failed');
		@$txtPayName="";
		@$txtAbvr="";
		if(mysql_affected_rows($link)>0)
		$Errs= "<div class='success'>Deleted Succesfully</div>";
		

			}

if(@$act=="edit" || @$act=="delete" )
{

		$query   = "SELECT  pa_abvr, pa_name,  date_updated,updated_by FROM sal_pay_allowances  where ap_id=".$paID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			{
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
				$txtPayName=@$row['pa_name'];
				$txtAbvr=@$row['pa_abvr'];
				$last_updated=@$row['date_updated'];
				$updated_by=@$row['updated_by'];
			}
	
}
?>

<form method="post" name="frm" id="frm" action="pay_allowances.php?paID=<?php echo $paID;?>&act=<?php echo $act; ?>" onsubmit="return chkMe();" >
 <div id="middleWrap">
		<div class="head"><h2> <?php  if($act=='add') echo "ADD"; else if($act=='edit') echo "EDIT"; else "DELETE";?> Pay & Allowances </h2></div>

 <span id="spErr"></span>
<table class="adminform">
 
  <tr>
    <td align="right" class="redstar">Pay / Allowances Name :</td>
    <td><input type="text" name="txtPayName" id="txtPayName" size="40"  value ='<?php  if($act=="add") ""; else echo $txtPayName; ?>' <?php if($act=="delete") echo "readonly"; ?> maxlength="200"/> 
    </td>
   </tr>
  <tr>
    <td align="right" class="redstar">ABBR. :</td>
    <td><input type="text" name="txtAbvr" id="txtAbvr" size="40"  value ='<?php  if($act=="add") ""; else echo $txtAbvr; ?>' <?php if($act=="delete") echo "readonly"; ?> maxlength="5"/>
     </td>
    </tr>
     <?php if ($act!="add") { ?>
 <tr>

    <td align="right">last Updated : </td>

    <td><?php if($act=="add") ""; else echo date("jS-M-Y g:i A", strtotime(@$last_updated));  ?></td>
    </tr><tr>
    <td align="right">Updated By : </td>
    <td><?php echo @$updated_by; ?></td>
    </tr>
  <tr>
  <?php } ?>  
  <tr>
   
    
        <td align="center" colspan=2><input type="submit"  class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
					
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
				 <?php  if(@$act!="delete") { ?>
			<input type="button" class="btn reset" value="RESET" id= "reset "name="reset" onClick="ClearField(this.form)" /> <?php } ?>
				 			<input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
          	<input type='hidden' name='action' value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' />
           </td>
    </tr>
</table>
</div>
</form>
<script language=javascript>
<!--

document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
//-->
   </script>
   
  </body>
  </html>

<?php
@session_start();
include('../../conn.php');
//include('../../check_session.php');
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
		var txtLoanPurposeName=document.frm.txtLoanPurposeName.value;
	if(txtLoanPurposeName.trim()=="")
	{
		alert("Please Enter Your PF / Loan Purpose Name ");
		document.frm.txtLoanPurposeName.value="";
		document.frm.txtLoanPurposeName.focus();
	    return false;
	 }
	 
	var txtLoanPurposeName=document.frm.txtLoanPurposeName.value.length;
	if(txtLoanPurposeName < 2){
		alert(" PF / Loan Purpose NameShould Be Atleast 2 characters");
		document.frm.txtLoanPurposeName.focus();
		return false;
	}
	var  txtAbvr=document.frm.txtAbvr.value;
	if(txtAbvr.trim()==""){
		alert("Please Enter Your ABBR. ");
		document.frm.txtAbvr.value="";
		document.frm.txtAbvr.focus();
		return false;
	}
	if(document.frm.txtAbvr.value!=""){
		 var  txtAbvr=document.frm.txtAbvr.value.length;
		 if(txtAbvr > 5){
		 	alert("Not Allow More Than 5 digits.");
		 	document.frm.txtAbvr.focus();
		 	return false;
		} } 
	
return true;	   
} 

	function ClearField(frm){
		  frm.txtLoanPurposeName.value = "";
		  frm.txtAbvr.value = "";
		  
		}
	</script>
</head>
<body>
<?php

$msg="";
 makesafe(extract($_GET));  
 makesafe(extract($_POST));
$Errs="";
if(@$action=="SAVE" || @$action=="UPDATE")
{

	if($Errs==""){
		if($txtLoanPurposeName=='')
			$Errs= "<div class='error'>Please Enter Your PF/Loan purpose name</div>";
		elseif($txtAbvr=='')
		$Errs= "<div class='error'>Please Enter ABBR.</div>";
	}

    if($action=="SAVE")
    {
		 $query1 = "SELECT loan_purpose_name from sal_loan_purpose where loan_purpose_name='".trim($txtLoanPurposeName)."'"; 
		$result1 = mysql_query($query1) or die('Error1, query failed');
		if(mysql_affected_rows($link)>0) 
			$Errs="<DIV class='error'>Duplicate PAY & DEDUCTION NAME</DIV>";
		
		else {
			$loan_purpose_id=getNextMaxId("sal_loan_purpose","loan_purpose_id")+1;
				
		 $sql="insert into sal_loan_purpose(loan_purpose_id,loan_purpose_name,loan_purpose_abvr,updated_by)";
         $sql = $sql ." values('".$loan_purpose_id."','".$txtLoanPurposeName."','".$txtAbvr."','".$_SESSION['emp_name']."')";   
		 $res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
            if(mysql_affected_rows($link)>0) 
        	$Errs= "<div class='success'>Record Saved successfully</div>";
		}
	}//save 	 
	
	if($action=="UPDATE")
	{
		$query   = "SELECT loan_purpose_name, loan_purpose_id from sal_loan_purpose where loan_purpose_name='".trim($txtLoanPurposeName)."' and loan_purpose_id !=".$loan_purposeID;
		$result  = mysql_query($query,$link) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			$Errs="<DIV class='error'>Duplicate Pay & Allownces Name</DIV>";
	
		else
		{
			if($Errs!='') {
				$msg=$Errs; }
				else
				{
	                $sql="update sal_loan_purpose set loan_purpose_name='".$txtLoanPurposeName."', loan_purpose_abvr='".$txtAbvr."', updated_by='".$_SESSION['emp_name']."' where loan_purpose_id=".$loan_purposeID." ";					 
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
}// save update
if(@$action=="DELETE")
{	
				
		$query   = "delete  FROM sal_loan_purpose where loan_purpose_id=".$loan_purposeID;
		$result  = mysql_query($query) or die('Error, query failed');
		@$txtLoanPurposeName="";
		@$txtAbvr="";
		if(mysql_affected_rows($link)>0)
				
			$Errs= "<div class='success'>Record Deleted Successfully</div>";
}

if(@$act=="edit" || @$act=="delete" )
{
		$query   = "SELECT  loan_purpose_abvr, loan_purpose_name,  date_updated,updated_by FROM sal_loan_purpose  where loan_purpose_id=".$loan_purposeID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			{
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			 	
				 $txtLoanPurposeName=@$row['loan_purpose_name'];
				 $txtAbvr=@$row['loan_purpose_abvr'];
				$last_update=date("jS-M-Y g:i A", strtotime(@$row['date_updated']));
				$updated_by=@$row['updated_by'];
			}
		
}
?>

<form method="post" name="frm" action="pay_loan_purpose.php?loan_purposeID=<?php echo $loan_purposeID;?>&act=<?php echo $act; ?>" onsubmit="return chkMe();" >
 <div id="middleWrap">
		<div class="head"><h2><?php  echo strtoupper($act);?> Pay Loan Purpose</h2></div>

 <span id="spErr"></span>
<table class="adminform">
 
  <tr>
    <td align="right"   class="redstar">PF Loan Purpose Name :</td>
    <td><input type="text" name="txtLoanPurposeName" id="txtLoanPurposeName" size="40"  value ='<?php if($act=="add") ""; else echo $txtLoanPurposeName; ?>' <?php if($act=="delete") echo "readonly"; ?> maxlength="200" />        </td>
   </tr>
  <tr>
    <td align="right"   class="redstar">ABBR. :</td>
    <td><input type="text" name="txtAbvr" id="txtAbvr" size="40"  value ='<?php if($act=="add") ""; else echo $txtAbvr; ?>' <?php if($act=="delete") echo "readonly"; ?> maxlength="5"/></td>
    </tr>
         <?php if ($act!="add") { ?>
 <tr>
    <td align="right">Last Updated : </td>
    <td><?php if($act=="add") ""; else echo @$last_update;  ?></td>
    </tr>
  <tr>
    <td align="right">Updated By : </td>
    <td><?php echo @$updated_by; ?></td>
   </tr>
<?php  } ?>    
  <tr>
   
    
        <td align="center" colspan=2><input type="submit" class="btn save" value='<?php 
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

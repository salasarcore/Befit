<?php
@session_start();
include('../../conn.php');
//include('../../check_session.php');
include ("../../functions/common.php");
$Errs="";
$stop_salary="";
$pay_stop_reason="";
$action = makeSafe ( @$_POST ['action'] );
makeSafe ( extract ( $_GET ) );

if (trim(@$action == "SAVE") || trim(@$action) == "UPDATE") 
{
	makeSafe ( extract ( $_POST ) );
	 $sql="update employee set pay_stop_reason='".$pay_stop_reason."',stop_salary='".$stop_salary."',updated_by='".$_SESSION['emp_name']."' where empid=".$empID;
		$res=mysql_query($sql,$link);
		if(mysql_affected_rows($link)==0)
			$Errs="<div class='success'>No Data Changed</div>";
		if(mysql_affected_rows($link)>0)
			$Errs="<div class='success'>Record Updated successfully</div>";
		if(mysql_affected_rows($link)<0)
			$Errs="<div class='error'>Record Not Updated successfully</div>";
	
}
if(@$act=="edit")
{

		$query   = "SELECT  stop_salary,pay_stop_reason FROM employee  where empid=".$empID;
		$result  = mysql_query($query) or die('Error, query failed');
		if(mysql_affected_rows($link)>0)
			{
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				$stop_salary=@$row['stop_salary'];
				$pay_stop_reason=@$row['pay_stop_reason'];
			
			}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title></title>
	<link href="../../css/classic.css" type="text/css" rel="stylesheet">
	<?php include('../../php/js_css_common.php');?>
</head>
<body>


<form method="post" name="frmManu" action="stop_salary.php?empID=<?php echo $empID;?>&act=<?php echo $act; ?>" >
 <div id="middleWrap">
		<div class="head"><h2>STOP SALARY</h2></div>

 <span id="spErr"></span>
<table class="adminform">
 
  <tr>
    <td align="right" class="mandate">STOP RUNNING ?</td>
    <td>
		<select name="stop_salary">
			<option value="Y" <?php if($stop_salary=="Y") echo " selected";?>>YES</option>
			<option value="N" <?php if($stop_salary=="N") echo " selected";?>>NO</option>
		</select>
	</td>
   </tr>
  <tr>
    <td align="right">CAUSE. :</td>
    <td><input type="text" name="pay_stop_reason" id="pay_stop_reason" size="40"  value ='<?php echo $pay_stop_reason; ?>'/></td>
    </tr>
 <tr>
   
    
        <td align="center" colspan=2><input type="submit" class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
					
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
			<input type="reset" class="btn reset" value="RESET" name="B2"><input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
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
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
   
  </body>
  </html>
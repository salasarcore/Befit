<?php
@session_start();
include('../../conn.php');
//include('../../check_session.php');
include("../../functions/employee/dropdown.php");
include("../../functions/dropdown.php");
include("../../functions/functions.php");
include("../../functions/common.php");
include_once("../../functions/comm_functions.php");
//include("../../classes/class.sample_image.php");

makeSafe(extract($_REQUEST));

$Errs="";
$empids = explode ( ",", $empID );
$str_empids = implode (",",$empids);

if(@$action=="SAVE")
{
	if(trim(@$paytype)=="")
	$Errs= "<div class='error'>Please Select Pay Type </div>";
	elseif(trim(@$amount)=="")
		$Errs= "<div class='error'>Please Enter Amount</div>";
	elseif(trim(@$amount)=="0")
		$Errs= "<div class='error'>Amount can not be 0</div>";
	else
	{
	if($Errs=="")
	{
		foreach ($empids as $empid)
		{
		$setting_id=getNextMaxId("emp_sal_settings","setting_id")+1;
		$sql = "select * from emp_sal_settings where empid='".$empid."' AND session = '".$_SESSION['d_session']."'  "; 
		$res = mysql_query ($sql,$link);
		
		if(mysql_num_rows($res)>0)
		{
			$row = mysql_fetch_assoc($res);
			if($row['pay_type']==$paytype){
				if($paytype=="H"){
				  
					 $sql = "select * from emp_sal_settings where study_dept_id ='".$dept_id."' and empid='".$empid."' AND session = '".$_SESSION['d_session']."' ";
				    	
				    $res = mysql_query($sql);
				    if(mysql_num_rows($res)>0) {
				    	$sql_up="update emp_sal_settings set session='".$_SESSION['d_session']."', study_dept_id='".@$dept_id."',pay_amount='".$amount."',updated_by='".$_SESSION['emp_name']."',reco_intime='".@$reco_intime."',reco_outtime='".@$reco_outtime."' where empid='".$empid."'  AND study_dept_id ='".$dept_id."' AND  session = '".$_SESSION['d_session']."' " ;
						$res_up=mysql_query($sql_up,$link);
					 }
					else{
						$sql="insert into emp_sal_settings(setting_id,empid,study_dept_id,pay_type,pay_amount,created_on,updated_on,updated_by,reco_intime,reco_outtime,session)";
						$sql = $sql ." values(".$setting_id.",".$empid.",'".@$dept_id."','".$paytype."','".$amount."',NOW(),NOW(),'".$_SESSION['emp_name']."','".@$reco_intime."','".@$reco_outtime."','".$_SESSION['d_session']."')";
						$res=mysql_query($sql,$link);
					}
				}
				else{
					$sql="update emp_sal_settings set study_dept_id='".@$dept_id."', session='".$_SESSION['d_session']."', pay_type='".$paytype."',pay_amount='".$amount."',updated_by='".$_SESSION['emp_name']."',reco_intime='".@$reco_intime."',reco_outtime='".@$reco_outtime."' where empid='".$empid."' ";
						mysql_query($sql,$link);
				}
			}
			else{
				 $sql_del = "delete from emp_sal_settings where empid='".$empid."' and session='".$_SESSION['d_session']."' ";
				$ers_del = mysql_query($sql_del);
				
				
				$sql="insert into emp_sal_settings(setting_id,empid,study_dept_id,pay_type,pay_amount,created_on,updated_on,updated_by,reco_intime,reco_outtime,session)";
						$sql = $sql ." values(".$setting_id.",".$empid.",'".@$dept_id."','".$paytype."','".$amount."',NOW(),NOW(),'".$_SESSION['emp_name']."','".@@$reco_intime."','".@$reco_outtime."','".$_SESSION['d_session']."')";
						$res=mysql_query($sql,$link);
			}
		}
		else
		{
			$sql="insert into emp_sal_settings(setting_id,empid,study_dept_id,pay_type,pay_amount,created_on,updated_on,updated_by,reco_intime,reco_outtime,session)";
			$sql = $sql ." values(".$setting_id.",".$empid.",'".@$dept_id."','".$paytype."','".$amount."',NOW(),NOW(),'".$_SESSION['emp_name']."','".@$reco_intime."','".@$reco_outtime."','".$_SESSION['d_session']."')";
			$res=mysql_query($sql,$link);
		}

		}//for each
		
		if (mysql_affected_rows($link)>0) 
			$Errs= "<div class='success'>Record Saved Successfully</div>";
			else $Errs= "<div class='error'>Record Not Saved Successfully</div>";
		
		}
	
	 }
   }?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>EMPLOYEE</title>
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<?php include('../../php/js_css_common.php');?>
<link rel="Stylesheet" href="../../../js/jquery.ui.timepicker.css" rel="stylesheet"/>
<script src="../../../js/jquery.ui.timepicker.js"></script>
<style>
.ui-timepicker-table .ui-timepicker-title
{
line-height:0.9em;
}
.ui-timepicker-table td, .ui-timepicker-table th.periods {
 font-size:10px;
}

.timestyle
{
width:100px;
height:22px;}

</style>
<script type="text/javascript">
$(document).ready(function(){
    
	   	 $(".dept_id").hide();
    	 $(".reco_intime").hide();
	     $(".reco_outtime").hide();
	     $(".amount").hide();	
	     
     $('#paytype').change(function(){
    	
    	 $("#dept_id").val('');
    	 $("#reco_intime").val('');
	     $("#reco_outtime").val('');
	     $("#amount").val('');		  	  		    

	     	if($('#paytype').val()=="M")
			{
     		 
		       $(".dept_id").hide();
		       $(".reco_intime").show();
		       $(".reco_outtime").show();
		       $(".amount").show();
		       $("#regTitle").html("Per Month");

			} 
     	else if($('#paytype').val()=="H")
     	{
     	  
      	   $(".dept_id").show();
      	   $(".reco_intime").hide();
	       $(".reco_outtime").hide();
	       $(".amount").show();
      	   $("#regTitle").html("Per Hour");
      	  
     	}
       	else 
     	{
     	 
     	 $(".dept_id").hide();
     	 $(".reco_intime").hide();
	     $(".reco_outtime").hide();
	     $(".amount").hide();
     	  $("#regTitle").html("");
     	  }
		});
   
});//main
	
function validateexpense(frm)
{
	var start_time = $('#reco_intime').val();
	var end_time = $('#reco_outtime').val();
	if(frm.paytype.value.trim()=="")
	{
		alert('Please select pay type');
		frm.paytype.value="";
		frm.paytype.focus();
		return false;
	}

	else if(frm.paytype.value.trim()=="M")
    {
		 // date picker 
		  if($('#reco_intime').val()=="")
		 {
			    alert('Please enter recommended in time');
			 	frm.reco_intime.value="";
				frm.reco_intime.focus();
				return false;
		 }
		 else if($('#reco_outtime').val()=="")
		 {
			    alert('Please enter  recommended out time');
			 	frm.reco_outtime.value="";
				frm.reco_outtime.focus();
				return false;
		 }
			
					
		 else if(start_time == end_time)
					{
						alert('Recommended in time and recommended out time cannot be same.');
						return false;
					}
					else if(start_time > end_time)
					{
						alert('Recommended in time cannot be greater than recommended out time.');
						return false;
					}
				
		else if(frm.amount.value.trim()=='') 
			{
				alert('Please enter amount');
				frm.amount.value="";
				frm.amount.focus();
				return false;
			}
			else if(frm.amount.value.trim()=='0') 
			{
				alert('Amount can not be 0');
				frm.amount.value="";
				frm.amount.focus();
				return false;
			}
	}

	else if(frm.paytype.value.trim()=="H") 
	{ 
		
	    if(frm.dept_id.value=="0")
	    {
 		alert('Please select department');
		frm.dept_id.value="";
		frm.dept_id.focus();
		return false;
        }
	
		// amount
		 else if(frm.amount.value.trim()=='') 
			{
				alert('Please enter amount');
				frm.amount.value="";
				frm.amount.focus();
				return false;
			}
		else if(frm.amount.value.trim()=='0') 
		{
			alert('Amount can not be 0');
			frm.amount.value="";
			frm.amount.focus();
			return false;
		}
	}

	
	else
return true;
}

</script>

</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2> EMPLOYEE SALARY SETTING</h2></div>
		
		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frmManu"	action="employee_salary_setting.php?empID=<?php echo $empID; ?>&act=<?php echo $act; ?>" onsubmit="return validateexpense(this);" autocomplete="off" enctype="multipart/form-data">
	    <table class="adminform">
				<tr>
					<td width="30%" align="right" class="redstar">Pay Type :</td>
					<td width="43%">
				
					<select name="paytype" id="paytype">
					<option value="">--Select  Type--</option>
					<option value="M"  >Monthly</option> 
					<option value="H" >Hourly</option> 
					</select>
					</td>
				</tr>
					
				<tr class="dept_id">
				<td width="30%" align="right" class="redstar">Select Department : </td>
				<td width="43%">	
					<?php 
					$sql="select session,section, d.*  from session_section s, mst_departments d where  s.freeze='N' and s.admission_open='Y' and s.department_id=d.department_id AND d.br_id='".$_SESSION['br_id']."' AND session = '".$_SESSION['d_session']."' GROUP BY  s.department_id ";
					$res=mysql_query($sql,$link) or die("Unable to connect to Server,We are sorry for inconvienent caused".mysql_error());
					echo "<select size='1' name='dept_id' id='dept_id'>";
					echo "<option value='0' selected>--Select Department--</option>";
					while($row1=mysql_fetch_array($res))
					echo"<option value='".$row1['department_id']."'>".$row1['department_name']."</option>";
						
					echo"</select>";?>
	
				</tr>
						
				<tr class="reco_intime">
					<td width="30%" align="right" class="redstar" >Recommended In Time :</td>
				<td width="43%">	
				<input name="reco_intime" type="text"  id="reco_intime" readonly="readonly" maxlength="10" />
						   <script>
				        	$('#reco_intime').timepicker({
				            	minutes: { interval: 1 },
				            	timeFormat: "HH:mm",
				            	rows:5
				            	});
			            	</script></td>
				</tr>
				<tr class="reco_outtime">
					<td width="30%" align="right" class="redstar">Recommended Out Time :</td>
					<td width="43%"><input name="reco_outtime" type="text" readonly="readonly"  id="reco_outtime" maxlength="10" />
						<script>
				        	$('#reco_outtime').timepicker({
				            	minutes: { interval: 1 },
				            	timeFormat: "HH:mm",
				            	rows:5
				            	});
			            	</script></td>
				</tr>		
				<tr class="amount">
					<td width="30%" align="right" class="redstar">Amount <div id="regTitle" style="display: inline;"> </div>  : </td>
					<td width="43%"><input type="text" name="amount" maxlength ="8"	id="amount"
					 onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')"size="25" maxlength="10"
					 placeholder = "<?php if(isset($_POST['amount']) ) echo $amount;?>" /> </td>
				</tr>
				
				<?php if(@$created_on!="") {?><tr><td align="right">Created At :</td><td><?php echo @$created_at; ?></td></tr><?php } ?>
				<?php if(@$updated_on!="") {?><tr><td align="right">Updated At :</td><td><?php echo @$updated_at; ?></td></tr><?php } ?>
				<?php if(@$updated_by!="") {?><tr><td align="right">Updated By :</td><td><?php echo @$updated_by; ?></td></tr><?php } ?>
				<tr>
					<td align="center" colspan=2>
					<input type="submit" class="btn save" value='SAVE' name="B1">
				 <input type=button	class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
				 <input	type='hidden' name='action'	value='SAVE' />
					</td>
				</tr>
			</table>
		</form>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
	</div>
</body>
</html>

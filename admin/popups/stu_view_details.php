<?php
@session_start();
include('../conn.php');
include('../functions/common.php');

/*include("../modulemaster.php");

	$id=option_applied_student_list_view_profile;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}
*/
$sql="SELECT adm_form_no,br_name, concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name, sex,pin,email, dob,present_address, mob,city,occupation,height,weight,off_tel_no,res_tel_no,physical_activity,present_medications,presently_pregnant,physician,present_physical_activity,physician_name,clinic_no,mobile_clinic,contact_person_name,contact_person_telno, date_applied,    admitted FROM admission_application,mst_branch WHERE  admission_application.br_id=mst_branch.br_id and  adm_form_no=".makeSafe(@$_GET['fromID']);
$result  = mysql_query($sql) or die('Error, query failed');

if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);

		$branch_name=$row['br_name'];
		$formno=$row['adm_form_no'];
		
		$stu_name=$row['stu_name'];
		$sex=$row['sex'];
		$dob=@$row['dob'];
		
		$present_address=$row['present_address'];
		$pin=$row['pin'];
		$mob=$row['mob'];
		$email=$row['email'];
		$occupation=$row['occupation'];
		$height=$row['height'];
		$weight=$row['weight'];
		$city=$row['city'];
		$offtelno=$row['off_tel_no'];
		$restelno=$row['res_tel_no'];
		$physicalactivity=$row['physical_activity'];
		$presentmedications=$row['present_medications'];
		$presentlypregnant=$row['presently_pregnant'];
		$physician=$row['physician'];
		$presentphysicalactivity=$row['present_physical_activity'];
		$physicianname=$row['physician_name'];
		$clinicno=$row['clinic_no'];
		$mobileclinic=$row['mobile_clinic'];
		$contactpersonname=$row['contact_person_name'];
		$contactpersontelno=$row['contact_person_telno'];
		

?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>BRANCHES</title>
<style>
tr.border_bottom td {
  border-bottom:1pt solid black;

}
td{
padding-bottom: 2px;
}
.cont{width:600px;margin:0px auto}
body{font-family:Arial;font-size:14px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;margin-left: 0px;}
table{font-family:Arial;font-size:14px;}.hide{display:none;}
</style>
<script>
 function printpage() {
 document.getElementById("print").className="hide";
  window.print();
  }
</script>
</head>
<body>
<div class="cont">
<!-- <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%" style="padding: 5px 0 0 0;">
<tr>
		<td width="15%">
		<img src="<?php echo is_file('../../site_img/school_logo/'.$subdomainname.'.png')?'../../site_img/school_logo/'.$subdomainname.'.png':'../../site_img/school_logo/demo.png';?>" width="100px" ></a></div>

		</td>
		<td align="center"><font style="font-size: 25px; font-weight: bold;"><?php echo @SCHOOL_NAME;?></font><br>
		<?php 
		$query="SELECT * FROM mst_branch where br_name='".@$branch_name."'";
		$res=mysql_query($query) or die('Error, query failed');
		$row1 = mysql_fetch_array($res, MYSQL_ASSOC);
		echo $row1['br_addr1'].", ".$row1['br_country'].", ". $row1['br_state']."-".$row1['br_pin'];
		?>
		</td>
	</tr>
</table>-->
	<table border="0" cellspacing="0" cellpadding="0" align="center" style="border: 1px solid #000000; frame: box; padding: 8px;margin-left:0px;" width="100%">
	
	<tr class="border_bottom" ><td colspan="6"><b>Admission Details</b></td></tr>
	
	<tr><td><br></td></tr>
	<tr>
		<td align="left"><b>Form No </b>
		<td colspan="6"><?php echo ":&nbsp;".@$formno;?> </td>
		
	</tr>
	<tr>
		<td align="left"><b>Branch </b></td>
		<td colspan="5"><?php echo ":&nbsp;".@$branch_name;?> </td>
		
	</tr>
	
	<tr ><td colspan="6"><br></td></tr>
	<tr><td></td></tr>
	  <tr class="border_bottom"><td colspan="6"><b>Applicant Information</b></td></tr>
	  <tr><td><br></td></tr>
	  <tr>
		<td align="left" width="100px"><b> Name </b></td>
		<td colspan=4><?php echo ":&nbsp;".@$stu_name; ?></td>
		
	  </tr>
	   <tr>
		<td align="left"><b>Gender </b></td>
		<td><?php echo ":&nbsp;".@$sex; ?></td><td></td>
		 </tr>
	  <tr>
		<td  align="left"><b>Birth Date </b></td>
		<td><?php echo  ":&nbsp;".date("jS-M-Y", strtotime(@$dob));?>  </td><td></td>
		
	  </tr>  
	
	 <tr>
	 <?php }?>
		<td align="left"><b>Mobile No. </b></td>
		<td><?php echo ":&nbsp;".@$mob; ?></td><td></td></tr><tr>
		 <td align="left" ><b>Email</b> </td>
		 <td><?php echo ":&nbsp;".@$email; ?></td>
	  </tr>
	 
		<td align="left"><b>Present Address </b></td>
		<td colspan="4"><?php echo ":&nbsp;".@$present_address;?></td>
	  </tr>
	 <tr>
		<td align="left"><b>Pin Code </b></td>
		<td><?php echo ":&nbsp;".@$pin; ?></td><td></td></tr>
		<tr>
		<td align="left"><b>Off Tel. No. </b></td>
		<td ><?php echo ":&nbsp;".$offtelno; ?></td></tr><tr>
			<td align="left"><b>Res. Tel. No.</b></td>
		<td><?php echo ":&nbsp;".$restelno; ?></td>
		  </tr>
	  
		   <tr> 
		<td align="left"><b>City</b></td>
		<td><?php echo ":&nbsp;".$city; ?></td></tr><tr>
		<td align="left"><b>Occupation</b></td>
		<td><?php echo ":&nbsp;".$occupation; ?></td>
		  </tr>
		  		   <tr> 
		<td align="left"><b>Height</b></td>
		<td><?php echo ":&nbsp;". $height; ?></td></tr><tr>
		<td align="left"><b>Weight</b></td>
		<td><?php echo ":&nbsp;".$weight; ?></td>
		  </tr>
			  		   <tr> 
		<td align="left" colspan="3"><b>Are you currently involved in any physical activity?</b></td>
		<td><?php echo ":&nbsp;".$physicalactivity;?></td>
		  </tr> <tr><td><br></td></tr>
		  		<tr class="border_bottom"><td colspan="6"><b>HEALTH FORM</b></td></tr>	  <tr><td><br></td></tr> 		   <tr> 
		<td align="left" colspan="3"><b>
Are you presently taking any medications? If yes then please list</b>
		</td>
		<td><?php echo ":&nbsp;".$presentmedications;?></td>
		  </tr>
		    <tr> 
		<td align="left" colspan="3"><b>
Are you presently pregnant in last three months? </b>
		</td>
		<td><?php echo ":&nbsp;".$presentlypregnant;?></td>
		  </tr>
		   <tr> 
	<td align="left" colspan="3"><b>Does your physician know you are participating in exercise program? </b></td>
		<td><?php echo ":&nbsp;". $physician;?>
	</td>
		  </tr>
		  	    <tr> 
		<td align="left" colspan="3"><b>
What physical activity are presently doing?	</b>	</td>
		<td><?php ":&nbsp;".$presentphysicalactivity;?></td>
		  </tr>
		    	    <tr> 
		<td align="left" colspan="3"><b>
Physician Name</b>	</td>
		<td ><?php ":&nbsp;".$physicianname;?></td>
		  </tr>
		  	    	    <tr> 
		<td align="left" colspan="3"><b>
Clinic No.</b>	</td>
		<td ><?php  ":&nbsp;".$clinicno;?></td></tr><tr>
		<td align="left" colspan="3"><b>
Clinic Mobile No.</b>	</td>
		<td ><?php echo  ":&nbsp;".$mobileclinic;?></td>
		  </tr>
		  		    	    <tr> 
		<td align="left" colspan="6"><b>
Person/s to contact in case of emergency?</b>	</td>
		 </tr>
		 		    	    <tr> 
		<td align="left" colspan="3"><b>
Contact Person Name	</b></td>
		<td ><?php echo ":&nbsp;". $contactpersonname;?></td></tr><tr>
		<td align="left" colspan="3"><b>
Contact Person No.</b>	</td>
		<td><?php echo  ":&nbsp;".$contactpersontelno;?></td>	  </tr>
				</table>
			<br>
		
<div id="print" align="center"> <input type="button" name="print" value="Print" onClick="printpage();" /></div>
</div>
</body>
</html>
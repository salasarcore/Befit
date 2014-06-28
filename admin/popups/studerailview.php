<?php 
@session_start();
include("../conn.php");
include('../check_session.php');
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/functions.php");
include("../functions/common.php");
$act=makeSafe(@$_GET['act']);
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../modulemaster.php");

$id=option_admission_register_view_details;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$studentID=makeSafe(@$_GET['studentID']);
$action=makeSafe(@$_POST['action']);
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>VIEW STUDENT</title>
<script type="text/javascript" src="../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../../js/hint.js"></script>
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<style>
.hint{right:100px}
</style>
</head>
<body>

<?php 

$studentID=makeSafe(@$_GET['studentID']);

$sql="select * from mst_students where stu_id=".$studentID;
$result  = mysql_query($sql) or die('Error, query failed');

$res=mysql_fetch_array($result);

$date = new DateTime($res['admission_date']);
$adminsiondate=$date->format('jS-M-Y');

$date2 = new DateTime($res['dob']);
$dob=$date2->format('jS-M-Y');

is_file("../../site_img/stuimg/".DOMAIN_IDENTIFIER."_".base64_encode($studentID).".".$res['mime']) ? $pic="../../site_img/stuimg/".DOMAIN_IDENTIFIER."_".base64_encode($studentID).".".$res['mime'] : $pic="../../images/".DEFAULT_IMAGE; 

?>

<div id="middleWrap"><div class="head" style="color: #000000;font-weight: bold;">APPLICANT : <?php echo $res['stu_fname']." ".$res['stu_mname']." ".$res['stu_lname'];?></div>


<div style="margin-left:390px;margin-top:10px;position:absolute"><img style="border-style:solid;border-width:medium;" src="<?php echo $pic?>" ></div>
   <table border="0" width="100%" cellpadding="7" style="border-spacing: 12px;" class="adminform">
   
   <tr><td id="align">Name :  <font class="fonts"><?php echo $res['stu_fname']." ".$res['stu_mname']." ".$res['stu_lname'];?></font></td></tr> 

    <tr><td id="align">Registeration No :  <font class="fonts"><?php echo  $res['reg_no'];?></font></td></tr>
 
     <tr><td id="align">Admission Date :  <font class="fonts"><?php echo $adminsiondate;?></font></td></tr>
    <tr><td id="align">Mobile Number :  <font class="fonts"><?php echo  $res['mob'];?></font></td></tr>
      
    <tr><td id="align">Email Id :  <font class="fonts"><?php echo  $res['email'];?></font></td></tr> 
   
     <tr><td id="leftalign">Sex :  <font class="fonts"><?php echo  $res['sex'];?></font></td><td id="align">Date of Birth :  <font class="fonts"><?php echo $dob;?></font></td></tr> 
    
    
      <tr><td id="align">Present Address :  <font class="fonts"><?php echo  $res['present_address'];?></font></td></tr>
   
      
    <tr><td id="align">Pin Code :  <font class="fonts"><?php echo  $res['pin'];?></font></td></tr> 
  
 
	  <tr>
		<td id="align">Off Tel. No. :</td>
		<td><?php echo $res['off_tel_no']; ?></td>
			<td id="align">Res. Tel. No. :</td>
		<td><?php echo $res['res_tel_no']; ?></td>
		  </tr>
	  
		   <tr> 
		<td id="align">City :</td>
		<td><?php echo $res['city']; ?></td>
		<td id="align">Occupation :</td>
		<td><?php echo $res['occupation']; ?></td>
		  </tr>
		  		   <tr> 
		<td id="align">Height :</td>
		<td><?php echo $res['height']; ?></td>
		<td id="align">Weight :</td>
		<td><?php echo $res['weight']; ?></td>
		  </tr>
			  		   <tr> 
		<td id="align">Are you currently involved in any physical activity?</td>
		<td><?php echo $res['physical_activity'];?></td>
		  </tr>
		  		<tr><td colspan="4" style="text-align: left;"><h3>HEALTH FORM</h3></td></tr>	  		   <tr> 
		<td id="align">
Are you presently taking any medications? If yes then please list
		</td>
		<td><?php echo $res['present_medications'];?></td>
		  </tr>
		    <tr> 
		<td id="align">
Are you presently pregnant in last three months?
		</td>
		<td><?php echo $res['presently_pregnant'];?></td>
		  </tr>
		   <tr> 
	<td id="align">Does your physician know you are participating in exercise program?</td>
		<td><?php echo $res['physician'];?>
	</td>
		  </tr>
		  	    <tr> 
		<td id="align">
What physical activity are presently doing?		</td>
		<td><?php $res['present_physical_activity'];?></td>
		  </tr>
		    	    <tr> 
		<td id="align">
Physician Name	</td>
		<td ><?php $res['physician_name'];?></td>
		  </tr>
		  	    	    <tr> 
		<td id="align">
Clinic No.	</td>
		<td ><?php  $res['clinic_no'];?></td>
		<td id="align">
Clinic Mobile No.	</td>
		<td ><?php echo  $res['mobile_clinic'];?></td>
		  </tr>
		  		    	    <tr> 
		<td id="align">
Person/s to contact in case of emergency?	</td>
		 </tr>
		 		    	    <tr> 
		<td id="align">
Contact Person Name	</td>
		<td ><?php echo  $res['contact_person_name'];?></td>
		<td id="align">
Contact Person No.	</td>
		<td><?php echo  $res['contact_person_telno'];?></td>	  </tr>
   </table>
  </div>
  </body>
  
  <style>
  #align{
 text-align:left;
  }
   #leftalign{padding-left:80px;} 
  .fonts{font-size:12px;font-weight:bold}
  </style>
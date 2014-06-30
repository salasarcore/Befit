<?php 
@session_start();
include("../conn.php");
//include('../check_session.php');
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/functions.php");
include("../functions/common.php");
$act=makeSafe(@$_GET['act']);
$empID=makeSafe(@$_GET['empID']);
$action=makeSafe(@$_POST['action']);
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>VIEW EMPLOYEE</title>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php include('../php/js_css_common.php');?>
<script type="text/javascript" src="../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../../js/hint.js"></script>
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<style>
.hint{right:100px}
</style>
<script>

</script>
</head>
<body>

<?php 

$empID=makeSafe(@$_GET['empID']);

$sql="select e.*,ed.* from employee as e left join employee_department as ed on e.department_id=ed.department_id where e.empid=".$empID;

$result  = mysql_query($sql) or die('Error, query failed');

$res=mysql_fetch_array($result);

is_file("../../site_img/emppic/".base64_encode($empID).".".$res['mime']) ? $pic="../../site_img/emppic/".base64_encode($empID).".".$res['mime'] : $pic="../../images/".DEFAULT_IMAGE;
?>


 <div id="middleWrap">
		<div class="head"><h2>Employee : <?php echo $res['emp_name'];?></h2></div>


<div style="margin-left:425px;margin-top:10px;position:absolute"><img style="border-style:solid;border-width:medium;" src="<?php echo $pic?>" ></div>
   <table border="0" width="100%" cellpadding="7" class="adminform" style="border-spacing: 12px;">
    <tr><td id="align">Name : <font class="fonts"><?php echo  $res['emp_name'];?></font></td></tr>
    <tr><td id="align">Employer ID : <font class="fonts"><?php echo  $res['emp_id'];?></font></td></tr>
	
    <tr><td id="align">Designation: <font class="fonts"><?php echo $res['department_name'];?> - <?php if($res['superior']=='S') echo 'Head'; else echo 'Employee'; ?></font></td></tr>
      <tr></tr>
    <tr><td id="align">Qualification : <font class="fonts"><?php echo  $res['emp_qualification'];?></font></td></tr>
    
   <?php 
   $date1 = new DateTime($res['emp_dob']);
   $dob=$date1->format('jS-M-Y');
   $date2 = new DateTime($res['emp_doj']);
   $doj=$date2->format('jS-M-Y');
 
   ?>
   
   <tr><td id="align">Date of Joining : <font class="fonts"><?php echo $doj;?></font></td></tr>
   <tr><td id="align">Mobile Number : <font class="fonts"><?php echo  $res['emp_mob'];?></font></td><td id="leftalign">Sex : <font class="fonts"><?php echo  $res['sex'];?></font></td></tr>
  <tr><td id="align">Email Id : <font class="fonts"><?php echo  $res['email'];?></font></td><td id="leftalign">Date of Birth : <font class="fonts"><?php echo  $dob;?></font></td></tr>
   <tr><td id="align">Present Address : <font class="fonts"><?php echo  $res['emp_addr_pre'];?></font></td><td id="leftalign">Permanent Address : <font class="fonts"><?php echo  $res['emp_addr_per'];?></font></td></tr>
   <tr><td id="align">Emergency Contact Number : <font class="fonts"><?php echo  $res['emp_ecn'];?></font></td><td id="leftalign">Home Phone Number : <font class="fonts"><?php echo  $res['home_phone'];?></font></td></tr>
   <tr><td id="align">Voter Id Number : <font class="fonts"><?php echo  $res['emp_epic'];?></font></td><td id="leftalign">Pan Number : <font class="fonts"><?php echo  $res['emp_pan'];?></font></td></tr>
    <tr><td id="align">Father's Name : <font class="fonts"><?php echo  $res['father_name'];?></font></td><td id="leftalign">Father's Number : <font class="fonts"><?php echo  $res['father_no'];?></font></td></tr>
    <tr><td id="align">Mother's Name : <font class="fonts"><?php echo  $res['mother_name'];?></font></td><td id="leftalign">Payment Type : <font class="fonts"><?php echo  $res['payment_type'];?></font></td></tr>
    <tr><td id="align">Wife/Husband's Name : <font class="fonts"><?php echo  $res['wife_husband'];?></font></td><td id="leftalign">Husband's Number : <font class="fonts"><?php echo  $res['husband_no'];?></font></td></tr>
   </table>
  </div>
  </body>
  
  <style>
  #align{
 text-align:left;
 width:50%;
  }
   #leftalign{padding-left:30px;} 
  .fonts{font-size:12px;font-weight:bold}
  </style>
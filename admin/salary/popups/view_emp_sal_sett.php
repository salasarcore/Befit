
<?php @session_start();
include("../../conn.php");
//include('../../check_session.php');
include("../../functions/common.php");
makeSafe(extract($_REQUEST));
 $empids = explode ( ",", $empID );

?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">

<title>Employee Salary Details</title>
<link rel="shortcut icon" href="../../favicon.ico">
<script type="text/javascript"   src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php include('../../php/js_css_common.php');?>
<link rel="Stylesheet" type="text/css" href="../../../css/jquery-ui.css" />
</head>
<body leftmargin="0" topmargin="0" style="text-align: left">
<div id="middleWrap">
		<div class="head"><h2>EMPLOYEE SALARY DETAILS</h2></div>

<?php foreach ($empids as $empid)
{
$row_emp = mysql_fetch_array(mysql_query("select * from employee where empid='".$empid."'"));

 $sql = "SELECT  s.*, e.*, de.department_name, b.br_name, ds.designation_name 
 		FROM emp_sal_settings s, employee e, mst_departments de, mst_designations ds, mst_branch b 
 		WHERE s.empid='".$empid."' AND  s.session='".$_SESSION['d_session']."' AND  s.empid=e.empid  AND e.br_id=b.br_id AND e.designation_id=ds.designation_id  AND e.department_id=e.department_id limit 0,1";
 $res = mysql_query ($sql,$link); 

if(mysql_num_rows($res)>0)
{
$row = mysql_fetch_assoc($res);

?>

<div style='border:1px solid black;border-radius:7px;margin-bottom:5px;'>
<div style='padding:5px 5px 5px 7px;'>
<table  style="table-layout: fixed;">

<tr>
	<td width="150px" ><b><font size='2.9' color="#000000">Emlployee Name / ID</font></b></td>
	<td style="word-break: break-all;">&nbsp; : &nbsp;<font size='2.9' color="#000000"> <?php echo $row['emp_name'];?> / (<?php echo $row['emp_id'];?>) </font></td>
</tr>
<tr>
	<td><b><font size='2.9' color="#000000">Branch / Department </font></b></td>
	<td><font size='2.5' color="#000000">&nbsp; : &nbsp; <?php echo $row['br_name'];?> / <?php echo $row['department_name'];?>  </font></td>
</tr>
<tr>
	<td><b><font size='2.9' color="#000000">Designation </font></b></td>
	<td style="word-break: break-all;">&nbsp; : &nbsp;<font size='2.5' color="#000000"> <?php echo $row['designation_name'];?> </font></td>
</tr>
<tr>
	<td><b><font size='2.9' color="#000000">Salary Type  </font></b></td>
	<td><font size='2.5' color="#000000">&nbsp; : &nbsp; <?php if($row['pay_type']=="M") echo "Monthly"; else { echo "Hourly"; ?></font></td>
</tr>
<tr><td> </td><td  style='padding-left:20px;'>	
	<table style='border-radius:1px; border:1px solid black; border-collapse:collapse;' cellspacing="10"  width="100%" >
	<tr><th style='border:1px solid black;'> Department </th>
	<th style='border:1px solid black;padding: 5px;'> Amount </th> </tr>
	
	<?php $res2 = mysql_query("select * from emp_sal_settings  where empid='".$row['empid']."'  AND session = '".$_SESSION['d_session']."'  "); 
		  
	while ($row2 = mysql_fetch_array($res2)) {
print_r($row2); 
	?>
	<tr>
	<?php  $sql="select s.*, d.* from emp_sal_settings s, mst_departments d, session_section ses where s.study_dept_id=d.department_id AND s.study_dept_id=ses.department_id AND s. session = '".$_SESSION['d_session']."' AND s.study_dept_id ='".$row2['study_dept_id']."'";
		 $res=mysql_query($sql,$link) or die("Unable to connect to Server,We are sorry for inconvienent caused");
		 $row1 = mysql_fetch_array($res); ?>
	<td align="center" ><?php echo  $row1['session']." - ".$row1['department_name']."";?></td>
	<td  align="center" style='border-left:1px solid black;padding:3px;'><?php echo $row2['pay_amount'];?>/-</td>
	</tr> <?php }//while ?>
	</table>
	
</td></tr>
	<?php }//else M ?>
<?php if($row['pay_type']=="M") { ?>
<tr>
	<td><b><font size='2.9' color="#000000">Salary Amount</font></b></td>
	<td><font size='2.5' color="#000000"> &nbsp; : &nbsp; <?php echo $row['pay_amount'];?>/-</font>
</tr> 
<tr>
	<td><b><font size='2.9' color="#000000">Recommended In time  </font></b></td>
	<td><font size='2.5' color="#000000"> &nbsp; : &nbsp; <?php echo date("g:i A",strtotime($row['reco_intime']));?> <b>Out Time </b> : <?php echo date("g:i A",strtotime($row['reco_outtime']));?></font>
</tr>

<?php  } ?>
</table>
</div></div>

<?php  } else if ($row_emp['empid']!="") { ?>
<div style='border:1px solid black;border-radius:7px;margin-bottom:5px;'>
<div style='padding:5px 5px 5px 7px;'>
<table style="table-layout: fixed;">

<tr>
	<th colspan="2" ><font size='2.9' color="#000000">No salary settings defined for <?php echo $row_emp['emp_name']; ?> / ID : <?php echo $row_emp['emp_id'];?></font></td>
</tr>
</table>
</div></div>
	
<?php }?>
<?php 
} ?> 
	<br>
</body>
</html>


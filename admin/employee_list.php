<?php 
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_employee_list,$modules)){ 
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_employee_list))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
	if(isAccessModule($id_admin, export_excel_employee_list))
		$hasexcelpermission = true;
	else
		$hasexcelpermission = false;
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
} */

$br_id=makeSafe(@$_POST['branches']);
?>



<script>
function selectID(objChk)
{
document.getElementById("empID").value=objChk;
}
function SubmitPage(Page)
{
document.getElementById("txtPage").value=Page;
document.myForm.submit();
}
function ActionScript(act)
{
	if(document.getElementById("empID").value=="" && act !="add")
	{
	  alert("Please select an Employee");
	  
	}
	else if(act=="view")
	{
		url="popups/empderailview.php?act="+act+"&empID="+document.getElementById("empID").value;
		open_modal(url,800,600,"VIEW EMPLOYEE")
		return true;
	}
	else
	{
		url="popups/employee.php?act="+act+"&empID="+document.getElementById("empID").value;
		open_modal(url,800,600,"EMPLOYEE")
		return true;
	}
	
		
}
function ExportExcel()
{
	url = 'employeeListToExcel.php?dept='+document.getElementById("department").value;
	window.open(url, '_blank');
}
</script>
<script>
function Publish(act,imgid,spanid)
{
	
	 var spid=spanid+imgid;
      var poststr = "empid="+imgid+"&act="+act+"&spid="+spid;
   
     makePOSTRequest('ajax/emp_approved.php', poststr,spid);
}
</script>
<div class="page_head">
<div id="navigation"><a href="admin.php">Home</a><a> Payroll</a>  <span style="color: #000000;">Employee List</span> </div>

<form name="myForm" id="myForm" action="admin.php?src=employee_list.php" method="POST">

<table width="100%" class="adminform1">
<tr>
	<td><h2>Employee List</h2></td>
	<td>
	<div id="option_menu">
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
		
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ExportExcel();">Export Excel</a>
		
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('view');">View Details</a>
	</div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">
Please Select Department : <?php $department_id=makeSafe(@$_POST['department']);
if(@$department_id=="") $department_id=0;
echo "<select size=\"1\" name=\"department\" id=\"department\" onchange=\"javascript:SubmitPage(1);\">";
echo "<option value=\"0\" selected>--Select Department--</option>";
$sql="select department_id,department_name  from employee_department where br_id=".$_SESSION['br_id']." order by department_name";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['department_id']."'";
		if($department_id==$row1['department_id']) echo "selected"; echo">".$row1['department_name']."</option>";
	}
echo"</select>";?>
</div>
<br>
<table width="100%" border="0" class="table table-bordered"" style="cursor: pointer;">
  <thead>		
   <tr>
    <th>#</th>
    <th>IMG</th>
    <th>NAME</th>
    <th>EMP ID</th>
    <th>SEX</th>
    <th>QUAL.</th>
    <th>BIRTH DATE</th>
    <th>ADDRESS</th>
   	<th nowrap>ECN</th>
   	<th nowrap>MOBILE</th>
   	<th nowrap>EMAIL</th>
   	 <th>PAYMENT TYPE</th>
   	<th>EMPLOYEE TYPE</th>
   	<th>LOGIN</th>
   </tr>
  <thead>
  <tbody>
   <?php
   $pageNum = 1;
$rowsPerPage=20;
$sqlWhere="";

	if(makeSafe(isset($_POST['txtPage'])))	    $pageNum = makeSafe($_POST['txtPage']);
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($offset<0) $offset=0;
	$i=0;
		$sql="select employee_department.department_name,employee.empid,emp_id,activated,emp_name,sex,emp_qualification,br_name,emp_doj,emp_dob,emp_addr_pre,emp_ecn,emp_mob,email,mime,payment_type,superior";
		$sql .=" from employee,mst_designations,mst_branch,employee_department where employee.department_id=employee_department.department_id and employee.br_id=mst_branch.br_id and mst_designations.designation_id=employee.designation_id and mst_branch.br_id='".@$_SESSION['br_id']."'";
	if($department_id>0)
	$sqlWhere =" and employee.department_id=".$department_id." ";
			
		$sqlOrder =" order by employee.date_updated desc";
		$limit=" LIMIT $offset, $rowsPerPage ";
		 $sql=$sql." ".$sqlWhere.$sqlOrder." ".$limit;
		
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			while($row=mysql_fetch_array($res))
		{
		$i=$i+1;
	?>

	<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>  onclick="selectID('<?php echo $row['empid']; ?>')" >
	 <td align="center"><input type="radio" name="rdoID" value="<?php echo $row['empid']; ?>" id="rdoID" /></td>
		<td align="center"><img src="<?php echo is_file("../site_img/emppic/".DOMAIN_IDENTIFIER."_".base64_encode($row['empid']).".".$row['mime']) ? "../site_img/emppic/".DOMAIN_IDENTIFIER."_".base64_encode($row['empid']).".".$row['mime'] : "../images/".DEFAULT_IMAGE;?>" width="20px" /></td>
		<td><?php echo $row['emp_name']; ?></td>
		<td align="center"><?php echo $row['emp_id']; ?></td>
		<td align="center"><?php echo $row['sex']; ?></td>
		<td align="center"><?php echo $row['emp_qualification']; ?></td>
		<td align="center"><?php echo date("jS-M-Y",strtotime($row['emp_dob'])); ?></td>
		<td><?php echo $row['emp_addr_pre']; ?></td>
		<td align="center"><?php echo $row['emp_ecn']; ?></td>
		<td align="center"><?php echo $row['emp_mob']; ?></td>
		<td align="center"><?php echo $row['email']; ?></td>
		<td align="center"><?php echo $row['payment_type']; ?></td>
<td align="center"><?php echo $row['department_name'];?> - <?php if($row['superior']=='S') echo 'Head'; else echo 'Employee'; ?></td><td align="center"><span id="pub<?php echo $row['empid'];?>"><a href ="javascript:Publish('<?php if($row['activated']=='Y') echo 'N'; else echo 'Y';?>',<?php echo $row['empid'];?>,'pub')"><img src="<?php if($row["activated"]=="Y") echo "../images/publish.png"; else  echo "../images/publish_x.png";?>"/></a></span></td>
	</tr>
	<?php
	}
	?> 
  </tbody>
    <tfoot> <tr>
<th colspan="15">
	<?php 
	$sql="SELECT count(emp_id) as numrows from employee,mst_designations,mst_branch,employee_department where employee.department_id=employee_department.department_id and employee.br_id=mst_branch.br_id and mst_designations.designation_id=employee.designation_id and mst_branch.br_id='".@$_SESSION['br_id']."'";
	 $sql=$sql." ".$sqlWhere;		
	$result  = mysql_query($sql) or die('Error, query failed');
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
	$numrows = $row['numrows'];
	if($numrows>0){
	$maxPage = ceil($numrows/$rowsPerPage);
	echo "TOTAL : ".$numrows." | PAGE(S) : ".$maxPage;
	$nav  = '';
	for($page =$pageNum-4;  $page <= $pageNum+4 ; $page++)
	{
	if($page>0)
   if ($page == $pageNum)
      $nav .= " $page "; // no need to create a link to current page
    else
      $nav .= " <a class='mylink' href=\"javascript:SubmitPage('$page')\">$page</a> ";
    if($page>=$maxPage) break;
}

if ($pageNum > 1)
{
   $page  = $pageNum - 1;
   $prev  = " <a class='mylink' href=\"javascript:SubmitPage('$page')\" >[Prev]</a> ";

   $first = " <a class='mylink' href=\"javascript:SubmitPage('1')\" >[First Page]</a> ";
} 
else
{
   $prev  = '&nbsp;'; // we're on page one, don't print previous link
   $first = '&nbsp;'; // nor the first page link
}

if ($pageNum < $maxPage)
{
   $page = $pageNum + 1;
   $next = " <a class='mylink' href=\"javascript:SubmitPage('$page')\" >[Next]</a> ";

   $last = " <a class='mylink' href=\"javascript:SubmitPage('$maxPage')\" >[Last Page]</a> ";
} 
else
{
   $next = '&nbsp;'; // we're on the last page, don't print next link
   $last = '&nbsp;'; // nor the last page link
}

echo $first . $prev . $nav . $next . $last;
echo "";
}
else 
	echo "NO RECORDS FOUND";
?>

<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>'/>
</b></th></tr>
</tfoot>
</table>
<input type="hidden" name="empID" id="empID" value="" />
</form>
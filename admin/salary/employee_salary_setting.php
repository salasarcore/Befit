<?php 
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_employee_salary_setting,$modules)){ 
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_employee_salary_setting))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}
 */
//$br_id=makeSafe(@$_POST['branches']);
?>

<script>
function SubmitPage(Page)
{
document.getElementById("txtPage").value=Page;
document.myForm.submit();
}
function ActionScript(act)
{
	 var checkValues = $('input[name=rdoID]:checked').map(function()
            {
                return $(this).val();
            }).get();
     if($.trim(checkValues)==""){
    	 alert("Please select an employee.");
 		return false;
     	}
 	else if(act=="view")
	{
		url="salary/popups/view_emp_sal_sett.php?act="+act+"&empID="+checkValues;
		open_modal(url,600,650,"EMPLOYEE SALARY DETAILS")
		return true;
	}
     else{
	    url="salary/popups/employee_salary_setting.php?act="+act+"&empID="+checkValues;
	    open_modal(url,550,300,"EMPLOYEE SALARY SETTING")
		return true;
     }
}
function selectallid()
{
	var elms=document.getElementsByName("rdoID");
	if($('#selectall').is(':checked')) {
		for (i=0;i<elms.length;i++){
			if (elms[i].type="checkbox" ){
				elms[i].checked = true;
				}
			}
	}
	else{
		for (i=0;i<elms.length;i++){
			if (elms[i].type="checkbox" ){
				elms[i].checked = false;
				
				}
			}
	}
}

function getSelected()
{
	var elms=document.getElementsByName("rdoID");
	for (i=0;i<elms.length;i++){
		if (elms[i].type="checkbox" ){
			if(!$('#'+elms[i].value).is(':checked')) {
				$('#selectall').attr('checked',false);
			}
		}
}
}


</script>

<div class="page_head">
<div id="navigation"><a href="pages.php">Home</a><a> Payroll</a>  <span style="color: #000000;">Employee Salary Setting List</span> </div>

<form name="myForm" id="myForm" action="pages.php?src=salary/employee_salary_setting.php" method="POST">

<table width="100%" class="adminform1">
<tr>
	<td><h2>Employee Salary Setting List</h2></td>
	<td>
	<div id="option_menu">
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('sett');">Salary Setting</a>
		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('view');">View Salary Setting</a>
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
<table width="100%" cellspacing="1" class="table table-bordered"" style="cursor: pointer;">
  <thead>		
   <tr>
    <th><input type="checkbox" id="selectall" name="selectall" value="" onclick="javascript:selectallid();"/></th>
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
		$sql .=" from employee,mst_designations,mst_branch,employee_department where employee.department_id=employee_department.department_id and employee.br_id=mst_branch.br_id and mst_designations.designation_id=employee.designation_id and employee.activated='Y'  and mst_branch.br_id='".@$_SESSION['br_id']."'";
	if($department_id>0)
	$sqlWhere =" and employee.department_id=".$department_id." ";
			
		$sqlOrder =" order by empid desc";
		$limit=" LIMIT $offset, $rowsPerPage ";
		 $sql=$sql." ".$sqlWhere.$sqlOrder." ".$limit;

		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			while($row=mysql_fetch_array($res))
		{
		$i=$i+1;
	?>

	<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>  onclick="getSelected();"  >
	 <td align="center"><input type="checkbox" id="<?php echo $row['empid'];?>" name="rdoID" value="<?php echo $row['empid'];?>" onclick="getSelected();"/></td>
		<td align="center"><img src="../site_img/emppic/<?php echo base64_encode($row['empid']).".".$row['mime']; ?>" width="20px" /></td>
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
<td align="center"><?php echo $row['department_name'];?> - <?php if($row['superior']=='S') echo 'Head'; else echo 'Employee'; ?></td>
	</tr>
	<?php
	}
	?> 
  </tbody>
  <tfoot>
    <tfoot> <tr>
<th colspan="15"><b>
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
<input type="hidden" name="empID[]" id="empID[]" value="" />
</form>
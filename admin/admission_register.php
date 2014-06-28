<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_admission_register,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if(($level!='Super Admin')&&($level!='Admin'))
	{
		if(!isAccessModule($id_admin, module_admission_register))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
	if(isAccessModule($id_admin, export_excel_student_admission))
		$hasexcelpermission = true;
	else
		$hasexcelpermission = false;
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}*/
?>
<script>
function selectID(objChk)
{
document.getElementById("studentID").value=objChk;
}
function SubmitPage(Page)
{

document.getElementById("txtPage").value=Page;
document.records.submit();
}
function ActionScriptImg()
{
	if(document.getElementById("studentID").value=="")
	  alert("Please select a Applicant");
	else
	{
		url="popups/student_image.php?studentID="+document.getElementById("studentID").value;
		open_modal(url,440,200,"IMAGE")
		return true;
	  }
}
function ActionScript(act)
{
	if(document.getElementById("studentID").value=="" && act !="add")
	  alert("Please select a Student");
	else  if(act=="edit")
	{
		url="popups/edit_student.php?act="+act+"&studentID="+document.getElementById("studentID").value;
		open_modal(url,800,600,"ADMISSION")
		return true;
	  }
	else if(act=="view")
	{
		url="popups/studerailview.php?act="+act+"&studentID="+document.getElementById("studentID").value;
		open_modal(url,730,550,"VIEW APPLICANT DETAILS")
		return true;
	}
}
function validatefilter()
{
	if(document.records.txtFilter.value.trim()=="")
	{
		alert('Please Specify Filter Criteria');
		document.records.txtFilter.value="";
		document.records.txtFilter.focus();
		return false;
	}
}
function ExportExcel()
{
	url = 'stuAdmissionToExcel.php';
	window.open(url, '_blank');
}
</script>
<?php
$filter=makeSafe(@$_POST['txtFilter']);
$department_id=makeSafe(@$_POST['department']);
?>
<div class="page_head">
<div id="navigation"><a href="pages.php"> Home</a><a> Admission</a><span style="color: #000000;"> Registered Applications</span></div>


<form name="records" action="pages.php?src=admission_register.php" method="POST" onsubmit="return validatefilter();">
<table width="100%" class="adminform1">
<tr>
	<td>
			<h2>Registered Applicants</h2>
	</td>
	<td>
		<div id="option_menu">
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScriptImg();">Upload Image</a>
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit Registration</a>
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('view');">View Details</a>
			
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ExportExcel();">Export Excel</a>
	
		</div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">
Name/Registration Number :<input type="text" name="txtFilter" value="">
			<input type="submit" name="btnGo" value="Go" class="btn btn-info">
</div>
<input type="hidden" name="studentID" id="studentID" value="" />

<br />
<table width="100%" border="0" class="table table-bordered"" style="cursor: pointer;">
  <thead>
  <tr>
    <th>#</th>
    <th>IMG</th>
    <th>REGISTRATION NO</th>
	<th>NAME</th>
    <th>DOB</th>
	<th>PRESENT ADDRESS</th>
	<th>MOBILE</th>
    <th>UPDATED BY <br>Employee Name[Emp_Code]</th>
  </tr>
  <thead>
  <tbody>
  <?php
	$pageNum = 1;
$rowsPerPage=20;

	if(makeSafe(isset($_POST['txtPage'])))	    $pageNum = makeSafe($_POST['txtPage']);
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($offset<0) $offset=0;
    $sql="SELECT  stu_id,reg_no,concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name,sex,present_address,mob,DATE_FORMAT(dob,'%d-%b-%Y') as dob,mime,updated_by from mst_students ";
	$sqlWhere=" WHERE (concat_ws(' ',stu_fname,stu_mname,stu_lname) like'%".$filter."%' or reg_no like'%".$filter."%') and br_id=".$_SESSION['br_id']." order by date_updated desc";
	$limit=" LIMIT $offset, $rowsPerPage ";
	$sql=$sql." ".$sqlWhere.$limit;
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		$i=0;
		while($row=mysql_fetch_array($res))
		{
		$i=$i+1;
		?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo $row['stu_id']; ?>')" >
	    <td><input type="radio" name="rdoID" value="<?php echo $row['stu_id']; ?>" id="rdoID" /></td>
		<td><img src="<?php echo is_file("../site_img/stuimg/".DOMAIN_IDENTIFIER."_".base64_encode($row['stu_id']).".".$row['mime']) ? "../site_img/stuimg/".DOMAIN_IDENTIFIER."_".base64_encode($row['stu_id']).".".$row['mime'] : "../images/".DEFAULT_IMAGE; ?>" width="30px" /></td>
		<td>&nbsp;<?php echo $row['reg_no'];?></td>
		<td>&nbsp;<?php echo $row['stu_name'];?></td>
		<td>&nbsp;<?php echo date("jS-M-Y",strtotime($row['dob']));?></td>
		<td>&nbsp;<?php echo $row['present_address'];?></td>
		<td>&nbsp;<?php echo $row['mob'];?></td>
		<td>&nbsp;<?php echo $row['updated_by'];?></td>
	  </tr>
	  <?php
	  }
	  ?>
	  </tbody><tfoot>
<th colspan="10"><b>
	<?php 
$sql="SELECT count(reg_no) as numrows FROM mst_students ";
 $sql=$sql." ".$sqlWhere;		
$result  = mysql_query($sql) or die('Error, query failed');
$row     = mysql_fetch_array($result, MYSQL_ASSOC);
$numrows = $row['numrows'];
			

// how many pages we have when using paging?
$maxPage = ceil($numrows/$rowsPerPage);
echo "TOTAL STUDENT :".$numrows." | PAGE(S) : ".$maxPage;

// print the link to access each page
$nav  = '';
//for($page = 1; $page <= $maxPage; $page++)
for($page =$pageNum-4;  $page <= $pageNum+4 ; $page++)
{
if($page>0)
   if ($page == $pageNum)
   {
      $nav .= " $page "; // no need to create a link to current page
   }
   else
   {
   
      $nav .= " <a class='mylink' href=\"javascript:SubmitPage('$page')\">$page</a> ";
   } 
   if($page>=$maxPage) break;
}

// creating previous and next link
// plus the link to go straight to
// the first and last page

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
?>

<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>'/>
</b></th></tr>
</tfoot>

</table>
<script>
document.records.txtFilter.focus();
</script>

<div style="clear:both"></div>
</form>	

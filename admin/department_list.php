<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_department_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if(($level!='Super Admin') && ($level!='Admin'))
	{
		if(!isAccessModule($id_admin, module_department_list))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}*/
?>
<script>
function SubmitPage(Page)
{

document.getElementById("txtPage").value=Page;
document.records.submit();
}
function selectID(objChk)
{
document.getElementById("departmentID").value=objChk;

if (objChk.checked==true)
	 document.getElementById("txtAction").value="add";
else
	 document.getElementById("txtAction").value="remove";
}
function ActionScript(act)
{
	if(document.getElementById("departmentID").value=="" && act !="add")
	{
	  alert("Please select a Course");
	  
	}
	else
	{
		url="popups/department.php?act="+act+"&departmentID="+document.getElementById("departmentID").value;
		open_modal(url,740,550,"COURSE")
		return true;
	  }
}
function validatedept(frm)
{
	  if(frm.txtFilter.value.trim()=="")
		{
			alert('Enter Department Name');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
}
</script>
<?php
$name_filter= makeSafe(@$_POST['txtFilter']);

?>
<div class="page_head">
<div id="navigation"><a href="pages.php"> Home</a><a> Administration</a> <span style="color: #000000;">Course Master List</span> </div>

	
<form name="records" action="pages.php?src=department_list.php" method="POST" onsubmit="return validatedept(this);">
<table width="100%" cellspacing="1" class="adminform1">
<tr>
	<td><h2>Course Master List</h2>
	</td>
	<td align="right">
	<div id="option_menu">
	<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
	<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
	<a  class="btn btn-danger" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>

</div>
	</td>
</tr>
</table>
</div>
<div class="search_bar">
Enter Course Name: <input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" /> 
	<input type="submit" name="btnGo" value="Go" class="btn btn-info">
</div>
<br>
<table width="100%" border="0" class="table table-bordered"" style="cursor: pointer;">
  <thead>
  <tr>
    <th>#</th>
    <th>COURSE NAME</th>
    <th>COURSE CODE</th>
    <th>ABOUT COURSE</th>
    <th>COURSE LAST UPDATED</th>
    <th>UPDATED BY</th>
  </tr>
  <thead>
  <tbody>
  <?php
  
	// by default we show first page
	$pageNum = 1;
$rowsPerPage=20;

	if(isset($_POST['txtPage']))	    $pageNum = makeSafe($_POST['txtPage']);
	// counting the offset
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($offset<0) $offset=0;
  
	$sql="SELECT  department_id, department_code, department_name, department_text, date_updated,updated_by from mst_departments";
	$sql=$sql." WHERE department_name LIKE '%".$name_filter."%' and br_id=".$_SESSION['br_id']." order by date_updated desc LIMIT $offset, $rowsPerPage";
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		$i=0;
		
		
		while($row=mysql_fetch_array($res))
		{
		
		$i=$i+1;
		
	?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1";?>  onClick="selectID('<?php echo $row['department_id'];?>')" >
	    <td align="center"><input type="radio" name="rdoID" value="<?php echo $row['department_id']; ?>" id="rdoID" /></td>
		<td>&nbsp;<?php echo$row['department_name'];?></td>
		<td>&nbsp;<?php echo$row['department_code'];?></td>
		<td>&nbsp;<?php echo$row['department_text'];?></td>
		<td>&nbsp;<?php echo date("jS-M-Y, g:i A",strtotime($row['date_updated']));?></td>
		<td>&nbsp;<?php echo$row['updated_by'];?></td>
		
	  </tr>
	  <?php
	  }
	  ?>
	  </tbody><tfoot>
<tr>
<th colspan="18">
<?php
$sql="SELECT  COUNT(department_id) as numrows from mst_departments  WHERE (department_name LIKE '%".$name_filter."%') and br_id=".$_SESSION['br_id']." ";
		$result  = mysql_query($sql) or die('Error, query failed');
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
	$numrows = $row['numrows'];
			

// how many pages we have when using paging?
$maxPage = ceil($numrows/$rowsPerPage);
echo "TOTAL RECORDS :".$numrows." | PAGE(S) : ".$maxPage;

// print the link to access each page
$self = $_SERVER['PHP_SELF'];
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
</th>
  </tr>
  </tfoot>
</table>
<script>
document.records.txtFilter.focus();
</script>

<div style="clear:both"></div>
<input type="hidden" name="txtPage" id="txtPage" value='<?php echo @$_POST['txtPage'];?>'/>
<input type="hidden" name="departmentID" id="departmentID" value="" />
<input type="hidden" name="txtAction" id="txtAction" value="" />
<input type="hidden" name="txtUpdateStr" id="txtUpdateStr" value="" />


</form>	
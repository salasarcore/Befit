<?php 
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_pay_allowances_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin' )
	{
		if(!isAccessModule($id_admin, module_pay_allowances_list))
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
?>

<script>
function selectID(objChk)
{
	document.getElementById("paID").value=objChk;
	
}
function SubmitPage(Page)
{

document.getElementById("txtPage").value=Page;
document.records.submit();
}
function ActionScript(act)
{
	if(document.getElementById("paID").value=="" && act !="add")
	{
	  alert("Please select a Pay & Allowances");
	  
	}
	else
	{
		url="salary/popups/pay_allowances.php?act="+act+"&paID="+document.getElementById("paID").value;
		open_modal(url,530,250,"PAY & ALLOWANCES")
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
</script>
<?php
$name_filter=trim(@$_POST['txtFilter']);

?>
<div class="page_head">
<div id="navigation"><a href="admin.php">Home</a><a> Payroll</a>  <span style="color: #000000;">Pay & Allowances List(All Branch)</span></div>

<form name="records" action="admin.php?src=salary/pay_allowances_list.php" method="POST" onsubmit="return validatefilter();">

<table width="100%" class="adminform1">
<tr>
	<td>
		<h2>Pay & Allowances List</h2>
	</td>
	<td>
		<div id="option_menu">
			<a  class="addnew" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
			<a  class="edit" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
			<a  class="delete" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>
           </div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">
Pay & Allowances Name :<input type="text" name="txtFilter" id="txtFilter" /> <input type="submit" name="btnGo" class="btn search" value="Go">
</div>
<br />
<table width="100%" border="0" class="adminlist" style="cursor: pointer;">
  <thead>
  <tr>
    <th>#</th>
    <th>PAY & ALLOWANCES NAME</th>
    <th>PAY & ALLOWANCES ABBR</th>
    <th>LAST UPDATED</th>
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
  
	$sql="SELECT ap_id, pa_abvr, pa_name, date_updated, updated_by from sal_pay_allowances";
	$sql=$sql." WHERE (pa_name LIKE '%".$name_filter."%') order by date_updated desc LIMIT $offset, $rowsPerPage";
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		$i=0;
		
		
		while($row=mysql_fetch_array($res))
		{
		
		$i=$i+1;
	?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>  onclick="selectID('<?php echo $row['ap_id']; ?>')"> 
	    <td align="center"><input type="radio" name="rdoID" value="<?php echo $row['ap_id']; ?>" id="rdoID" /></td>
		<td width="40%" style="word-break: break-all;">&nbsp;<?php echo $row['pa_name'];?></td>
		<td align="center"><?php echo$row['pa_abvr'];?></td>
		<td align="center"><?php echo date("jS-M-Y g:i A", strtotime($row['date_updated']));?></td>
		<td align="center"><?php echo$row['updated_by'];  ?></td>
		
	  </tr>
	  <?php
	  }  
	  $sql="SELECT  COUNT(ap_id) as numrows from sal_pay_allowances  WHERE (pa_name LIKE '%".$name_filter."%') order by pa_name ";
	  $result  = mysql_query($sql) or die('Error, query failed');
	  $row     = mysql_fetch_array($result, MYSQL_ASSOC);
	  $numrows = $row['numrows'];
	  ?>
	</tbody><tfoot>
<tr>
	  <?php
		if($numrows>0)
		{
	?>
<th colspan="15">
<?php
// how many pages we have when using paging?
$maxPage = ceil($numrows/$rowsPerPage);
echo "TOTAL RECORDS : ".$numrows." | PAGE(S) : ".$maxPage;

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
</th>
<?php } else echo "<th colspan=18>NO RECORDS FOUND</th>";?>

  </tr>
  </tfoot>
</table>
<script>
document.records.txtFilter.focus();
</script>

<div style="clear:both"></div>
<input type="hidden" name="txtPage" id="txtPage" value='<?php echo @$_POST['txtPage'];?>'/>
<input type="hidden" name="paID" id="paID" value="" />
</form>	
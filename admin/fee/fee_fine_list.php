<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_fee_fine_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin' )
	{
		if(!isAccessModule($id_admin, module_fee_fine_list))
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
document.getElementById("fineID").value=objChk;
}
function ActionScript(act)
{
	if(document.getElementById("fineID").value=="" && act !="add")
	{
	  alert("Please select a Fee");
	}
	else
	{
		url="fee/popups/fee_fine.php?act="+act+"&fineID="+document.getElementById("fineID").value;
		open_modal(url,500,380,"FEE FINE")
		return true;
	}
}

function valfilter()
{
	var name=document.records.txtFilter.value;
	if(name.trim()=="")
	{
		alert('Please specify some filter criteria to search');
		document.records.txtFilter.value="";
		document.records.txtFilter.focus();
		return false;
	}
	return true;
}
</script>
<?php
$name_filter=makeSafe(@$_POST['txtFilter']);

?>
<div class="page_head">
<div id="navigation"><a href="pages.php"> Home</a><a> Fees</a>  <span style="color: #000000;">Fees Fine </span></div>

<form name="records" action="pages.php?src=fee/fee_fine_list.php" method="POST" onsubmit="return valfilter();">

<table width="100%" class="adminform1">
<tr>
	<td>
		<h2>Fees Fine </h2>
	</td>
	<td>
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
Fee Fine Name:<input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" /> <input type="submit" name="btnGo" value="Go" class="btn btn-info">
</div>
<br />
<table width="100%" class="table table-bordered" style="cursor: pointer;">
  <thead>
  <tr>
    <th>#</th>
    <th>FINE NAME</th>
    <th>PARTICULAR NAME</th>
    <th>FINE AMOUNT</th>
    <th>CREATED AT</th>
    <th>UPDATED AT</th>
    <th>UPDATED BY</th>
  </tr>
  <thead>
  <tbody>
  <?php
  
	// by default we show first page
	$pageNum = 1;
$rowsPerPage=20;
	// if $_GET['page'] defined, use it as page number
	if(isset($_POST['txtPage']))
	{
	    $pageNum = makeSafe($_POST['txtPage']);
	}
	
	// counting the offset
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($offset<0) $offset=0;
  
	$sql="SELECT fd.*,fp.name as particular_name from fee_fine_master fd,fee_particulars fp where fd.fee_particulars_id=fp.fee_particulars_id and fp.fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")";
	if(@$name_filter!="")
	$sql=$sql." and fd.fine_name LIKE '%".$name_filter."%'";
	$sql=$sql." order by fd.updated_at desc LIMIT $offset, $rowsPerPage";
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		$i=0;
		
		
		while($row=mysql_fetch_array($res))
		{
		
		$i=$i+1;
	?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo $row['fine_id']; ?>')" >
	    <td align="center" width="50px"><input type="radio" name="rdoID" value="<?php echo $row['fine_id']; ?>" id="rdoID" /></td>
	   <td align="left"><?php echo $row['fine_name'];?></td>
	   <td align="center"><?php echo $row['particular_name'];?></td>
		<td align="center"><?php echo $row['fine_amount'];?></td>
		<td align="center"><?php echo date("jS-M-Y, g:iA",strtotime($row['created_at']));?></td>
		<td align="center"><?php echo date("jS-M-Y, g:iA",strtotime($row['updated_at']));?></td>
		<td align="center"><?php echo $row['updated_by'];?></td>
		
		
	  </tr>
	  <?php
	  }
	  ?>
	  </tbody><tfoot>
<tr>
<th colspan="18">
<?php
$sql="SELECT  COUNT(fine_id) as numrows from fee_fine_master fd,fee_particulars fp where fd.fee_particulars_id=fp.fee_particulars_id and fp.fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")";
	if(@$name_filter!="")
	$sql=$sql." and fd.fine_name LIKE '%".$name_filter."%'";
	$sql=$sql." order by fd.fine_name";
		$result  = mysql_query($sql) or die('Error, query failed');
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
	$numrows = $row['numrows'];
			
if($numrows>0){
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
echo "";}
else
	echo "NO RECORDS FOUND";
?>
</th>
  </tr>
  </tfoot>
</table>
<script>
document.records.txtFilter.focus();
</script>

<div style="clear:both"></div>
<input type="hidden" name="fineID" id="fineID" value="" />
<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>' />
</form>	
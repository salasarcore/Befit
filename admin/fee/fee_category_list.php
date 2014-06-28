<?php
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_fee_category_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin' )
	{
		if(!isAccessModule($id_admin, module_fee_category_list))
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
document.getElementById("feeID").value=objChk;
}
function ActionScript(act)
{
	if(document.getElementById("feeID").value=="" && act !="add")
	  	alert("Please select a Fee Category");
	else if(act=="addsubcat")
		location.href="pages.php?src=fee/fee_particulars_list.php&feeID="+document.getElementById("feeID").value;
	else
	{
		url="fee/popups/fee_category.php?act="+act+"&feeID="+document.getElementById("feeID").value;
		open_modal(url,530,320,"FEE CATEGORY")
		return true;
	}
}
function SubmitPage(Page)
{
document.getElementById("txtPage").value=Page;
document.fee.submit();
}
function validatefeecat(frm)
{
	  if(frm.txtFilter.value.trim()=="")
		{
			alert('Enter Fee Category Name');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="pages.php"> Home</a><a> Fee</a>  <span style="color: #000000;">Fee Category List</span>
</div>


<form name="fee" action="pages.php?src=fee/fee_category_list.php" method="POST" onsubmit="return validatefeecat(this);">
	<table width="100%" class="adminform1">
		<tr>
			<td>
		<h2>Fee Category List</h2>
			</td>
			<td>
				<div id="option_menu">
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
					<a class="btn btn-danger" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>
					<a class="btn btn-info" href="javascript:void(0);" onclick="javascript:ActionScript('addsubcat');">Add New Fee Particulars</a>
				</div>
			</td>
		</tr>
	</table>
	</div>
	<div class="search_bar">
		<?php  $name_filter= makeSafe(@$_POST['txtFilter']); ?>
			Fee Category Name : <input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" maxlength="200" />
			<input type="submit" name="btnGo" value="Go" class="btn btn-info">
	</div>
	<br />
	<table width="100%" border="0" class="table table-bordered" style="cursor: pointer;table-layout: fixed;">
		<thead>
			<tr>
				<th width="4%">#</th>
				<th width="20%">FEE CATEGORY NAME</th>
				<th width="31%">DESCRIPTION</th>
				<th width="15%">CREATED AT</th>
				<th width="15%">UPDATED AT</th>
				<th width="15%">UPDATED BY <br>Employee Name[Emp_Code]</th>
			</tr>
		</thead>
		<tbody>
			<?php

			// by default we show first page
			$pageNum = 1;
			$rowsPerPage=20;

			if(makeSafe(isset($_POST['txtPage'])))	    $pageNum = makeSafe($_POST['txtPage']);
			// counting the offset
			$offset = ($pageNum - 1) * $rowsPerPage;
			if($offset<0) $offset=0;

			$sql="SELECT * from fee_categories WHERE br_id=".$_SESSION['br_id'];
			$sql=$sql." and name LIKE '%".$name_filter."%' order by updated_at desc LIMIT $offset, $rowsPerPage";
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$i=0;


			while($row=mysql_fetch_array($res))
			{

				$i=$i+1;
				?>
			<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo base64_encode($row['fee_categories_id']);?>')">
				<td align="center"><input type="radio" name="rdoID" value="<?php echo $row['fee_categories_id']; ?>" id="rdoID" /></td>
				<td style="word-wrap: break-word;"><?php echo $row['name'];?></td>
				<td style="word-wrap: break-word;"><?php echo $row['description'];?></td>
				<td align="center">&nbsp;<?php echo date("jS-M-Y, g:iA",strtotime($row['created_at']));?></td>
				<td align="center">&nbsp;<?php echo date("jS-M-Y, g:iA",strtotime($row['updated_at']));?></td>
				<td align="center">&nbsp;<?php echo $row['updated_by'];?></td>
			</tr>
			<?php
	  }
	  ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6"><?php
				$sql="SELECT  COUNT(fee_categories_id) as numrows from fee_categories  WHERE (name LIKE '%".$name_filter."%') and br_id=".$_SESSION['br_id']." order by name ";
				$result  = mysql_query($sql) or die('Error, query failed');
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				$numrows = $row['numrows'];
				if($numrows>0){

					// how many pages we have when using paging?
					$maxPage = ceil($numrows/$rowsPerPage);
					echo "TOTAL RECORDS :".$numrows." | PAGE(S) : ".$maxPage;

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
				}
				else
					echo "NO RECORDS FOUND";
				?></th>
			</tr>
		</tfoot>
	</table>
	<div style="clear: both"></div>
	<input type="hidden" name="feeID" id="feeID" value="" />
	<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>' />
</form>
<script>
document.fee.txtFilter.focus();
</script>

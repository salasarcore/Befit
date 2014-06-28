<?php
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_fee_particulars_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin' )
	{
		if(!isAccessModule($id_admin, module_fee_particulars_list))
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
function selectID(objChk)
{
document.getElementById("feeID").value=objChk;
}
function ActionScript(act)
{
	if(document.getElementById("feeID").value=="" && act !="add")
	  	alert("Please select a Fee Particulars");
	else
	{
		url="fee/popups/fee_particulars.php?act="+act+"&feeID="+document.getElementById("feeID").value+"&catID="+document.getElementById("catID").value;
		open_modal(url,530,350,"FEE PARTICULAR")
		return true;
	}
}
function SubmitPage(Page)
{
document.getElementById("txtPage").value=Page;
document.fee.submit();
}
function validatefeeparticular(frm)
{
	  if(frm.txtFilter.value.trim()=="")
		{
			alert('Enter Fee Particular Name');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
}
</script>

<div id="navigation">
	<a href="pages.php"> Home</a><a> Fee</a>
	<a  href="pages.php?src=fee/fee_category_list.php"> Fee Category List</a> <span style="color: #000000;"> Fee Particular List</span>
</div>
<h2>Fee Particular List</h2>
<br>
<?php 
if(makeSafe(isset($_GET['feeID'])) && makeSafe($_GET['feeID'])!="") 
{
	$catID=base64_decode(makeSafe($_GET['feeID']));
	$sql="select * from fee_categories where fee_categories_id=".@$catID." and br_id=".$_SESSION['br_id'];
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	if(mysql_num_rows($res)>0)
	{
		$results=mysql_fetch_array($res);
	

?>
<form name="fee" action="pages.php?src=fee/fee_particulars_list.php&feeID=<?php echo makeSafe($_GET['feeID']); ?>" method="POST" onsubmit="return validatefeeparticular(this);">
	<table width="100%" class="adminform1">
		<tr>
			<td>
			<?php  $name_filter= makeSafe(@$_POST['txtFilter']); ?>
			Fee Particular Name :<input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" maxlength="200" />
			<input type="submit" name="btnGo" value="Go" class="btn btn-info">
			</td>
			<td>
				<div id="option_menu">
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
					<a class="btn btn-danger" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>
				</div>
			</td>
		</tr>
	</table>
	<br />
	<table width="100%" class="table table-bordered" style="cursor: pointer;table-layout: fixed;">
		<thead>
		<tr>
			<th colspan="7" style="word-wrap: break-word;">MAIN CATEGORY NAME : <?php echo strtoupper($results['name']); ?></th>
			</tr>
			<tr>
				<th width="4%">#</th>
				<th width="15%">FEE PARTICULAR NAME</th>
				<th width="20%">DESCRIPTION</th>
				<th width="16%">AMOUNT</th>
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

			if(makeSafe(isset($_POST['txtPage']))) $pageNum = makeSafe($_POST['txtPage']);
			// counting the offset
			$offset = ($pageNum - 1) * $rowsPerPage;
			if($offset<0) $offset=0;

			$sql="SELECT * from fee_particulars where fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id']." and fee_categories_id=".@$catID.")";
			$sql=$sql." and name LIKE '%".$name_filter."%' order by name LIMIT $offset, $rowsPerPage";
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$i=0;


			while($row=mysql_fetch_array($res))
			{

				$i=$i+1;
				?>
			<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo base64_encode($row['fee_particulars_id']);?>')">
				<td align="center"><input type="radio" name="rdoID" value="<?php echo $row['fee_particulars_id']; ?>" id="rdoID" /></td>
				<td style="word-wrap: break-word;"><?php echo $row['name'];?></td>
				<td style="word-wrap: break-word;"><?php echo $row['description'];?></td>
				<td align="center">&nbsp;<?php echo $row['total_amount'];?></td>
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
				<th colspan="7"><?php
				$sql="SELECT  COUNT(fee_particulars_id) as numrows from fee_particulars  WHERE fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id']." and fee_categories_id=".@$catID.") and name LIKE '%".$name_filter."%' order by name ";
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
	<input type="hidden" name="catID" id="catID" value="<?php echo makeSafe($_GET['feeID']); ?>" />
	<input type="hidden" name="txtAction" id="txtAction" value="" />
	<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>' />
</form>
<script>
document.fee.txtFilter.focus();
</script>
<?php 
	}
	else
		echo "<b>INVALID FEE CATEGORY SELECTED.</b>";
}
else
	echo "<b>PLEASE SELECT FEE CATEGORY TO VIEW ITS PARTICULARS.</b>";

?>
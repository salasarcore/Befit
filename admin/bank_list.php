<?php 
include_once("functions/common.php");

/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_bank_list,$modules)){ 
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if(($level!='Super Admin') && ($level!='Admin')){
		if(!isAccessModule($id_admin,module_bank_list)){
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
document.getElementById("bankID").value=objChk;
}
function ActionScript(act)
{
	if(document.getElementById("bankID").value=="" && act !="add")
	  	alert("Please select a bank");
	else
	{
		url="popups/add_bank.php?act="+act+"&bankID="+document.getElementById("bankID").value;
		open_modal(url,530,320,"BANK DETAILS")
		return true;
	}
}
function SubmitPage(Page)
{
document.getElementById("txtPage").value=Page;
document.bank.submit();
}
function validatebank(frm)
{
	  if(frm.txtFilter.value.trim()=="")
		{
			alert('Enter Bank Name');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="pages.php"> Home</a><a> Administration</a>  <span style="color: #000000;">Bank List</span>
</div>

<form name="bank" action="pages.php?src=bank_list.php" method="POST" onsubmit="return validatebank(this);">
	<table width="100%" class="adminform1">
		<tr>
			<td>
		<h2>Bank List</h2>
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
	</div>
	<div class="search_bar">
		<?php  $name_filter= makeSafe(@$_POST['txtFilter']); ?>
			Bank Name : <input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" maxlength="200" />
			<input type="submit" name="btnGo" value="Go" class="btn btn-info">
	</div>
	<br />
	<table width="100%" border="0" class="table table-bordered"" style="cursor: pointer;table-layout: fixed;">
		<thead>
			<tr>
				<th width="4%">#</th>
				<th width="20%">BANK NAME</th>
				<th width="31%">ACCOUNT NUMBER</th>
				<th width="31%">IFSC CODE</th>
				<th width="31%">BRANCH NAME</th>
				<th width="15%">CREATED ON</th>
				<th width="15%">UPDATED ON</th>
				<th width="15%">UPDATED BY</th>
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

			$sql="SELECT * from bank_master";
			$sql=$sql." where bank_name LIKE '%".$name_filter."%' order by updated_on desc LIMIT $offset, $rowsPerPage";
			$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused1");
			$i=0;


			while($row=mysql_fetch_array($res))
			{

				$i=$i+1;
				?>
			<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo base64_encode($row['bank_id']);?>')">
				<td align="center"><input type="radio" name="rdoID" value="<?php echo $row['bank_id']; ?>" id="rdoID" /></td>
				<td style="word-wrap: break-word;"><?php echo $row['bank_name'];?></td>
				<td style="word-wrap: break-word;"><?php echo $row['account_number'];?></td>
				<td style="word-wrap: break-word;"><?php echo $row['ifsc_code'];?></td>
				<td style="word-wrap: break-word;"><?php echo $row['branch_name'];?></td>
				<td align="center">&nbsp;<?php echo date("jS-M-Y, g:iA",strtotime($row['created_on']));?></td>
				<td align="center">&nbsp;<?php echo date("jS-M-Y, g:iA",strtotime($row['updated_on']));?></td>
				<td align="center">&nbsp;<?php echo $row['updated_by'];?></td>
			</tr>
			<?php
	  }
	  ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="8"><?php
				$sql="SELECT  COUNT(bank_id) as numrows from bank_master  WHERE bank_name LIKE '%".$name_filter."%'";
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
	<input type="hidden" name="bankID" id="bankID" value="" />
	<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>' />
</form>
<script>
document.bank.txtFilter.focus();
</script>
<?php
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_expense_category_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_expense_category_list))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
} */
?>
<script>
function selectID(objChk)
{
	document.getElementById("exp_cat_id").value=objChk;
}
function ActionScript(act)
{
	if(document.getElementById("exp_cat_id").value=="" && act !="add")
	{
	  	alert("Please select a Expense Category");
	}
	else if(act=="expensemaster")
		location.href="pages.php?src=expense/expense_master_list.php&exp_cat_id="+document.getElementById("exp_cat_id").value;
	else
	{
		url="expense/popups/expense_category.php?act="+act+"&exp_cat_id="+document.getElementById("exp_cat_id").value;
		open_modal(url,530,250,"EXPENSE CATEGORY")
		return true;
	}
}
function SubmitPage(Page)
{
	document.getElementById("txtPage").value=Page;
	document.expense.submit();
}
function validateexpense(frm)
{
	  if(frm.txtFilter.value.trim()=="")
		{
			alert('Enter Expense Category Name');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="admin.php"> Home</a><a> Expense</a>  <span style="color: #000000;">Expense Category List</span>
</div>

<br>
<form name="expense" action="admin.php?src=expense/expense_category_list.php"	method="POST" onsubmit="return validateexpense(this);">
	<table width="100%" class="adminform1">
		<tr>
			<td>
			<h2>Expense Category List</h2>
			</td>
			<td>
				<div id="option_menu">
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
					<a class="btn btn-danger" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('expensemaster');">Add Expense Master</a>
				</div>
			</td>
		</tr>
	</table>
	</div>
	<div class="search_bar">
	<?php  $name_filter= makeSafe(@$_POST['txtFilter']); ?>
			Expense Category Name :<input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" maxlength="200" />
			<input type="submit" name="btnGo" value="Go" class="btn btn-info">
	</div>
	<br />
	<table width="100%" border="0" class="table" style="cursor: pointer;table-layout: fixed;">
		<thead>
			<tr>
				<th>#</th>
				<th>EXPENSE CATEGORY NAME</th>
				<th>CREATED ON</th>
				<th>UPDATED ON</th>
				<th>UPDATED BY</th>
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

			$sql="SELECT  * from expense_category";
			$sql=$sql." WHERE name LIKE '%".$name_filter."%'  order by updated_at desc LIMIT $offset, $rowsPerPage";
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$i=0;

			while($row=mysql_fetch_array($res))
			{
				$i=$i+1;
				?>
			<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>	onclick="selectID('<?php echo base64_encode($row['exp_cat_id']);?>')">
				<td align="center"><input type="radio" name="rdoID" value="<?php echo base64_encode($row['exp_cat_id']); ?>" id="rdoID" /></td>
				<td style="word-wrap: break-word;"><?php echo $row['name'];?></td>
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
				<th colspan="5"><?php
				$sql="SELECT  COUNT(exp_cat_id) as numrows from expense_category WHERE name LIKE '%".$name_filter."%'  order by name ";
				$result  = mysql_query($sql) or die('Error, query failed');
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				$numrows = $row['numrows'];
				if($numrows>0){

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
				}
				else
					echo "NO RECORDS FOUND";
				?></th>
			</tr>
		</tfoot>
	</table>
	<div style="clear: both"></div>
	<input type="hidden" name="exp_cat_id" id="exp_cat_id" value="" />
<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>'/>
</form>
<script>
document.expense.txtFilter.focus();
</script>

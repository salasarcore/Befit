<?php
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_expense_master_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin' )
	{
		if(!isAccessModule($id_admin, module_expense_master_list))
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
document.getElementById("expense_id").value=objChk;
}
function ActionScript(act)
{
	if(document.getElementById("expense_id").value=="" && act !="add")
	  	alert("Please select a expense");
	else
	{
		url="expense/popups/expense_master.php?act="+act+"&expense_id="+document.getElementById("expense_id").value+"&exp_cat_id="+document.getElementById("exp_cat_id").value;
		open_modal(url,530,250,"EXPENSE MASTER")
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
	  if(frm.txtFilter.value.trim()=="" && frm.typeFilter.value.trim()=="")
		{ 
			alert('Enter Expense Name Or Type');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
	  return true; 
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="pages.php"> Home</a><a> Expense</a>
	<a  href="pages.php?src=expense/expense_category_list.php"> Expense Category List</a>  <span style="color: #000000;">Expense Master List</span>
</div>

<br>
<?php 
if(makeSafe(isset($_GET['exp_cat_id'])) && makeSafe($_GET['exp_cat_id'])!="") 
{
	$exp_cat_id=base64_decode(makeSafe($_GET['exp_cat_id']));
	$sql="select * from expense_category where exp_cat_id=".@$exp_cat_id;
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	if(mysql_num_rows($res)>0)
	{
		$results=mysql_fetch_array($res);
	

?>
<form name="expense" action="pages.php?src=expense/expense_master_list.php&exp_cat_id=<?php echo makeSafe(base64_encode($exp_cat_id)); ?>" method="POST" onsubmit="return validateexpense(this);">
	<table width="100%" class="adminform1">
		<tr>
			<td>
			<h2>Expense Master List</h2>
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
	<?php   $name_filter= makeSafe(@$_POST['txtFilter']);
			$type_Filter=makeSafe(@$_POST['typeFilter']); ?>
			Expense Name : <input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" maxlength="200" />
			Expense Type : 	<select name="typeFilter" id="typeFilter">
					<option value="">--Select Expense Type--</option>
					<option value="Fixed" <?php if(@$type_Filter=="Fixed") echo "selected"; ?> >Fixed</option>
					<option value="Floating" <?php if(@$type_Filter=="Floating") echo "selected"; ?> >Floating</option>
					</select>		
			
			<input type="submit" name="btnGo" value="Go" class="btn btn-info">
	</div>
	<br />
	<table width="100%" class="table" style="cursor: pointer;table-layout: fixed;">
		<thead>
		<tr>
			<th colspan="6" style="word-wrap: break-word;">MAIN EXPENSE CATEGORY NAME : <?php echo strtoupper($results['name']); ?></th>
			</tr>
			<tr>
				<th width="4%">#</th>
				<th width="15%">EXPENSE NAME</th>
				<th width="15%">EXPENSE TYPE</th>
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

			if(makeSafe(isset($_POST['txtPage']))) $pageNum = makeSafe($_POST['txtPage']);
			// counting the offset
			$offset = ($pageNum - 1) * $rowsPerPage;
			if($offset<0) $offset=0;
			
			$sql="SELECT * from expense_master where exp_cat_id in (select exp_cat_id from expense_category where  exp_cat_id=".@$exp_cat_id.")";
			$sql=$sql." and name LIKE '%".$name_filter."%'";
			if($type_Filter!="")
			{
			$sql = $sql."  and type='".$type_Filter."'";	
			}
			$off = " order by name LIMIT $offset, $rowsPerPage";
			$sql = $sql.$off;
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$i=0;


			while($row=mysql_fetch_array($res))
			{

				$i=$i+1;
				?>
			<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo base64_encode($row['expense_id']);?>')">
				<td align="center"><input type="radio" name="rdoID" value="<?php echo $row['expense_id']; ?>" id="rdoID" /></td>
				<td style="word-wrap: break-word;"><?php echo $row['name'];?></td>
				<td><?php echo $row['type']; ?></td>
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
				$sql="SELECT  COUNT(expense_id) as numrows from expense_master  WHERE exp_cat_id in (select exp_cat_id from expense_category where br_id=".$_SESSION['br_id']." and exp_cat_id=".@$exp_cat_id.") and name LIKE '%".$name_filter."%' ";
				if($type_Filter!="")
				{
					 $sql = $sql."  and type='".$type_Filter."'";
				}
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
	<input type="hidden" name="expense_id" id="expense_id" value="" />
	<input type="hidden" name="exp_cat_id" id="exp_cat_id" value="<?php echo makeSafe(@$_GET['exp_cat_id']); ?>" />
	<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>' />
</form>
<script>
document.expense.txtFilter.focus();
</script>
<?php 
	}
	else
		echo "<b>INVALID EXPENSE CATEGORY SELECTED.</b>";
}
else
	echo "<b>PLEASE SELECT EXPENSE CATEGORY TO VIEW ITS EXPENSE MASTERS.</b>";

?>
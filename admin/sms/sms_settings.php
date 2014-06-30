<?php
include_once("../functions/common.php");

/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 */
//include("modulemaster.php");
if(in_array(module_sms_settings,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_sms_settings))
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

?>
<script>

function SubmitPage(page)
{
	document.getElementById("txtPage").value=page;
	document.records.submit();
}
function selectID(sms_type_id)
{
	document.getElementById("sms_type_id").value=sms_type_id;
	$('.'+ sms_type_id).attr('checked',true);
}
function ActionScript(act)
{
	if(act == 'history')
	{
		url="sms/sms_purchase_history.php"
		open_modal(url,650,550,"SMS HISTORY")
		return true;
	}
	else if(act == 'edit' || act == 'delete')
	{
	if(document.getElementById("sms_type_id").value=="")
	{
		alert("Please select a module.");
		return false;
	}
	else
	{
		url="sms/popups/sms_settings_func.php?act="+act+"&sms_type_id="+document.getElementById("sms_type_id").value;
		open_modal(url,740,450,"SMS SETTINGS")
		return true;
	}
	}
	else
	{
		url="sms/popups/sms_settings_func.php?act="+act;
		open_modal(url,740,450,"SMS SETTINGS")
		return true;
	}
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="pages.php">Home</a><a> Utilities</a><a>SMS</a> <span style="color: #000000;">SMS Settings</span>
</div>

<form name="records" action="pages.php?src=sms/sms_settings.php"
	method="POST">
	<input type="hidden" name="fromID" id="fromID" value="" />

	<table width="100%">
		<tr>
		<td><h2>SMS Settings</h2></td>
			<td>
				<div id="option_menu">
					<a class="addnew" href="javascript:void(0);" onClick="javascript:ActionScript('history');">SMS HISTORY</a>
					<a class="addnew" href="javascript:void(0);" onClick="javascript:ActionScript('add');">ADD</a> 
					<a class="edit" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">EDIT</a>
					<a class="delete" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">DELETE</a>
				</div>
			</td>
		</tr>
	</table>
	</div>
	<br />
	<table width="100%" border="0" class="adminlist" style="cursor: pointer;">
		<thead>
			<tr>
				<th>#</th>
				<th>MODULE NAME</th>
				<th>SEND TYPE</th>
				<th>APPROVED ?</th>
				<th>TEMPLATE TYPE</th>
				<th>TEMPLATE</th>
				<th>CREATED ON</th>
				<th>DATE UPDATED</th>
				<th>UPDATED BY <br>Employee Name[Emp Code]</th>
			</tr>		
		<tbody>
			<?php
			// by default we show first page
			$pageNum = 1;
			$rowsPerPage=20;

			if(makeSafe(isset($_POST['txtPage'])))	    $pageNum = makeSafe($_POST['txtPage']);

			$offset = ($pageNum - 1) * $rowsPerPage;
			if($offset<0) $offset=0;

			$sqlWhere="";
			$sql = "SELECT * FROM sms_settings";
			$limit="order by date_updated desc LIMIT $offset, $rowsPerPage";
			$sql=$sql." ".$sqlWhere.$limit;
			$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$i=0;

			if(mysql_num_rows($res)>0)
			{
				while($row=mysql_fetch_array($res))
				{

					$i=$i+1;
					?>
			<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo $row['sms_type_id']; ?>')">
				<td style="text-align:center;"><input type="radio" name="rdoID" value="<?php echo $row['sms_type_id']; ?>" id="rdoID" /></td>
				<td style="text-align:center;"><?php echo $row['module_name'];?></td>
				<td style="text-align:center;"><?php if($row['send_type']=='A') echo 'AUTO'; else echo 'MANUAL';?></td>
				<td style="text-align:center;"><?php if($row['approved_status']=='Y') echo 'YES'; else echo 'NO'?></td>
				<td style="text-align:center;"><?php if($row['template_type']=='T') echo 'TRANSACTIONAL'; else echo 'PROMOTIONAL';?></td>
				<td style="text-align:center;"><?php echo $row['template'];?></td>
				<td style="text-align:center;"><?php echo date('jS-M-Y',strtotime($row['created_on']));?></td>
				<td style="text-align:center;"><?php echo date('jS-M-Y',strtotime($row['date_updated']));?></td>
				<td style="text-align:center;"><?php echo $row['updated_by'];?></td>
			</tr>
			<?php
				}
				?>
		</tbody>
		<tfoot>
		
			<th colspan="9"><input type="hidden" name="sms_type_id" id="sms_type_id" value='<?php echo $row['sms_type_id'];?>'/><?php 
			$sql="SELECT count(*) as numrows FROM sms_settings";
			$sql=$sql." ".$sqlWhere;
			$result  = mysql_query($sql,$link) or die('Error, query failed');
			$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			$numrows = $row['numrows'];


			// how many pages we have when using paging?
			$maxPage = ceil($numrows/$rowsPerPage);
			echo "TOTAL RECORDS : ".$numrows." | PAGE(S) : ".$maxPage;

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
						$nav .= "<a class='mylink' href=\"javascript:SubmitPage('$page')\">$page</a> ";
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
			{
				echo "<tr><th colspan='9'>NO RECORDS FOUND</th></tr>";?>
				<script>
				$('.edit').hide();
				$('.delete').hide();
				</script>			
			<?php }
			?><input type="hidden" name="rdID" id="rdID" value='' /> <input type="hidden" name="txtPage" id="txtPage" value='<?php echo @$_POST['txtPage'];?>' />
		</th>
		</tfoot>
	</table>
	<script>
</script>

	<div style="clear: both"></div>
</form>

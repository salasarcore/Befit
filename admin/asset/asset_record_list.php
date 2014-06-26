<?php
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_asset_record_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_asset_record_list))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
	if(isAccessModule($id_admin, export_excel_asset_records))
		$hasexcelpermission = true;
	else
		$hasexcelpermission = false;
}
else
{
	echo "<script>location.href='pages.php';</script>";
	exit;
}*/
include_once("functions/common.php");
?>
<script>
function selectID(objChk)
{
document.getElementById("assetrecID").value=objChk;
}
function ActionScript(act)
{
	if(document.getElementById("assetrecID").value=="" && act !="add")
	{
	  	alert("Please select a Asset");
	}
	else
	{
		url="asset/popups/asset_record.php?act="+act+"&assetrecID="+document.getElementById("assetrecID").value;
		open_modal(url,530,400,"ASSET RECORD")
		return true;
	}
}
function SubmitPage(Page)
{

document.getElementById("txtPage").value=Page;
document.asset.submit();
}
function validateassetreclist(frm)
{
	  if(frm.txtFilter.value.trim()=="")
		{
			alert('Enter Asset Name');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
}
function ExportExcel()
{
	url = 'asset/assetRecordToExcel.php';
	window.open(url, '_blank');
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="admin.php"> Home</a><a> Asset</a>  <span style="color: #000000;">Asset Records List</span>
</div>

<br>
<form name="asset" action="admin.php?src=asset/asset_record_list.php"	method="POST" onsubmit="return validateassetreclist(this);">
	<table width="100%" class="adminform1">
		<tr>
			<td>
			<h2>Asset Records List</h2>
			</td>
			<?php  $name_filter= makeSafe(@$_POST['txtFilter']); ?>
			<td>
				<div id="option_menu">
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
					<a class="btn btn-danger" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>
					
					<a id="exportexcel" class="btn btn-info" href="javascript:void(0);" onClick="javascript:ExportExcel();">Export Excel</a>
				
				</div>
			</td>
		</tr>
	</table>
	</div>
	<div class="search_bar">
			Fee Asset Name :<input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" maxlength="200" />
			<input type="submit" name="btnGo" value="Go" class="btn btn-info">
	</div>
	<br />
	<table width="100%" border="0" class="table" style="cursor: pointer;">
		<thead>
			<tr>
				<th>#</th>
				<th>ASSET NAME</th>
				<th>QUANTITY</th>
				<th>PRICE</th>
				<th>TOTAL PRICE</th>
				<th>ON DEMAND</th>
				<th>RECEIVED DATE</th>
				<th>ATTACHMENT</th>
				<th>CREATED AT</th>
				<th>UPDATED AT</th>
				<th>UPDATED BY</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$pageNum = 1;
			$rowsPerPage=20;
			
			if(makeSafe(isset($_POST['txtPage'])))	    $pageNum = makeSafe($_POST['txtPage']);
			// counting the offset
			$offset = ($pageNum - 1) * $rowsPerPage;
			if($offset<0) $offset=0;
			$sql="select am.name,ar.* from asset_master as am, asset_record as ar where am.asset_id=ar.asset_id";
			$sql=$sql." and (name LIKE '%".$name_filter."%')  order by updated_at desc LIMIT $offset, $rowsPerPage";
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$i=0;
				
			$uploaddir="../site_img/assets/";
			// by default we show first page
			
			while($row=mysql_fetch_array($res))
			{

				$i=$i+1;
				?>
			<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>	onclick="selectID('<?php echo base64_encode($row['asset_record_id']);?>')">
				<td align="center"><input type="radio" name="rdoID" value="<?php echo base64_encode($row['asset_record_id']); ?>" id="rdoID" /></td>
				<td >&nbsp;<?php echo $row['name'];?></td>
				<td align="center">&nbsp;<?php echo $row['quantity'];?></td>
				<td align="center">&nbsp;<?php echo $row['price'];?></td>
				<td align="center">&nbsp;<?php echo $row['quantity']*$row['price'];?></td>
				<td align="center">&nbsp;<?php echo ($row['on_demand']=="Y") ? "YES" : "NO"; ?></td>
				<td align="center"><?php echo date("jS-M-Y",strtotime($row['received_date']));?></td>
				<td align="center"><?php if($row['attachment']!="") {?><a href="<?php echo $uploaddir.DOMAIN_IDENTIFIER."_".$row['attachment']; ?>" target="_blank"><img src="css/classic/message_attachment.png" height="20px" width="20px"/></a><?php }?></td>
				<td align="center"><?php echo date("jS-M-Y, g:iA",strtotime($row['created_at']));?></td>
				<td align="center"><?php echo date("jS-M-Y, g:iA",strtotime($row['updated_at']));?></td>
				<td align="center"><?php echo $row['updated_by'];?></td>
			</tr>
			<?php
	  }
	  ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="18"><?php
				$sql="SELECT  COUNT(ar.asset_id) as numrows from asset_master as am, asset_record as ar where am.asset_id=ar.asset_id and (name LIKE '%".$name_filter."%')  order by name ";
				$result  = mysql_query($sql) or die('Error, query failed');
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				$numrows = $row['numrows'];
				if($numrows>0){

				// how many pages we have when using paging?
				$maxPage = ceil($numrows/$rowsPerPage);
				echo "TOTAL RECORDS :".$numrows." | PAGE(S) : ".$maxPage;

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
	<input type="hidden" name="assetrecID" id="assetrecID" value="" />
<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>'/>
</form>
<script>
document.asset.txtFilter.focus();
</script>

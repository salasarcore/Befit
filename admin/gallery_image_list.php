
<script>
function selectID(objChk)
{
document.getElementById("imageID").value=objChk;

if (objChk.checked==true)
	 document.getElementById("txtAction").value="add";
else
	 document.getElementById("txtAction").value="remove";
}
function ActionScript(act)
{
	if(document.getElementById("imageID").value=="" && act !="add")
	{
	  alert("Please select a Image");
	  
	}
	else
	{
	 url="popups/upload_gallery_image.php?act="+act+"&imageID="+document.getElementById("imageID").value+"&galleryID="+document.getElementById("txtGalleryID").value;
	 	 open_modal(url,600,400,"UPLOAD IMAGE")
	  }
}
	
function check()
{
	 if (document.getElementById("txtFilter").value.trim()=="")
	 {
		 alert("Please enter image description");
		 return false;
	 }
}

</script>
<?php
$name_filter=makeSafe(@$_POST['txtFilter']);
$galleryID=makeSafe(@$_REQUEST['galleryID']);
?>
<div class="page_head">
<div id="navigation"><a href="pages.php">Home</a><a> Utility</a><a href="pages.php?src=gallery_list.php" > Gallery List</a>  <span style="color: #000000;">Image List</span></div>

<form name="records" action="pages.php?src=gallery_image_list.php&galleryID=<?php echo $galleryID; ?>&page=<?php echo makeSafe(@$_GET['page']); ?>" method="POST">
<table width="100%" class="adminform1">
<tr>
<td>
	<h2>Image List</h2>	
	</td>
	<td height="40px" cellpadding="1">
		<div id="option_menu">
			<a class="addnew" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
            <a class="edit" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
            <a class="delete" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>
          
           </div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">
Enter Image Description: <input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" /> 
		<input type="submit" name="btnGo" value="Go" class="btn search" onclick="return check();">
</div>
<table width="100%" border="0" class="adminlist" style="table-layout:fixed;cursor: pointer;">
			<thead>
  <?php
  $rowsPerPage = 15;
  $pageNum = 1;
  if(makeSafe(isset($_GET['pageNum'])))    $pageNum = makeSafe($_GET['pageNum']);
  $offset = ($pageNum - 1) * $rowsPerPage;
  $sql="";
	$sql.="SELECT image_id,image_description, gallery_id, is_gallery_image, mime, updated_by, date_updated from  gallery_images where  gallery_id='".$galleryID."' order by date_updated desc ";
	
	if($name_filter!="")
	{
		$sql.=" and image_description like '%".trim($name_filter)."%'";
	}
		$res=mysql_query($sql);
		if(mysql_affected_rows()>0)
		{?>
			
			<tr>
			<th width="5%">#</th>
			<th width="15%">IMAGE</th>
			<th width="25%">IMAGE DESCRIPTION</th>
			<th width="20%">DATE UPDATED</th>
			<th width="25%">UPDATED BY <br>Employee Name[Emp_Code]</th>
			</tr>
			<thead>
			<tbody>
			<?php 
	$i=0;
	while($row=mysql_fetch_array($res))
	{
	$i=$i+1;
	?>
	
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo $row['image_id']; ?>')" >
	    <td align="center" width="50">
			<input type="radio" name="rdoID" value="<?php echo $row['image_id']; ?>" id="rdoID"/></td>
		<td align="center" width="100">
		    <img src="../site_img/gallery/thumb/<?php echo DOMAIN_IDENTIFIER."_".base64_encode($row['image_id']).".".$row['mime']; ?>" width="30px" />
		
		</td>
		<td align="left" style="word-wrap: break-word;">&nbsp;<?php echo$row['image_description'];?></td>
		<td align="center">&nbsp;<?php echo date("jS-M-Y g:i A", strtotime($row['date_updated']));?></td>
		<td align="center">&nbsp;<?php echo$row['updated_by'];?></td>
		
	   </tr>
	  <?php
	  }
		}
	?>
	  </tbody>
	  <tfoot>
	  <th colspan='5'>
	<?php // how many rows we have in database
	$query   = "SELECT  count(*) as numrows from gallery_images where gallery_id=".$galleryID;
	$result  = mysql_query($query);
	$row     = mysql_fetch_array($result, MYSQL_ASSOC);
	$numrows = $row['numrows'];
	$maxPage = ceil($numrows/$rowsPerPage);
	$offset++;
	if(($offset+15)<$numrows){$endpage=($offset+$rowsPerPage-1);}else{$endpage=$numrows;}
	
	if($numrows!=0)
	{
		echo "<span style='font-size:12px;'>Showing : ".$offset." - ".$endpage." of ".$numrows." Record(s) : </span>";
	}
	else
	{
		echo "<div style='font-size:12px;text-align:center;height:3px;'>NO RECORDS FOUND</div>";
	}
	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);

	// print the link to access each page
	$nav  = '';
	for($page = 1; $page <= $maxPage; $page++)
	{
	   if ($page == $pageNum)
	   {
		  $nav .= " $page "; // no need to create a link to current page
	   }
	   else
	   {
	   
		  $nav .= " <a href=\"gallery_image_list.php?pageNum=$page\">$page</a> ";
	   } 
	}

	// creating previous and next link
	// plus the link to go straight to
	// the first and last page

	if ($pageNum > 1)
	{
	   $page  = $pageNum - 1;
	   $prev  = " <a href=\"gallery_image_list.php?pageNum=$page\">[Prev]</a> ";

	   $first = " <a href=\"gallery_image_list.php?pageNum=1\">[First Page]</a> ";
	} 
	else
	{
	   $prev  = '&nbsp;'; // we're on page one, don't print previous link
	   $first = '&nbsp;'; // nor the first page link
	}

	if ($pageNum < $maxPage)
	{
	   $page = $pageNum + 1;
	   $next = " <a href=\"gallery_image_list.php?pageNum=$page\">[Next]</a> ";

	   $last = " <a href=\"gallery_image_list.php?pageNum=$maxPage\">[Last Page]</a> ";
	} 
	else
	{
	   $next = '&nbsp;'; // we're on the last page, don't print next link
	   $last = '&nbsp;'; // nor the last page link
	}

	echo $first . $prev . $nav . $next . $last;

					 
	?>
	</th>
	  </tfoot>
</table>

<div style="clear:both"></div>
<input type="hidden" name="imageID" id="imageID" value="" />
<input type="hidden" name="txtAction" id="txtAction" value="" />
<input type="hidden" name="txtGalleryID" id="txtGalleryID" value="<?php echo $_GET['galleryID'];?>" />
</form>	
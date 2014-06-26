<?php

?>
<script>
function selectID(objChk)
{
document.getElementById("galleryID").value=objChk;
}
function ActionScript(act)
{
	if(document.getElementById("galleryID").value=="" && act !="add")
	{
	  alert("Please select a Gallery");
	  
	}
	else
	{
	 
	 url="popups/gallery.php?act="+act+"&galleryID="+document.getElementById("galleryID").value;
	 	 open_modal(url,600,400,"IMAGE GALLERY")

	  }
}
function ActionScriptList()
{
	if(document.getElementById("galleryID").value=="")
	  alert("Please select a Gallery");
	 
	else
	{
	location.href="pages.php?src=gallery_image_list.php&galleryID="+document.getElementById("galleryID").value;
	return true;
	  }
}
function check()
{
	 if (document.getElementById("txtFilter").value.trim()=="")
	 {
		 alert("Please enter gallery name");
		 return false;
	 }
}
</script>
<?php
$name_filter=makeSafe(@$_POST['txtFilter']);

?>
<div class="page_head">
<div id="navigation"><a href="pages.php">Home</a><a> Utility</a>   <span style="color: #000000;">Gallery List</span></div>

<form name="records" action="pages.php?src=gallery_list.php&page=<?php echo makeSafe(@$_GET['page']); ?>" method="POST">
<table width="100%" class="adminform">
<tr>
	<td>
		<h2>Gallery List</h2>
	</td>
	<td cellpadding="1">
		<div id="option_menu">
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
            <a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
            <a  class="btn btn-danger" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>
            <a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScriptList();">Add Image</a>
           </div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">
Enter Gallery Name: <input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" /> 
		<input type="submit" name="btnGo" value="Go" class="btn btn-info" onclick="return check();">
</div>
<br />
<table class="table table-bordered">
  <thead>


  <?php
  $rowsPerPage = 15;
  $pageNum = 1;
  if(makeSafe(isset($_GET['pageNum'])))    $pageNum = makeSafe($_GET['pageNum']);
  $offset = ($pageNum - 1) * $rowsPerPage;
  $sql="";
	$sql.="SELECT  gallery_id, gallery_name, gallery_description, date_updated,updated_by from gallery where br_id=".$_SESSION['br_id'];
if($name_filter!="")
	{
		$sql.=" and (gallery_name LIKE '%".trim($name_filter)."%' )";
	} 
	$sql.=" order by date_updated DESC LIMIT $offset, $rowsPerPage ";
	
	$res=mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		?>
		  <tr>
    <th width="5%">#</th>
    <th width="25%">GALLERY NAME</th>
    <th width="25%">GALLERY DESCRIPTION</th>
    <th width="15%">DATE UPDATED</th>
     <th width="20%">UPDATED BY</th>
  </tr>
  </thead>
    <tbody>
    
	<?php 
	
	
	$i=0;
	while($row=mysql_fetch_array($res))
	{
	$i=$i+1;
	?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>  onclick="selectID('<?php echo $row['gallery_id']; ?>')" >
	    <td align="center"><input type="radio" name="rdoID" value="<?php echo $row['gallery_id']; ?>" id="rdoID" /></td>
		<td style="word-wrap: break-word;">&nbsp;<?php echo$row['gallery_name'];?></td>
		<td style="word-wrap: break-word;">&nbsp;<?php echo$row['gallery_description'];?></td>
		<td align="center">&nbsp;<?php echo date("jS-M-Y, g:i A",strtotime($row['date_updated']));?></td>
		<td align="center">&nbsp;<?php echo$row['updated_by'];?></td>
	   </tr>
	  <?php
	  }
}
	 ?>
	
	  </tbody><tfoot>
<th colspan='5'>
	<?php // how many rows we have in database
	$query   = "SELECT  count(*) as numrows from gallery where br_id=".$_SESSION['br_id'];
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
	   
		  $nav .= " <a href=\"gallery_list.php?pageNum=$page\">$page</a> ";
	   } 
	}

	// creating previous and next link
	// plus the link to go straight to
	// the first and last page

	if ($pageNum > 1)
	{
	   $page  = $pageNum - 1;
	   $prev  = " <a href=\"gallery_list.php?pageNum=$page\">[Prev]</a> ";

	   $first = " <a href=\"gallery_list.php?pageNum=1\">[First Page]</a> ";
	} 
	else
	{
	   $prev  = '&nbsp;'; // we're on page one, don't print previous link
	   $first = '&nbsp;'; // nor the first page link
	}

	if ($pageNum < $maxPage)
	{
	   $page = $pageNum + 1;
	   $next = " <a href=\"gallery_list.php?pageNum=$page\">[Next]</a> ";

	   $last = " <a href=\"gallery_list.php?pageNum=$maxPage\">[Last Page]</a> ";
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
<input type="hidden" name="galleryID" id="galleryID" value="" />
</form>	
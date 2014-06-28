<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_contact_category,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin' )
	{
		if(!isAccessModule($id_admin, module_contact_category))
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
function validatecategoryname()
{
	var catname=document.records.catname.value;
	
if(catname.trim()=="")
{
	alert('Enter Category Name');
	document.records.catname.value="";
	document.records.catname.focus();
	return false;
}
return true;
}
function selectID(brID)
{
document.getElementById("catid").value=brID;
}


function ActionScript(act)
{
	if(document.getElementById("catid").value=="" && act !="add")
	{
	  alert("Please select the Category");
	}
	else
	{
	 
	 	url="popups/cntct_cat.php?act="+act+"&catid="+document.getElementById("catid").value;
	 	open_modal(url,600,200,"CONTACT CATEGORIES")
	 }
	  
}

</script>
<div class="page_head">
<div id="navigation"><a href="pages.php"> Home</a><a> Administration</a><span style="color: #000000;"> Contact Categories</span> </div>
 <form name="records"  action="pages.php?src=contact_category.php" method="POST" onsubmit="return validatecategoryname();">
<table width="100%" class="adminform1">
   <tr>
   <td>
 <h2>Contact Categories</h2>  
</td>
    <td style="text-align: right;" align="right">
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
<?php  $strfilter= makeSafe(@$_POST['txtFilter']); ?>
Enter Category Name: <input type="text" name="txtFilter" value="<?php echo $strfilter; ?>" name="catname" id="catname"  /> 
<input type="submit" name="submit" id="submit" value="Go" class="btn btn-info"/>

</div>
<table style="width:100%;margin-top:10px;cursor: pointer;" cellspacing="0" class="table table-bordered"">

  <thead>		
   <tr>
   <th width="20px">#</th>
    <th>CATEGORY NAME</th>
    <th>UPDATED ON</th>
    <th>UPDATED BY <br>Employee Name[Emp_Code]</th>
  </tr>
  <thead>
  <tbody>
   <?php
   
	$i=0;
	$sql="";
		$sql.="select * from school_contact_category where school_contact_cat_name LIKE '%".$strfilter."%' order by date_updated desc";
		
					$res=mysql_query($sql);

		while($row=mysql_fetch_array($res))
		{
		$i=$i+1;
		
	
	?>
		 <tr  onclick="selectID('<?php echo $row['school_contact_cat_id']; ?>')" >
			<td align="center"><input type="radio" name="rdoID" value="<?php echo $row['br_id']; ?>" id="rdoID" /></td>
			<td align="center"><?php echo $row['school_contact_cat_name'];?></td>
			<td align="center"><?php echo date("jS-M-Y, g:iA",strtotime($row['date_updated']));?></td>
			<td align="center"><?php echo $row['updated_by'];?></td>
					
		</tr>
	<?php
	}
	?> 
  </tbody>
  <tfoot>
  <tr>
<th colspan="10">
	<?php 
$sql="SELECT count(*) as numrows from school_contact_category where school_contact_cat_name LIKE '%".$strfilter."%'";
$result  = mysql_query($sql) or die('Error, query failed');
$row     = mysql_fetch_array($result, MYSQL_ASSOC);
$numrows = $row['numrows'];
if($numrows>0)
	echo "TOTAL RECORDS : ".$numrows;
else 
	echo "NO RECORDS FOUND";
?>
</th>
</tr>
</tfoot>
</table>
	<div style="clear: both"></div>
<input type="hidden" name="catid" id="catid" value=''/>
</form>

<br/>

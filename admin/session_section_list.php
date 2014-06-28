<?php
 include_once("functions/common.php");
 
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_session_section_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if(($level!='Super Admin')&&($level!='Admin'))
	{
		if(!isAccessModule($id_admin, module_session_section_list))
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
document.getElementById("sessionID").value=objChk;

if (objChk.checked==true)
	 document.getElementById("txtAction").value="add";
else
	 document.getElementById("txtAction").value="remove";
}
function ActionScript(act)
{
	if(document.getElementById("sessionID").value=="" && act !="add")
	{
	  alert("Please select a session-section");
	  
	}
	else
	{
		url="popups/session_section.php?act="+act+"&sessionID="+document.getElementById("sessionID").value;
		open_modal(url,500,320,"SESSION SECTION")
		return true;
	  }
}

function Publish(act,field,spid,id)
{
var tab="session_section";
var condition_field="session_id";
var poststr = "id="+id+"&act="+act+"&spid="+spid+"&field="+field+"&tab="+tab+"&condition_field="+condition_field ;
makePOSTRequest('pub_unpub.php', poststr,spid);
}

function validatesection(frm)
{
	if(frm.department.value==0)
	{
		alert('Select Department');
		frm.department.focus();
		return false;
	}
}
</script>
<?php
$department_id=makeSafe(@$_POST['department']);


?>
<div class="page_head">
<div id="navigation"><a href="pages.php">Home</a><a> Administration</a><span style="color: #000000"> Course Wise Batch List</span> </div>


	
<form name="records" action="pages.php?src=session_section_list.php&page=<?php echo makeSafe(@$_GET['page']); ?>" method="POST" onsubmit="return validatesection(this);">
<table width="100%" cellspacing="1">
<tr>
	<td><h2>Course Wise Batch List</h2></td>
	<td align="right">
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
<?php department($department_id);?> <input type="submit" value="Go" class="btn btn-info">
</div>
<br />
<table width="100%" border="0" class="table table-bordered"" style="cursor: pointer;">
  <thead>
  <tr>
    <th>#</th>
    <th>COURSE NAME</th>
    <th>SESSION</th>
    <th>BATCH</th>
    <th>ADMISSION OPEN</th>
    <th>FREEZE</th>
   
    <th>BATCH LAST UPDATED</th>
    <th>UPDATED BY <br>Employee Name[Emp_Code]</th>
  </tr>
  <thead>
  <tbody>
  <?php
  	$sql="SELECT  session_id, session,department_name, admission_open,freeze,section, session_section.date_updated,session_section.updated_by from session_section,mst_departments where session_section.department_id=mst_departments.department_id and mst_departments.br_id=".$_SESSION['br_id'];
	if(@$_POST['department'] >0)	$sql .=" and mst_departments.department_id=".$department_id;
 $sql .=" order by session_id desc";
 
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		$i=0;
	
		while($row=mysql_fetch_array($res))
		{
		
		$i=$i+1;
	?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>  onclick="selectID('<?php echo $row['session_id']; ?>')" >
	    <td align="center"><input type="radio" name="rdoID" value="<?php echo $row['session_id']; ?>" id="rdoID" /></td>
		<td align="center">&nbsp;<?php echo$row['department_name'];?></td>
		<td align="center">&nbsp;<?php echo$row['session'];?></td>
		<td align="center">&nbsp;<?php echo$row['section'];?></td>
		<td align="center"><span id="s_<?php echo @$row['session_id'];?>"> <a href ="javascript:Publish('<?php if($row['admission_open']=='Y') echo 'N'; else echo 'Y';?>','admission_open','s_<?php echo @$row['session_id'];?>',<?php echo @$row['session_id'];?>)"><img src="../images/<?php if($row["admission_open"]=="Y") echo "publish.png"; else  echo "publish_x.png";?>"/></a></span></td>
		<td align="center"><span id="f_<?php echo @$row['session_id'];?>"> <a href ="javascript:Publish('<?php if($row['freeze']=='Y') echo 'N'; else echo 'Y';?>','freeze','f_<?php echo @$row['session_id'];?>',<?php echo @$row['session_id'];?>)"><img src="../images/<?php if($row["freeze"]=="Y") echo "publish.png"; else  echo "publish_x.png";?>"/></a></span></td>
		<td align="center">&nbsp;<?php echo date("jS-M-Y, g:i A",strtotime($row['date_updated']));?></td>
		<td align="center">&nbsp;<?php echo$row['updated_by'];?></td>
	  </tr>
	  <?php
	  }
	  ?>
	  </tbody><tfoot>
<tr>
<th colspan="9"></th>
  </tr>
  </tfoot>
</table>
<div style="clear:both"></div>
<input type="hidden" name="sessionID" id="sessionID" value="" />
<input type="hidden" name="txtAction" id="txtAction" value="" />
<input type="hidden" name="txtUpdateStr" id="txtUpdateStr" value="" />


</form>	
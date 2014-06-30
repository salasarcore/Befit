<?php 
include_once("functions/common.php");

/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_session_list,$modules)){ 
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if(($level!='Super Admin') && ($level!='Admin')){
		if(!isAccessModule($id_admin,module_session_list)){
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
	  alert("Please select a session");
	  
	}
	else
	{
		url="popups/session.php?act="+act+"&sessionID="+document.getElementById("sessionID").value;
		open_modal(url,500,300,"SESSION")
		return true;
	  } 	
}

function Publish(act,field,spid,id)
{
var tab="session";
var condition_field="session_id";
var poststr = "id="+id+"&act="+act+"&spid="+spid+"&field="+field+"&tab="+tab+"&condition_field="+condition_field ;
makePOSTRequest('pub_unpub.php', poststr,spid);
}	
function validatesession(frm)
{
	  if(frm.txtFilter.value.trim()=="")
		{
			alert('Enter Session Name');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
}
</script>
<?php
$department_id=makeSafe(@$_POST['department']);
$name_filter= makeSafe(@$_POST['txtFilter']);
?><form name="records" action="pages.php?src=session_list.php&page=<?php echo makeSafe(@$_GET['page']); ?>" method="POST" onsubmit="return validatesession(this);">

<div class="page_head">
<div id="navigation"><a href="pages.php"> Home</a><a> Administration</a> <span style="color: #000000;"> Session Master List</span> </div>


<table width="100%" cellspacing="1" class="adminform1">
<tr>
	<td><h2>Session Master List</h2>
		
	</td>
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
Enter Session Name: <input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" /> 
		<input type="submit" name="btnGo" value="Go" class="btn btn-info">
</div>
<table width="100%" border="0" class="table table-bordered"" style="cursor: pointer;">
  <thead>
  <tr>
    <th>#</th>
    <th>SESSION NAME</th>
    <th>SESSION START DATE</th>
    <th>FREEZED</th>
    <th>DATE UPDATED</th>
    <th>UPDATED BY </th>
  </tr>
  <thead>
  <tbody>
  <?php
  
  
  	$sql="SELECT  session_id, session_name,start_date,freeze, date_updated,updated_by from session where (session_name like '%".trim($name_filter)."%' )  order by date_updated desc";
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		$i=0;
		
		
		while($row=mysql_fetch_array($res))
		{
		
		$i=$i+1;
	?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>  onclick="selectID('<?php echo $row['session_id']; ?>')" >
	    <td align="center"><input type="radio" name="rdoID" value="<?php echo $row['session_id']; ?>" id="rdoID" /></td>
		<td>&nbsp;<?php echo $row['session_name'];?></td>
		<td>&nbsp;<?php echo date("jS-M-Y",strtotime($row['start_date']));?></td>
		<td align="center"><span id="f_<?php echo @$row['session_id'];?>"> <a href ="javascript:Publish('<?php if($row['freeze']=='Y') echo 'N'; else echo 'Y';?>','freeze','f_<?php echo @$row['session_id'];?>',<?php echo @$row['session_id'];?>)"><img src="../images/<?php if($row["freeze"]=="Y") echo "publish.png"; else  echo "publish_x.png";?>"/></a></span></td>
		<td><?php echo date("jS-M-Y, g:i A",strtotime($row['date_updated']));?></td>
		<td>&nbsp;<?php echo $row['updated_by'];?></td>
		
	  </tr>
	  <?php
	  }
	  ?>
	  </tbody><tfoot>
<tr>
<th colspan="6"></th>
  </tr>
  </tfoot>
</table>
<div style="clear:both"></div>
<input type="hidden" name="sessionID" id="sessionID" value="" />
<input type="hidden" name="txtAction" id="txtAction" value="" />
<input type="hidden" name="txtUpdateStr" id="txtUpdateStr" value="" />


</form>	
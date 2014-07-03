<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 */
//include("modulemaster.php");
/*if(in_array(module_applied_student_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if(($level!='Super Admin')&&($level!='Admin'))
	{
		if(!isAccessModule($id_admin, module_applied_student_list))
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

$old="";
if(date("Y-m-d", strtotime($date_registered))<CUSTOMIZED_APPLY_ONLINE_FORM_RELEASE_DATE){
	$old="old";
}*/
?>
<script>

function SubmitPage(page)
{
document.getElementById("txtPage").value=page;
document.records.submit();
}
function selectID(objChk)
{
document.getElementById("fromID").value=objChk;
}
/*function ActionScriptEdit()
{
	if(document.getElementById("fromID").value=="")
	{
	  alert("Please select a Student");
	  
	}
	else
	{
		url="popups/admission.php?fromID="+document.getElementById("fromID").value;
		open_modal(url,600,350,"ACCEPT ADMISSION")
		return true;
	  }
}*/


function ActionScriptView(){
	
	if(document.getElementById("fromID").value=="")
	{
	  alert("Please select a application");
	  
	}
	else
	{
		url="popups/stu_view_details.php?fromID="+document.getElementById("fromID").value;
		open_modal(url,800,600,"VIEW PROFILE")
		return true;
	
	  }
}

function ActionScript()
{
	if(document.getElementById("fromID").value=="")
	{
	  alert("Please select a application");
	  
	}
	else
	{
		url="popups/admission.php?fromID="+document.getElementById("fromID").value;
		open_modal(url,600,350,"ACCEPT ADMISSION")
		return true;
	  }
}

function ActionScriptReject()
{
	if(document.getElementById("fromID").value=="")
	{
	  alert("Please select a application");
	  
	}
	else
	{
		url="popups/admission_reject.php?fromID="+document.getElementById("fromID").value;
		open_modal(url,500,300,"REJECT ADMISSION")
		return true;
	  }
}
function ActionScriptapply()
{
	
		url="popups/apply_online.php?act=add&fromID="+document.getElementById("fromID").value;
		open_modal(url,800,600,"APPLY ONLINE")
		return true;

}
function ActionScriptbranchadmins()
{
		url="popups/branch_admins.php"
		open_modal(url,800,600,"SMS ALERTS")
		return true;

}
/* function ActionScriptedit(formno)
{
	var value='<?php echo $old;?>';
	if(value=="old")
		url="popups/apply_online.php?act=edit&fromID="+formno;
	else
		url="popups/apply_online_customized.php?act=edit&fromID="+formno;
	
		open_modal(url,800,600,"APPLY ONLINE")
		return true;

}*/
</script>
<?php
$name_filter=makeSafe(@$_POST['txtFilter']);
//$department_id=makeSafe(@$_POST['department']);
$status=makeSafe(@$_POST['status']);
$adt=makeSafe(@$_POST['adt']);
?>
<div class="page_head">
<div id="navigation"><a href="pages.php">Home</a><a> Admission</a><span style="color: #000000;"> Applied Students List </span></div>

<form name="records" action="pages.php?src=applied_student_list.php" method="POST">
<input type="hidden" name="fromID" id="fromID" value="" />

<table width="100%" class="adminform1">
<tr>
	<td>
		<h2>Applied Candidates List</h2>
	</td>
	<td>
		<div id="option_menu">
			<!-- <a  class="addnew" href="javascript:void(0);" onClick="javascript:ActionScriptEdit();">Edit</a>
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScriptbranchadmins();">SMS Alerts</a>	-->
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScriptapply();">Apply Online</a>	
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScriptView();">View Profile</a>
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript();">Accept Application</a>
			<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScriptReject();">Reject Application</a>
		</div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">

		Form No:<input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter; ?>" />
		Admitted Status :
		<select name="status" id="status">
		<option value="">All</option>
		<option value="Y">Approved</option>
		<option value="N">Pending</option>
		<option value="R">Rejected</option>		
		</select>
		<span class="redstar">Applied Date :</span><input name="adt" type="text" class="date" id="adt" value="<?php echo @$adt;?>" size="11" />
<script type="text/javascript">
  $(function() {
		$( "#adt" ).datepicker({
			numberOfMonths: [1,2],
			dateFormat: 'yy-mm-dd',
			maxDate: new Date()
		});
	});
</script> 
</td>	
		

		<input type="button" name="btnGo" value="Go" class="btn btn-info" onclick="SubmitPage(0);">
</div>
<br />
<table width="100%" border="0" class="table table-bordered"" style="cursor: pointer;">
  <thead>
  <tr>
    <th>#</th>
    <th>FORM NO</th>
	<th> NAME</th>
    <th>PERMANENT ADDRESS</th>
    <th>MOBILE</th>
    <th>BIRTH DATE</th>	
    <th>APPLIED DATE</th>
    <th>ADMITTED ?</th>
 <!--    <th>#</th>-->
  </tr>
  <thead>
  <tbody>
  <?php
  // by default we show first page
	$pageNum = 1;
$rowsPerPage=50;

	if(makeSafe(isset($_POST['txtPage'])))	    $pageNum = makeSafe($_POST['txtPage']);

	$offset = ($pageNum - 1) * $rowsPerPage;
	if($offset<0) $offset=0;
	
  $sqlWhere="";
	$sql="SELECT adm_form_no,date_applied, concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name, sex,pin,email, dob, present_address,mob,  admitted FROM admission_application";
	if(@$status!="")    $sqlWhere = $sqlWhere." and admitted='".@$status."' ";
	if($name_filter!="") $sqlWhere = $sqlWhere." and  adm_form_no like '".trim($name_filter)."'";
	if($adt!="") $sqlWhere = $sqlWhere." and  date_applied like '%".trim($adt)."%'";
	
	

	$limit=" order by adm_form_no desc LIMIT $offset, $rowsPerPage";

	 $sql=$sql." ".$sqlWhere.$limit;
	
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		$i=0;

		if(mysql_num_rows($res)>0)
		{
		while($row=mysql_fetch_array($res))
		{
		$date=$row['date_applied'];
		$i=$i+1;
	?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo $row['adm_form_no']; ?>')" >
	    <td align="center"><input type="radio" name="rdoID" value="<?php echo $row['adm_form_no']; ?>" id="rdoID" /></td>
		<td align="center"><?php echo $row['adm_form_no'];?></td>
		
		<td><?php echo $row['stu_name'];?></td>
		
		<td><?php echo $row['present_address'];?></td>
		<td align="center"><?php echo $row['mob'];?></td>
		<td align="center"><?php echo date("jS-M-Y",strtotime($row['dob']));?></td>		
		<td align="center"><?php echo date("jS-M-Y g:iA",strtotime($date));?></td>
		<td align="center"><?php if($row['admitted']=="Y") echo "APPROVED"; elseif($row['admitted']=="N") echo "PENDING"; elseif($row['admitted']=="R") echo "REJECTED"; else echo "";?></td>
		<!-- <td align="center"><?php if($row['admitted']=="Y" || $row['admitted']=="R" ) echo ""; else {?><a href="javascript:void(0);" onClick="javascript:ActionScriptedit(<?php echo $row['adm_form_no'];?>);">Edit</a><?php }?>-->
	  </tr>
	  <?php
	  }
	  ?>
	  </tbody><tfoot><tr>
    <th colspan="15">
	<?php 
$sql="SELECT count(adm_form_no) as numrows FROM admission_application";
 $sql=$sql." ".$sqlWhere;		
$result  = mysql_query($sql) or die('Error, query failed');
$row     = mysql_fetch_array($result, MYSQL_ASSOC);
$numrows = $row['numrows'];
			

// how many pages we have when using paging?
$maxPage = ceil($numrows/$rowsPerPage);
echo "TOTAL STUDENT :".$numrows." | PAGE(S) : ".$maxPage;

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
			echo "<tr><th colspan='10'>NO RECORDS FOUND</th></tr>";
?><input type="hidden" name="rdID" id="rdID" value=''/>
</th></tr>
<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe($_POST['txtPage']);?>'/>

</tfoot>
</table>
<script>
document.records.txtFilter.focus();
</script>

<div style="clear:both"></div>
</form>	
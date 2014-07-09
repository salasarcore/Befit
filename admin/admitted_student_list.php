<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_admitted_student_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if(($level!='Super Admin')&&($level!='Admin'))
	{
		if(!isAccessModule($id_admin, module_admitted_student_list))
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
document.getElementById("studentID").value=objChk;
}
function ActionScript(act)
{
	if(document.getElementById("studentID").value=="" && act !="add")
	{
	  alert("Please select a Applicant");
	}
	else
	{
		url="popups/edit_student_rollno.php?act="+act+"&studentID="+document.getElementById("studentID").value;
		open_modal(url,500,300,"STUDENT CLASS DETAILS")
		return true;
	  }
}
</script>
<?php
$name_filter=makeSafe(@$_POST['txtFilter']);
$department_id=makeSafe(@$_POST['department']);

?>
<div class="page_head">
<div id="navigation"><a href="pages.php"> Home</a><a> Admission</a> <span style="color: #000000;">Course Wise Applicants List</span></div>

<form name="records" action="pages.php?src=admitted_student_list.php" method="POST">
<input type="hidden" name="studentID" id="studentID" value="" />
<table width="100%" class="adminform1">
<tr>
	<td>
		<h2>Course Wise Applicants List</h2>
	</td>
	<td>
		<div id="option_menu">
	<!-- 		<a  class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">EDIT</a>-->
		</div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">
<select name="department" id="department">
<?php $sql="select * from session_section where session='".$_SESSION['d_session']."'";
$res=mysql_query($sql);
while($row=mysql_fetch_array($res))
{
?>
<option value="<?php echo $row['session_id'];?>"><?php echo $row['section'];?></option>
<?php }?>
</select>
	
		<input type="submit" name="btnGo" value="Go" class="btn btn-info">
</div>
<br />
 
	<?php 
	$sql="SELECT  a.stu_id,a.reg_no,concat_ws(' ',a.stu_fname,a.stu_mname,a.stu_lname) as stu_name, c.department_name, s.session, s.section, b.rollno,b.updated_by from mst_students a,student_class b,mst_departments c,session_section s";
	$sqlWhere=" WHERE a.stu_id=b.stu_id and status='A' and c.department_id=b.department_id and s.session_id=b.session_id and a.br_id=".makeSafe($_SESSION['br_id'])." ";
	
	
	if($department_id!="0" && $department_id!="") $sqlWhere = $sqlWhere." and s.session_id='".$department_id."'";

	 $sql=$sql." ".$sqlWhere." order by a.date_updated desc";
	  
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		$i=0;
		
		
		?>
		
	<table width="100%" border="0" class="table table-bordered"" style="cursor: pointer;">
  <thead>
  <tr>
    <th>#</th>
    <th>REGISTRATION NO</th>
	<th>NAME</th>
    <th>SESSION</th>
    <th>COURSE</th>
    <th>UPDATED BY</th>
  </tr>
  <thead>
  <tbody>

		<?php
		while($row=mysql_fetch_array($res))
		{
		
		
		$i=$i+1;
	?>
	  <tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo $row['stu_id']; ?>')" >
	    <td><input type="radio" name="rdoID" value="<?php echo $row['stu_id']; ?>" id="rdoID" /></td>
		<td>&nbsp;<?php echo $row['reg_no'];?></td>
		<td>&nbsp;<?php echo$row['stu_name'];?></td>
		<td>&nbsp;<?php echo$row['session'];?></td>
		<td>&nbsp;<?php echo$row['section'];?></td>
		<td>&nbsp;<?php echo$row['updated_by'];?></td>
		
	  </tr>
	  <?php
	  }
	  ?>
	  </tbody><tfoot>
<tr>
<th colspan="18"><?php echo "Total Student : ". $i;?>
</th>
  </tr>
  </tfoot>
</table>
<div style="clear:both"></div>
</form>	
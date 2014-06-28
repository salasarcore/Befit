<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_fee_discount_report,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_fee_discount_report))
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
function discountdetails(act,id)
{
if(act=="student"){
		url="fee/popups/fee_discount_report.php?act="+act+"&stu_id="+id;
		open_modal(url,800,600,"STUDENT FEES DISCOUNT REPORT")

}
else if(act=="dept"){
		url="fee/popups/fee_discount_report.php?act="+act+"&dept_id="+id;
		open_modal(url,800,600,"FEES DISCOUNT REPORT")

}
else if(act=="all"){
	url="fee/popups/fee_discount_report.php?act="+act+"&collection_date="+$("#adt").val();
	open_modal(url,800,600,"FEES DISCOUNT REPORT")

}
else if(act=="customize")
{
	url="customize_report.php?type=STUDENT";
	open_modal(url,650,400,"REPORT CUSTOMIZATION")
}
}
function selectID(stuid,exptid)
{
document.getElementById("stuid").value=stuid;
document.getElementById("exptid").value=exptid;

$('#tab tr').on('click', function() {
$('#tab tr').removeClass('selected');
$(this).toggleClass('selected');
$(this).find('input:radio').attr('checked', 'checked');
});
}
function  populateDetails(act)
{

	if(act=="all")
	{
		$("#stuList").empty();
		var url="fee/ajax/get_discount_collection_list.php?act="+act+"&collection_date="+$("#adt").val();
		makePOSTRequest(url,'','stuList');
	}
	else if(act=="selected")
	 {
		 $("#stuList").empty();
		var url="fee/ajax/get_discount_collection_list.php?act="+act+"&dept_id="+$("#department").val()+"&collection_date="+$("#adt").val();
		makePOSTRequest(url,'','stuList');
	 }
	
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="pages.php"> Home</a><a> Fees</a><a> Reports</a>  <span style="color: #000000;">Fee Discount Report</span>
</div>

<form name="records" action="pages.php?src=fee_collect.php">
<table width="100%" class="adminform1">
<tr>
<td> 
<h2>Fee Discount Report</h2>
				 </td>	
<td>
		<div id="option_menu">
			<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:discountdetails('all',0);">All Departments</a> 
			<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:discountdetails('customize',0);">Customize Report</a> 
		</div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">
<span class="redstar">Select Department :</span>

  <select name="department" id="department" onchange="populateDetails('selected');">
<?php
		
		$sql="select d.* from mst_departments d where d.br_id=".$_SESSION['br_id']." order by department_name";
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
			echo "<option value=''>--All Departments--</option>";
			while($row=mysql_fetch_array($res))
			{
				echo "<option value='".$row['department_id']."'";
				if(@$department_id==$row['department_id']) echo " Selected"; echo ">".$row['department_name']."</option>";
			}
		  ?>
	  
		  </select>
		  Select Date : <input name="adt" type="text" class="date" id="adt" value="<?php echo @$adt;?>" size="11" readonly onchange="populateDetails('selected');"/>
        <script type="text/javascript">
				  $(function() {
						$( "#adt" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
						});
					});

				  </script> 
</div> 
<div id="stuList"></div>
				  <input type="hidden" name="stuid" id="stuid" value="" />
				  <input type="hidden" name="exptid" id="exptid" value="" />
				  </form>	
<script type="text/javascript">
populateDetails('all');
</script>

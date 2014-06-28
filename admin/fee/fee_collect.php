<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.

//include("modulemaster.php");
if(in_array(module_fee_collection_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_fee_collection_list))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
	if(isAccessModule($id_admin, export_excel_fee_collection))
		$hasexcelpermission = true;
	else
		$hasexcelpermission = false;
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}
*/
?>
<script>	
function ActionScript(act)
{
if(act=="collect"){
		url="pages.php?src=fee/popups/fee_collection.php";
	window.location=url;
		return true;
	}
else if(act=="extenddate"){
		url="fee/popups/fee_extend_due_date.php";
		open_modal(url,800,450,"EXTEND DUE DATE")
		return true;
	}
else if(act=="installment"){
		url="fee/popups/fee_installment.php";
		open_modal(url,800,600,"FEES INSTALLMENT")
		return true;
	}
else if(act=="customize")
{
	url="customize_report.php?module=FEE COLLECTION&type=STUDENT";
	open_modal(url,650,400,"REPORT CUSTOMIZATION")
	return true;
}
else if(act=="viewdetails")
	{
		if(document.getElementById("stuid").value.trim()!="")
		{
			url="fee/popups/fee_details.php?stuid="+document.getElementById("stuid").value+"&exptid="+document.getElementById("exptid").value;
			open_modal(url,800,600,"FEES DETAILS")
			return true;
		}
		else
		{
			alert("Please Select Student");
			return false;
		}
	}
}
function selectID(stuid,exptid)
{
document.getElementById("stuid").value=stuid;
document.getElementById("exptid").value=exptid;

$('#tab').css('cursor', 'pointer');
$('#tab tr').on('click', function() {
$('#tab tr').removeClass('selected');
$(this).toggleClass('selected');
$(this).find('input:radio').attr('checked', 'checked');
});
}
function  populateStudent(act)
{
	if(act=="all")
	{
		var url="fee/ajax/get_collection_list.php?act="+act;
		makePOSTRequest(url,'','stuList');
	}
	else if(act=="sectionlist")
	 {
		 $("#section").empty();
		 $("#feeexpected").empty();
		var url="fee/ajax/get_collection_list.php?act="+act+"&department="+$("#department").val();
		makePOSTRequest(url,'','section');
	 }
    else if(act=="feeexpectedlist")
	 {
   		var url="fee/ajax/get_collection_list.php?act="+act+"&department="+$("#department").val()+"&section="+$("#section").val();
		makePOSTRequest(url,'','feeexpected');
	 }
    else if(act=="search")
    {
        if($("#department").val()!="0"){
	   		var url="fee/ajax/get_collection_list.php?act="+act+"&department="+$("#department").val()+"&section="+$("#section").val()+"&expected="+$("#feeexpected").val();
			makePOSTRequest(url,'','stuList');
        }
        else
        {
            alert("Please select atleast department to perform search");
            return false;
        }
	}
	
}
function ExportExcel()
{
	url = "fee/feeCollectionToExcel.php";
	window.open(url, '_blank');
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="pages.php"> Home</a><a> Fees</a>  <span style="color: #000000;">Collect fees</span>
</div>


<form name="records" action="pages.php?src=fee_collect.php">
<table width="100%" class="adminform1">
<tr>
<td> 
<h2>Collect fees</h2>
				 </td>
	
<td>
<?php
?>
		<div id="option_menu">
			<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('installment');">Assign Installment</a>
			<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('collect');">Collect Fee</a>
			<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('extenddate');">Extend Due Date</a>
			<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('viewdetails');">View Details</a>
			<!-- <a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('customize',0);">Customize Report</a>-->
	
			<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ExportExcel();">Export Excel</a>

		</div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">
<span class="redstar">Select Course :</span>

  <select name="department" id="department" onchange="populateStudent('sectionlist')">
<?php
		
		$sql="select d.*,s.session,s.section,s.session_id,s.freeze from mst_departments d, session_section s where d.department_id=s.department_id and s.freeze='N' and s.session='".makesafe($_SESSION['d_session'])."' and d.br_id=".$_SESSION['br_id']." order by department_name,session_id desc";
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
			echo "<option value='0'>--Select Course--</option>";
			while($row=mysql_fetch_array($res))
			{
				echo "<option value='".$row['department_id']."'";
				if(@$department_id==$row['department_id']) echo " Selected"; echo ">".$row['department_name']."</option>";
			}
		  ?>
	  
		  </select>
		  <span class="redstar">Select Section :</span>
		  <select name="section" id="section" onchange="populateStudent('feeexpectedlist')">

	 
		  </select>
		   <span class="redstar">Select Fees :</span>
		  <select name="feeexpected" id="feeexpected" onchange="populateStudent('student')">

	 
		  </select>
<input type="button" name="search" id="search" value="Go" class="btn btn-info" onclick="populateStudent('search')"/>
</div>
<div id="stuList"></div>
				  <input type="hidden" name="stuid" id="stuid" value="" />
				  <input type="hidden" name="exptid" id="exptid" value="" />
				  </form>	
<script type="text/javascript">
populateStudent('all');
</script>

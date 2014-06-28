<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_monthly_expected_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_monthly_expected_list))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
	if(isAccessModule($id_admin, export_excel_monthly_expected_list))
		$hasexcelpermission = true;
	else
		$hasexcelpermission = false;
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}*/

?>
<script>	
function  populateStudent(act)
{
	if(act=="all")
	{
		var url="fee/ajax/get_monthly_expected_list.php?act="+act;
		makePOSTRequest(url,'','stuList');
	}
    else if(act=="search")
    {
        if(($("#department").val()=="0") && ($("#month").val()=="")){
        	alert("Please select department or month to perform search");
            return false;
        }
        else{
        	$("#month_year").html($("#month").attr("name"));
	   		var url="fee/ajax/get_monthly_expected_list.php?act="+act+"&department="+$("#department").val()+"&monthyear="+$("#month").val();
			makePOSTRequest(url,'','stuList');
        }
	}
}
function ExportExcel()
{
	var monthyear=$("#month").val();
	url = "fee/monthlyExpectedToExcel.php?monthyear="+monthyear+"&department="+$("#department").val();
	window.open(url, '_blank');
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="pages.php"> Home</a><a>Fees</a> <span style="color: #000000;">Monthly Expected Fees</span>
</div>
<table width="100%" class="adminform1">
<tr>
<td> 
<h2>Monthly Expected Fees</h2>
</td>
<td>
		<div id="option_menu">
			
	
			<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ExportExcel();">Export Excel</a>

		</div>	</td>
</tr>	
</table>
</div>
<div class="search_bar">
<span class="redstar">Select Department :</span>
<select name="department" id="department">
<?php
	$sql="select d.* from mst_departments d where d.br_id=".$_SESSION['br_id']." order by department_name";
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
		echo "<option value='0'>--Select Department--</option>";
		while($row=mysql_fetch_array($res))
		{
			echo "<option value='".$row['department_id']."'";
			if(@$department_id==$row['department_id']) echo " Selected"; echo ">".$row['department_name']."</option>";
		}
?>
</select>
  	Select Month : 
		   <script type="text/javascript">
$(function()
{
    $(".monthPicker").datepicker({
        dateFormat: 'mm-yy',
        changeMonth: true,
        changeYear: true,

        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
            $(this).attr("name",$.datepicker.formatDate('MM-yy', new Date(year, month, 1)));
        }
    });

    $(".monthPicker").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        });
    });
});
</script>
<input type="text" id="month" name="month" class="monthPicker" readonly="readonly" placeholder="Select Month"/>
<input type="button" name="search" id="search" value="Go" class="btn btn-info" onclick="populateStudent('search')"/>
<label style="float: right; display: inline;">Fees Expected Details For <font style="color: green;" id="month_year"><?php echo date("F-Y");?></font>.</label>
</div>
<div id="stuList"></div>
<script type="text/javascript">
populateStudent('all');
</script>

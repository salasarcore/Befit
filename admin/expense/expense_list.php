<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_expense_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_expense_list))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
	if(isAccessModule($id_admin, export_excel_expense_list))
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
function ActionScript(act)
{
	if(document.getElementById("expense_list_id").value=="" && act !="add")
	{
	  	alert("Please select a Expense");
	}
	else
	{
		url="expense/popups/expense_record.php?act="+act+"&expense_list_id="+document.getElementById("expense_list_id").value;
		open_modal(url,530,500,"EXPENSE");
		return true;
	}
}
function selectID(expense_list_id)
{
document.getElementById("expense_list_id").value=expense_list_id;

$('#tab tr').on('click', function() {
$('#tab tr').removeClass('selected');
$(this).toggleClass('selected');
$(this).find('input:radio').attr('checked', 'checked');
});
}
function  populateDetails(act)
{
var from_date=$("#from_date").val();
var to_date=$("#to_date").val();
var from_amount=$("#from_amount").val();
var to_amount=$("#to_amount").val();
	if((from_date!="" && to_date=="")||(from_date=="" && to_date!="")){
		alert('Please enter "time from" and "time to"');
		return false;
	}
	else if((from_amount!="" && to_amount=="")||(from_amount=="" && to_amount!="")){
		alert('Please specify amount range');
		return false;
	}
	else if(act=="expensereport"){
		url="expense/popups/expense_report.php?expense_id="+$("#expense").val()+"&from_date="+$("#from_date").val()+"&to_date="+$("#to_date").val()+"&from_amount="+$("#from_amount").val()+"&to_amount="+$("#to_amount").val();
		window.open(url,'EXPENSE REPORT','height=600,width=1000,scrollbars=1');
	}
	else{
	$("#expenseList").empty();
	var url="expense/ajax/get_expense_list.php?act="+act+"&expense_id="+$("#expense").val()+"&from_date="+$("#from_date").val()+"&to_date="+$("#to_date").val()+"&from_amount="+$("#from_amount").val()+"&to_amount="+$("#to_amount").val();
	makePOSTRequest(url,'','expenseList');
	}
}
function ExportExcel()
{
	url = 'expense/expenseListToExcel.php';
	window.open(url, '_blank');
}
</script>
<div class="page_head">
<div id="navigation">
	<a href="pages.php"> Home</a><a> Expense</a>  <span style="color: #000000;">Expense List</span>
</div>

<form name="records" action="pages.php?src=expense/expense_list.php">
<table width="100%" class="adminform1">
<tr>
<td> <h2>Expense List</h2>
</td>	
	<td>
		<div id="option_menu">
			<a class="addnew" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
			<a class="edit" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
			<a class="delete" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>
			<a class="addnew" href="javascript:void(0);" onClick="javascript:populateDetails('expensereport');">Print Report</a>
		
			<a class="addnew" href="javascript:void(0);"
						onClick="javascript:ExportExcel();">Export Excel</a>
			
		</div>
	</td>
</tr>	
</table>
</div>
<div class="search_bar">

<span class="redstar">Select Expense : </span>
<select name="expense" id="expense">
<?php
		$sql="select expense_id,c.name as cat_name,m.type as type,m.name as exp_name from expense_category as c,expense_master as m where c.exp_cat_id=m.exp_cat_id ";
		$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
		echo "<option value=''>--All Expenses-Type(Category)--</option>";
		while($row=mysql_fetch_array($res))
		{
			echo "<option value='".$row['expense_id']."'";
			echo ">".$row['exp_name']."-".$row['type']." (".$row['cat_name'].")</option>";
		}
?>
	  	</select>
	  	Time Period : <input name="from_date" type="text" class="date" id="from_date" style="max-width: 6em;" readonly/>
        <script type="text/javascript">
				  $(function() {
						$( "#from_date" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
						});
					});

		</script> 
			To <input name="to_date" type="text" class="date" id="to_date" size="5" style="max-width: 6em;" readonly/>
        <script type="text/javascript">
				  $(function() {
						$( "#to_date" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
						});
					});
		</script>
		Amount Range : 
		<input name="from_amount" type="text" class="date" id="from_amount" style="max-width: 6em;" onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="10"/> 
		To
		<input name="to_amount" type="text" class="date" id="to_amount" style="max-width: 6em;" onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="10"/>
		
		<input type="button" onclick="populateDetails('')" value="Go" class="btn search"/>
		<a class="mylink" href="javascript:void(0);" style="font-size: 12px; margin-left: 20px;" onClick="javascript:populateDetails('today');">Today</a>,
		<a class="mylink" href="javascript:void(0);" style="font-size: 12px;"  onClick="javascript:populateDetails('month');">Last Month</a>,
		<a class="mylink" href="javascript:void(0);" style="font-size: 12px;" onClick="javascript:populateDetails('quarter');">Last Quarter</a>
</div>
<div id="expenseList"></div>
<input type="hidden" name="expense_list_id" id="expense_list_id" value="" />
</form>	
<script type="text/javascript">
populateDetails('');
</script>

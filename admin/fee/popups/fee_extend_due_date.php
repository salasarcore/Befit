<?php 
@session_start();
include('../../conn.php');?>
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../../php/js_css_common.php');
/*include("../../modulemaster.php");


$id=option_fee_collection_list_extendduedate;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
<script type="text/javascript" src="../../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<script language="javascript">

$(document).ready(function()
{
	$("#feeselect").hide();
	$("#feeDtls").hide();
	var valid="";
	$("#reg_no").blur(function()	{
	if($.trim($("#reg_no").val())!=""){
		
		$("#feeselect").hide();
		$("#feeexpected").hide();
		$("#feeDtls").hide();
		$("#stuDtls").empty();
		$("#exptDtls").empty();
		$("#stuDtls").removeClass().addClass('messagebox').text('Checking...').fadeIn("slow");

		$.post("../ajax/populate_student_details.php",{ regno:$(this).val() } ,function(data)
		        {
				  var mytool_array=data.split(":");
				  if($.trim(mytool_array[0])=='no')
				  {  
				  	$("#stuDtls").fadeTo(200,0.1,function()
					{ 
					  $(this).html('Invalid Registration Number').addClass('messageboxerror').fadeTo(900,1);
					});	
		          }
				  else
				  {
				  	$("#stuDtls").fadeTo(200,0.1,function()
					{ 

					  $(this).html(data).addClass('messageboxok').fadeTo(900,1);	
					});
				  	
				  }

		        });

		$.post("../ajax/get_collection_details.php",{ regno:$(this).val() } ,function(data)
		        {

				  	$("#exptDtls").fadeTo(200,0.1,function() 
					{ 

					  $(this).html(data).addClass('messageboxok').fadeTo(900,1);	
					});
				  

		        });
        
		$.post("../ajax/get_expected_list.php?duedate='Y'",{ regno:$(this).val() } ,function(data)
        {
		  var mytool_array=data.split(":");
		  if($.trim(mytool_array[0])=='no')
		  {  
		  	$("#feeexpected").fadeTo(200,0.1,function()
			{ 
			  $(this).html($.trim(mytool_array[1])).addClass('messageboxerror').fadeTo(900,1);
			});	
          }
		  else
		  {
		  	$("#feeexpected").fadeTo(200,0.1,function()
			{ 
		  		$("#feeexpected").removeClass('messageboxerror');
			  $(this).html(data).addClass('messageboxok').fadeTo(900,1);	
			});
		  	
		  }

        });
         

		}
		else
		{
			alert("Enter Registration Number");
			$("#reg_no").val('');
			$("#reg_no").focus();
			return false;
		}

	});

	$('#interval,#extended').keyup(function(){
		var value = $('#this').val();
		if(this.value.match(/[^0-9]/g))
		{
			this.value = this.value.replace(/[^0-9]/g,'');
		}
	});
});

function submitform(frm)
{
	
	var days=frm.interval.value;
	if(days.trim()=="")
	{
	alert("Please enter interval in days");
	frm.interval.value="";
	frm.interval.focus();
	}
	else if(days.trim()>365)
	{
	alert("Day in interval can nat be greater than a year");
	frm.interval.value="";
	frm.interval.focus();
	}
	else
	{
		if(confirm("Are you sure you want proceed?")){
			var url = "../ajax/submit_fee_extend_due_date.php?act=submit";

		    $.ajax({
		           type: "POST",
		           url: url,
		           data: $("#fees").serialize(),
		           success: function(data)
		           {
		        	   $("#feeselect").hide();
		        	   $("#feeDtls").html(data);
		        	   $("#feeDtls").show();
		           }
		         });

    		return false;
		}
		else
			$("#no_of_inst").focus();
	}
}

function populatedetails()
{
	if($("#expecteddropdown").val()!='')
		$("#feeselect").show();
	else
		$("#feeselect").hide();
	
	$("#feeDtls").hide();
	$("#no_of_inst").val('');
	$("#interval").val('');
}
</script>
</head>
<body>
	<div class="shadow">
		<form name="fees" id="fees" autocomplete="off">
			<table width="100%" style="font-size: 12px;">
				<tr>
					<td>REGISTRATION NO: <input name="reg_no" id="reg_no" type="text"	value="" size="15" maxlength="30" /></td>
				</tr>
				<tr>
					<td colspan="2" align="left">
						<div id="stuDtls" style="float: left; margin-right: 20px;"></div>
						<div id="exptDtls" style="float: left;"></div>
					</td>
				</tr>
				<tr>
					<td align="center" width="100%"><div id="feeexpected" style="float: center;"></div></td></tr>
					<tr><td align="center" width="100%"><div id="feeselect" style="float: left; width: 100%;">
					<table style="width: 100%;">
					<tr><td align="right" style='font-size:12; width:50%;'>NUMBER OF DAYS EXTENSION :</td><td><input type="text" name="interval" id="interval" required="required" maxlength="3"/></td></tr>
					<tr><td align="center" colspan="2">
					<input type="button" align="center" name="Save" class="btn save" value="SAVE" id="Save" style="" onclick="return submitform(this.form);" />
					<input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
					</td></tr>
					
					</table>
					</div></td>
				</tr>
				<tr>
					<td align="center" width="100%"><div id="feeDtls" style="float: left; width: 100%;"></div></td>
				</tr>
				<tr>
					<td align="center"><div id="paidDtls" style="float: center;"></div></td>
				</tr>
				
			</table>
		</form>
	</div>
</body>
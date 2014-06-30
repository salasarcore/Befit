<?php
include_once("functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_add_sms_contacts,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_add_sms_contacts))
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
function checkInputData() {	
	var passedValidation = false;
	if($.trim($("#number").val())=="" && $.trim($("#name").val())=="" && $("#file").val()==""){
		alert('Please fill atleast one detail.');
		$("#name").val('');
		$("#number").val('');
		$("#name").focus();
		
	}
	else if($.trim($("#number").val())=="" && $.trim($("#name").val())=="" && $("#file").val()!=""){
		 passedValidation = true;
	}
	else if($.trim($("#number").val())=="" && $.trim($("#name").val())!=""){
		alert('Please enter mobile number.');
		$("#number").val('');
		$("#number").focus();
	}
	else if($.trim($("#number").val())!="" && $.trim($("#name").val())==""){
		alert('Please enter name.');
		$("#name").val('');
		$("#name").focus();
	}
	else if($.trim($("#number").val())!="" && $("#number").val().length!=10){
		alert('Mobile number should be 10 digits.');
		$("#name").focus();
	}
	else{
		$.ajax({
		    type: "GET",
		    dataType:"JSON",
		    async: false,
		    url: "sms/ajax/check_contacts.php?number="+$("#number").val()+"&name="+$("#name").val(),
		    success: function(data)
		    {
		 	   if(data=="false"){
		 	 	   alert("Mobile number already exist.");
		 	 	 $("#number").focus();
		 	   }
		 	   else{
			 	   if($("#number").val()!="" && $("#file").val()==""){
						alert('Contact details saved successfully');
						$("#name").val('');
						$("#number").val('');
						$("#name").focus();
				 	   }
			 	   else
				 	   passedValidation = true;
		 	   }
		    }
		  });
		}
	return passedValidation;
}

$(document).ready(function() {
	$("#name").focus();
    $("#records").on("submit", function () {
        return checkInputData();
    });
});
</script>	
<div class="page_head">
<div id="navigation"><a href="pages.php"> Home</a><a>Utility</a><a>SMS</a><a>For Nonregistered Users</a><span style="color: #000000;"> Add Contacts</span></div>
<h2>Add Contacts</h2>
</div>

<div style="padding-left: 10px;">
<form name="records" id="records" action="pages.php?src=sms/previewexcel.php" method="post" enctype="multipart/form-data" autocomplete="off">
<a class="mylink" onclick="window.location = 'sms/Import contacts Excel/nonregistered_contacts.xls';" style="font-weight: bolder;" title='download the excel file' href="javascript:void(0);" target="_self">DOWNLOAD EXCEL FORMAT</a><br><br>
<table class="adminform1">
			<tr>
				<td align="right"><label>Name : </label></td>
				<td><input type="text" name="name" id="name" maxlength="200" /></td>
			</tr>
			<tr>
				<td align="right"><label>Mobile Number : </label></td>
				<td><input type="text" name="number" id="number" maxlength="10" onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" /></td>
			</tr>
			<tr>
				<td align="right"><label>Import Excel : </label></td>
				<td><input type="file" name="file" id="file"/></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-top: 10px;"><input type="submit" class="btn save" name="submit" id="submit" value="Proceed"/></td>
			</tr>
		</table>
</form>
</div>
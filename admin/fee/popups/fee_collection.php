<?php
@session_start();
/*$id=option_fee_collection_list_collectfee;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/

$regno="";
if(makeSafe(isset($_GET['stu_id']))){
	
	$stuid = makeSafe($_GET['stu_id']);
	$ID = base64_decode($stuid);
	$row=getDetailsById("mst_students","stu_id",$ID);
	$regno = $row['reg_no'];
	
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>


<script language="javascript">
$(document).ready(function()
{
		$("#paytypediv").hide();
		
	
	var datastring="value="+$("#reg_no_hidden").val();
	 if($.trim($("#reg_no_hidden").val())!=""){
	  	 $.ajax({
	  		type: "GET",
			url:"fee/ajax/search_students.php",
			data: datastring,
			dataType:"JSON",
			 success: function(data){
				$('#reg_no').val(data[0].value);
				$("#reg_no").blur();
			}
	  	}); }

	$("#reg_no").keyup(function(){
		$("#reg_no_hidden").val('');
		var datastring="value="+$("#reg_no").val();
		
		  	 $.ajax({
		  		type: "GET",
				url:"fee/ajax/search_students.php",
				data: datastring,
				 dataType:"JSON",
				success: function(data){
					
					 var values=data;
					 
		       $("#reg_no").autocomplete({
			       minlength:0,
		    		source: function (request,response){

		          		var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");

		              	var matching = $.grep(values, function (value){
		                  var name = value.value;
		                  var id = value.id;
		                 
		                  
		               return matcher.test(name) || matcher.test(id);
		              });

						response(matching);
		       
		          },	
		          select:function( event, ui ){
		           	  
		            	
		            	$('#reg_no').val(ui.item.value);
		            	$('#reg_no_hidden').val(ui.item.id);
		                }
		          
		       });

		       	
		    		
				}
		  	});
	});

	$('#reg_no').keypress(function(e) {		    
	    if ( e.which == 13 ) {e.preventDefault();	  $("#reg_no").trigger("blur");  }
	});
	
	$("#reg_no").blur(function() {  
		$("#trnsrefno").val('');
      if($.trim($("#reg_no_hidden").val())!=""){
		$("#stuDtls").empty();
		$("#exptDtls").empty();
		$("#feeselect").empty();
		$("#feeDtls").empty();
		$("#paidDtls").empty();
		$("#paytypediv").hide();
		$("#stuDtls").removeClass().addClass('messagebox').text('Checking...').fadeIn("slow");

		
		
		$.post("fee/ajax/populate_student_details.php",{ regno:$('#reg_no_hidden').val() } ,function(data)
        {
        
		  var mytool_array=data.split(":");
		  if($.trim(mytool_array[0])=='no')
		  {  
		  	$("#stuDtls").fadeTo(200,0.1,function()
			{ 
		  		$("#reg_no").val('');
		  		$("#reg_no").focus();
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

		$.post("fee/ajax/get_collection_details.php",{ regno:$('#reg_no_hidden').val() } ,function(data)
		        {
	       
				  	$("#exptDtls").fadeTo(200,0.1,function() 
					{ var mytool_array=data.split("<\/script>");
					  if($.trim(mytool_array[1])!=""){
					  $(this).html(data).addClass('messageboxok').fadeTo(900,1);	
					  $("#paytypediv").show();
						}
					});
		        });

		}
		
      
	});


	$("#trnsrefno").blur(function() {
		$("#reg_no").val('');
	      if($.trim($("#trnsrefno").val())!=""){
			$("#stuDtls").empty();
			$("#exptDtls").empty();
			$("#feeselect").empty();
			$("#feeDtls").empty();
			$("#paidDtls").empty();
			$("#paytypediv").hide();
			var expected="";

			$.ajaxSetup({async: false});

			$.post("fee/ajax/get_transaction_ref_details.php",{ trnsrefno:$('#trnsrefno').val() } ,function(data)
			        {
			        
					  var mytool_array=data.split(":");
					  if($.trim(mytool_array[0])=='no')
					  {  
					  	$("#stuDtls").fadeTo(200,0.1,function()
						{ 
					  		
						  $(this).html(mytool_array[1]).addClass('messageboxerror').fadeTo(900,1);
						  
						});	
			          }
					  else
					  {
						  var my_array=data.split(",");
					  		$('#reg_no_hidden').val(my_array[0]);	
					  		$('#expectedtrns').val(my_array[1]);
					  		
					  }
			        });
	        
			if($.trim($("#reg_no_hidden").val())!=""){
			$.post("fee/ajax/populate_student_details.php",{  regno:$('#reg_no_hidden').val() } ,function(data)
	        {
	        
			
			  	$("#stuDtls").fadeTo(200,0.1,function()
				{ 
				  $(this).html(data).addClass('messageboxok').fadeTo(900,1);	
				});
			  
	        });


			$.post("fee/ajax/get_collection_details.php",{  regno:$('#reg_no_hidden').val() } ,function(data)
			        {
		       
					  	$("#exptDtls").fadeTo(200,0.1,function() 
						{ var mytool_array=data.split("<\/script>");
						  if($.trim(mytool_array[1])!=""){
						  $(this).html(data).addClass('messageboxok').fadeTo(900,1);	
							}
						});
			        });

			var dataString = 'act=search&expected='+$('#expectedtrns').val()+"&regno="+$('#reg_no_hidden').val();
		    $.ajax({
		    	type: "POST",
		    	async: false, 
		    	url:"fee/ajax/get_fee_details.php",
			    data: dataString,
			    success: function(html){
			    	$("#paidDtls").html(html);
				}
	   		});

			}
	      }
		});

	
	$("input[name=paytype]:radio").change(function () {
	var regno=$.trim($("#reg_no_hidden").val());
	var act=$(this).val();

	document.getElementById("feeDtls").innerHTML = "<br><img src='../../../images/loading.gif' alt='Loading..'><br> Please Wait ..";

	$.ajax({
    	type: "GET",
    	url:"fee/ajax/get_fee_details.php",
	    data: "act="+act+"&regno="+regno,
	    success: function(html){
	    	$("#feeDtls").html(html);
	    	$("#paidDtls").empty();
		}
		});
	});
});

function submitform()
{
	var err="";
	if($('input[id=payexpt]:radio').is(':checked')){
		var count = $("[type='checkbox']:checked").length;
		if(count==0){
			err="not checked";
alert("Please select atleast one fee installment to proceed further");
return false;
			}
	}

	if(confirm("Are you sure you want proceed?")){
	var url = "fee/ajax/submit_fees.php?act=submit";

    $.ajax({
           type: "GET",
           url: url,
           data: $("#fees").serialize(),
           success: function(data)
           {       
        	   $("#feeDtls").empty();
        	   $("#paidDtls").html(data);
        	   $("#payall").prop('checked', false);
        	   $("#payexpt").prop('checked', false);
           }
         });
    		return false;
		}
		else
			return false;

}

function populateexpected(act)
{
	$("#exptid").val('');
	$("#feeDtls").empty();
	$("#paidDtls").empty();
	 if(act=="expected")
	 {
		var id= $("#expected").children(":selected").attr("id");
    	if($("#expected").val()!=""){
		var url="fee/ajax/get_expected_details.php?act="+act+"&exptid="+id;
		makePOSTRequest(url,'','feeDtls');
    	}
	}
}
function populatediscount(obj)
{
	$(obj).parent().siblings().children('input[id=discount]').val(Math.round($(obj).children(":selected").attr("id")));

	if(obj.value!=0)
	 	$(obj).parent().siblings().children('input[id=discount]').attr("readonly",false);
	else
		$(obj).parent().siblings().children('input[id=discount]').attr("readonly",true);
	
}
function deletecollection(coll_id)
{
var r = confirm("Delete this means deleting all the fee transaction data for this collection. You cannot undo this action.");
	if(r == true)
	{
		var r = confirm("Are you sure you want to complete this action ?");
		if(r == true)
		{
		$.ajax({
	    	type: "GET",
	    	url:"fee/ajax/delete_fees.php",
		    data: "fee_collection_id="+coll_id,
		    success: function(html){
		    	alert(html);
		    	populateexpt();
			}
    	});
		}
	}
}
function populateexpt()
{
	var expectedid = $("#expected").val();
	var regno=$.trim($("#reg_no_hidden").val());
	if(document.forms["fees"].elements["expected"].selectedIndex != -1){
	var dataString = 'act=search&expected='+expectedid+"&regno="+regno;
		    $.ajax({
		    	type: "POST",
		    	url:"fee/ajax/get_fee_details.php",
			    data: dataString,
			    success: function(html){
			    	$("#paidDtls").html(html);
				}
	    });
}
else
$("#paidDtls").empty();
}
</script>
<link rel="Stylesheet" href="../js/jquery.ui.timepicker.css" rel="stylesheet"/>
<script src="../js/jquery.ui.timepicker.js"></script>
<style>
.ui-timepicker-table .ui-timepicker-title
{
line-height:0.9em;
}
.ui-timepicker-table td, .ui-timepicker-table th.periods {
 font-size:10px;
}
.timestyle
{
width:100px;
height:22px;
}
</style>
</head>
<body>
<div class="page_head">
<div id="navigation">
	<a href="pages.php"> Home</a><a> Fees</a><a href="pages.php?src=fee/fee_collect.php">Collect fees</a> <span style="color: #000000;">Fees Collection</span>
</div>

<table width="100%" class="adminform1">
<tr>
<td> 
<h2>Fees Collection <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>"  style="padding-left:10px;" class="btn close"> <span style="padding-left:7px;">BACK</span></a></h2>
				 </td>
</tr>	
</table>
</div>
	<div class="shadow">
		<form name="fees" id="fees" autocomplete="on">
			<table width="100%" style="font-size: 12px;">
				<tr>
					
					
				<td style="padding-top: 10px;">REGISTRATION NO/NAME : <input name="reg_no" id="reg_no" type="text"	value="" maxlength="30" style="width: 30%"   /> TRNASACTION REFERENCE NUMBER : <input name="trnsrefno" id="trnsrefno" type="text"	value="" maxlength="30" style="width: 20%" /></td>
					
				</tr>
				<tr>
					<td align="left" style="padding-top: 10px;">
						<div id="stuDtls" style="float: left; margin-right: 20px;"></div>
						<div id="exptDtls" style="float: left;"></div>
					</td>
				</tr>
				<tr>
					<td align="center" width="100%">
					<div id="paytypediv" style="float: center;">
						<input type="radio" name="paytype" id="payall" value="all"> All Fees <input type="radio" id="payexpt" name="paytype" value="expected"> Expected Wise
					</div>
					</td>
				</tr>
				<tr>
					<td align="center" width="100%"><div id="feeselect" style="float: center;"></div></td>
				</tr>
				<tr>
					<td align="center" width="100%"><div id="feeDtls" style="float: left; width: 100%;"></div></td>
				</tr>
				<tr>
					<td align="center"><div id="paidDtls" style="float: center;"></div></td>
				</tr>
			</table>
			
			<input name="reg_no_hidden" id="reg_no_hidden" type="hidden"	value="<?php echo $regno; ?>" size="15" maxlength="30" />
			<input name="expectedtrns" id="expectedtrns" type="hidden"	value="" size="15" maxlength="30" />
		</form>
	</div>
</body>

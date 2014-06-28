<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include("../../functions/common.php");
include("../../functions/comm_functions.php");

$act=makeSafe(@$_GET['act']);
?>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<link rel="Stylesheet" type="text/css" href="../../../css/jquery-ui.css" />
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../../modulemaster.php");

if($act=="add")
$id=option_fee_expected_list_add;
elseif($act=="delete")
$id=option_fee_expected_list_delete;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$action=makeSafe(@$_POST['action']);
$Errs="";
if(makeSafe(@$_GET['expectedID'])!="") $expectedID=makeSafe(base64_decode(@$_GET['expectedID']));

if($action=="SAVE" || @$action=="UPDATE")
{
	$null_array= array();
	$section1=array();
	$getstudents=array();
	$poststudents=array();
	$students=array();
	$sections=array();
	$getexpectedstudents=array();
	$feeexpectedname=makeSafe($_POST['txtfeeexpectedname']);
	$feeparticularid=makeSafe($_POST['fee_particular']);
	$start=makeSafe($_POST['adtstart']);
	$last=makeSafe($_POST['adtlast']);
	$due=makeSafe($_POST['adtdue']);
	$startdate=strtotime(makeSafe($_POST['adtstart']));
	$lastdate=strtotime(makeSafe($_POST['adtlast']));
	$duedate=strtotime(makeSafe($_POST['adtdue']));
	$rdoall=makeSafe($_POST['rdoall']);

	if(trim(@$feeexpectedname)=="")
		$Errs= "<div class='error'>Please Enter Fee Expected Name</div>";
	elseif($feeparticularid=="")
	$Errs= "<div class='error'>Please select Fee Particular Name</div>";
	else if($lastdate <= $startdate)
		$Errs="<div class='error'>Last Date should be greater than start date.</div>";
	else if($duedate < $lastdate)
		$Errs="<div class='error'>Due Date should be greater than last date.</div>";
	else if($duedate <= $startdate)
		$Errs="<div class='error'>Due Date should be greater than start date.</div>";
	else
	{
		if(@$action=="SAVE")
		{
			$newfeeid=getNextMaxId("fee_expected","fee_expected_id")+1;
			if($rdoall=="all"){
				$sql1="select GROUP_CONCAT(distinct(ss.session_id) SEPARATOR ',') as section_id from session_section ss,student_class sc where ss.session_id=sc.session_id and  ss.session='".$_SESSION['d_session']."' and ss.department_id in (select department_id from mst_departments where br_id=".$_SESSION['br_id'].")";
				$res1=mysql_query($sql1);
				$sec=mysql_fetch_assoc($res1);
				$section1=explode(",",$sec['section_id']);
			}
			elseif($rdoall=="section")
			{
				if(isset($_POST['section'])) $sections= $_POST['section'];
				for($x=0; $x<count($sections); $x++)
					$section1[$x]=makeSafe($sections[$x]);
			}
			elseif($rdoall=="student")
			{
				if(isset($_POST['section'])) $sections= $_POST['section'];
				for($x=0; $x<count(@$sections); $x++)
					$section1[$x]=makeSafe(@$sections[$x]);
					
				if(isset($_POST['student'])) $students= $_POST['student'];
				for($y=0; $y<count($students); $y++)
					$poststudents[$y]=makeSafe($students[$y]);
			}
			if(!empty($section1) && $section1[0]!=""){
				if($rdoall=="student" && (empty($poststudents) || $poststudents[0]==""))
					$Errs= "<div class='error'>No Student(s) Selected For This Fee Expected.</div>";
				else
				{
					foreach($section1 as $section){
						unset($getstudents);
						$sql2="select * from fee_expected where fee_particulars_id=".$feeparticularid." and FIND_IN_SET('".$section."',section_id)";
						$res=mysql_query($sql2);
						if(mysql_num_rows($res)>0){
							$resultset=mysql_fetch_assoc($res);
							$expectedupdateid=$resultset['fee_expected_id'];

							if($rdoall=="student")
								$sql3="select stu_id from student_class where stu_id IN (".implode(',',$poststudents).") and session_id=".$section;
							else
								$sql3="select stu_id from student_class where session_id=".$section;

							$res3=mysql_query($sql3);
							while ($row = mysql_fetch_assoc($res3))
								$getstudents[]=$row['stu_id'];

							$sql4="select stu_id from student_expected_fees where section_id=".$section." and fee_expected_id=".$expectedupdateid;
							$res4=mysql_query($sql4);
							while ($row = mysql_fetch_assoc($res4))
								$getexpectedstudents[]=$row['stu_id'];

							$results=array_diff($getstudents, $getexpectedstudents);
							$resultstudents=array_merge($results,$null_array);

							foreach ($resultstudents as $student){
								$newfeeexpectedid=getNextMaxId("student_expected_fees","student_expected_fees_id")+1;
								$sql1="insert into student_expected_fees (student_expected_fees_id,fee_expected_id,stu_id,section_id)";
								$sql1=$sql1."values(".$newfeeexpectedid.",".@$expectedupdateid.",".$student.",".$section.")";
								mysql_query($sql1);
							}
						}
						else
						{
							if($rdoall=="student")
								$sql2="select stu_id from student_class where stu_id IN (".implode(',',$poststudents).") and session_id=".$section;
							else
								$sql2="select stu_id from student_class where session_id=".$section;

							$res2=mysql_query($sql2);
							if (mysql_num_rows($res2)>0)
							{
								while ($row = mysql_fetch_assoc($res2))
									$getstudents[]=$row['stu_id'];

								$sql1="select section_id from fee_expected where fee_expected_id=".$newfeeid;
								$res1=mysql_query($sql1);
								if(mysql_num_rows($res1) > 0)
								{
									$row=mysql_fetch_array($res1);
									$sectionstr=$row['section_id'].",".$section;
									$sql="update fee_expected set section_id='".$sectionstr."' where fee_expected_id=".$newfeeid;
									mysql_query($sql,$link);
								}
								else
								{
									$sql="insert into fee_expected(fee_expected_id,fee_particulars_id,name,start_date,last_date,due_date,section_id,created_at,updated_at,updated_by)";
									$sql = $sql ." values('". @$newfeeid. "','".@$feeparticularid."','".@$feeexpectedname."','".@$start."','".@$last."','".@$due."','".$section."',NOW(),NOW(),'".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
									mysql_query($sql,$link);
								}

								foreach ($getstudents as $student){
									$newfeeexpectedid=getNextMaxId("student_expected_fees","student_expected_fees_id")+1;
									$sql1="insert into student_expected_fees (student_expected_fees_id,fee_expected_id,stu_id,section_id)";
									$sql1=$sql1."values(".$newfeeexpectedid.",".@$newfeeid.",".$student.",".$section.")";
									$res=mysql_query($sql1);
								}
							}
						}
						if(mysql_affected_rows($link)>=0)
							$Errs= "<div class='success'>Record Saved Successfully</div>";
						else
							$Errs= "<div class='error'>Record Not Saved Successfully</div>";
					}
				}
			}
			else
				$Errs= "<div class='error'>No Section(s) Selected For This Fee Expected.</div>";
		}
	}
}
if(@$action=="DELETE")
{
	$query   = "select fee_collection_id as id from fee_collection where fee_expected_id=".@$expectedID." union all 
	select fee_payment_type_id as id from fee_payment_type  where fee_expected_id=".@$expectedID;
	$result  = mysql_query($query);
	if(mysql_num_rows($result)>0)
		$Errs= "<div class='error'>To delete this record, please delete its related records from fee collection and fee installments.</div>";
	else
	{
		$query  = "delete FROM student_expected_fees where fee_expected_id=".@$expectedID;
		
		$result  = mysql_query($query);
		if($result)
		{
			$query  = "delete  FROM fee_expected where fee_expected_id=".@$expectedID;
			$result  = mysql_query($query);
			if($result)
				$Errs=  "<div class='success'>Record Deleted Successfully</div>";
			else
				$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
		}
		else
			$Errs=  "<div class='error'>Record Not Deleted Successfully</div>";
	}
}

if(@$act=="edit" || @$act=="delete" )
{
	$row=getDetailsById("fee_expected","fee_expected_id",@$expectedID);
	if(!empty($row))
	{
		$particular_id=@$row['fee_particulars_id'];
		$expected_name=@$row['name'];
		$start_date=@$row['start_date'];
		$last_date=@$row['last_date'];
		$due_date=@$row['due_date'];
		$sections=@$row['section_id'];
		$created_at=date("jS-M-Y, g:iA",strtotime(@$row['created_at']));
		$updated_at=date("jS-M-Y, g:iA",strtotime(@$row['updated_at']));
		$updated_by=@$row['updated_by'];

		$secstr="";
		$secids=explode(",",$row['section_id']);
		foreach($secids as $sec)
		{
			$getdet=getDetailsById("session_section","session_id",$sec);
			$getdeptdet=getDetailsById("mst_departments","department_id",$getdet['department_id']);
			$secstr.=$getdeptdet['department_name']." - ".$getdet['section'].", ";
		}
		$secstr=rtrim($secstr,", ");
	}
}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">


<script>
$(document).ready(function () {
	    $('#section').hide();
	    $('#student').hide();
	    $('#label').hide();
	    $('#rdosec').click(function () {
		    $('#allstu').hide();
	    	 $('#section').show();
	    	 $('#label').show();
	    	 $('#student').hide();
	    });
	    $('#rdostu').click(function () { 
		    $('#allstu').hide();
	    	 $('#section').show();
	    	 $('#label').show();
	    	 $('#student').show();
	    });
	    
	    $('#rdoall').click(function () { 
		    $('#allstu').show();
	    	 $('#section').hide();
	    	 $('#label').hide();
	    	 $('#student').hide();
	    });
	 
  		  $('#section').change(function(){
				var str = "";
				var count = $("#section :selected").length;
				var value = $("#section").val();
			
				if(value!="selectsection")
				{
					$("#section :selected").each(function () {
					if($(this).val() == "selectsection")
					 	$("#section option[value='selectsection']").attr('selected', false);
					else
						str += $(this).text() + " ";
					}); 

					  $("#sec_id").val($("#section").val());
					
	<?php 
		/**
		 *  This code will display message that more than max values are selected 
		 *  and will deselect the last selected value. 
		 */
	?>

			var value = $("#sec_id").val();
			$('#section option:selected').removeAttr('selected');
			
			var myValue = value.split(',');
			for(var i = 0; i<myValue.length ; i++ )
				$("#section option[value='" + myValue[i] + "']").attr('selected', 'selected');
					
			var sectionname = "";
			 $("#section :selected").each(function () {
				 sectionname += $(this).text() + ",";
				 
			 });
		<?php 
		/**
		 * this code will call ajax function and will fill student option values for the selected sections.
		 */
		?>
			var sectionid = $("#sec_id").val();
			var dataString = 'action=search&value='+sectionid+'&name='+sectionname;
				    $.ajax({
				    	type: "GET",
				    	url:"../ajax/students_ajax.php",
					    data: dataString,
					    success: function(html){
					    	$("#student").html("");
					    	$("#student").append(html);
					    	<?php 
					    	/**
					    	 * This code will auto select the students when the section is deselected.
					    	 */
							?>
					    	var studentid =  $("#stu_id").val();
					    	if(studentid!="")
					    	{
						    	var myValue = studentid.split(',');	
						    	for(var i = 0; i<myValue.length ; i++ )
									$("#student option[value='" + myValue[i] + "']").attr('selected', 'selected');
																
						    	$('#student').trigger('change');
					    	}
						}
			    });
			}
			else
			{
				$("#section").val("");
				$("#student").val("");
			}
	});  
});

function validatefeeexpected(frm)
{
	if(frm.txtfeeexpectedname.value.trim()=="")
	{
		alert('Fee expected name should not be blank');
		frm.txtfeeexpectedname.value="";
		frm.txtfeeexpectedname.focus();
		return false;
	}
	if(frm.fee_particular.value==""){
		alert("Please select fee particular name");
		frm.fee_particular.focus();
		return false;
	}
	if(frm.adtstart.value.trim()==""){
		alert("Please select start date");
		frm.adtstart.focus();
		return false;
	}
	if(frm.adtlast.value.trim()==""){
		alert("Please select last date");
		frm.adtlast.focus();
		return false;
	}
	if(frm.adtdue.value.trim()==""){
		alert("Please select due date");
		frm.adtdue.focus();
		return false;
	}
	 if (Date.parse($.trim($('#adtlast').val())) <= Date.parse($.trim($('#adtstart').val())))
	 {
         alert('Last date should be greater than Start date  ');
         frm.adtlast.focus();
          return false;
	 }
	
	 if (Date.parse($.trim($('#adtdue').val())) < Date.parse($.trim($('#adtlast').val())))
	 {
         alert('Due date can not be less than last date  ');
         frm.adtdue.focus();
          return false;
	 }

	 if($.trim($("#action").val())=="DELETE")
	 {
		 if(!confirm("Are you sure, you want to delete this record?"))
		 {
			 return false;
		 }
	 }

}
function ClearField(frm)
{
	  frm.txtfeeexpectedname.value = "";
	  frm.fee_particular.value="";
	  frm.adtstart.value="";
	  frm.adtlast.value="";
	  frm.adtdue.value="";
	  $('#section').hide();
	  $('#student').hide();
	  $('#label').hide();
	  $('#rdoall').attr('checked',true);
	  frm.txtfeeexpectedname.focus();
}
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2>
			<?php echo strtoupper($act); ?>
			FEE EXPECTED</h2></div>
		
		
		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frm"	action="fee_expected.php?act=<?php echo $act; ?>&expectedID=<?php echo base64_encode(@$expectedID);?>"	onsubmit="return validatefeeexpected(this);" autocomplete="off">
			<table class="adminform">
				<tr>
					<td width="44%" align="right" class="redstar">Fee Expected Name :</td>
					<td width="43%">
					<input type="text" name="txtfeeexpectedname" id="txtfeeexpectedname" size="30"	value='<?php echo @$expected_name; ?>' maxlength="200"	<?php if(@$act=="delete") echo "readonly"; ?> />
					</td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Fee Particular Name :</td>
					<td width="43%">
					<select name="fee_particular" id="fee_particular" <?php if(@$act=="edit" || @$act=="delete") echo "disabled"; ?> >
					<?php
					$res=array();
					$sqlquery=mysql_query("select * from fee_particulars where fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")");
					while($row=mysql_fetch_array($sqlquery))
							$res[]=$row;
					echo "<option value='' selected>--Select fee Particular--</option>";
					foreach($res as $row1)
					{
						echo"<option value='".$row1['fee_particulars_id']."' id='".$row1['total_amount']."'";
						if(@$particular_id==$row1['fee_particulars_id']) echo "selected";
						echo ">".$row1['name']."</option>";
					}
						 ?>
					</select>
					</td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Start Date:</td>
					<td width="43%">
					<input name="adtstart" type="text" class="date"	id="adtstart" size="11" value='<?php echo @$start_date; ?>'	readonly <?php if(@$act=="delete") echo "disabled"; ?> />
					<script	type="text/javascript">
					  $(function() {
						$( "#adtstart" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});
					});
				  </script>
				  </td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Last Date:</td>
					<td width="43%">
					<input name="adtlast" type="text" class="date" id="adtlast" size="11" value='<?php echo @$last_date; ?>' readonly <?php if(@$act=="delete") echo "disabled"; ?> />
					<script	type="text/javascript">
				  		$(function() {
						$( "#adtlast" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});
					});
				  </script>
				</td>
				</tr>
				<tr>
					<td width="44%" align="right" class="redstar">Due Date:</td>
					<td width="43%">
					<input name="adtdue" type="text" class="date"	id="adtdue" size="11" readonly value='<?php echo @$due_date; ?>' <?php if(@$act=="delete") echo "disabled"; ?> />
					<script	type="text/javascript">
						  $(function() {
							$( "#adtdue" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});
					});
				  </script>
				 </td>
				</tr>
				<?php if($act!="add"){?><tr><td align="right">Selected Sections :</td><td><?php echo @$secstr; ?></td></tr><?php }?>
				<tr>
					<td width="44%" align="center" colspan="2">
					<input type="radio"	name="rdoall" id="rdoall" value="all" <?php if(@$act=="delete") echo "disabled"; ?> checked="checked">All Departments
					<input type="radio" name="rdoall" id="rdosec" value="section" <?php if(@$act=="delete") echo "disabled"; ?> >Section 
					<input	type="radio" name="rdoall" id="rdostu" value="student"	<?php if(@$act=="delete") echo "disabled"; ?> >Student
					<div id="label" name="label" style="color: #FF0000;">Press Ctrl to select multiple values.</div>
					</td>
				</tr>
				
				<tr>
					<td  align="center" colspan="2" >
						<div id="allstu" style="width:400px;  height:100px; color:#555555; margin-top:12px; border:1px solid #cccccc; border-radius: 4px; padding:0px; overflow:auto">
							<table align="left" style="margin-left:10px; ">
								<tr>
									<td style="font-size:14px;  font-family:Arial; color:#555555;">Department List</td>
								</tr>
								<?php
								$sql="SELECT DISTINCT mst_departments.department_name, session_section.section FROM session_section , mst_departments, student_class  WHERE session_section.session_id=student_class.session_id AND session_section.department_id = mst_departments.department_id AND session_section.session='".$_SESSION['d_session']."' AND mst_departments.br_id='".$_SESSION['br_id']."' AND session_section.freeze = 'N'";
								$res=mysql_query($sql);
								while($row=mysql_fetch_array($res,MYSQL_ASSOC)){
								?>	<tr>
										<td><li style="font-size:14px;  font-family:Arial; color:#555555;"><?php echo $row['department_name']; ?>-<?php echo $row['section']; ?></li></td>
									</tr>
								<?php }
								
								?>
								
							</table>
						</div>
					</td>
				</tr>	
				
				<tr>
					<td align="center" colspan="2">
					<select name="section[]" id="section" multiple="multiple" style="width: 400px; height: 100px;" <?php if(@$act=="delete") echo "disabled"; ?> >
							<option value='selectsection'>Select Sections</option>
							<?php
							$sql="select DISTINCT ss.section ,ss.session_id,d.department_name,d.department_id from session_section ss,student_class sc, mst_departments d where ss.session_id=sc.session_id and ss.department_id=d.department_id and ss.session='".$_SESSION['d_session']."' and d.br_id=".$_SESSION['br_id']." AND ss.freeze = 'N'";
							$res=mysql_query($sql);
							while($row1=mysql_fetch_array($res))
								echo "<option value=".$row1['session_id'].">".$row1['department_name']."-".$row1['section'].'</option>';
							?>
					</select><br />
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
					<select name="student[]" id="student" multiple size="8" style="width: 400px;"	<?php if(@$act=="delete") echo "disabled"; ?>>
					</select>
					</td>
				</tr>
				<?php if(@$created_at!="") {?><tr><td align="right">Created At :</td><td><?php echo @$created_at; ?></td></tr><?php } ?>
				<?php if(@$updated_at!="") {?><tr><td align="right">Updated At :</td><td><?php echo @$updated_at; ?></td></tr><?php } ?>
				<?php if(@$updated_by!="") {?><tr><td align="right">Updated By :</td><td><?php echo @$updated_by; ?></td></tr><?php } ?>
				<tr>
					<td align="center" colspan=2>
					<input type="submit" class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
					if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
				 <?php if(@$act!="delete"){?> <input type="button" class="btn reset"	value="RESET" id="reset " name="reset"	onClick="ClearField(this.form)" /> <?php }	?>
				 <input type=button class="btn close"	value="CLOSE" onClick="parent.emailwindow.close();">
				 <input	type='hidden' name='action' id="action"	value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' />
					</td>
				</tr>
			</table>
			<input type='hidden' name='session_id' id='session_id'	value='<?php echo $session_id; ?>'>
			<input type="hidden" id="sec_id" /><input type="hidden" id="stu_id" />
		</form>
		<script language=javascript>
			document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
		</script>
	</div>
</body>
</html>

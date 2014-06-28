<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include("../../functions/common.php");
include("../../functions/comm_functions.php");

$act=makeSafe(@$_GET['act']);
?>
<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../../modulemaster.php");

$id=option_fee_expected_list_editsection;
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

$row2=array();
$sections=array();
$section1=array();
$students=array();
$poststudents=array();
$getstudents=array();
if(@$action=="UPDATE")
{
	$feeparticularid=makeSafe($_POST['particular_id']);

	if(isset($_POST['sections'])) $sections= $_POST['sections'];
	for($x=0; $x<count(@$sections); $x++)
		$section1[$x]=makeSafe(@$sections[$x]);

	if(isset($_POST['student'])) $students= $_POST['student'];
	for($y=0; $y<count($students); $y++)
		$poststudents[$y]=makeSafe($students[$y]);
	
	if(!(!empty($section1) && empty($poststudents))){
		$query  = "delete  FROM student_expected_fees where fee_expected_id=".@$expectedID;
		mysql_query($query);

		$sql="update fee_expected set section_id='' where fee_expected_id=".$expectedID;
		mysql_query($sql,$link);
	}
	if(!empty($poststudents)){
		foreach($section1 as $section){
			unset($getstudents);
			$sql2="select * from fee_expected where fee_particulars_id=".$feeparticularid." and FIND_IN_SET('".$section."',section_id) and fee_expected_id!=".@$expectedID;
			$res=mysql_query($sql2);
			if(mysql_num_rows($res)==0){
				$sql2="select stu_id from student_class where stu_id IN (".implode(',',$poststudents).") and session_id=".$section;
				$res2=mysql_query($sql2);
				if (mysql_num_rows($res2)>0)
				{
					while ($row = mysql_fetch_assoc($res2))
						$getstudents[]=$row['stu_id'];

					$sql1="select section_id from fee_expected where fee_expected_id=".$expectedID;
					$res1=mysql_query($sql1);

					$row=mysql_fetch_array($res1);
					$sectionstr=$row['section_id'].",".$section;
					$sectionstr=ltrim($sectionstr,",");
					$sql="update fee_expected set section_id='".$sectionstr."' where fee_expected_id=".$expectedID;
					mysql_query($sql,$link);

					foreach ($getstudents as $student){
						$newfeeexpectedid=getNextMaxId("student_expected_fees","student_expected_fees_id")+1;
						$sql1="insert into student_expected_fees (student_expected_fees_id,fee_expected_id,stu_id,section_id)";
						$sql1=$sql1."values(".$newfeeexpectedid.",".@$expectedID.",".$student.",".$section.")";
						$res=mysql_query($sql1);
					}
				}
			}
		}
	}
	if(mysql_affected_rows($link)>0)
		$Errs= "<div class='success'>Record Updated Successfully</div>";
}

if(@$act=="editsection")
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

<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<link rel="Stylesheet" type="text/css" href="../../../css/jquery-ui.css" />
<script>
$(document).ready(function () {

	$("input[name='sections[]']").click(function(){
		var str = "";
		var value = "";
		var name = "";
		document.getElementById("loading").innerHTML = "<br><img src='../../../images/loading.gif' alt='Loading..'><br> Please Wait ..";
		 $("input[name='sections[]']:checked").each(function(){
			 value += $(this).val()+",";
			 name +=$(this).attr("id")+",";
		  });
	  
		name=name.substring(0, name.length -1);
		value=value.substring(0, value.length -1);
		var expected_id=$.trim($("#expt_id").val());
			
		<?php 
		/**
		 * this code will call ajax function and will fill student option values for the selected sections.
		 */
		?>
		var dataString = 'action=editsection&value='+value+'&name='+name+'&expected_id='+expected_id;
			    $.ajax({
			    	type: "GET",
			    	url:"../ajax/students_ajax.php",
				    data: dataString,
				    success: function(html){
				    	$("#students").html("");
				    	$("#loading").html("");
				    	$("#students").append(html);
					}
		    });
	});  
});

function validatefeeexpectedsection(frm)
{
	if($.trim($("#action").val())=="UPDATE")
		 if(!confirm("Are you sure, you want to update this record?"))
			 return false;
}
</script>
</head>
<body>
	<div id="middleWrap">
		<div class="head"><h2>EDIT FEE EXPECTED SECTIONS</h2></div>
		<span id="spErr"><?php echo $Errs;?> </span>
		<form method="post" name="frm" action="edit_fee_expected_sections.php?act=<?php echo $act; ?>&expectedID=<?php echo base64_encode(@$expectedID);?>"	onsubmit="return validatefeeexpectedsection(this);"	autocomplete="off">
			<table class="adminform" width="100%">
				<tr>
					<th width="44%" align="right">Fee Expected Name :</th>
					<td width="43%"><label><?php echo @$expected_name; ?></label></td>
				</tr>
				<tr>
					<th align="right">Selected Sections :</th>
					<td><?php echo @$secstr; ?></td>
				</tr>
				<tr><td><span id="loading"></span></td></tr>
				<tr>
					<th>Department - Section Name</th>
					<th>Roll No - Student Name</th>
				</tr>
				<tr>
					<td align="left" style="vertical-align: top;">
						<div style="overflow: auto; width: 97%; height: 300px; border: 1px solid #336699; padding-left: 5px">
							<?php 
							$sql="select distinct(section_id) as section_id from student_expected_fees where fee_expected_id=".@$expectedID;
							$ressql=mysql_query($sql);
							while($result=mysql_fetch_assoc($ressql))
								$row2[]=$result['section_id'];
							$sql="select DISTINCT ss.section ,ss.session_id as session_id,d.department_name,d.department_id from session_section ss,student_class sc, mst_departments d where ss.session_id=sc.session_id and ss.department_id=d.department_id and ss.session='".$_SESSION['d_session']."'";
							$res=mysql_query($sql);
							while($row1=mysql_fetch_array($res)){
								?>
							<input type="checkbox" name="sections[]" id="<?php echo $row1['department_name']." - ".$row1['section']; ?>" value="<?php echo $row1['session_id']; ?>"
								<?php
								if(in_array($row1['session_id'], @$row2)) echo "checked";
            					echo ">".$row1['department_name']." - ".$row1['section']; ?><br>
							<?php }?>
						</div>
					</td>
					<td>
						<div style="overflow: auto; width: 95%; height: 300px; border: 1px solid #336699; padding-left: 5px" id="students"></div>
					</td>
				</tr>
				<?php if(@$created_at!="") {?><tr><th align="right">Created At :</th><td><?php echo @$created_at; ?></td></tr><?php } ?>
				<?php if(@$updated_at!="") {?><tr><th align="right">Updated At :</th><td><?php echo @$updated_at; ?></td></tr><?php } ?>
				<?php if(@$updated_by!="") {?><tr><th align="right">Updated By :</th><td><?php echo @$updated_by; ?></td></tr><?php } ?>
				<tr>
					<td align="center" colspan=2>
						<input type="submit" class="btn save" value="UPDATE"	name="B1">
						<input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
						<input type='hidden' name='action' id="action" value="UPDATE" />
					</td>
				</tr>
			</table>
			<input type='hidden' name='session_id' id='session_id' value='<?php echo $session_id; ?>'>
			<input type="hidden" id="particular_id"	name="particular_id" value="<?php echo @$particular_id; ?>" />
			<input type="hidden" id="expt_id" value="<?php echo @$expectedID; ?>" />
		</form>
		<script language=javascript>
			document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
		</script>
	</div>
</body>
</html>

<?php 
@session_start();
include('../../conn.php');
include('../../functions/common.php');
include('../../functions/comm_functions.php');

makeSafe(extract($_REQUEST));
/*include("../../modulemaster.php");
$id=option_fee_discount_report_alldepartment;
$id_admin=$_SESSION['empid'];
$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
?>
	<link href="../../css/classic.css" rel="stylesheet" type="text/css">
<?php
	include('../../modules/js_css_common.php');
	echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
	exit;
	}
}*/
?>
<style>
.hide{visibility: hidden;}
</style>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript">
function getKey(keyval,loc){
if(keyval in localStorage)
	$("#"+loc).show();
 else 
	$("#"+loc).remove();
}
</script>
<script>
 function printpage() {
 document.getElementById("print").className="hide";
  window.print();
}
</script>

<div class="cont">
<table align="center" width="100%" style="padding: 5px 0 5px 0;">
<tr>
		<td width="15%"  align="center">
		<img src="<?php echo is_file('../../../site_img/school_logo/'.@$subdomainname.'.png')?'../../../site_img/school_logo/'.@$subdomainname.'.png':'../../../site_img/school_logo/demo.png';?> " width="100px" />
		</td>
		<td align="center"><h2><?php echo @SCHOOL_NAME;?></h2>
		<?php 
		$query="SELECT * FROM mst_branch where br_name='".@$_SESSION['br_name']."'";
		$res=mysql_query($query) or die('Error, query failed');
		$row1 = mysql_fetch_array($res, MYSQL_ASSOC);
		echo $row1['br_addr1'].", ".$row1['br_country'].", ". $row1['br_state']."-".$row1['br_pin'];
		?>
		</td>
</tr>
</table>
<hr/>

<?php

if($act=="dept" || $act=="all")
{
 $sql="SELECT md.department_id,department_name, sum(collected_amount) as collected,IFNULL(sum(discount_amount),0) as discount
					from fee_collection as fc
					INNER JOIN student_expected_fees as sef ON fc.stu_id=sef.stu_id and sef.fee_expected_id=fc.fee_expected_id
					INNER JOIN fee_expected as fe ON fe.fee_expected_id=sef.fee_expected_id
					INNER JOIN session_section as ss ON ss.session_id=sef.section_id
					INNER JOIN mst_departments as md ON md.department_id=ss.department_id
					LEFT JOIN fee_discount_collection as fdc ON fc.fee_collection_id=fdc.fee_collection_id
					LEFT JOIN fee_particulars as fp ON fp.fee_particulars_id=fe.fee_particulars_id where md.br_id=".$_SESSION['br_id'];
 					if(trim(@$dept_id)!="" && @$dept_id!='null') $sql.="  and md.department_id=".$dept_id;
 					if(trim(@$collection_date)!="") $sql.="  and date(fc.created_at)='".$collection_date."'";
			$sql.=" GROUP BY md.department_id";
			$collection=mysql_query($sql);
if(mysql_num_rows($collection)>0){
$total_amount=0;
$total_collected=0;
$total_discount=0;
?>
<br>
<table width="100%" >
<tr><td align="center">DEPARTMENT WISE FEES DISCOUNT DETAILS <?php if(trim(@$collection_date)!="") echo "OF ".date("jS-M-Y",strtotime($collection_date)); ?></td></tr>
<tr>
<td valign="top">
<table width="100%" style="frame:box;border: 1px solid; border-collapse: collapse; table-layout:fixed; font-size: 14px;" >
<tr>
  	<th style="border-bottom: 1px solid #000000;" align="center">DEPARTMENT NAME</th>
  	<th style="border-bottom: 1px solid #000000;" align="center">TOTAL AMOUNT</th>
  	<th style="border-bottom: 1px solid #000000;" align="center">TOTAL COLLECTED</th>
  	<th style="border-bottom: 1px solid #000000;" align="center">TOTAL DISCOUNT</th>
</tr>
<?php 
	while ($row = mysql_fetch_array($collection)) {
		$sqlparticulars=mysql_fetch_assoc(mysql_query("select sum(total_particulars_amount) as collected from (select (count(sef.stu_id))*total_amount as total_particulars_amount, sef.fee_expected_id,sef.section_id
						from student_expected_fees as sef,mst_departments as md,session_section as ss,fee_expected as fe,fee_particulars as fp
						where md.department_id=ss.department_id and ss.session_id=sef.section_id
						and fe.fee_expected_id=sef.fee_expected_id and fe.fee_particulars_id=fp.fee_particulars_id and md.br_id=".$_SESSION['br_id']." and md.department_id=".$row['department_id']."
						group by md.department_id,sef.fee_expected_id) as collected"));
?>
<tr>
	<td style="word-wrap: break-word;" align="center"><?php echo $row['department_name'];?></td>
	<td  align="center"><?php echo round($sqlparticulars['collected']); ?></td>
	<td  align="center"><?php echo round($row['collected']);?></td>
	<td  align="center"><?php echo round($row['discount']);?></td>
</tr>
  					
<?php 
$total_amount+=round($sqlparticulars['collected']);
$total_collected+=round($row['collected']);
$total_discount+=round($row['discount']);
}
if($act=="all"){
?>
<tr>
<th style="border-top: 1px solid #000000;" align="right">TOTAL : </th>
<th style="border-top: 1px solid #000000;" align="center"><?php echo $total_amount;?></th>
<th style="border-top: 1px solid #000000;" align="center"><?php echo $total_collected;?></th>
<th style="border-top: 1px solid #000000;" align="center"><?php echo $total_discount;?></th>
</tr>
</table>
</td>
</tr>
</table>
<?php }
}
}
elseif ($act=="student")
{
	$sql="SELECT fe.name,concat_ws(' ',ms.stu_fname,ms.stu_mname,ms.stu_lname) as stu_name,rollno,section, sum(collected_amount) as collected,IFNULL(sum(discount_amount),0) as discount,sef.fee_expected_id
	from fee_collection as fc
	INNER JOIN student_expected_fees as sef ON fc.stu_id=sef.stu_id and sef.fee_expected_id=fc.fee_expected_id
	INNER JOIN mst_students as ms ON ms.stu_id=sef.stu_id
	INNER JOIN student_class as sc ON sc.stu_id=sef.stu_id
	INNER JOIN fee_expected as fe ON fe.fee_expected_id=sef.fee_expected_id
	INNER JOIN session_section as ss ON ss.session_id=sef.section_id
	INNER JOIN mst_departments as md ON md.department_id=ss.department_id
	LEFT JOIN fee_discount_collection as fdc ON fc.fee_collection_id=fdc.fee_collection_id
	LEFT JOIN fee_particulars as fp ON fp.fee_particulars_id=fe.fee_particulars_id
	where sef.stu_id=$stu_id group by  sef.fee_expected_id";
	
	$collection=mysql_query($sql);
		if(mysql_num_rows($collection)>0){
		$stu_class=getDetailsById("student_class","stu_id",$stu_id);
		$stu_details=getDetailsById("mst_students","stu_id",$stu_id);
		$department=getDetailsById("mst_departments","department_id",$stu_class['department_id']);
		$section=getDetailsById("session_section","session_id",$stu_class['session_id']);
?>
<div align="center"><u>STUDENT DETAILS</u></div>
<table width="100%"  id="mytable">
<tr>
<td align="left" style="padding-left: 20px;">Name : <?php echo $stu_details['stu_fname']." ".$stu_details['stu_mname']." ".$stu_details['stu_lname']?></td>
<td align="right" style="padding-right: 20px;">Roll No : <?php echo $stu_class['rollno']?></td>
</tr>

<tr>
<td align="left" style="padding: 0 20px 0 20px;">Section : <?php echo $section['section']?></td>
<td align="right" style="padding: 0 20px 0 20px;">Department : <?php echo $department['department_name']?></td>
</tr>

<tr>
<td id='branchtd' align="left" style="padding: 0 20px 0 20px;">Branch : <?php echo $row1['br_name'];?></td>
<?php echo "<script>getKey('branchcheck','branchtd');</script>" ?>
<td id='gendertd' align="right" style="padding: 0 20px 0 20px;">Gender : <?php echo $stu_details['sex'];?></td>
<?php echo "<script>getKey('gendercheck','gendertd');</script>" ?>
</tr>

<tr>
<td id='mstatustd' align="left" style="padding: 0 20px 0 20px;">Marital Status : <?php echo $stu_details['m_status'];?></td>
<?php echo "<script>getKey('mstatuscheck','mstatustd');</script>" ?>
<td id='dobtd' align="right" style="padding: 0 20px 0 20px;">Date Of Birth: <?php echo date('jS-M-Y',strtotime($stu_details['dob']));?></td>
<?php echo "<script>getKey('dobcheck','dobtd');</script>" ?>
</tr>

<tr>
<td id='castetd' align="left" style="padding: 0 20px 0 20px;">Caste : <?php echo $stu_details['cust'];?></td>
<?php echo "<script>getKey('castecheck','castetd');</script>" ?>
<td id='nationalitytd' align="right" style="padding: 0 20px 0 20px;">Nationality: <?php echo $stu_details['nationality'];?></td>
<?php echo "<script>getKey('nationalitycheck','nationalitytd');</script>" ?>
</tr>

<tr>
<td id='religiontd' align="left" style="padding: 0 20px 0 20px;">Religion : <?php echo $stu_details['religioun'];?></td>
<?php echo "<script>getKey('religioncheck','religiontd');</script>" ?>
</tr>

<tr>
<td id='fathertd' align="left" style="padding: 0 20px 0 20px;">Father Name : <?php echo $stu_details['father_name'];?></td>
<?php echo "<script>getKey('fathercheck','fathertd');</script>" ?>
<td id='mothertd' align="right" style="padding: 0 20px 0 20px;">Mother Name: <?php echo $stu_details['mother_name'];?></td>
<?php echo "<script>getKey('mothercheck','mothertd');</script>" ?>
</tr>

<tr>
<td id='fatheroccutd' align="left" style="padding: 0 20px 0 20px;">Father Occupation : <?php echo $stu_details['father_occupation'];?></td>
<?php echo "<script>getKey('fatheroccucheck','fatheroccutd');</script>" ?>
<td id='motheroccutd' align="right" style="padding: 0 20px 0 20px;">Mother Occupation : <?php echo $stu_details['mother_occupation'];?></td>
<?php echo "<script>getKey('motheroccucheck','motheroccutd');</script>" ?>
</tr>
</table>
<script>
$('#mytable tr').each(function(){
    var hide = true;
    var i=0;
    $(this).find('td').each(function(){

        if($(this).html() != ''){
            hide = false;
            if(i%2==0)
            	$(this).css('text-align','left');

            i++;
        }
    });
    if(hide == true)
        $(this).hide();
});

</script>
<br>
<table width="100%" >
<tr><td align="center"><u>FEES DISCOUNT DETAILS</u></td></tr>
<tr>
<td valign="top">
<table width="100%"  style="frame:box;border: 1px solid; border-collapse: collapse; table-layout:fixed; font-size: 14px;"  >
<tr>
	<th style="border-bottom: 1px solid #000000;" align="center">FEE NAME</th>
	<th style="border-bottom: 1px solid #000000;" align="center">TOTAL AMOUNT</th>
	<th style="border-bottom: 1px solid #000000;" align="center">TOTAL COLLECTED</th>
	<th style="border-bottom: 1px solid #000000;" align="center">TOTAL DISCOUNT</th>
</tr>
<?php 
$total_amount=0;
$total_collected=0;
$total_discount=0;
while ($row = mysql_fetch_array($collection)) {
	$sqlparticulars=mysql_fetch_assoc(mysql_query("select sum(total_amount) as collected, sef.fee_expected_id,sef.section_id
				from student_expected_fees as sef,mst_departments as md,session_section as ss,fee_expected as fe,fee_particulars as fp
				where md.department_id=ss.department_id and ss.session_id=sef.section_id
				and fe.fee_expected_id=sef.fee_expected_id and fe.fee_particulars_id=fp.fee_particulars_id and sef.stu_id=$stu_id and sef.fee_expected_id=$row[fee_expected_id]
				"));
?>
<tr>
  	<td  align="center"><?php echo $row['name'];?></td>
  	<td  align="center"><?php echo round($sqlparticulars['collected']); ?></td>
  	<td  align="center"><?php echo round($row['collected']);?></td>
  	<td  align="center"><?php echo round($row['discount']);?></td>
</tr>
	<?php 		
		$total_amount+=round($sqlparticulars['collected']);
$total_collected+=round($row['collected']);
$total_discount+=round($row['discount']);
}
?>
<tr>
<th style="border-top: 1px solid #000000;" align="right">TOTAL : </th>
<th style="border-top: 1px solid #000000;" align="center"><?php echo $total_amount;?></th>
<th style="border-top: 1px solid #000000;" align="center"><?php echo $total_collected;?></th>
<th style="border-top: 1px solid #000000;" align="center"><?php echo $total_discount;?></th>
</tr>
</table>
</td>
</tr>
</table>
<?php 
}}
?>
<table  width='100%'>
<tr><td colspan="2" align="right" style="padding-right: 15px;"> <br />Authorized Signatory<br /><br /></td></tr>
</table>
<hr />
<table  width="100%" >
<tr><td></td><td align="right" style="padding-right: 50px;">For <?php echo @SCHOOL_NAME;?></td></tr>
<tr><td colspan="2" align="center"><input type="button" id="print" name="print" value="Print" onClick="printpage();" /></td></tr>
</table> 
</div>
<?php 
@session_start();
include('../../conn.php');
include('../../functions/common.php');
include('../../functions/comm_functions.php');

$act=makeSafe(@$_GET['act']);
/*include("../../modulemaster.php");

$id=option_fee_collection_list_viewdetails;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		include('../../modules/js_css_common.php');
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$stuid=makeSafe($_REQUEST['stuid']);
$exptid=makeSafe($_REQUEST['exptid']);
$total_collected=0;
$count=0;
$next_inst="";
$stu_class=getDetailsById("student_class","stu_id",$stuid);
$stu_details=getDetailsById("mst_students","stu_id",$stuid);
$department=getDetailsById("mst_departments","department_id",$stu_class['department_id']);
$section=getDetailsById("session_section","session_id",$stu_class['session_id']);

$sqlcoll="select * from fee_collection where fee_expected_id=".$exptid." and stu_id=".$stuid;
$result=mysql_query($sqlcoll);
while ($rows = mysql_fetch_assoc($result))
	$collection[]=$rows;


$expected=getDetailsById("fee_expected","fee_expected_id",$exptid);
$particular=getDetailsById("fee_particulars","fee_particulars_id",$expected['fee_particulars_id']);
?>
<style>
.hide{visibility: hidden;}
</style>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/ddsmoothmenu.js"></script>
<script type="text/javascript" src="../../../js/date_time_currency_number_email.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../../../js/hint.js"></script>
<script type="text/javascript" src="../../../js/ajax.js"></script>
<script type="text/javascript" src="../../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../../js/dhtmlwindow.js"></script>
<script type="text/javascript" src="../../../js/modal.js"></script>
<script type="text/javascript">
function getKey(keyval,loc){
if(keyval in localStorage)
	$("."+loc).show();
 else 
	$("."+loc).remove();
}
</script>
<script>
 function printpage() {
 document.getElementById("print").className="hide";
  window.print();
}
</script>
<body>


<div class="cont">
<table align="center" width="100%" style="padding: 5px 0 5px 0;border: 1px solid; border-collapse: collapse; ">
<tr><th colspan="2" style="border-bottom: 1px solid;"><font size="5">FEES RECEIPT</font></th></tr>
<tr>
		<td width="15%"  align="center" valign="middle">
		<img src="<?php echo is_file('../../../site_img/school_logo/'.@$subdomainname.'.png')?'../../../site_img/school_logo/'.@$subdomainname.'.png':'../../../site_img/school_logo/demo.png';?> " width="80px" />
		</td>
		<td align="center"><font style="font-size: 25px; font-weight: bold;"><?php echo @SCHOOL_NAME;?></font><br>
		<?php 
		$query="SELECT * FROM mst_branch where br_name='".@$_SESSION['br_name']."'";
		$res=mysql_query($query) or die('Error, query failed');
		$row1 = mysql_fetch_array($res, MYSQL_ASSOC);
		echo $row1['br_addr1'].", ".$row1['br_country'].", ". $row1['br_state']."-".$row1['br_pin'];
		?>
		</td>
</tr>
<tr><td colspan="2">
<hr style="width: 98%;margin:0;padding:0;"/>
<table width="100%" class="mytable" >
<tr><td style="padding: 0 20px 0 20px;">Receipt No. : <?php echo $row1['br_name']." - ".$collection[count($collection)-1]['fee_collection_id'];?></td><td align="right" style="padding: 0 20px 0 20px;">Date : <?php echo date("jS-M-Y");?></td></tr>

<tr>
<td align="left" style="padding: 0 20px 0 20px;">Name : <?php echo $stu_details['stu_fname']." ".$stu_details['stu_mname']." ".$stu_details['stu_lname'];?></td>
<td align="right" style="padding: 0 20px 0 20px;">Roll No : <?php echo $stu_class['rollno']?></td>
</tr>

<tr>
<td align="left" style="padding: 0 20px 0 20px;">Section : <?php echo $section['section']?></td>
<td align="right" style="padding: 0 20px 0 20px;">Department : <?php echo $department['department_name']?></td>
</tr>

<tr>
<td class='branchtd' align="left" style="padding: 0 20px 0 20px;">Branch : <?php echo $row1['br_name'];?></td>
<?php echo "<script>getKey('branchcheck','branchtd');</script>" ?>
<td class='gendertd' align="right" style="padding: 0 20px 0 20px;">Gender : <?php echo $stu_details['sex'];?></td>
<?php echo "<script>getKey('gendercheck','gendertd');</script>" ?>
</tr>

<tr>
<?php echo "<script>getKey('mstatuscheck','mstatustd');</script>" ?>
<td class='dobtd' align="right" style="padding: 0 20px 0 20px;">Date Of Birth: <?php echo date('jS-M-Y',strtotime($stu_details['dob']));?></td>
<?php echo "<script>getKey('dobcheck','dobtd');</script>" ?>
</tr>


</table>
<br />
<table width="100%" >
<tr><td align="center"><u>FEES DETAILS OF <?php echo $expected['name']; ?></u></td></tr>
<tr>
<td valign="top">
<table  width="100%" style="frame:box;border: 1px solid; border-collapse: collapse; table-layout:fixed; font-size: 14px;">
<tr >
<td style="border-bottom: 1px solid #000000; padding: 2px;" align="center">Collected Date</td>
<td style="border-bottom: 1px solid #000000;" align="center">Collected Amount</td>
<td class="discounttd" style="border-bottom: 1px solid #000000;" align="center">Discount Amount</td>
<td class="finetd" style="border-bottom: 1px solid #000000;" align="center">Fine Amount</td>
<td style="border-bottom: 1px solid #000000;" align="center">Total Amount</td>
<td class="remarktd" style="border-bottom: 1px solid #000000;" align="center">Remark</td></tr>
<?php 
foreach($collection as $row)
{
	$total_collected_amt=0;
	$fine_amount=0;
	$sqlcoll="select discount_amount from fee_discount_collection where fee_collection_id=".$row['fee_collection_id'];
	$result3=mysql_query($sqlcoll);
	$row3 = mysql_fetch_assoc($result3);
	$total_collected_amt=$row['collected_amount']+$row3['discount_amount'];
	
	$sqlcoll="select fine_amount from fee_fine_collection where fee_collection_id=".$row['fee_collection_id'];
	$result4=mysql_query($sqlcoll);
	$row4 = mysql_fetch_assoc($result4);
	$fine_amount=$row4['fine_amount'];
	
	?>
	<tr>
	<td align="center" style="padding: 2px;"><?php echo date('jS-M-Y',strtotime($row['updated_at']));?></td>
	<td align="center"><?php echo round($row['collected_amount']);?></td>
	<td class="discounttd" align="center"><?php echo round($row3['discount_amount']);?></td>

	<td class="finetd" align="center"><?php echo round($row4['fine_amount']);?></td>
	<td align="center"><?php echo round($total_collected_amt);?></td>
	<td class="remarktd" align="center" style="word-break: break-all;"><?php echo $row['remark'];?></td></tr>
<?php 
$total_collected+=$total_collected_amt;
}
?>
</table> 
</td></tr>
</table>
<?php echo "<script>getKey('fee_collection_remark','remarktd');</script>" ?>
<?php echo "<script>getKey('fee_collection_fine_amount','finetd');</script>" ?>
<?php echo "<script>getKey('fee_collection_discount_amount','discounttd');</script>" ?>
<?php 
$sql="select * from fee_payment_type where stu_id=".$stuid." and fee_expected_id=".$exptid;
$res=mysql_query($sql);
$row=mysql_fetch_assoc($res);
$no_of_inst=$row['no_of_installments'];
$inst_amt=$row['installment_amount'];
$total_amount=$particular['total_amount'];
$count=1;
for($i=1;$i<=$no_of_inst;$i++)
	if($total_collected>=($i*$inst_amt)) $count++;
	$due_date=date("Y-m-d",strtotime("+".($count*$row['interval_in_days'])." days", strtotime($row['start_date'])));
?>
<table width="100%" class="mytable">
<tr>
<td width="40%" style="padding-left: 20px;"><lable style="font-size:12px;">*Total amount does not include fine amount</lable></td>
</tr>
<tr>
<td class="noofinstallments" style="padding: 0 20px 0 20px;">Number of Installment(s) Paid : <?php echo $count;?></td>
<td class="duefee" style="padding: 0 20px 0 20px;" align="right">Due Fee : <?php echo round($total_amount-$total_collected)."/- Only";?></td>
</tr>
<tr>
<td class="nextinstallment" style="padding: 0 20px 0 20px;">Next Installment : <?php echo $inst_amt;?></td>
<td class="duedate" style="padding: 0 20px 0 20px;" align="right">Due Date : <?php echo date("jS-M-Y",strtotime($due_date));?></td>
</tr>
</table>
<?php echo "<script>getKey('fee_collection_no_of_installments','noofinstallments');</script>" ?>
<?php echo "<script>getKey('fee_collection_due_fee','duefee');</script>" ?>
<?php echo "<script>getKey('fee_collection_next_installment','nextinstallment');</script>" ?>
<?php echo "<script>getKey('fee_collection_due_date','duedate');</script>" ?> 
<table  width='100%'>
<tr><td colspan="2" align="right" style="padding-right: 15px;"><br />Authorized Signatory<br /><br /></td></tr>
</table>
<hr  style="width: 98%;margin:0;padding:0;"/>
<table  width="100%" >
<tr><td></td><td align="right" style="padding-right: 50px;">For <?php echo @SCHOOL_NAME;?></td></tr>
</table>
</td></tr> 
</table>
<table  width="100%" >
<tr>
<td colspan="2" align="center"><input type="button" id="print" name="print" value="Print" onClick="printpage();" />
</tr>
</table>
<script>
$('.mytable tr').each(function(){
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
</div>
</body>
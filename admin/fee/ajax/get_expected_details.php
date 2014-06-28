<?php 
include("../../conn.php");
include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
$act=makeSafe($_REQUEST['act']);
if($act=="expected"){
	$exptid=makeSafe($_REQUEST['exptid']);
	$expected=getDetailsById("fee_expected","fee_expected_id",$exptid);
	$particular=getDetailsById("fee_particulars","fee_particulars_id",$expected['fee_particulars_id']);
	$totalamount=$particular['total_amount'];
?>
<label > Enter Amount : </label><input type="text" name="collectedamount" id="collectedamount" value="" onkeyup="if (/[^(\d*?)(\.\d{1,2})?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)(\.\d{1,2})?$]/g,'')" maxlength="10"/>
 <br /><label style="margin-left: 6%">&nbsp;Remark : </label><input type="text" name="remark" id="remark" value="" maxlength="200"/>
 <br /><input type="button" name="submit" id="submit" style="margin-left: 10%" value="Collect" onclick="submitform();"/>
<input type='hidden' name='totalfees' id='totalfees' value="<?php echo $totalamount;?>" />
<input type="hidden" name="exptid" id="exptid" value="<?php echo $exptid;?>" />
<?php 

}
?>


	
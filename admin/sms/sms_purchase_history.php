<?php
include_once("functions/comm_functions.php");
include_once("../functions/common.php");
include_once("../globalConfig.php");

/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 */
include('modules/js_css_common.php');


makesafe(extract($_REQUEST));

$id = option_sms_settings_sms_history;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}

?>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script	type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/redmond/jquery-ui.css" />
<script	src="//code.jquery.com/jquery-1.10.2.js"></script>
<script	src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<br>
<br />
<div id="details1" style="valign: top; margin-left: 68px; margin-top: -28px;">
	<?php 
	@session_start();
	makeSafe(extract($_REQUEST));
	$sql = "SELECT tran_id, school_id, no_of_transactional_purchase, no_of_promotional_purchase, date_purchased, payment_type,transaction_details, bank_name, remarks, updated_by, date_updated 
			FROM schools sc LEFT JOIN school_sms_purchase_dtls spd ON sc.schoolid = spd.school_id WHERE sc.domain_identifier = '".$subdomainname."'";
	$order = " ORDER BY date_purchased DESC";
	$limit = " LIMIT 1";
	$sql=$sql." ".$order.$limit;
	$res=mysql_query($sql,$scslink) or die("Unable to connect to Server, We are sorry for inconvienence caused");
	$row=mysql_fetch_assoc($res);
	$transactional = mysql_query("select count(sms_type) as t_sent from sms_transaction_log where date_sent >= '".$row['date_purchased']."' AND sms_type='T'") or die("Unable to connect to server. We are sorry for the inconvennience caused.");
	$promotional = mysql_query("select count(sms_type) as p_sent from sms_transaction_log where date_sent >= '".$row['date_purchased']."' AND sms_type='P'") or die("Unable to connect to server. We are sorry for the inconvennience caused.");
	$totalnum = mysql_query("select count(*) as total from sms_transaction_log") or die("Unable to connect to server. We are sorry for the inconvennience caused.");
	if($totalnum)
		$total = mysql_fetch_assoc($totalnum);
	if($transactional)
		$tran = mysql_fetch_assoc($transactional);
	if($promotional)
		$prom = mysql_fetch_assoc($promotional);	
	?>
	<div id='details' width="50%">
		<div style='margin-bottom: 6px;'>
			<br>
			<script>
  $(function() {
   $("#tabs1").tabs();
 });
 </script>

			<div id="tabs1" style="width: 500px; align: center;">
				<ul>
					<li><a id='tb1' href="#tabs-1" style='font-family: arial; font-size: 14px; color: #0E864E;'>Current Active SMS Plan</a></li>
					<li><a id='tb2' href="#tabs-2" style='font-family: arial; font-size: 14px; color: #0E864E;'>SMS Purchase History</a></li>
				</ul>
				<div id="tabs-1">
								<div style='border: 1px solid black; border-radius: 7px; margin-bottom: 5px; margin-right: 5px; height: 50%; width: 100%; display: inline-block; padding-top: 10px;'>
									<div style='padding: 5px 5px 5px 5px;'>
										<table border=0 style="font-family: arial; font-size: 14px; bottom: 25px;" width="100%" cellpadding="2">
								<tr valign="top">
									<th style='padding-left: 15px; padding-bottom: 15px; align: center; font-size: 15px;' colspan="2"><u>CURRENT ACTIVE SMS PLAN</u></th>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; width: 65%;'><span>Purchase Date </span>
									<span style="float: right; text-indent: 50px; padding-right: 5px"> : </span></td>
									<td><?php echo date('jS-M-Y',strtotime($row['date_purchased'])); ?></td>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; width: 65%;'><span>Transactional Purchases </span>
									<span style="float: right; text-indent: 20px; padding-right: 5px"> : </span></td>
									<td><?php echo $row['no_of_transactional_purchase']; ?></td>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; width: 65%;'><span>Promotional Purchases </span>
									<span style="float: right; text-indent: 20px; padding-right: 5px"> : </span></td>
									<td><?php echo $row['no_of_promotional_purchase'];?></td>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; width: 65%;'><span>Transactional Messages Consumed</span>
									<span style="float: right; text-indent: 20px; padding-right: 5px"> : </span></td>
									<td><?php echo $tran['t_sent'];?></td>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; padding-bottom: 15px; width: 65%;'><span>Promotional Messages Consumed</span>
									<span style="float: right; text-indent: 20px; padding-right: 5px"> : </span></td>
									<td><?php echo $prom['p_sent'];?></td>
								</tr>
								<tr valign="top">
									<th style='padding-left: 15px; padding-bottom: 15px;' colspan=2><span>TOTAL MESSAGES SENT TO DATE : <?php echo $total['total'];?></span></th>
								</tr>
							</table>
									</div>
								</div>
				</div>
				<div id="tabs-2">
					<?php					
					$sql1 = "SELECT tran_id, school_id, no_of_transactional_purchase, no_of_promotional_purchase, date_purchased, payment_type,transaction_details, bank_name, remarks, updated_by, date_updated
			FROM schools sc LEFT JOIN school_sms_purchase_dtls spd ON sc.schoolid = spd.school_id WHERE sc.domain_identifier = '".$subdomainname."' order by date_purchased desc";
					$res1=mysql_query($sql1,$scslink) or die("Unable to connect to server. We are sorry for the inconvenience caused.");
					$rowscount = mysql_num_rows($res1);
					$count = 0;
					?>
					<div style='border: 1px solid black; border-radius: 7px; margin-bottom: 5px; width: 100%;'><div style='padding: 5px 5px 5px 7px;'>
							<table border=0 style="font-family: arial; font-size: 14px; bottom: 25px;" width="100%" cellpadding="2">								
								<?php
									while($row1=mysql_fetch_array($res1))
{?>
								<tr valign="top">
									<th style='padding-left: 15px; padding-top: 10px; padding-bottom: 15px; align: center; font-size: 15px;' colspan="2"><u>PURCHASE DETAILS FOR <?php echo date('jS M,Y',strtotime($row1['date_purchased']));?></u></th>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; width: 45%;'><span>Transactional Purchases </span>
									<span style="float: right; padding-right: 5px"> : </span></td>
									<td style="width: 50%;"><?php echo $row1['no_of_transactional_purchase']; ?></td>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; width: 45%;'><span>Promotional Purchases </span>
									<span style="float: right; text-indent: 20px; padding-right: 5px"> : </span></td>
									<td style="width: 50%;"><?php echo $row1['no_of_promotional_purchase'];?></td>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; width: 45%;'><span>Payment Type </span>
									<span style="float: right; text-indent: 20px; padding-right: 5px"> : </span></td>
									<td style="width: 50%;"><?php echo $row1['payment_type'];?></td>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; width: 45%;'><span>Bank Name </span>
									<span style="float: right; text-indent: 20px; padding-right: 5px"> : </span></td>
									<td style="width: 50%;"><?php echo $row1['bank_name'];?></td>
								</tr>
								<tr valign="top">
									<td style='padding-left: 15px; width: 45%; padding-bottom: 15px;'><span>Transaction Details </span>
									<span style="float: right; text-indent: 20px; padding-right: 5px"> : </span></td>
									<td style="width: 50%; padding-bottom: 5px;"><?php echo $row1['transaction_details'];?></td>
								</tr>
								<?php $count++; 
								if($count!=$rowscount){?>
								<tr><td colspan=2>
								<hr/></td>
								</tr>
								<?php }}
								?>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
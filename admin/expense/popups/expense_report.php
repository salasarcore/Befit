<?php 
@session_start();
include_once("../../functions/common.php");
include("../../conn.php");
//include('../../check_session.php');
include_once("../../functions/comm_functions.php");
$msg="";
makeSafe(extract($_REQUEST));

?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>EXPENSE REPORT</title>

<link href="css/print.css" rel="stylesheet" type="text/css">
<style>
.hide{display:none;}
</style>
<script>
 function printpage() {
 document.getElementById("print").className="hide";
  window.print();
}
</script>
</head>
<body leftmargin="0" topmargin="0">
<div align="center">
<div class="cont" style="frame:box;border: 1px solid;width:850px;margin-top: 25px;">
<table align="center" width="800px" style="padding: 5px 0 5px 0;" border="0">
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
<hr style="width: 800px;"/>
<table  align="center"  width="800px" border="0">
	<tr><td align="center"  style="padding: 0 0 10px 90px;font-size:14px;width: 580px;">EXPENSE REPORT
	<?php if(trim(@$expense_id)!="") {
				echo " OF <b> ".strtoupper(getDetailsById("expense_master","expense_id",$expense_id)['name'])."</b>";
}
if(trim(@$from_date)!="") {
	echo strtoupper(" BETWEEN <b> ".date("jS-M-Y",strtotime($from_date))."</b> TO <b>".date("jS-M-Y",strtotime($to_date))."</b>");
}
?>
	</td><td style="padding-bottom: 10px;font-size:14px;"><b>Date : </b><?php echo date("jS-M-Y",strtotime(mysql_fetch_assoc(mysql_query("select curdate() as today"))['today']));?></td></tr>
</table>

<table align="center" style="width: 800px;frame:box;border: 1px solid; font-size:14px;border-collapse: collapse; table-layout:fixed;">
<thead>
		<tr>
			<th style="border-bottom: 1px solid #000000;">SR.NO.</th>
			<?php if(trim(@$expense_id)=="") {?><th style="border-bottom: 1px solid #000000;">EXPENSE NAME</th><?php }?>
			<th style="border-bottom: 1px solid #000000;">DATE</th>
			<th style="border-bottom: 1px solid #000000;">AMOUNT</th>
			<th style="border-bottom: 1px solid #000000;">REMARK</th>
			<th style="border-bottom: 1px solid #000000;">CREDITED TO</th>
		</tr>
	</thead>
<?php
$expense_amount=0;

$sql="select m.*,l.* from expense_list as l, expense_master as m, expense_category as c where c.exp_cat_id=m.exp_cat_id and c.br_id='".$_SESSION['br_id']."' and l.expense_id=m.expense_id";
		if(trim(@$expense_id)!="") $sql.=" and l.expense_id=".$expense_id;

		if($from_date!="" && $to_date!="") 
			$sql.=" and date between '".$from_date."' and '".$to_date."'";
		elseif ($from_date!="")
			$sql.=" and date >= '".$from_date."'";
		elseif ($to_date!="")
			$sql.=" and date <= '".$to_date."'";
		
		if($from_amount!="" && $to_amount!="")
			$sql.=" and amount between '".$from_amount."' and '".$to_amount."'";
		elseif ($from_amount!="")
			$sql.=" and amount >= '".$from_amount."'";
		elseif ($to_amount!="")
			$sql.=" and amount <= '".$to_amount."'";
			
	$sql.=" order by l.date ";

	$i=0;
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused ");
	while($row=mysql_fetch_array($res))
	{
		$i=$i+1;
		?>
	<tr >
	<td  align="center"><?php echo $i;?></td>
		<?php if(trim(@$expense_id)=="") {?> <td style="word-wrap: break-word;" align="center"><?php echo $row['name']; ?></td><?php }?>
		<td align="center"><?php echo date("jS-M-Y",strtotime($row['date']));?></td>
		<td align="center"><?php echo round($row['amount'])." (".$row['amount_currency'].")"; ?></td>
		<td style="word-wrap: break-word;" align="center"><?php echo $row['remark']; ?></td>
		<td align="center"><?php echo $row['credited_to']; ?></td>
	</tr>
	<?php
	$expense_amount+=round($row['amount']);
	}
	?>
	<tr><th  style="border-top: 1px solid #000000;" colspan="<?php if(trim(@$expense_id)=='')  echo "6"; else echo "5"; ?>" >TOTAL EXPENSE : <?php echo $expense_amount;?>/-</th></tr>
	</tbody>
</table>
<br>
 <table width='800px' align="center">
	<tr><td colspan="2" align="right" style="padding-right: 15px;"> <br />Authorized Signatory<br /><br /></td></tr>
</table>
<hr style="width: 800px;"/>
<table  width="800px" align="center">
<tr><td></td><td align="right" style="padding-right: 35px;">For <?php echo @SCHOOL_NAME;?></td></tr>
<tr><td colspan="2" align="center"><input type="button" id="print" name="print" value="Print" onClick="printpage();" /></td></tr>
</table> 
</div>
</div>
</body>

</html>
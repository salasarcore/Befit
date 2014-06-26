<?php
@session_start();
include("../../conn.php");
//include('../../check_session.php');
include_once("../../functions/common.php");
include_once("../../functions/comm_functions.php");
makeSafe(extract($_REQUEST));
$uploaddir="../../../site_img/expense/";
?>
<br>
<table id="tab" name="tab" width="100%" style="cursor: pointer;table-layout: fixed;" class="table">
	<thead>
		<tr>
			<th>#</th>
			<th>CATEGORY</th>
			<th>EXPENSE NAME</th>
			<th>DATE</th>
			<th>AMOUNT</th>
			<th>CURRENCY TYPE</th>
			<th>ATTACHMENT</th>
			<th>REMARK</th>
			<th>CREDITED TO</th>
			<th>CREATED ON</th>
			<th>UPDATED ON</th>
			<th>UPDATED BY</th>
		</tr>
	</thead>
<tbody>

	<?php
	
		$sql="select m.*,l.*,c.name as cat_name from expense_list as l, expense_master as m, expense_category as c where c.exp_cat_id=m.exp_cat_id and c.br_id='".$_SESSION['br_id']."' and l.expense_id=m.expense_id";
		if(trim(@$expense_id)!="") $sql.=" and l.expense_id=".$expense_id;
		if($act==""){
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
		}
		elseif($act=="today")
			$sql.=" and date = CURDATE()";
		elseif($act=="month")
			$sql.=" and MONTH(date) = MONTH(DATE_SUB(CURDATE(),INTERVAL 1 MONTH))";
		elseif($act=="quarter")
			$sql.=" and date BETWEEN CURDATE() - INTERVAL 3 MONTH AND CURDATE()";
			
 	$sql.=" order by m.updated_at desc";

	$i=0;
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused ");
	while($row=mysql_fetch_array($res))
	{
		$i=$i+1;
		?>
	<tr  class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?>	onclick="selectID('<?php echo base64_encode($row['expense_list_id']);?>')">
	<td align="center"><input type="radio" name="rdoID" value="<?php echo base64_encode($row['expense_list_id']); ?>" id="rdoID" /></td>
		<td align="center"><?php echo $row['cat_name']; ?></td>
		<td style="word-wrap: break-word;" align="center"><?php echo $row['name']; ?>( <?php echo $row['type'];?> )</td>
		<td align="center"><?php echo date("jS-M-Y",strtotime($row['date']));?></td>
		<td align="center"><?php echo round($row['amount']); ?></td>
		<td align="center"><?php echo $row['amount_currency']; ?></td>
		<td align="center"><?php if($row['mime']!="") {?><a href="<?php echo $uploaddir.DOMAIN_IDENTIFIER."_".base64_encode($row['expense_list_id']).".".$row['mime']; ?>" target="_blank"><img src="css/classic/message_attachment.png" height="20px" width="20px"/></a><?php }?></td>
		<td style="word-wrap: break-word;" align="center"><?php echo $row['remark']; ?></td>
		<td align="center"><?php echo $row['credited_to']; ?></td>
		<td align="center"><?php echo date("jS-M-Y, g:iA",strtotime($row['created_at']));?></td>
		<td align="center"><?php echo date("jS-M-Y, g:iA",strtotime($row['updated_at']));?></td>
		<td align="center"><?php echo $row['updated_by'];?></td>
	</tr>
	<?php
	}
	?>
	</tbody>
	<tfoot>
	<tr><th colspan="12">
	<?php 
	if($i>0)
		echo "TOTAL RECORDS : $i";
	else
		echo "NO RECORDS FOUND";
	?>
	</th></tr>
	</tfoot>
	</table>
<?php 	

if(makeSafe(@$_REQUEST['action'])=="expns")
{ 
	echo '<option  value="">--Select Expense--</option>';
	$sql="select m.*,c.name as cat_name from expense_master as m, expense_category as c where m.exp_cat_id=c.exp_cat_id and c.exp_cat_id='".makeSafe($_REQUEST['catid'])."' and  br_id=".$_SESSION['br_id'];
	$res=mysql_query($sql);
	while ($rows = mysql_fetch_array($res)) {
	echo "<option value='".$rows['expense_id']."'";
    echo ">".$rows['name']."(". $rows['type'].")</option>"; }

} 
?>
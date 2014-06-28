<?php
@session_start();
include('../../conn.php');
include('../../check_session.php');
include('../../functions/comm_functions.php');
include_once("../../functions/common.php");

function getDiscount($discount_id="")
{
	$sql="select * from fee_discount where discarded='N' order by name";
	$res=mysql_query($sql);
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['fee_discount_id']."' id='".$row1['discount_amount']."'";
		if($discount_id==$row1['fee_discount_id']) echo "selected"; echo">".$row1['name']."</option>";
	}
}

$act=makeSafe($_REQUEST['act']);
$regno=makeSafe($_REQUEST['regno']);
$total_collected=0;
$total_amount=0;
$total_balance=0;
$balance=0;
$countrows=0;
$resrows=0;
$collection=array();
$transaction=array();
if($act=="all"){
	$student_details=getDetailsById("mst_students","reg_no",$regno);
	if(!empty($student_details)){
		$stuid=$student_details['stu_id'];

		$sqlcoll="select * from student_expected_fees where stu_id=".$stuid;
		$result=mysql_query($sqlcoll);
		while ($row3 = mysql_fetch_assoc($result))
		$expected_details[]=$row3;

		if(!empty($expected_details)){
			echo "<table class='adminlist' width='80%' style='text-align:center;'>";
			echo "<thead><tr><th><b>Name</b></th>
			<th><b>Total</b></th>
			<th><b>Received</b></th>
			<th><b>Balance</b></th>
			<th><b>Payment Mode</b></th>
			<th><b>From Bank</b></th>
			<th><b>Account Number</b></th>
			<th><b>Transaction Reference Number</b></th>
			<th><b>Transaction Details</b></th>
			<th><b>To Bank</b></th>
			<th><b>Discount Category</b></th>
			<th><b>Discount</b></th>
			<th><b>Fine</b></th>
			
			<th><b>Transaction Status</b></th>
			<th><b>Transaction Date</b></th>
			<th><b>Realised ??</b></th>
			<th><b>Realisation DateTime</b></th>
					<th><b>Remark</b></th>
			</tr></thead>";

			foreach($expected_details as $row)
			{
				unset($collection);
				$total_collected=0;
				$expected=getDetailsById("fee_expected","fee_expected_id",$row['fee_expected_id']);
				$particular=getDetailsById("fee_particulars","fee_particulars_id",$expected['fee_particulars_id']);
				$total_amount=$particular['total_amount'];
				$sqlcoll="select * from fee_collection where fee_expected_id=".$expected['fee_expected_id']." and stu_id=".$stuid;
				$finedet=getDetailsById("fee_fine_master","fee_particulars_id",$expected['fee_particulars_id']);

				$result=mysql_query($sqlcoll);
				while ($row2 = mysql_fetch_assoc($result))
				$collection[]=$row2;
				if(!empty($collection)){
					foreach($collection as $rows){
						$sqlcoll="select discount_amount from fee_discount_collection where fee_collection_id=".$rows['fee_collection_id'];
						$result3=mysql_query($sqlcoll);
						$row3 = mysql_fetch_assoc($result3);
							
						$total_collected+=($rows['collected_amount']+$row3['discount_amount']);
					}
				}

				$sql="select * from fee_payment_type where stu_id=".$stuid." and fee_expected_id=".$row['fee_expected_id'];
				$res=mysql_query($sql);
				$fineamt=0;
				if(mysql_num_rows($res)>0){
					$count=0;
					$row=mysql_fetch_assoc($res);
					$no_of_inst=$row['no_of_installments'];
					$inst_amt=$row['installment_amount'];
					$total_amount=$particular['total_amount'];

					for($i=1;$i<=$no_of_inst;$i++)
					if($total_collected>=($i*$inst_amt)) $count++;

					$sqldate="select curdate() as today";
					$resdate=mysql_query($sqldate);
					$today=mysql_fetch_assoc($resdate);

					$calc=0;
					$remaining=0;
					$count_extended=0;
						
					for($i=$count;$i<=$no_of_inst;$i++){

						if($count==0 && $i==$count)
							$due_date=date("Y-m-d",strtotime($expected['due_date']));
						else
							$due_date=date("Y-m-d",strtotime("+".($i*$row['interval_in_days'])." days", strtotime($row['start_date'])));

						if($due_date<=date("Y-m-d",strtotime("+".$row['interval_in_days']." days", strtotime($today['today']))))
						{
								
							$extended_days=0;
							if($count_extended==0)
								$extended_days=$row['extended_days'];

							$count_extended++;
							if($due_date<date("Y-m-d",strtotime($today['today']))){
								if(!empty($finedet)) $fineamt+=round($finedet['fine_amount']);
							}
						}
					}
				
				$balance=$total_amount-$total_collected;
					
				echo "<tr>
				<td>".$expected['name']."</td>
				<td>".round($total_amount)."</td>
				<td>".round($total_collected)."</td>
				<td>".round($balance)."</td>";?>

				<?php if($balance!=0){
					$countrows++;
					?>
				<td>
				<select style="max-width: 4em;" name='paymode[<?php echo $expected['fee_expected_id'];?>]'	id='paymode[<?php echo $expected['fee_expected_id'];?>]'>
						<option value='CASH'>CASH</option>
						<option value='DEMAND DRAFT'>DEMAND DRAFT</option>
						<option value='CHEQUE'>CHEQUE</option>
				</select>
				</td>
				<td><input type='text' maxlength="200" style="max-width: 4em;" value='' name='frombank[<?php echo $row['fee_expected_id']; ?>]' id='frombank[<?php echo $row['fee_expected_id']; ?>]' autocomplete="on"/></td>
				<td><input type='text' style="max-width: 4em;" value='' name='accountno[<?php echo $row['fee_expected_id']; ?>]' id='accountno'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="50" /></td>
				<td><input type='text' style="max-width: 6em;" value='' name='trnsrefno[<?php echo $row['fee_expected_id']; ?>]' id='trnsrefno'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="50" /></td>
				<td><input type='text' maxlength="200" style="max-width: 6em;" value='' name='paydet[<?php echo $row['fee_expected_id']; ?>]' id='paydet[<?php echo $row['fee_expected_id']; ?>]' /></td>
				<td>
				<select style="max-width: 4em;" name='tobank[<?php echo $expected['fee_expected_id'];?>]'	id='tobank[<?php echo $expected['fee_expected_id'];?>]'>
				<option value='0'>-Select Bank-</option>
				<?php 
				$sqlbank=mysql_query("select * from bank_master",$link);
				while($row1=mysql_fetch_array($sqlbank))
				{
					echo"<option value='".$row1['bank_id']."'>".$row1['bank_name']." (".$row1['branch_name'].")</option>";
				}
				?>
				</select>
				</td>
				
				<td><select style="max-width: 4em;" name='seldiscount[<?php echo $expected['fee_expected_id'];?>]'	id='seldiscount[<?php echo $expected['fee_expected_id'];?>]' onchange="populatediscount(this);">
						<option value='0' id='0'>-Select Discount-</option>
							<?php getDiscount();?>
					</select>
				</td>
				<td><input type='text' style="max-width: 3em;" value='0' name='discount[<?php echo $row['fee_expected_id']; ?>]' id='discount'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" readonly maxlength="10" /></td>
				<td><input type='text' style="max-width: 3em;" value='<?php echo round($fineamt);?>' name='fine[<?php echo $row['fee_expected_id']; ?>]' id='fine'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" <?php if($fineamt==0) echo "readonly"; ?> maxlength="10" /></td>
				
				<td><b><font color='#FF0000'><b>UNPAID</b></font></td><td>-</td><td>-</td><td>-</td>		<td><input type='text' style="max-width: 5em;" value='' name='remark[<?php echo $row['fee_expected_id']; ?>]' id='remark' /></td>	
				<?php }
			else{
				
echo "<td colspan='14'><font color='#268C3C;'><b>PAID</b></font></td>";
}

				}
				else
				echo "<tr><td colspan='18'>Define Installment Type of $expected[name] To Proceed Further";
	
	echo "</tr>";
	$total_balance+=$balance;
			}
			echo "<tr><td colspan='18'><b>Total Balance Amount : ". $total_balance."/- Only</b></td></tr>";
			echo "<tr><td colspan='18'>";?>
<?php
if($countrows>0) {
?>
			<input	type='button' class="btn save" value=SAVE name='submit' onClick='submitform();'>
			<input	type='button' class="btn close" value='CLOSE' onClick='parent.emailwindow.close();'>
<?php }
else 
	echo "<b><font color='#FF0000'>You have no pending dues.</font></b>";
?>
<?php 			echo  "</td></tr></table>";
		}
	}
}
elseif ($act=="expected"){
	$student_details=getDetailsById("mst_students","reg_no",$regno);
	if(!empty($student_details)){
		$stuid=$student_details['stu_id'];
		$sqlcoll="select * from student_expected_fees where stu_id=".$stuid;

		$result=mysql_query($sqlcoll);
		while ($row3 = mysql_fetch_assoc($result))
		$expected_details[]=$row3;

		if(!empty($expected_details)){
			$str="";
			foreach($expected_details as $row)
			{
				$total_collected=0;
				unset($collection);
				$expected=getDetailsById("fee_expected","fee_expected_id",$row['fee_expected_id']);
				$particular=getDetailsById("fee_particulars","fee_particulars_id",$expected['fee_particulars_id']);

				$str.= "<option id='$sqlcoll' value='$expected[fee_expected_id]'>$expected[name]</option>";
			}

			if($str!="")
			{
				echo "<lable style='color: #FF0000;float:center;'>Press Ctrl to select multiple fee entries.</lable><br>";
				echo "<select name='expected[]' id='expected' multiple='multiple' style=' width:200px; height:100px;' onchange='populateexpt();'>";
				echo $str;
				echo  "</select>";
			}
			else
			echo "<font color='#FF0000'>You have no pending dues.</font>";
		}
	}
}
elseif ($act=="search")
{
	$student_details=getDetailsById("mst_students","reg_no",$regno);
	if(!empty($student_details)){
		$stuid=$student_details['stu_id'];
		$expected_details=explode(",",makeSafe($_REQUEST['expected']));

		if(!empty($expected_details)){
			echo "<table class='adminlist' style='text-align:center;width:70%'>";
			echo "<thead><tr><th style='padding:2px;'><b>#</b></th><th><b>Name</b></th>
			<th style='padding:0;'><b>Due Date</b></th>
			<th style='padding:2px;'><b>Inst. Amount</b></th>
			<th><b>Amount</b></th>
			<th><b>Payment Mode</b></th>
			<th><b>From Bank</b></th>
			<th><b>Account Number</b></th>
			<th><b>Transaction Reference Number</b></th>
			<th><b>Transaction Details</b></th>
			<th><b>To Bank</b></th>
			<th><b>Discount Category</b></th>
			<th><b>Discount</b></th>
			<th><b>Fine</b></th>
			<th style='padding:2px;'><b>Transaction Status</b></th>
			<th><b>Transaction Date</b></th>
			<th style='padding:2px;'><b>Realised ??</b></th>
			<th style='padding:2px;'><b>Realisation DateTime</b></th>
			<th><b>Remark</b></th>
			<th style='padding:2px;'><b>Delete</b></th>
			</tr></thead>";

			foreach($expected_details as $expectedid)
			{
				unset($collection);
				$total_collected=0;
				$count=0;
				$expected=getDetailsById("fee_expected","fee_expected_id",$expectedid);
				$particular=getDetailsById("fee_particulars","fee_particulars_id",$expected['fee_particulars_id']);
				$finedet=getDetailsById("fee_fine_master","fee_particulars_id",$expected['fee_particulars_id']);

				$total_amount=$particular['total_amount'];
				$sqlcoll="select * from fee_collection where fee_expected_id=".$expected['fee_expected_id']." and stu_id=".$stuid;
					
				$result=mysql_query($sqlcoll);
				while ($row2 = mysql_fetch_assoc($result))
				$collection[]=$row2;
				if(!empty($collection)){
					foreach($collection as $rows){
						$sqlcoll="select discount_amount from fee_discount_collection where fee_collection_id=".$rows['fee_collection_id'];
						$result3=mysql_query($sqlcoll);
						$row3 = mysql_fetch_assoc($result3);
						$total_collected+=($rows['collected_amount']+$row3['discount_amount']);
					}
				}
				$balance=$total_amount-$total_collected;

				$sql="select * from fee_payment_type where stu_id=".$stuid." and fee_expected_id=".$expectedid;
				$res=mysql_query($sql);

				if(mysql_num_rows($res)>0){
					$row=mysql_fetch_assoc($res);
					$no_of_inst=$row['no_of_installments'];
					$inst_amt=$row['installment_amount'];
					$total_amount=$particular['total_amount'];

					for($i=1;$i<=$no_of_inst;$i++)
					if($total_collected>=($i*$inst_amt)) $count++;

					$sqldate="select curdate() as today";
					$resdate=mysql_query($sqldate);
					$today=mysql_fetch_assoc($resdate);

					$calc=0;
					$remaining=0;
					$count_extended=0;

					
					
							
							
					$countdata=0;
					$resrows=0;
				$countrows=0;
				$discount=0;
				$discountid=0;
				$fine=0;
				$sqltrns="select t.*,fc.*,fc.fee_collection_id from fee_collection as fc,fee_transaction_details as t where fc.fee_collection_id=t.fee_collection_id and fee_expected_id=".$expected['fee_expected_id']." and stu_id=".$stuid;
				$resulttrns=mysql_query($sqltrns);
				$resrows=mysql_num_rows($resulttrns);
				while ($transaction = mysql_fetch_assoc($resulttrns)){
$countdata++;
							echo "<tr>
							<td><input type='checkbox' value='$i' name='trns$expectedid' id='trns$expectedid' /></td>
							<td>".$expected['name']."</td>
							<td>".date("jS-M-Y",strtotime("+".($i*$row['interval_in_days'])." days", strtotime($row['start_date'])))."</td>
							<td>".round($inst_amt)."</td>";
				$status="";
				if($transaction['realisation_datetime']!="0000-00-00 00:00:00"){
					$status="realised";
					$countrows++;
				}
				$sqlcoll="select * from fee_discount_collection where fee_collection_id=".$transaction['fee_collection_id'];
				$result3=mysql_query($sqlcoll);
				$row3 = mysql_fetch_assoc($result3);
				
				if(!empty($row3)){
					$discount=$row3['discount_amount'];
					$discountid=$row3['fee_discount_id'];
				}
				
				$sqlcoll="select * from fee_fine_collection where fee_collection_id=".$transaction['fee_collection_id'];
				$result3=mysql_query($sqlcoll);
				$row3 = mysql_fetch_assoc($result3);
				if(!empty($row3)){
				$fine=$row3['fine_amount'];
				}
				?>
				<td><input type='text' style="max-width: 3em;" size='2' value='<?php echo $transaction['collected_amount']; ?>' name='expectedids[<?php echo $expectedid; ?>][<?php echo $i; ?>]' id='expectedids'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="10" /></td>
				<td>
				<select style="max-width: 4em;" name='paymode[<?php echo $transaction['trans_id'];?>]'	id='paymode[<?php echo $transaction['trans_id'];?>]' <?php echo "disabled"; ?>>
				<option <?php if($transaction['payment_mode']=="CASH") echo "selected"; ?> value='CASH'>CASH</option>
				<option <?php if($transaction['payment_mode']=="DEMAND DRAFT") echo "selected"; ?> value='DEMAND DRAFT'>DEMAND DRAFT</option>
				<option <?php if($transaction['payment_mode']=="CHEQUE") echo "selected"; ?> value='CHEQUE'>CHEQUE</option>
				</select>
				</td>
				<td><input type='text' maxlength="200" style="max-width: 4em;" value='<?php echo $transaction['from_bank']; ?>' name='frombank[<?php echo $transaction['trans_id']; ?>]' id='frombank[<?php echo $transaction['trans_id']; ?>]' autocomplete="on" <?php  echo "readonly"; ?>/></td>
				<td><input type='text' style="max-width: 3em;" value='<?php echo $transaction['from_bank_acc_no']; ?>' name='accountno[<?php echo $transaction['trans_id']; ?>]' id='accountno'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="50" <?php  echo "readonly"; ?> /></td>
				<td><input type='text' style="max-width: 3em;" value='<?php echo $transaction['trans_ref_no']; ?>' name='trnsrefno[<?php echo $transaction['trans_id']; ?>]' id='trnsrefno'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="50" <?php  echo "readonly"; ?> /></td>
				<td><input type='text' maxlength="200" style="max-width: 4em;" value='<?php echo $transaction['transaction_remark']; ?>' name='paydet[<?php echo $transaction['trans_id']; ?>]' id='paydet[<?php echo $transaction['trans_id']; ?>]' <?php  echo "readonly"; ?> /></td>
				<td>
				<select style="max-width: 5em;" name='tobank[<?php echo $transaction['trans_id'];?>]'	id='tobank[<?php echo $transaction['trans_id'];?>]' <?php  echo "disabled"; ?>>
				<option value='0'>-Select Bank-</option>
				<?php
				$sqlbank=mysql_query("select * from bank_master",$link);
				while($row1=mysql_fetch_array($sqlbank))
				{
					echo"<option value='".$row1['bank_id']."'";
					if($transaction['to_bank']==$row1['bank_id']) echo "selected";
					echo ">".$row1['bank_name']." (".$row1['branch_name'].")</option>";
				}
				?>
				</select>
				</td>
				<td>
					<select style="max-width: 4em;" name='seldiscount[<?php echo $transaction['trans_id'];?>]'	id='seldiscount[<?php echo $transaction['trans_id'];?>]' <?php  echo "disabled"; ?> onchange="populatediscount(this);">
						<option value='0' id='0'>-Select Discount-</option>
							<?php getDiscount($discountid);?>
					</select>
				</td>
				<td><input type='text' style="max-width: 2.5em;" value='<?php echo $discount; ?>' name='discount[<?php echo $transaction['trans_id']; ?>]' id='discount'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" readonly maxlength="10" <?php  echo "readonly"; ?> /></td>
				<td><input type='text' style="max-width: 2.5em;" value='<?php echo round($fine);?>' name='fine[<?php echo $transaction['trans_id']; ?>]' id='fine'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" <?php echo "readonly"; ?> maxlength="10" /></td>
				
		<?php 

			if($status!="realised")
				echo "<td><font color='#0000FF'>IN PROGRESS</font></td>";
			else 
				echo "<td><font color='#268C3C'><b>PAID</b></font></td>";

			echo "<td>".date("jS-M-Y g:iA",strtotime($transaction['trans_datetime']))."</td>"; 
			
			if($status!="realised"){?>
				<td><input type='checkbox' value="<?php echo $transaction['trans_id']; ?>" name='realised[<?php echo $transaction['trans_id']; ?>]' id='<?php echo $transaction['trans_id']; ?>'/></td>
		<?php }
			else
				echo "<td>YES</td>";
			
			if($status!="realised"){

			?>
		<td>Date :	<input name='realiseddate[<?php echo $transaction['trans_id']; ?>]' id="to<?php echo $transaction['trans_id']; ?>" type="text" class="date"  style="max-width: 5em;"  size="10" value="<?php echo date("Y-m-d");?>" readonly />
						
					<script type="text/javascript">
				  		$(function() {
							$( "#to<?php echo $transaction['trans_id']; ?>" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd'
							});
						});

				  		</script>
				  	
					 Time : <input style="margin-left: 8px;max-width: 5em;"  name='realisedtime[<?php echo $transaction['trans_id']; ?>]' type="text" id="end_time<?php echo $transaction['trans_id']; ?>" value="<?php echo date('H:i:s');?>" size="11" maxlength="10" /> 
						<script>
				        	$('#end_time<?php echo $transaction['trans_id']; ?>').timepicker({
				            	minutes: { interval: 1 },
				            	timeFormat: "HH:mm",
				            	rows:5
				            	});
				        </script></td>
			<?php 	
			}else
				echo "<td>".date("jS-M-Y g:iA",strtotime($transaction['realisation_datetime']))."</td>";?>
		<td><input type='text' style="max-width: 4em;" value='<?php echo $transaction['remark']; ?>' name='remark[<?php echo $transaction['trans_id']; ?>]' id='remark' <?php  echo "readonly"; ?> /></td>
		<td>
		<?php 
		if($countdata==$resrows)
			echo "<a href =\"javascript:deletecollection('".$transaction['fee_collection_id']."');\"><img src='../images/publish_x.png' /></a>";
		
		?>
		</td>
		</tr><?php 
} 
					for($i=$count;$i<$no_of_inst;$i++){
						echo "<tr>
							<td><input type='checkbox' value='$i' name='checkedinst[$expectedid][$i]' id='checkedinst$expectedid' /></td>
							<td>".$expected['name']."</td>
							<td>".date("jS-M-Y",strtotime("+".($i*$row['interval_in_days'])." days", strtotime($row['start_date'])))."</td>
							<td>".round($inst_amt)."</td>";
							$fineamt=0;
							$extended_days=0;
							if($count_extended==0)
							$extended_days=$row['extended_days'];
								
							$count_extended++;
							if(date("Y-m-d",strtotime("+".(($i*$row['interval_in_days'])+$extended_days)." days", strtotime($row['start_date'])))<date("Y-m-d",strtotime($today['today']))){
								if(!empty($finedet)) $fineamt=$finedet['fine_amount'];
							}

							$inst=$i+1;
							$calc=(($inst*$inst_amt)-($total_collected+$remaining));
							$remaining+=$calc;

							?>
							<td><input type='text' style="max-width: 3em;" size='2' value='<?php echo $calc; ?>' name='expectedids[<?php echo $expectedid; ?>][<?php echo $i; ?>]' id='expectedids'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="10" /></td>
							
				<td>
				<select style="max-width: 4em;" name='paymode[<?php echo $expectedid; ?>][<?php echo $i; ?>]'	id='paymode[<?php echo $expectedid; ?>][<?php echo $i; ?>]'>
						<option value='CASH'>CASH</option>
						<option value='DEMAND DRAFT'>DEMAND DRAFT</option>
						<option value='CHEQUE'>CHEQUE</option>
				</select>
				</td>
				<td><input type='text' maxlength="200" style="max-width:4em;" value='' name='frombank[<?php echo $expected['fee_expected_id'];?>][<?php echo $i; ?>]' id='frombank[<?php echo $expected['fee_expected_id'];?>][<?php echo $i; ?>]' autocomplete="on"/></td>
				<td><input type='text' style="max-width: 4em;" value='' name='accountno[<?php echo $expected['fee_expected_id'];?>][<?php echo $i; ?>]' id='accountno'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="50" /></td>
				<td><input type='text' style="max-width: 4em;" value='' name='trnsrefno[<?php echo $expected['fee_expected_id'];?>][<?php echo $i; ?>]' id='trnsrefno'	onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" maxlength="50" /></td>
				<td><input type='text' maxlength="200" style="max-width: 4em;" value='' name='paydet[<?php echo $expected['fee_expected_id'];?>][<?php echo $i; ?>]' id='paydet[<?php echo $expected['fee_expected_id'];?>][<?php echo $i; ?>]' /></td>
				<td>
				<select style="max-width: 4em;" name='tobank[<?php echo $expected['fee_expected_id'];?>][<?php echo $i; ?>]'	id='tobank[<?php echo $expected['fee_expected_id'];?>][<?php echo $i; ?>]'>
				<option value='0'>-Select Bank-</option>
				<?php 
				$sqlbank=mysql_query("select * from bank_master",$link);
				while($row1=mysql_fetch_array($sqlbank))
				{
					echo"<option value='".$row1['bank_id']."'>".$row1['bank_name']." (".$row1['branch_name'].")</option>";
				}
				?>
				</select>
				</td>		
							<td><select style="max-width: 4em;" name='seldiscount[<?php echo $expected['fee_expected_id'];?>][<?php echo $i; ?>]' id='seldiscount[<?php echo $expected['fee_expected_id'];?>]' onchange="populatediscount(this);">
							<option value='0' id='0'>-Select Discount-</option>
							<?php getDiscount();?>
							</select>
							</td>
							<td><input type='text' style="max-width: 3em;" size='2' value='0' name='discount[<?php echo $expectedid; ?>][<?php echo $i; ?>]' id='discount' onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" readonly maxlength="10" /></td>
							<td><input type='text' style="max-width: 3em;" size='2' value='<?php echo round($fineamt);?>' name='fine[<?php echo $expectedid; ?>][<?php echo $i; ?>]' id='fine' onkeyup="if (/[^(\d*?)?$]/g.test(this.value)) this.value = this.value.replace(/[^(\d*?)?$]/g,'')" <?php if($fineamt==0) echo "readonly"; ?> maxlength="10" /></td>
							
							</td><td><font color='#FF0000'>UNPAID</font></td><td>-</td><td>-</td><td>-</td>	<td><input type='text' style="max-width: 4em;" size='2' value='' name='remark[<?php echo $expectedid; ?>][<?php echo $i; ?>]' id='remark' maxlength="500" />	</td><td></td>
							<?php
					}
					echo "</tr>";

					$total_balance+=$balance;
					echo "<tr><td colspan='20'><b>Total Balance Amount : ". $total_balance."/- Only</b></td></tr>";
					?>
					<?php
				}
				else
				echo "<tr><td colspan='20'>Define Installment Type of $expected[name] To Proceed Further";
			}
			?>
			<tr><td colspan='20'><input  class='btn save' type='button' value=SAVE name='submit' onClick='submitform();'> <input type='button' class='btn close' value='CLOSE' onClick='parent.emailwindow.close();'>
			<?php 
			}
				echo  "</td></tr></table>";
	}
}
?>
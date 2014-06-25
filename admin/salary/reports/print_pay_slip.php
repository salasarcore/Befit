<style>

.msg {
    border: 1px solid #CA031B;
    border-radius: 5px;
    box-shadow: 0 0 5px #FF0000;
    color: #FF0000;
    font-size: 16px;
    padding: 5px;
}
</style>

<?php @session_start();
include('../../conn.php');
//include('../../check_session.php');
include_once("../../functions/common.php");
$month=makeSafe(@$_GET['month']);
$year=makeSafe(@$_GET['year']);
$department_id=makeSafe(@$_GET['department']);
$emp_id=makeSafe($_GET['emp_id']);

$sql3 = "select stop_salary from employee where empid=$emp_id ";
$res3 = mysql_query($sql3);
$row3 = mysql_fetch_array($res3);
$sal = $row3['stop_salary'];
if($sal=='Y')
{
	echo "<div class='msg'>This employee salary has been stopped.</div> "; exit;
}

$sqlWhere="";
 $sql="select distinct ed.designation_name,d.department_name,e.emp_name,e.emp_id,e.empid,b.br_name,b.br_addr1,br_country,br_state,br_pin,br_fax,br_email, e.stop_salary ";
 $sql.=" from monthly_salary_earnings m,employee e,employee_department d,mst_branch b,mst_designations ed ";
 $sql.=" where  d.department_id=e.department_id and ed.designation_id=e.designation_id and m.empid=e.empid and e.br_id=b.br_id and m.month='".$month."' and m.year=".$year." and b.br_id=".makeSafe($_SESSION['br_id'])." and e.empid=".$emp_id." ";
if($department_id>0) $sqlWhere =" and e.department_id=".$department_id." ";
  $sql=$sql." ".$sqlWhere; 
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
$row=mysql_fetch_array($res);
$n = mysql_num_rows($res);
if($n==0) {  echo "<div class='msg'>No data for  $month-$year</div> "; exit; }
 


?>
<style>
.hide{display:none;}

.t1
{
border-collapse:collapse;
}
.t1 td, th
{
border:1px solid black;
    padding:2px 20px 2px 20px;

}

</style>

<script>
 function printpage() {
	 
 document.getElementById("print").className="hide";
 
  window.print();
}
</script>
<table width=750 align=center style="border:1px solid">
<tr><td>
<table align="center" width="700" style="padding: 5px 0 5px 0;">
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
<hr/ style="width:700px" >

<table width="700" align=center >
<tr ><td colspan="2" align="center" ><u>EMPLOYEE PAY SLIP</u></td></tr>
<tr>
<td align="left" style="padding-left: 20px;">EMPLOYEE NAME : <?php echo $row['emp_name']; ?></td>
<td align="left" style="padding-right: 20px;">EMPLOYEE ID : <?php echo $row['emp_id']?></td></tr>
<tr>
<td align="left" style="padding-left: 20px;">DESIGNATION :<?php echo $row['designation_name']?> &nbsp; DEPARTMENT : <?php echo $row['department_name']?></td>
<td align="left" style="padding-right: 20px;">PAY SLIP: <?php echo $month."-".$year ?></td>

</tr>

</table>
<br />
<table class="t1" width="700" align="center" border="1" cellspacing="0" >

<?php  		$sqlE="SELECT head_type, head_abvr, amount from monthly_salary_earnings where empid=".$row['empid']." and month='".$month."' and year=".$year;
				$resE=mysql_query($sqlE) or die("Unable to connect to Server, We are sorry for inconvienent caused");
				$i=1;
				$total_earnings=0; 
				  $nE=mysql_num_rows($resE);
				   if($nE>0)
				   {  ?>
		<tr><th colspan="3" align="center"><br>Earnings(Rs.)</td></tr>
		<tr>
			<td width="70">SL.NO</td>
			<td>HEAD</td>
			<td width="220" align='right'>AMOUNT</td>
		</tr>
			
			<?php
		
				while($rowE=mysql_fetch_array($resE))
					{
					
					?>
				  <tr>
					
					<td><?php echo $i;?></td>
					<td><?php echo $rowE['head_type'];?></td>
					<td align="right"><?php echo $rowE['amount'];?></td>								
				  </tr>
				 
				  <?php
				   $i=$i+1;
				  $total_earnings=$total_earnings+$rowE['amount'];
				  }
				   
				   echo"<tr><td align='right' colspan='4'><span style='padding-right:30px'> TOTAL EARNING(S) : </span>".number_format($total_earnings,2)."</td></tr>";
				   } else echo "";
				   
				   $sqlE="SELECT head_type, head_abvr, amount from monthly_salary_deductions where empid=".$row['empid']." and month='".$month."' and year=".$year;
				   $resE=mysql_query($sqlE) or die("Unable to connect to Server, We are sorry for inconvienent caused");
				   $i=1;
				   $total_deductions=0;
				   $nE=mysql_num_rows($resE);
				   if($nE>0)
				   {
				  ?>
	<tr><th colspan="3"><br>Deductions(Rs.)</td></tr>
		<tr>
			<td>SR.NO</td>
			<td>HEAD</td>
			<td align='right'>AMOUNT</td>
		</tr>
			
			<?php
			
				while($rowE=mysql_fetch_array($resE))
					{
					
					?>
				  <tr>
					
					<td><?php echo $i;?></td>
					<td><?php echo $rowE['head_type'];?></td>
					<td align="right"><?php echo $rowE['amount'];?></td>								
				  </tr>
				 
				  <?php
				  $i=$i+1;
				  $total_deductions=$total_deductions+$rowE['amount'];
				  }
				   
				   echo"<tr><td align='right' colspan='4'><span style='padding-right:30px'> TOTAL DEDUCTION(S) : </span>".number_format($total_deductions,2)."</td></tr>";
				   } else echo "";
				  ?>
				  
				  
				  
				<?php 	$sqlE="SELECT  	loan_purpose,total_inst_no, monthly_salary_loan_recovery.inst_no, monthly_salary_loan_recovery.amount from monthly_salary_loan_recovery,sal_salary_info_loan  where  monthly_salary_loan_recovery.emp_loan_id=sal_salary_info_loan.emp_loan_id and monthly_salary_loan_recovery.empid=".$row['empid']." and month='".$month."' and year=".$year;
				$resE=mysql_query($sqlE) or die("Unable to connect to Server, We are sorry for inconvienent caused");
				$i=1;
				$loan_deductions=0;
				$nE=mysql_num_rows($resE);
				if($nE>0)
				{	 ?>
			<tr><th colspan="3"><br>Recoveries of Loan(Rs.)</td></tr>	
		<tr>
			<td>SL.NO</td>
			<td>HEAD</td>
			<td align='right'>AMOUNT</td>
		</tr>
			
			<?php
			
				while($rowE=mysql_fetch_array($resE))
					{
					
					?>
				  <tr>
					<td><?php echo $i;?></td>
					<td><?php echo $rowE['loan_purpose'];?><br />Inst:<?php echo $rowE['inst_no']-1; ?>/<?php echo $rowE['total_inst_no'];?></td>
					<td align="right"><?php echo $rowE['amount'];?></td>								
				  </tr>
				 
				  <?php
				  $i=$i+1;
				  $loan_deductions=$loan_deductions+$rowE['amount'];
				  }
				   
				   echo"<tr><td align='right' colspan='4'><span style='padding-right:30px'> TOTAL LOAN(S)  : </span>".number_format($loan_deductions,2)."</td></tr>";
				  
				} else echo "";?>
				  <tr><td colspan='3' align='right'><b><span style='padding-right:30px'>NET PAY :</span><?php echo number_format(($total_earnings-$total_deductions-$loan_deductions),2);?></b></td></tr>
				  
			
				</table>		 
	

	<br/>
	<hr / style="width:700px" >
		<br/>
<table style="width:700px" >
<tr><td></td><td align="right" style="padding-right: 50px;">For <?php echo @SCHOOL_NAME;?></td></tr>
<tr><td colspan="2" align="center"><input type="button" id="print" name="print" value="PRINT" onClick="printpage();" /></td></tr>
</table> 
</td></tr></table>

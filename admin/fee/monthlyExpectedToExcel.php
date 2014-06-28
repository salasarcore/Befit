<?php
require_once '../phpexcel/Classes/PHPExcel.php';
require_once '../phpexcel/Classes/PHPExcel/IOFactory.php';
require_once '../../globalConfig.php';
include ("../../functions/common.php");
include_once("../functions/comm_functions.php");

$dept_id=makeSafe(@$_REQUEST['department']);
$monthyear=makeSafe(@$_REQUEST['monthyear']);

$sql="select fpt.*,md.*,sc.*,concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name from fee_payment_type as fpt, mst_departments as md, student_class as sc,mst_students as ms where fpt.stu_id=sc.stu_id and ms.stu_id=fpt.stu_id and sc.department_id=md.department_id and ms.br_id=".$_SESSION['br_id'];

if(trim(@$dept_id)!="" && @$dept_id!='0') $sql.=" and md.department_id=".@$dept_id;

$paymentdetails=mysql_query($sql);

$monthlydetails=array();
$totalmonthlydetails=array();

$collection=array();
$unrealised=array();
$total_collected=0;
$total_amount=0;
$count_extended=0;
$expectedamt=0;

$sqldate="select curdate() as today";
$resdate=mysql_query($sqldate);
$today=mysql_fetch_assoc($resdate);
$seldate=date("m-Y",strtotime($today['today']));
	
if(trim($monthyear)!="")
	$seldate=$monthyear;
		
		
if(mysql_num_rows($paymentdetails)>0){
		
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$column = 'A';	
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount, "TOTAL MONTHLY EXPECTED AMOUNT");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount, "TOTAL TRANSACTION AMOUNT");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"TOTAL UNREALISED AMOUNT");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"TOTAL PENDING DUES AMOUNT");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
	$rowCount = 4;
	$column = 'A';
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount, "STUDENT NAME");	
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount, "DEPARTMENT");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"FEE EXPECTED NAME");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"INSTALLMENT AMOUNT");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ( $column . $rowCount, "DUE DATE");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ( $column . $rowCount, "TRANSACTION STATUS");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$column++;
	
		
	$rowCount += 1;	
	$k=0;
	while ($row = mysql_fetch_array($paymentdetails)) {
		unset($collection);
		unset($unrealised);
		$unrealised=array();
		$due_date="";
			
		$total_collected=0;
		$expected=getDetailsById("fee_expected","fee_expected_id",$row['fee_expected_id']);
		$particular=getDetailsById("fee_particulars","fee_particulars_id",$expected['fee_particulars_id']);
		$total_amount=$particular['total_amount'];
			
		$sqlcoll="select fc.* from fee_collection as fc, fee_transaction_details as ft where fc.fee_collection_id=ft.fee_collection_id and fc.fee_expected_id=".$expected['fee_expected_id']." and fc.stu_id=".$row['stu_id']." and cast(ft.realisation_datetime as datetime) <> 0";
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
			
		$count=0;
	
		$no_of_inst=$row['no_of_installments'];
		$inst_amt=$row['installment_amount'];
		$total_amount=$particular['total_amount'];
			
		for($i=1;$i<=$no_of_inst;$i++)
		if($total_collected>=($i*$inst_amt)) $count++;
			
		$calc=0;
		$remaining=0;
			
	
		for($i=$count;$i<$no_of_inst;$i++){
		if(date("m-Y",strtotime("+".($i*$row['interval_in_days'])." days", strtotime($row['start_date'])))==$seldate)
				$calc++;
		}
	
		$sqlcoll="select fc.*,ft.* from fee_collection as fc, fee_transaction_details as ft where fc.fee_collection_id=ft.fee_collection_id and fc.fee_expected_id=".$expected['fee_expected_id']." and fc.stu_id=".$row['stu_id'];
		$results=mysql_query($sqlcoll);
		while ($row2 = mysql_fetch_assoc($results))
			$unrealised[]=$row2;
	
			for($i=$count;$i<$no_of_inst;$i++){
			if(date("m-Y",strtotime("+".($i*$row['interval_in_days'])." days", strtotime($row['start_date'])))==$seldate){
					$due_date=date("jS-M-Y",strtotime("+".($i*$row['interval_in_days'])." days", strtotime($row['start_date'])));
						$curmonth=date("F-Y",strtotime($due_date));
								$monthlydetails[$k]['stu_name']=$row['stu_name'];
								$monthlydetails[$k]['department_name']=$row['department_name'];
								$monthlydetails[$k]['expected_name']=$expected['name'];
								$monthlydetails[$k]['installment_amount']=round($row['installment_amount']);
								$monthlydetails[$k]['due_date']=$due_date;
								
								if(array_key_exists($i, $unrealised)){
									if($unrealised[$i]['realisation_datetime']=="0000-00-00 00:00:00")
										$monthlydetails[$k]['status'] = "In progress";
								}
								else
								{
								$monthlydetails[$k]['status'] = "Unpaid";
								$expectedamt+=round($row['installment_amount']);
								}
	
	$remaining++;
	
	$k++;
			}
		}
	}

	$total_expected=0;
	$sql="select sum(collected_amount)+IFNULL(sum(discount_amount),0) as collected_amount
			from fee_collection as fc
			LEFT JOIN student_class as sc ON sc.stu_id=fc.stu_id
			LEFT JOIN mst_students as ms ON ms.stu_id=sc.stu_id
			LEFT JOIN mst_departments as md ON sc.department_id=md.department_id
			LEFT JOIN fee_discount_collection ON fc.fee_collection_id=fee_discount_collection.fee_collection_id
			where ms.br_id=".$_SESSION['br_id']." and ";
	
	if(trim($monthyear)!="")
		$sql.="MONTH(fc.updated_at)='".explode("-",$monthyear)[0]."' and YEAR(fc.updated_at)='".explode("-",$monthyear)[1]."'";
	else
		$sql.="MONTH(fc.updated_at)=MONTH(CURDATE()) and YEAR(fc.updated_at)=YEAR(CURDATE())";
	
	if(trim(@$dept_id)!="" && @$dept_id!='0') $sql.=" and md.department_id=".@$dept_id;
	$resultcollected=mysql_fetch_array(mysql_query($sql));
	
	
	$sql="select sum(collected_amount)+IFNULL(sum(discount_amount),0) as collected_amount
			from fee_collection as fc
			LEFT JOIN student_class as sc ON sc.stu_id=fc.stu_id
			LEFT JOIN fee_transaction_details as ftd ON ftd.fee_collection_id=fc.fee_collection_id
			LEFT JOIN mst_students as ms ON ms.stu_id=sc.stu_id
			LEFT JOIN mst_departments as md ON sc.department_id=md.department_id
			LEFT JOIN fee_discount_collection ON fc.fee_collection_id=fee_discount_collection.fee_collection_id
			where ms.br_id=".$_SESSION['br_id']." and cast(ftd.realisation_datetime as datetime) = 0 and ";
	
	if(trim($monthyear)!="")
		$sql.="MONTH(fc.updated_at)='".explode("-",$monthyear)[0]."' and YEAR(fc.updated_at)='".explode("-",$monthyear)[1]."'";
	else
		$sql.="MONTH(fc.updated_at)=MONTH(CURDATE()) and YEAR(fc.updated_at)=YEAR(CURDATE())";
	
	if(trim(@$dept_id)!="" && @$dept_id!='0') $sql.=" and md.department_id=".@$dept_id;
	$resultcollectedrealised=mysql_fetch_array(mysql_query($sql));
	
	
	$total_expected=$resultcollected['collected_amount']+$expectedamt;
	
	$total_realised=$resultcollectedrealised['collected_amount'];
	
	$totalmonthlydetails['expected']=round($total_expected);
	$totalmonthlydetails['transaction']=round($resultcollected['collected_amount']);
	$totalmonthlydetails['unrealized']=round($total_realised);
	$totalmonthlydetails['pending']=round($expectedamt+$total_realised);
	
	$rowCount=2;
	$column = 'A';
	for($j = 0; $j < count ( $totalmonthlydetails ); $j ++)
	{
	if(array_key_exists("expected",$totalmonthlydetails))
	{
		if(array_search("expected", array_keys($totalmonthlydetails)) == $j)
		{
			$value = $totalmonthlydetails ['expected'];
		}
	
		if(array_search("transaction", array_keys($totalmonthlydetails)) == $j)
		{
			$value = $totalmonthlydetails ['transaction'];
		}
	
		if(array_search("unrealized", array_keys($totalmonthlydetails)) == $j)
		{
			$value = $totalmonthlydetails ['unrealized'];
		}
		if(array_search("pending", array_keys($totalmonthlydetails)) == $j)
		{
			$value = $totalmonthlydetails ['pending'];
		}
		
	}
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount,$value);
	$column++;
	}
	
	$rowCount=5;
	$previouscount=0;
	$repeatationcount=0;
	$i=0;
	foreach($monthlydetails as $details) 
	{
		
		$expname = "";
		$stuname = "";
		$department="";
		$inst_amt= "";
		$status= "";
		$duedate="";
		$column = 'A';

		for($j = 0; $j < count ( $details ); $j ++) 
		{

				$value = "";
			
			if(array_key_exists("stu_name",$details)) 
			{

				if(array_search("stu_name", array_keys($details)) == $j)
				{
					$value = $details ['stu_name'];
				}
				
				if(array_search("department_name", array_keys($details)) == $j)
				{
					$value = $details ['department_name'];
				}
				
				if(array_search("expected_name", array_keys($details)) == $j)
				{
					$value = $details ['expected_name'];
				}
				if(array_search("installment_amount", array_keys($details)) == $j)
				{
					$value = $details ['installment_amount'];
				}
				if(array_search("due_date", array_keys($details)) == $j)
				{
					$value = $details ['due_date'];
				}
				if(array_search("status", array_keys($details)) == $j)
				{
					$value = $details ['status'];
				}
			}
			
			
			$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount,$value);
			$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$column++;
			
		} // for()
			
			if (isset($monthlydetails[$i+1]['stu_name'])) {
				
		if($monthlydetails[$i]['stu_name']==$monthlydetails[$i+1]['stu_name'])
			$objPHPExcel->getActiveSheet()->mergeCells("A".($rowCount).":A".($rowCount+1));
			$objPHPExcel->getActiveSheet()->getStyle("A".($rowCount).":A".($rowCount+1))
		->getAlignment()
		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
			}
			
			if(($monthlydetails[$i]['stu_name']==$monthlydetails[$i+1]['stu_name']) && ($monthlydetails[$i]['department_name']==$monthlydetails[$i+1]['department_name'])){
				
			$objPHPExcel->getActiveSheet()->mergeCells("B".($rowCount).":B".($rowCount+1));
			$objPHPExcel->getActiveSheet()->getStyle("B".($rowCount).":B".($rowCount+1))
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}
			
			if (isset($monthlydetails[$i+1]['expected_name'])) {
			
				if(($monthlydetails[$i]['stu_name']==$monthlydetails[$i+1]['stu_name']) && ($monthlydetails[$i]['department_name']==$monthlydetails[$i+1]['department_name']) && ($monthlydetails[$i]['expected_name']==$monthlydetails[$i+1]['expected_name']))
					$objPHPExcel->getActiveSheet()->mergeCells("C".($rowCount).":C".($rowCount+1));
					$objPHPExcel->getActiveSheet()->getStyle("C".($rowCount).":C".($rowCount+1))
				->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
			}
		$i++;
		$rowCount ++;
	} // while()
	
	if($monthyear=="")
		$monthyear=date("m-Y");
	
	$objPHPExcel->getActiveSheet ()->setTitle ("Monthly Expected List(".$monthyear.")");
	$objPHPExcel->setActiveSheetIndex ( 0 );
	$filename = "MonthlyExpectedList(".$monthyear.").xls";
	// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
	$objWriter->save ( "" . $filename );
	// Save Excel5 file
	$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
	$objWriter->save ( "" . $filename );
	header ( "Location: $filename" );
	
	exit;
} // if()
else
{
	echo '<script>alert("No Data Available");</script>';
	echo '<script>window.close();</script>';
}
?>
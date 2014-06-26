<?php

require_once '../phpexcel/Classes/PHPExcel.php';
require_once '../phpexcel/Classes/PHPExcel/IOFactory.php';
require_once '../conn.php';

$query="select expense_list_id,date,amount, remark, credited_to,created_at,updated_by from expense_list order by expense_list_id asc";
$result=mysql_query($query,$link);

$number_rows=mysql_num_rows($result);
if ($number_rows>0)
{
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$column = 'A';
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "ID");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(8);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Date");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Amount");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Remarks");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Credited To");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Created Date");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Updated By");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$rowCount += 1;

	while($row=mysql_fetch_array($result))
	{		
		$id="";
		$date="";
		$amount="";
		$remarks="";
		$creditedto = "";
		$createddate = "";
		$updatedby="";
		$column = 'A';
		 
		for($j=0; $j<mysql_num_fields($result);$j++)
		{
			if(!isset($row[$j]))
			$value = NULL;
			elseif ($row[$j] != "")
			$value = strip_tags($row[$j]);
			else
			$value = "";
			
			if(mysql_field_name($result,$j)=="expense_list_id")
			{	
				if($row['expense_list_id']!="")
  				{  		
  					$id.=$row['expense_list_id']; 
  				}
  				if($row['date']!="")
  				{
  					$date.=$row['date'];
  				}
  				if($row['amount']!="")
  				{
  					$amount.=$row['amount'];
  				}
  				if($row['remark']!="")
  				{
  					$remarks.=$row['remark'];
  				}
  				if($row['credited_to']!="")
  				{
  					$creditedto.=$row['credited_to'];  	
  				}
  				if($row['created_at']!="")
  				{
  					$createddate.=$row['created_at'];
  				}
  				if($row['updated_by']!="")
  				{
  					$updatedby.=$row['updated_by'];
  				}			
  			
			} //if
		
	
			if(mysql_field_name($result,$j)=="expense_list_id")
			{
				$value=$id;
				
			}
			if(mysql_field_name($result,$j)=="date")
			{
				$value=$date;
			}
			if(mysql_field_name($result,$j)=="amount")
			{
				$value=$amount;
			}
			if(mysql_field_name($result,$j)=="remark")
			{
				$value=$remarks;
			}
			if(mysql_field_name($result,$j)=="credited_to")
			{
				$value=$creditedto;
			}
			if(mysql_field_name($result,$j)=="created_at")
			{
				$value=$createddate;
			}
			if(mysql_field_name($result,$j)=="updated_by")
			{
				$value=$updatedby;
			}
			
			$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount,$value);
			$column++;
			
		} //for()
			
		$rowCount++;
			
	} //while()	

	$objPHPExcel->getActiveSheet()->setTitle('Expenses List');
	$objPHPExcel->setActiveSheetIndex(0);
	$filename = "ExpensesList" . time(). ".xls";
	// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("".$filename);
	// Save Excel5 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save("".$filename);
	header("Location: $filename");
	exit;
	
	
	
}//if()
else
{
	echo '<script>alert("No Data Available");</script>';
	echo '<script>window.close();</script>';
}
?>

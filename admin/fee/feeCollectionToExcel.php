<?php
require_once '../phpexcel/Classes/PHPExcel.php';
require_once '../phpexcel/Classes/PHPExcel/IOFactory.php';
require_once '../conn.php';
include ("../functions/common.php");

makeSafe (extract($_REQUEST));

$query = "select fc.fee_expected_id, fe.name,fc.stu_id, concat_ws(' ',ms.stu_fname,ms.stu_mname,ms.stu_lname) as stu_name, fc.collected_amount,fc.extended_days,fc.remark,fc.created_at, 
		fc.updated_at,fc.updated_by from fee_collection fc join fee_expected fe on fc.fee_expected_id=fe.fee_expected_id join mst_students ms on fc.stu_id=ms.stu_id and ms.br_id=".$_SESSION['br_id'];
$result = mysql_query($query,$link);

$number_rows = mysql_num_rows($result);
if($number_rows > 0) 
{
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$column = 'A';	
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount, "Expected ID");	
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(10);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount, "Expected Name");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"Member ID");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"Member Name");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ( $column . $rowCount, "Collected Amount");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"Extended Days");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"Remark");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"Created At");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"Updated On");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue ($column.$rowCount,"Updated By");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
		
	$rowCount += 1;	
	while ($row=mysql_fetch_array($result)) 
	{
		$expid = "";
		$expname = "";
		$stuid = "";
		$stuname = "";
		$amt="";
		$extended= "";
		$remark= "";
		$createdat = "";
		$updatedon="";
		$updatedby="";
		$column = 'A';
		
		for($j = 0; $j < mysql_num_fields ( $result ); $j ++) 
		{
			if (! isset ( $row [$j] ))
				$value = NULL;
			elseif ($row [$j] != "")
				$value = strip_tags ( $row [$j] );
			else
				$value = "";
			if(mysql_field_name($result,$j)=="fee_expected_id") 
			{
				if($row ['fee_expected_id'] != "") 
				{
					$expid .= $row ['fee_expected_id'];
				}
				if($row['name']!="") 
				{
					$expname.= $row['name'];
				}
				if($row['stu_id']!="") 
				{
					$stuid.=$row['stu_id'];
				}
				if($row['stu_name']!="") 
				{
					$stuname.= $row['stu_name'];
				}
				if($row['collected_amount'] != "") 
				{
					$amt.= $row['collected_amount'];
				}
				if($row['extended_days'] != "") 
				{
					$extended .= $row['extended_days'];
				}
				if($row['remark'] != "")
				{
					$remark.= $row['remark'];
				}
				if($row['created_at'] != "")
				{
					$createdat.= $row['created_at'];
				}
				if($row['updated_at'] != "")
				{
					$updatedon.= $row['updated_at'];
				}
				if($row['updated_by'] != "")
				{
					$updatedby.= $row['updated_by'];
				}
			} // if
			
			if(mysql_field_name($result, $j) == "fee_expected_id")
			{
				$value = $expid;
			}
			if(mysql_field_name ($result, $j) == "name") 
			{
				$value = $expname;
			}
			if(mysql_field_name ($result, $j) == "stu_id")
			{
				$value = $stuid;
			}
			if(mysql_field_name($result,$j)=="stu_name") {
				$value = $stuname;
			}
			if(mysql_field_name($result,$j) == "collected_amount")
			{
				$value = $amt;
			}
			if(mysql_field_name($result,$j) == "extended_days")
			{
				$value = $extended;
			}
			if(mysql_field_name($result,$j)=="remark") 
			{
				$value = $remark;
			}
			if(mysql_field_name($result,$j)=="created_at")
			{
				$value = $createdat;
			}
			if(mysql_field_name($result,$j)=="updated_at")
			{
				$value = $updatedon;
			}
			if(mysql_field_name($result,$j)=="updated_by")
			{
				$value = $updatedby;
			}
			
			$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount,$value);
			$column++;
		} // for()
		
		$rowCount ++;
	} // while()
	$objPHPExcel->getActiveSheet ()->setTitle ( 'Fee Collection List' );
	$objPHPExcel->setActiveSheetIndex ( 0 );
	$filename = "FeeCollectionList" . time () . ".xls";
	// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
	$objWriter->save ( "" . $filename );
	// Save Excel5 file
	$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
	$objWriter->save ( "" . $filename );
	header ( "Location: $filename" );
	exit ();
} // if()
else
	{
		echo '<script>alert("No Data Available");</script>';
		echo '<script>window.close();</script>';
	}
?>
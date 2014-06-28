<?php

require_once 'phpexcel/Classes/PHPExcel.php';
require_once 'phpexcel/Classes/PHPExcel/IOFactory.php';
require_once 'conn.php';
require_once 'functions/common.php';

makeSafe(extract($_REQUEST));
$query = "select reg_no, concat_ws(' ',stu_fname,stu_mname,stu_lname) as stu_name,sex,dob,present_address,mob,admission_date,date_updated,updated_by from mst_students"; 
$result=mysql_query($query,$link);
$number_rows=mysql_num_rows($result);
if($number_rows>0)
{
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$column = 'A';
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Reg No.");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(10);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Name");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(10);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Gender");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(10);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Date OF Birth");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Address");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Mobile");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Admission Date");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(10);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Updated Date");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(10);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Updated By");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(10);
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$column++;
	$rowCount += 1;

	while($row=mysql_fetch_array($result))
	{
		$regno="";
		$stuname="";
		$gender="";
		$dob="";
		$address="";
		$mob="";
		$addmndate="";
		$dateupdated="";
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

			if(mysql_field_name($result,$j)=="reg_no")
			{	
				if($row['reg_no']!="")
  				{
  					$regno.=$row['reg_no']; 
  				}
  				if($row['stu_name']!="")
  				{
  					$stuname.=$row['stu_name'];
  				}
  				if($row['sex']!="")
  				{
  					$gender.=$row['sex'];
  				}
  				if($row['dob']!="")
  				{
  					$dob.=$row['dob'];
  				}
  				if($row['present_address']!="")
  				{
  					$address.=$row['present_address'];
  				}
  				if($row['mob']!="")
  				{
  					$mob.=$row['mob'];
  				}
  				if($row['admission_date']!="")
  				{
  					$addmndate.=$row['admission_date'];
  				}
  				if($row['date_updated']!="")
  				{
  					$dateupdated.=$row['date_updated'];
  				}
  				if($row['updated_by']!="")
  				{
  					$updatedby.=$row['updated_by'];
  				}
			} //if
		
	
			if(mysql_field_name($result,$j)=="reg_no")
			{
				$value=$regno;
				
			}
			if(mysql_field_name($result,$j)=="stu_name")
			{
				$value=$stuname;
			}
			if(mysql_field_name($result,$j)=="sex")
			{
				$value=$gender;
			}
			if(mysql_field_name($result,$j)=="dob")
			{
				$value=$dob;
			}
			if(mysql_field_name($result,$j)=="present_address")
			{
				$value=$address;
			}
			if(mysql_field_name($result,$j)=="mob")
			{
				$value=$mob;
			}
			if(mysql_field_name($result,$j)=="admission_date")
			{
				$value=$addmndate;
			}
			if(mysql_field_name($result,$j)=="date_updated")
			{
				$value=$dateupdated;
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
	
	$objPHPExcel->getActiveSheet()->setTitle('Registered Applicant List');
	$objPHPExcel->setActiveSheetIndex(0);
	$filename = "RegisteredApplicantList" . time(). ".xls";
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

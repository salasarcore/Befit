<?php

require_once 'phpexcel/Classes/PHPExcel.php';
require_once 'phpexcel/Classes/PHPExcel/IOFactory.php';
require_once 'conn.php';

$query = "select enquiry_no,name,height,weight,age,health_problems,category,phone_no,alt_phone_no,program_recommended,alt_program_recommended,consultant,follow_no,enquiry_for,enquiry_date from contact_us";
$result=mysql_query($query,$link);
$number_rows=mysql_num_rows($result);
if($number_rows>0)
{
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$column = 'A';
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Enquiry No.");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(8);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Name");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Height");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Weight");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Age");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Health Problems");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Category");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Phone No.");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Altername Phone No.");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Program Recommended");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, " Alternate Program Recommended");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Consultant");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Follow No.");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Enquiry For");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Enquiry Date");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	$rowCount += 1;

	while($row=mysql_fetch_array($result))
	{	
		$enquiryno="";
		$name="";
		$height="";
		$weight = "";
		$age = "";
		$healthproblems="";
		$category="";
		$phoneno="";
		$altphoneno="";
		$programrecommended="";
		$altprogramrecommended="";
		$consultant="";
		$followno="";
		$enquiryfor="";
		$enquirydate="";
		
		$column = 'A';
		 
		for($j=0; $j<mysql_num_fields($result);$j++)
		{
			if(!isset($row[$j]))
			$value = NULL;
			elseif ($row[$j] != "")
			$value = strip_tags($row[$j]);
			else
			$value = "";

			if(mysql_field_name($result,$j)=="enquiry_no")
			{	
				if($row['enquiry_no']!="")
  				{
  					$enquiryno.=$row['enquiry_no']; 
  				}
  				if($row['name']!="")
  				{
  					$name.=$row['name'];
  				}
  				if($row['height']!="")
  				{
  					$height.=$row['height'];
  				}
  				if($row['weight']!="")
  				{
  					$weight.=$row['weight'];
  				}
  				if($row['age']!="")
  				{
  					$age.=$row['age'];  	
  				}
  				if($row['health_problems']!="")
  				{
  					$healthproblems.=$row['health_problems'];
  				}
  				if($row['category']!="")
  				{
  					$category.=$row['category'];
  				}
  				if($row['phone_no']!="")
  				{
  					$phoneno.=$row['phone_no'];
  				}
  				if($row['alt_phone_no']!="")
  				{
  					$altphoneno.=$row['alt_phone_no'];
  				}
  				if($row['program_recommended']!="")
  				{
  					$programrecommended.=$row['program_recommended'];
  				}
  				if($row['alt_program_recommended']!="")
  				{
  					$altprogramrecommended.=$row['alt_program_recommended'];
  				}
  				if($row['consultant']!="")
  				{
  					$consultant.=$row['consultant'];
  				}
  				if($row['follow_no']!="")
  				{
  					$followno.=$row['follow_no'];
  				}
  				if($row['enquiry_for']!="")
  				{
  					$enquiryfor.=$row['enquiry_for'];
  				}
  				if($row['enquiry_date']!="")
  				{
  					$enquirydate.=$row['enquiry_date'];
  				}
			} //if
		
	
			if(mysql_field_name($result,$j)=="enquiry_no")
			{
				$value=$enquiryno;
				
			}
			if(mysql_field_name($result,$j)=="name")
			{
				$value=$name;
			}
			if(mysql_field_name($result,$j)=="height")
			{
				$value=$height;
			}
			if(mysql_field_name($result,$j)=="weight")
			{
				$value=$weight;
			}
			if(mysql_field_name($result,$j)=="age")
			{
				$value=$age;
			}
			if(mysql_field_name($result,$j)=="health_problems")
			{
				$value=$healthproblems;
			}
			if(mysql_field_name($result,$j)=="category")
			{
				$value=$category;
			}
			if(mysql_field_name($result,$j)=="phone_no")
			{
				$value=$phoneno;
			}
			if(mysql_field_name($result,$j)=="alt_phone_no")
			{
				$value=$altphoneno;
			}
			if(mysql_field_name($result,$j)=="program_recommended")
			{
				$value=$programrecommended;
			}
			if(mysql_field_name($result,$j)=="alt_program_recommended")
			{
				$value=$altprogramrecommended;
			}
			if(mysql_field_name($result,$j)=="consultant")
			{
				$value=$consultant;
			}
			if(mysql_field_name($result,$j)=="follow_no")
			{
				$value=$followno;
			}
			if(mysql_field_name($result,$j)=="enquiry_for")
			{
				$value=$enquiryfor;
			}
			
			if(mysql_field_name($result,$j)=="enquiry_date")
			{
				$value=$enquirydate;
			}
			
			$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount,$value);
			$column++;
			
		} //for()
			
		$rowCount++;
		
			
	} //while()	
	
	$objPHPExcel->getActiveSheet()->setTitle('Enquirers List');
	$objPHPExcel->setActiveSheetIndex(0);
	$filename = "EnquirersList" . time(). ".xls";
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

<?php

require_once 'phpexcel/Classes/PHPExcel.php';
require_once 'phpexcel/Classes/PHPExcel/IOFactory.php';
require_once 'conn.php';
require_once 'functions/common.php';

makeSafe(extract($_REQUEST));
$query = "select emp.emp_id,emp.emp_name,emp.sex,mst.designation_sc,emp.emp_qualification,emp.emp_doj,emp.emp_addr_pre,emp.emp_ecn,emp.emp_mob,emp.email,emp.access_level,emp.payment_type,
		emp.date_updated,emp.updated_by from employee emp join mst_designations mst on emp.designation_id=mst.designation_id where br_id=".$_SESSION['br_id']." ";
if($dept!=0)
	$query.=" and department_id=".@$dept;
$result=mysql_query($query,$link);
$number_rows=mysql_num_rows($result);
if($number_rows>0)
{
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$column = 'A';
	$calculatedWidth="";
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "ID");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(8);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Name");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Gender");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Designation");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Qualification");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Date OF Joining");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Address");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Emergency Contact");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Mobile");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Email");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Access Level");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Payment Type");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Date Updated");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	
	$column++;
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Updated By");
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	
	$column++;
	$rowCount += 1;

	while($row=mysql_fetch_array($result))
	{
		$id="";
		$name="";
		$gender="";
		$designation = "";
		$qualification = "";
		$doj="";
		$address="";
		$ecn="";
		$mob="";
		$email="";
		$accesslevel="";
		$payment_type="";
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

			if(mysql_field_name($result,$j)=="emp_id")
			{	
				if($row['emp_id']!="")
  				{
  					$id.=$row['emp_id']; 
  				}
  				if($row['emp_name']!="")
  				{
  					$name.=$row['emp_name'];
  				}
  				if($row['sex']!="")
  				{
  					$gender.=$row['sex'];
  				}
  				if($row['emp_qualification']!="")
  				{
  					$qualification.=$row['emp_qualification'];
  				}
  				if($row['emp_doj']!="")
  				{
  					$doj.=$row['emp_doj'];  	
  				}
  				if($row['emp_addr_pre']!="")
  				{
  					$address.=$row['emp_addr_pre'];
  				}
  				if($row['emp_ecn']!="")
  				{
  					$ecn.=$row['emp_ecn'];
  				}
  				if($row['emp_mob']!="")
  				{
  					$mob.=$row['emp_mob'];
  				}
  				if($row['email']!="")
  				{
  					$email.=$row['emp_email'];
  				}
  				if($row['access_level']!="")
  				{
  					$accesslevel.=$row['access_level'];
  				}
  				if($row['payment_type']!="")
  				{
  					$payment_type.=$row['payment_type'];
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
		
	
			if(mysql_field_name($result,$j)=="emp_id")
			{
				$value=$id;
				
			}
			if(mysql_field_name($result,$j)=="emp_name")
			{
				$value=$name;
			}
			if(mysql_field_name($result,$j)=="sex")
			{
				$value=$gender;
			}
			if(mysql_field_name($result,$j)=="emp_qualification")
			{
				$value=$qualification;
			}
			if(mysql_field_name($result,$j)=="emp_doj")
			{
				$value=$doj;
			}
			if(mysql_field_name($result,$j)=="emp_addr_pre")
			{
				$value=$address;
			}
			if(mysql_field_name($result,$j)=="emp_ecn")
			{
				$value=$ecn;
			}
			if(mysql_field_name($result,$j)=="emp_mob")
			{
				$value=$mob;
			}
			if(mysql_field_name($result,$j)=="emp_email")
			{
				$value=$email;
			}
			if(mysql_field_name($result,$j)=="access_level")
			{
				$value=$accesslevel;
			}
			if(mysql_field_name($result,$j)=="payment_type")
			{
				$value=$payment_type;
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
	$objPHPExcel->getActiveSheet()->setTitle('Employee List');
	$objPHPExcel->setActiveSheetIndex(0);
	$filename = "EmployeeList" . time(). ".xls";
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

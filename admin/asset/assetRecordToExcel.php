<?php

require_once '../phpexcel/Classes/PHPExcel.php';
require_once '../phpexcel/Classes/PHPExcel/IOFactory.php';
require_once '../conn.php';

$query = "select ar.asset_record_id,am.name,quantity,ar.price,ar.on_demand,ar.received_date,ar.created_at,ar.updated_by from asset_record ar
			left join asset_master am on ar.asset_id=am.asset_id";
$result = mysql_query($query, $link);

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
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Asset Name");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Quantity");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Price");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "On Demand?");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Received Date");
	$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
	$column++;
	
	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Created At");
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
		//print_r($row);
		//echo"<br>";
		
		$id="";
		$name="";
		$quantity="";
		$price="";
		$demand= "";
		$receiveddate= "";
		$createdate="";
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
			
			if(mysql_field_name($result,$j)=="asset_record_id")
			{	
				if($row['asset_record_id']!="")
  				{  		
  					$id.=$row['asset_record_id']; 
  				}
  				if($row['name']!="")
  				{
  					$name.=$row['name'];
  				}
  				if($row['quantity']!="")
  				{
  					$quantity.=$row['quantity'];
  				}
  				if($row['price']!="")
  				{
  					$price.=$row['price'];
  				}
  				if($row['on_demand']!="")
  				{
  					if($row['on_demand'] == "Y")
  					$demand.='YES';
  					else
  						$demand.='NO';
  				}
  				if($row['received_date']!="")
  				{
  					$receiveddate.=$row['received_date'];
  				}
  				if($row['created_at']!="")
  				{
  					$createdate.=$row['created_at'];
  				}
  				if($row['updated_by']!="")
  				{
  					$updatedby.=$row['updated_by'];
  				}
			
			} //if
		
	
			if(mysql_field_name($result,$j)=="asset_record_id")
			{
				$value=$id;
				
			}
			if(mysql_field_name($result,$j)=="name")
			{
				$value=$name;
			}
			if(mysql_field_name($result,$j)=="quantity")
			{
				$value=$quantity;
			}
			if(mysql_field_name($result,$j)=="price")
			{
				$value=$price;
			}
			if(mysql_field_name($result,$j)=="on_demand")
			{
				$value=$demand;
			}
			if(mysql_field_name($result,$j)=="received_date")
			{
				$value=$receiveddate;
			}
			if(mysql_field_name($result,$j)=="created_at")
			{
				$value=$createdate;
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
	$objPHPExcel->getActiveSheet()->setTitle('Asset Records');
	$objPHPExcel->setActiveSheetIndex(0);
	$filename = "AssetRecords" . time(). ".xls";
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
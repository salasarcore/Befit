<?php 
error_reporting( E_ALL ^ E_DEPRECATED ^ E_NOTICE);
extract($_POST);
$excelname= $_FILES["file"]["name"];
require_once 'ExcelUpload/phpExcelReader/Excel/reader.php';

function add_person($name,$number)
{
	global $ExcelData;
	{
		$ExcelData []= array(
				'name' => $name,
				'number' => $number,
		);
	}
}

$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('CP1251');
if ($_FILES["file"]["error"] > 0)
{
	echo "<div class='error'>Please select an excel file</div>";
}
else
{
	 if(($_FILES["file"]["type"] == "application/vnd.ms-excel"))	
	{
		$extn=explode('.',$_FILES["file"]["name"]);
		$upath="ExcelUpload/phpExcelReader/temp/".$excelname;
		move_uploaded_file($_FILES['file']['tmp_name'],$upath);
	}
	else
	{
		echo "<div class='error'>".$_FILES["file"]["name"]." is not readable. Please upload again</div>";
	} 
}

$file="ExcelUpload/phpExcelReader/temp/".$excelname;
$data->read($file);

for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
{
    $name='';
	$number=''; 
	$name=$data->sheets[0]['cells'][$i][1];
	$number=$data->sheets[0]['cells'][$i][2];
	add_person($name,$number);
}
?>
<h2>Please Check your data..</h2>
<div align="center" style="font-size:16px;margin-bottom:10px;">
<form action="pages.php?src=sms/savedata.php" method="post" enctype="multipart/form-data">
<input type="hidden" id="save" name="save" value="<?php echo $excelname?>" >
<input type="Submit" value="Save"  class="btn save" id="Submit" name="Submit">
<input type="button" onclick="window.location.href='pages.php?src=sms/contacts.php'" class="btn close" style="margin-left:6px;" value="Upload Again!">
</form>
</div>
<?php 
include('print_excel_data.php');
?>
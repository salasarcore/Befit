<?php 
include_once("../../".COMMON_CODE."/functions/common.php");
error_reporting( E_ALL ^ E_DEPRECATED ^ E_NOTICE);
require_once 'ExcelUpload/phpExcelReader/Excel/reader.php';
makeSafe(extract($_POST));
$excelname= makeSafe($_POST["save"]);
$j=2;
$found="";
function add_person($name,$number)
{
	global $ExcelData;
	global $found;
	global $j;
	foreach($ExcelData as $dataentry){
		if($dataentry['number']==trim($number)){
			$found.=$j.", ";
		}
	}
	$j++;
	if($found=="")
	{
		$ExcelData []= array(
				'name' => $name,
				'number' => $number,
		);
	}
}

$ExcelData = array();
$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('CP1251');
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
$msg="";
$Err="";
$i=2;
foreach($ExcelData as $rowData)
{
	
	if(trim($rowData['name'])=='')
     	$Err.= "<div class='error'>Name should not be blank at line ".$i."</div><br>";
	if(trim($rowData['number'])=='')
		$Err.= "<div class='error'>Mobile number should not be blank at line ".$i."</div><br>";
	if(trim($rowData['number'])!='' && strlen(trim($rowData['number']))!=10)
		$Err.= "<div class='error'>Mobile number should be 10 digits at line ".$i."</div><br>";
	if(trim($rowData['number'])!='' && !is_numeric(trim($rowData['number'])))
		$Err.= "<div class='error'>Mobile number should be numeric at line ".$i."</div><br>";
	
	if(!makeSafe(isset($_POST['overwrite']))){
		$sqlnum="select * from sms_non_registered_users where mobile_num='".trim($rowData['number'])."'";
		$resnum=mysql_query($sqlnum,$link);
		if(mysql_num_rows($resnum)>0)
			$msg.="<div class='error'>Duplicate mobile number at line ".$i."</div><br>";
	}
$i++;
}

if($found!="")
	$msg.="<div class='error'>Duplicate mobile number in uploaded excel file at line ".rtrim($found,", ")."</div><br>";

$msg=$Err.$msg;

if(trim($msg)==""){
foreach($ExcelData as $rowData)
{
	$updatedby=$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']';
	if(makeSafe(isset($_POST['overwrite']))){
		if($overwrite=="overwrite"){
			$sqlnum="select * from sms_non_registered_users where mobile_num='".trim($rowData['number'])."'";
			$resnum=mysql_query($sqlnum,$link);
			if(mysql_num_rows($resnum)>0){
				$sqlupdate="update sms_non_registered_users set name='".trim($rowData['name'])."' where mobile_num='".trim($rowData['number'])."'";
				mysql_query($sqlupdate,$link);
			}
			else
			{
				$studentid=getNextMaxId('sms_non_registered_users','user_id')+1;
				$sql="insert into sms_non_registered_users(user_id,name,mobile_num,updated_by)
					values('".$studentid."','".makeSafe($rowData['name'])."','".makeSafe($rowData['number'])."','".$updatedby."');";
				$res1=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			}
		}
		else{
			$sqlnum="select * from sms_non_registered_users where mobile_num='".trim($rowData['number'])."'";
			$resnum=mysql_query($sqlnum,$link);
			if(mysql_num_rows($resnum)<=0){
				$studentid=getNextMaxId('sms_non_registered_users','user_id')+1;
				$sql="insert into sms_non_registered_users(user_id,name,mobile_num,updated_by)
					values('".$studentid."','".makeSafe($rowData['name'])."','".makeSafe($rowData['number'])."','".$updatedby."');";
				$res1=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			}
		}
	}
	else{
			$studentid=getNextMaxId('sms_non_registered_users','user_id')+1;
			$sql="insert into sms_non_registered_users(user_id,name,mobile_num,updated_by)
			values('".$studentid."','".makeSafe($rowData['name'])."','".makeSafe($rowData['number'])."','".$updatedby."');";
			$res1=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
		
	}
}
}
if(trim($msg)!="")
{
echo "<h2>Data has not been saved..</h2>";	
?>
<div  style="margin-left:10px;margin-bottom:5px;font-size:14px;">
<form action="pages.php?src=sms/savedata.php" method="post" enctype="multipart/form-data">
<input type="hidden" id="save" name="save" value="<?php echo $excelname?>" >
<?php if($Err=="" && $found=="") { ?><input type="radio" name="overwrite" id="overwrite" value="overwrite" /> Overwrite Duplicate Contacts <input type="radio" name="overwrite" id="skip" value="skip"/> Skip Duplicate Contacts<br><br><?php } ?>
<h3><?php echo $msg; ?></h3> 
<input type="Submit" value="Save"  class="btn save" id="Submit" name="Submit">
<input type="button" onclick="window.location.href='pages.php?src=sms/contacts.php'" class="btn close" style="margin-left:6px;" value="Upload Again!">
</form>
</div>
<?php
}
else{
?>
<h2>Data Saved Successfully..</h2>
<div style="margin-left:10px;margin-bottom:5px;font-size:14px;"><a class="btn close" align="left" href="pages.php?src=sms/contacts.php">BACK</a></div>
<?php
}
?>
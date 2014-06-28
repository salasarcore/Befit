<?php 
@session_start();
include("../conn.php");
include('../check_session.php');
include('../classes/class.sample_image.php');
include("../functions/functions.php");
$act=@$_GET['act'];
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
/*include("../modulemaster.php");

	$id=option_admission_register_upload_image;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
$studentID=@$_GET['studentID'];

if(@$act=="upload")
{


		
	if ($_FILES["file"]["error"] > 0)
	{
		$msg= "<div class='error'>Please Select an Image</div>";
	}
	else
	{

		if (($_FILES["file"]["type"] == "image/gif")
				|| ($_FILES["file"]["type"] == "image/jpeg")
				|| ($_FILES["file"]["type"] == "image/jpg")
				|| ($_FILES["file"]["type"] == "image/pjpeg")
				|| ($_FILES["file"]["type"] == "image/x-png")
				|| ($_FILES["file"]["type"] == "image/png"))
		{
			$extn=explode('.',$_FILES["file"]["name"]);
			$upath="../../site_img/stuimg/".DOMAIN_IDENTIFIER."_".base64_encode($studentID).".".$extn[1];
			$SourceFile = $_FILES["file"]["tmp_name"];//Source image
			upload_image($upath,130,170,1);
			$f = new SimpleImage;
			$f->watermark($upath);
			
			$sql="update mst_students set mime='".$extn[1]."',updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where stu_id=".$studentID;
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$msg= "<div class='success'>Image Uploaded successfully</div>";
		}
		else
			$msg= "<div class='error'>Invalid format Only JPG, JPEG, PNG, GIF files are allowed</div>";
	}
}

?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>UPLOAD IMAGE</title>


<script type="text/javascript">
function validatefile()
{

	var filename = document.frm.file.value;
	var validationStatus = true;
	var parts = filename.split('.');
	
	var exts = ['png', 'jpg', 'jpeg', 'gif'];
     if(exts.indexOf(parts[parts.length-1].toLowerCase()) == -1) 
       {
    	 alert('Only PNG, JPG, JPEG and GIF files are allowed');
         validationStatus = false;
         document.frm.file.value="";
         document.frm.file.focus();
        }

     return validationStatus;

}
</script>
</head>
<body>
<div id="middleWrap">
		<div class="head"><h2>UPLOAD IMAGE</h2></div>

<?php echo @$msg;?>
<form id="frm" name="frm" method="post" action="student_image.php?studentID=<?php echo $studentID;?>&act=upload"  enctype="multipart/form-data" onsubmit="return validatefile();">
<table  class="adminform">
<tr>
	<td  align="right" class="redstar">Select Photo :</td>
	<td><input name="file" id="file" type="file" /> <br><p class="hint_text">(only .jpeg, .jpg, .gif, .png image format is allowed) </p> </td>
	</tr> 
	<tr>
	<td align="center" colspan=2>
	<input type="submit" class="btn save" value='UPLOAD' name="B1">
	<input type="button" class="btn close" value="CLOSE" onClick="parent.emailwindow.close();">
			</td>
			</tr>
</table>

 </form>
 
  </div>
   
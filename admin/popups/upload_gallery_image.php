<?php 
@session_start();
include("../conn.php");
include_once("../../functions/functions.php");

include('../check_session.php');

$Description="";
$act=makeSafe(@$_GET['act']);
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
$imageID=makeSafe(@$_GET['imageID']);
$galleryID=makeSafe(@$_GET['galleryID']);
$action=makeSafe(@$_POST['action']);
$Errs="";
include_once('../classes/class.sample_image.php');
if(@$action=="SAVE" || @$action=="UPDATE")
{	

$Img=makeSafe(@$_POST['file']);
$Description=makeSafe($_POST['txtDescription']);
$logoChecked= makeSafe(@$_POST['chkLogo']);

if(@$action=="SAVE")
{	
	if ($_FILES["file"]["error"] > 0)// no logo upload
		{
			
				$Errs="<DIV class='error'>Please select Image</DIV>";
			
		}		
	else //logo upload
		{
			$extn=explode('.',$_FILES["file"]["name"]);
			if($extn[1]=='jpg' || $extn[1]=='jpeg' || $extn[1]=='gif' || $extn[1]=='png' || $extn[1]=='JPG'|| $extn[1]=='JPEG'|| $extn[1]=='GIF'|| $extn[1]=='PNG')
				
			{
				$imgid=getNextMaxId("gallery_images","image_id")+1;
				$sql="insert into gallery_images(image_id,image_description, gallery_id, mime,updated_by) values(".$imgid.",'".$Description."',".$galleryID.",'".$extn[1]."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0)
				{
				    $extn=explode('.',$_FILES["file"]["name"]);
					$sql="SELECT max(image_id) FROM gallery_images ";
					$res1=mysql_query($sql,$link) or die(mysql_error($link));
					if(mysql_affected_rows($link)>0)
						{
						  $row=mysql_fetch_array($res1);
						  $maximageID=$row[0];
						}
					$upathThumb="../../site_img/gallery/thumb/".$imgid.".".$extn[1];
					$upathBig="../../site_img/gallery/bigimg/".$imgid.".".$extn[1];
					
					
					move_uploaded_file($_FILES["file"]["tmp_name"],$upathBig);
					//move_uploaded_file($_FILES["file"]["tmp_name"],$upathThumb);
						
					//---watermark for big image
					if($extn[1]=='jpg' || $extn[1]=='jpeg' || $extn[1]=='JPG'|| $extn[1]=='JPEG') {
					$f = new SimpleImage;
					$f->watermark($upathBig); }
					
					$image = new SimpleImage();
					$image->load($upathBig);
					
					$targetWidth=60;
					$targetHeight=60;
					if ($image->getWidth()>=$image->getHeight()) 
						$percentage = ($targetWidth / $image->getWidth());
					else 
						$percentage = ($targetHeight / $image->getHeight());
	
					$width = round($image->getWidth() * $percentage);
					$height = round($image->getHeight() * $percentage);
					$image->resize($width,$height);
					$image->save($upathThumb);
					$image = new SimpleImage();
					$image->load($upathBig);
					$targetWidth=640;
					$targetHeight=480;
					if ($image->getWidth()>=$image->getHeight()) 
						$percentage = ($targetWidth / $image->getWidth());
					else 
						$percentage = ($targetHeight / $image->getHeight());
	
					$width = round($image->getWidth() * $percentage);
					$height = round($image->getHeight() * $percentage);
					$image->resize($width,$height);
					$image->save($upathBig);
					
					$Errs="<DIV class='success'>Record Saved Successfully</DIV>";
				}
					else
						$Errs="<DIV class='sicess'>Record Not Saved Successfully</DIV>";
			}
			else
			{
				$Errs="<DIV class='error'>Invalid Image upload</DIV>";
			}
		}	
}
if(@$action=="UPDATE")
 {	
  
 $extn=explode('.',$_FILES["file"]["name"]);
		  if($logoChecked=="Y")// if logo changed then sql will be diffrent
			{
				if($extn[1]=='jpg' || $extn[1]=='jpeg' || $extn[1]=='gif' || $extn[1]=='png' || $extn[1]=='JPG'|| $extn[1]=='JPEG'|| $extn[1]=='GIF'|| $extn[1]=='PNG')
					//$extn=explode('.',$_FILES["file"]["name"]);
					$sql="update gallery_images set mime='".$extn[1]."',image_description='".$Description."' where  image_id=".$imageID;
				else
					$Errs="<DIV class='error'>Invalid Image upload</DIV>";
			}
			else
				$sql="update gallery_images set image_description='".$Description."' where image_id=".$imageID;
			
			// now update
			$res=mysql_query($sql,$link);
			if($res)
			{ 						
						$Errs="<div class='success'>Record Updated Successfully</div>";
				
			if($logoChecked=="Y")// if logo changed
				{
					$extn=explode('.',$_FILES["file"]["name"]);
					$upathThumb="../../site_img/gallery/thumb/".($imageID).".".$extn[1];
					$upathBig="../../site_img/gallery/bigimg/".($imageID).".".$extn[1];
					move_uploaded_file($_FILES["file"]["tmp_name"],$upathBig);
					move_uploaded_file($_FILES["file"]["tmp_name"],$upathThumb);
					//---watermark for big image
					if($extn[1]=='jpg' || $extn[1]=='jpeg' || $extn[1]=='JPG'|| $extn[1]=='JPEG') {
							
					$f = new SimpleImage;
					$f->watermark($upathBig); }
					$Errs=$Errs."<div class='error'>Image Updated</div>";
					$image = new SimpleImage();
					$image->load($upathBig);
					
					$targetWidth=60;
					$targetHeight=60;
					if ($image->getWidth()>=$image->getHeight()) 
						$percentage = ($targetWidth / $image->getWidth());
					else 
						$percentage = ($targetHeight / $image->getHeight());

					$width = round($image->getWidth() * $percentage);
					$height = round($image->getHeight() * $percentage);
					$image->resize($width,$height);
					$image->save($upathThumb);
					
					$image = new SimpleImage();
					$image->load($upathBig);
					$targetWidth=640;
					$targetHeight=480;
					if ($image->getWidth()>=$image->getHeight()) 
						$percentage = ($targetWidth / $image->getWidth());
					else 
						$percentage = ($targetHeight / $image->getHeight());

					$width = round($image->getWidth() * $percentage);
					$height = round($image->getHeight() * $percentage);
					$image->resize($width,$height);
					$image->save($upathBig);
				}	
			}
			if(mysql_affected_rows($link)>0)
			$Errs="<div class='success'>Record Saved Successfully</div>";
			if(mysql_affected_rows($link)==0)
			$Errs="<div class='success'>No data Change Found</div>";
			if(mysql_affected_rows($link)<0)
			$Errs="<div class='error'>Record Not Saved Successfully</div>";
		
 } //END OF update

}//END OF SAVD OR UPDATE 

if(@$action=="DELETE" )
{
	$Errs="";
			   $query   = "delete from gallery_images  where  image_id=".$imageID;
				$result  = mysql_query($query);
				if(mysql_affected_rows($link)>0)
					{
					 	$Errs="<div class='success'>Image Deleted Successfully</div>";;
						
				 	}
				else
			      		$Errs= "Image Can't be Deleted";	
			
			
}

if(@$act=="edit" || @$act=="delete" )
{
	
		$query   = "SELECT  image_id,mime,image_description FROM gallery_images WHERE image_id=".$imageID;

		$result  = mysql_query($query);
		if(mysql_affected_rows($link)>0)
			{
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			 	
				$mime=@$row['mime'];
				$Description=@$row['image_description'];
				
				}
		
}

?>


	<SCRIPT language=javascript>
function validategallery()
{
if(document.frmManu.file.value!="")
{
	 return validateattachfile(document.frmManu.file.value);
}
}
function ClearField(frm){
	 
	  frm.file.value = "";
	  frm.chkLogo.value = "";
	  frm.txtDescription.value = "";
	  frm.myimg.value = "";
}
function validateattachfile(filename)
{
	var validationStatus = true;
	var parts = filename.split('.');
	
	var exts = ['jpg', 'jpeg', 'png', 'gif','JPG','JPEG','PNG','GIF'];
     if(exts.indexOf(parts[parts.length-1]) == -1) 
       {
    	 alert('Only JPG, JPEG, PNG and GIF files are allowed');
         validationStatus = false;
        }

     return validationStatus;
}
function waitSign()
{
	document.getElementById("WS").innerHTML='Uploading...';
}
</script>

<html>
<head>
	<meta http-equiv="Content-Language" content="en-us">
	<title>UPLOAD GALLERY IMAGE</title>
	<link rel="shortcut icon" href="../favicon.ico">
</head>
<body>

<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act);?> IMAGE </h2></div>

<span id="spErr"><?php echo $Errs;?></span>
	<div id="WS" name="WS" ></div>

<form action="upload_gallery_image.php?act=<?php echo $act; ?>&imageID=<?php echo $imageID; ?>&galleryID=<?php echo $galleryID; ?>" method="post" enctype="multipart/form-data" name="frmManu" onsubmit="return validategallery();">

<div id="WS"></div>
<table class="adminform" width="500px"> 
<tr>
    <td align="right" valign="top">Image Path :</td>
    <td>
    	<input type="file" name="file" id="file" size="40"  value ='<?php echo @$file;?>' />
		<br />
		(.jpg,.jpeg,.png,.gif files only)</br>
		<input type="checkbox" name="chkLogo"  value="Y" />Tick it to modify Image
                   
    </td>
  </tr>
  <tr>
    <td align="right" valign="top">IMAGE SIZE</td>
    <td>
    
    	
<input type="radio" value="800X600" name="imgsize">800 X 600<br />
<input type="radio" value="600X480" checked name="imgsize">600 X 480<br />
                   
</td>
</tr>
<tr>
<td align="right">Description :</td>
<td>
<textarea name="txtDescription" id="txtDescription" cols="45" rows="5"><?php if(@$act!="add") echo $Description; ?></textarea>
</td>
</tr>
<tr>
<td colspan="2" align="center"><input type="submit" class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1" id="B1" onclick="waitSign();" >
		<?php  if(@$act!="delete") {?>	<input type="button" class="btn reset" value="RESET" id= "reset "name="reset" onClick="ClearField(this.form)" /><?php } ?>
			<input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close()">
          	<INPUT type='hidden' name='action' value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>'>
</td>
<INPUT type='hidden' name='imageID' value='<?php echo @$imageID; ?>'>
<INPUT type='hidden' name='galleryID' value='<?php echo @$galleryID; ?>'>
</tr>       
 </table>
 </form>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo @$Errs; ?>";
</script>
   </div>
   </body>
   </html>
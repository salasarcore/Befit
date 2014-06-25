<?php 

include("../conn.php");
include_once("../../functions/functions.php");

include('../check_session.php');

$gallery_name="";
$gallery_description="";
$Errs="";
makeSafe(extract($_REQUEST));
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php


if(@$action=="SAVE" || @$action=="UPDATE")
{
	
	$gallery_description=str_replace("\n","<br />",makeSafe(@$_POST['txtgallery_description']));
	
	

    if($action=="SAVE")
	{
		if($galleryName=="")
		{
			$Errs= "<div class='error'>Please Enter Gallery Name</div>";
	
		}
		else
		{
		$newgalleryid=getNextMaxId("gallery","gallery_id")+1;
			$sql="insert into gallery(gallery_id,br_id,gallery_name,gallery_description,updated_by)";
        $sql = $sql ." values(".$newgalleryid.",".$_SESSION['br_id'].",'".$galleryName."','".$gallery_description."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
	        $res=mysql_query($sql,$link);
	        if(mysql_affected_rows($link)>0)
	        	
	        	$Errs= "<div class='success'>Record Saved Successfully</div>";
	        	
	        else
	        	$Errs="<div class='error'>Record Not Saved Successfully</div>";
		}
	}
	else	
	{
		if($galleryName=="")
			$Errs= "<div class='error'>Please Enter Gallery Name</div>";
		else
	{	$sql="update gallery set gallery_name='".$galleryName."',gallery_description='".$gallery_description."',updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where gallery_id=".$galleryID;
		$res=mysql_query($sql,$link); 
		if(mysql_affected_rows($link)==0)
			$Errs="<div class='success'>No Data Changed</div>";
		if(mysql_affected_rows($link)>0)
			$Errs="<div class='success'>Record Updated successfully</div>";
		if(mysql_affected_rows($link)<0)
			$Errs="<div class='error'>Record Not Updated successfully</div>";
	}
 if($action=="SAVE")
		$act=="add";
	else
	$act=="edit";
}
}
if(@$action=="DELETE")
{
	$query1 = "select * from gallery_images where gallery_id=".$galleryID;
	$result1= mysql_query($query1);
	if(mysql_affected_rows($link)>0)
	{
		$Errs="<div class='error'>can not delete the gallery directly</div>";
	}
	else 
	{
	$query   = "delete  FROM gallery where gallery_id=".$galleryID;
	$result  = mysql_query($query);
	if(mysql_affected_rows($link)>0)
		$Errs= '<div class="success">gallery Deleted Successfully</div>';
	}	
}

if(@$act=="edit" || @$act=="delete" )
{

		$query   = "SELECT  gallery_description, gallery_name,  date_updated,updated_by FROM gallery  where gallery_id=".$galleryID;
		$result  = mysql_query($query);
		if(mysql_affected_rows($link)>0)
			{
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			 	
				$gallery_name=@$row['gallery_name'];
				$gallery_description=str_replace("<br />","\n",@$row['gallery_description']);
				$last_updated=@$row['date_updated'];
				$updated_by=@$row['updated_by'];
			}
		
}
?>


<html>
<head>
	<meta http-equiv="Content-Language" content="en-us">
	<title>IMAGE GALLERY</title>
	<link rel="shortcut icon" href="../favicon.ico">
	<SCRIPT>
function ClearField(frm){
	  document.frmManu.galleryName.value = "";
	  document.frmManu.txtgallery_description.value = "";
	}
</SCRIPT>
</head>
<body>
<div id="middleWrap">
		<div class="head"><h2><?php echo strtoupper($act);?> IMAGE GALLERY</h2></div>

 <span id="spErr"><?php echo $Errs;?></span>
<form method="post" name="frmManu" action="gallery.php?galleryID=<?php echo makeSafe($galleryID);?>&act=<?php echo makeSafe($act); ?>" >

<table class="adminform" width="500px">
 
  <tr>
    <td width="44%" align="right" class="redstar">Gallery Name :</td>
    <td width="43%">
        <input type="text" name="galleryName" id="galleryName" size="40" maxlength="200" value ='<?php if(@$act!="add") echo $gallery_name; ?>'/>        </td>
    </tr>
  <tr>
    <td align="right">Description :</td>
    <td><textarea type="text" name="txtgallery_description" id="txtgallery_description" maxlength="500" cols="40" rows="5"><?php if(@$act!="add") echo $gallery_description; ?></textarea></td>
    </tr>
 <tr>
    <td align="right">Last Updated : </td>
    <td><?php echo @$last_updated; ?></td>
    </tr><tr>
    <td align="right">Updated By : </td>
    <td><?php echo @$updated_by; ?></td>
    </tr>
  <tr>    
        <td align="center" colspan=2><input type="submit" class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
					
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1">
			<?php  if(@$act!="delete") {?><input type="button" class="btn reset" value="RESET" id= "reset "name="reset" onClick="ClearField(this)"  /> <?php }?>
			<input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close()">
          	<input type='hidden' name='action' value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' />
           </td>
    </tr>
</table>
</form>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
</div>
</body>
</html>
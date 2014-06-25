<?php
function upload_image($upath,$width,$height,$aRatio)
{
	move_uploaded_file($_FILES["file"]["tmp_name"],$upath);
	$image = new SimpleImage();
	$image->load($upath);
	$targetWidth=$width;
	$targetHeight=$height;
	if ($image->getWidth()>=$image->getHeight()) 
		$percentage = ($targetWidth / $image->getWidth());
	else 
		$percentage = ($targetHeight / $image->getHeight());

	$width = round($image->getWidth() * $percentage);
	$height = round($image->getHeight() * $percentage);
	$image->resize($width,$height);
	$image->save($upath);
					
}					
					
					
function mysql_err() {
 echo mysql_error();
 }?>
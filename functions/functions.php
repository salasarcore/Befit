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
			
function makeSafe($string){
	$string=trim($string);
		$string=str_replace("'","",$string);
		$string=str_replace("\\","",$string);
		$string=(get_magic_quotes_gpc() ? stripslashes($string) : $string);
		
			
			
			return str_replace("'","",$string);
	
	}
function getNextMaxId($table_name,$col_name)
 {
      $query="Select max($col_name) from $table_name";
      $result=mysql_query($query);
      $row=mysql_fetch_array($result);
      return $row[0];
 }
		
					
function mysql_err() {
 echo mysql_error();
 }?>
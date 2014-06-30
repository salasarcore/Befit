
<?php 
function createTemplate($param,$path)
{
$data="";
$img=$path;
	
	
	
  $data.="<!DOCTYPE>
	<html>
	<head>
	<style>
	body { font-family: verdana;}
	.menutop {color:#000000;
	background: none repeat scroll 0 0 #dce9f9;
   	display: block;
    padding: 8px 10px;
	text-decoration: none;
	font-size:12px;
	margin-top:-7px;
	margin-left:1px;
	margin-right:1px;
	margin-bottom:1px; color:black}
	</style>
	</head>
	<body>
	 <table border='0' width='80%' style='margin:15px; border:1px solid #424646;font-family:arial; font-size:14px; border-collapse:collapse;' cellspacing='0'>
	 <tr>
	 <td align='left' width='30%'>
 	 <img align='center' style='padding-left:10px;padding-top:10px;margin-left:10px;'  src='".$img."'></td>";
		
     	$data.="<td align='left' width='70%' style= 'font-size:18px;font-weight:bold;padding-left:10px;'> Befit Ladies Gym	</td>"; 
	  	$data.="
    </tr>
	  	<tr>
	  	<td colspan='2'><hr style='height:1px; margin:0 10 0 10;'></td>		
	  	</tr>
	<tr>
	 <td colspan='2' style='padding-left:10px;'><br>".$param."
	 </td>
	 </tr>
	  <tr>
	  <td colspan='2' style='padding-left:10px;'><br>
	 Regards,
	  </td>
	  </tr>";
    
     	$data.="<tr><td colspan='2' style='padding-left:10px;'>Befit Ladies Gym </td></tr><tr><td style='padding-left:10px;' colspan='2'><a href='#' style='color:#0069A6;' target='_blank'></a></td></tr>";
	  	$data.="
	   <tr>
	   <td colspan='2'>
	  <div style='clear:both;'>
	  &nbsp;
		</div>
	   </td>
	  </tr>
	  </table>
	 </body>
	</html>";

return $data;
echo $data;
}

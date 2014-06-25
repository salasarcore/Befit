<?php include("../conn.php");
include_once("../../functions/functions.php");

include('../check_session.php');
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title></title>
	<link href="../css/popup.css" type="text/css" rel="stylesheet">
	
</head>
<body>
 <div id="middleWrap">
	<?php 	

if( @$_GET['nid']=="")
$sql="select nid , ntype , ndate ,nbody, subject,mime,published  from news  where published='Y' order by nid desc limit 0,1 ";
else
$sql="select nid , ndate ,nbody, subject,published  from news  where published='Y' and nid=".base64_decode(makeSafe($_GET['nid']))." order by nid desc limit 0,1 ";
$result  = mysql_query($sql) or die('Error, query failed');
if(mysql_affected_rows($link)>0)
	{
		$row     = mysql_fetch_array($result, MYSQL_ASSOC);
		echo "<div class='courses-top'><h2>Subject : ".$row['subject']."</h2>";
		echo "<div class='date' style='color:#000000'><h3>Date : ".date("jS-M-Y, g:i A",strtotime($row['ndate']))."</h3></div><br>";
		echo "<div class='notice_body' style='word-wrap:break-word; font-size:13px;color:#000000;'><b>Description : </b><br><div style='border: 1px solid #cccccc; padding: 4px;color:#000000;'>";
		echo (trim($row['nbody'])!="") ? $row['nbody'] : "NO DESCRIPTION";
		echo "</div></div>";
		echo"</div>";
		}
?>
</div>
</body>
</html>

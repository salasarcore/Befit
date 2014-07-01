<link rel="stylesheet" type="text/css" href="css/classic.css"/>
<?php 

@session_start();
include('admin/conn.php');
include('admin/functions/common.php');
$msg="";
$comment="";

if(makeSafe(isset($_REQUEST['submit'])))
{
	
	$comment=makeSafe($_REQUEST['comment']);
	//$stu_id=$_SESSION['stu_id'];
	$notice_id=makeSafe($_REQUEST['noticeid']);
	$id=getNextMaxId("notice_feedback","id")+1;
	$query="INSERT INTO notice_feedback VALUES(".$id.",".$notice_id.",'".$comment."',now())";
	$result=mysql_query($query);
	if($result)
	{
		$msg="<div class='success'>Feedback sent successfully.</div>";
		$comment="";
	}
	
	else
		$msg="<div class='error'>Unable to sent Feedback! Please try again.</div>";
}
	if(makeSafe(@$_GET['nid'])=="")
	$sql="select nid , ntype , ndate ,nbody, subject,mime,published  from notice_board  where published='Y' order by nid desc limit 0,1 ";
	else
	$sql="select nid , ntype , ndate ,nbody, subject,mime,published  from notice_board  where published='Y' and nid=".base64_decode(makeSafe($_GET['nid']))." order by nid desc limit 0,1 ";
	$result  = mysql_query($sql) or die('Error, query failed'.mysql_error());
	if(mysql_affected_rows($link)>0)
	{
		$row     = mysql_fetch_array($result, MYSQL_ASSOC);
		echo "<div class='courses-top'><font class='subj'><b>Subject: ".$row['subject']."</b></font></div><br />";
		echo "<div class='date' style='font-size:18px;'><b>Date:</b> ".date_format(date_create($row['ndate']), 'jS F Y, g:ia')."</div>";
		echo "<div class='notice_body' style='word-wrap:break-word; font-size:20px; padding:10px 0 10px 0;'>".$row['nbody']."</div>";
		$path="notice/".base64_encode($row['nid']).".".$row['mime'];
		if(is_file($path))
		echo "<br /><div class='".$row['mime']."'><a href='".$path."' target='blank' title='".$row['subject']."'>Download Attachment</a></div>";
	
		$sqlnotice="select a.*,concat_ws(' ',b.stu_fname,b.stu_mname,b.stu_lname) as stu_name from notice_feedback as a join mst_students as b on a.stu_id=b.stu_id WHERE a.notice_id=".base64_decode(makeSafe($_GET['nid']))." order by feedback_datetime desc";
		$resultnotice=mysql_query($sqlnotice);
		if(isset($_SESSION['stu_id'])){
			while ($row = mysql_fetch_array($resultnotice)) {
				echo "<table frame=''  style='width: 100%;padding:5px 5px 8px 5px; BORDER:1PX SOLID #B8B8B8 ;border-radius: 5px;'>";
				echo "<tr>";
				echo "<td style='padding-bottom:3px;' align='left'><b>".ucfirst($row['stu_name'])."'s</b> Parent Says,</td><td align='right' style='font-size:13px;'>".date_format(date_create($row['feedback_datetime']), 'jS F Y, g:ia')."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan='2'>".$row['feedback']."</td>";
				echo "</tr>";
				echo "</table>";
				echo "<br>";
			}
		}

				
		?>
		<form name="noticeform" action="" method="post" onsubmit="return validatenotice();">
		<font style="font-size:18px;"><b> Comment here...</b></font>
		<input name="noticeid" id="noticeid" type="hidden" value=<?php echo base64_decode(makeSafe($_GET['nid'])); ?> >
			<textarea rows="10" cols="30" name="comment" id="comment" style="width:99%;"><?php echo $comment;?></textarea><br>
			<input type="submit" value="submit" name="submit" id="submit" style="font-size:20px; margin-top: 8px; width:100px; height:40px;"/><?php echo @$msg;?>
			</form>
		<?php 
			
	}
		
		

	
?>

<script>
function validatenotice()
{
	var comment=document.noticeform.comment.value;

	if(comment=="")
	{
		alert("Enter some feedback comment");
		document.noticeform.comment.focus();
		return false;
	}
}
</script>	
<style>
.success{text-align:left;height:20px;color:green;font-size:20px; }
.error{text-align:left;height:20px;color:red;font-size:20px;}	
</style>		
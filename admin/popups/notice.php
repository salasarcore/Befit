<?php 
@session_start();
include("../conn.php");
include("../functions/employee/dropdown.php");
include("../functions/dropdown.php");
include("../functions/common.php");
include('../check_session.php');
include("../../classes/class.sample_image.php");
$ua=getBrowser();

?>

<link href="../css/classic.css" rel="stylesheet" type="text/css">
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">

<title>BRANCHES</title>
<link rel="shortcut icon" href="../saplimg/favicon.ico">
<script type="text/javascript"   src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../js/ajax.js"></script>
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />

<script type="text/javascript">
function FCKeditor_OnComplete( editorInstance )
{
	var oCombo = document.getElementById( 'cmbSkins' ) ;

	// Get the active skin.
	var sSkin = editorInstance.Config['SkinPath'] ;
	sSkin = sSkin.match( /[^\/]+(?=\/$)/g ) ;

	oCombo.value = sSkin ;
	oCombo.style.visibility = '' ;
}

function ChangeSkin( skinName )
{
	window.location.href = window.location.pathname + "?Skin=" + skinName ;
}
function validatenotice(frm)
 {
    if(frm.subj.value.length==0)
   { 
     alert("Blank Subject.....");
     frm.subj.focus();
     return false;
   }
    else if(frm.file.value!="")
    {
    	return validateattachfile(frm.file.value);
    }

   return true;
 }

function validateattachfile(filename)
{
	var validationStatus = true;
	var parts = filename.split('.');
	
	var exts = ['rtf', 'doc', 'txt', 'html','docx','jpg', 'jpeg', 'png', 'gif', 'pdf', 'xls', 'xlsx','JPG','PNG','JPEG','GIF'];
     if(exts.indexOf(parts[parts.length-1]) == -1) 
       {
    	 alert('Only RTF, DOC, DOCX, HTML, JPG, JPEG, PNG, GIF, PDF, XLS, XLSX and TEXT files are allowed');
         validationStatus = false;
        }

     return validationStatus;

}
 $(document).ready(function(){
   $("#cancelimage").hide();
 $("#cancelimage").click(function(){
  $("#file").val("");
  $("#cancelimage").hide();
 });
 $("#file").change(function(){
  if($("#file").val()!="")
  $("#cancelimage").show();
  else
  $("#cancelimage").hide();
  });
  });
</script>
	
</head>

<body leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0" style="text-align: left">


<div id="middleWrap">
		<div class="head"><h2>NOTICE</h2></div>

<span id="spErr"><?php echo @$msg;?></span>

<?php
@session_start();
include("../../fckeditor/fckeditor.php") ;
$Errs="";$msg="";
if(@$_GET['act']=="send")
{
	@$ntype=trim(makeSafe($_POST['ntype']));
	@$subj=	makeSafe($_POST['subj']);
	$repchar=array("'");
	@$cont=str_replace($repchar,"",trim(makeSafe($_POST['solution'])));
	@$today=$dt=date("Y-m-d H:i:s");
	$extn=explode('.',$_FILES["file"]["name"]); 
	$mime=$extn[0];
	
	//echo "mime:".$mime."<br/>";
	
	 if ($_FILES["file"]["error"] > 0)// no logo upload
		$mime="";
	else
	{
	   $extn=explode('.',$_FILES["file"]["name"]);
	   $mime=$extn[1];
	   }
	   $allowed = array('application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain','application/vnd.ms-excel','application/pdf','text/html','image/gif','image/jpeg','image/jpg','image/pjpeg','image/x-png','image/png');
	   
if($subj=="")
	echo "<div class='error'>Please Enter Subject</div>";
elseif($_FILES["file"]["name"]!="" && !in_array($_FILES['file']['type'], $allowed))
{
	echo "<script>alert('Only RTF, DOC, DOCX, HTML, JPG, JPEG, PNG, GIF, PDF, XLS, XLSX and TEXT files are allowed');";
	echo "</script>";

}
else
{	  $nid=getNextMaxId("notice_board","nid")+1;
	$query = "insert into notice_board(nid,br_id,ntype ,ndate,subject ,nbody,mime,updated_by) values('".$nid."',".$_SESSION['br_id'].",'".$ntype."','".$today."','".$subj."','".$cont."','".$mime."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";

	$result = mysql_query($query) or die('Query failed: ');
			if (!($_FILES["file"]["error"] > 0))
			{
				$extn=explode('.',$_FILES["file"]["name"]);
				$upath="../../site_img/notice/".DOMAIN_IDENTIFIER."_".base64_encode($nid).".".$extn[1];
	            move_uploaded_file($_FILES["file"]["tmp_name"],$upath);
				// watermark function
	           
				if ($extn[1]=='JPG' ||  $extn[1]=='jpg' || $extn[1]=='jpeg' || $extn[1]=='JPEG' ) {
				$f = new SimpleImage;  $f->watermark($upath,$extn[1]);
             }
	            
			}
			$msg ="<DIV class='success'>Record Saved Successfully</DIV>";
}//send
}

?>

<form method="POST"  action="notice.php?act=send" enctype="multipart/form-data" onsubmit="return validatenotice(this);">
<table border="0" width="90%" id="table1" class="adminform" align="center">
			<tr>
				<td align="right">Notice type/Reference Category :</td>
				<td><input type="text" name="ntype" size="67" tabindex="0"></td>
			</tr>
			<td align="right">Date :</td>
			<td>
		<?php
			$dt=date("jS-M-Y, g:iA");
			echo $dt;
		?>
      		</td>
			</tr>
			
			<tr>
				<td align="right" class='redstar'>Subject :</td>
				<td>
				<input type="text" name="subj" size="55" tabindex="1"></td>
			</tr>
			<tr>
				<td align="right">Attachment :</td>
				<td>
				<input type="file" name="file" id="file" />
				<img name="image" id ="cancelimage"  alt="cancel" src="../../css/classic/close.png" width="15px" height="15px" title="De-select the file" style="cursor: pointer; vertical-align: middle;">
    <br/>
				
				(.jpg,.jpeg,.png,.gif,.txt,.html,.doc,.docx files only)</br></td>
			</tr>
		<tr>
		<td  align="right" style="vertical-align:top;">Description :</td>
		
			<td <?php  if ( @$ua['name']=='Google Chrome') echo "style='border-bottom: 1px solid black;  border-top: 1px solid black;'";?> style=" border-top: 1px solid black;'">	
<?php
    $sBasePath="../../fckeditor/";
	$oFCKeditor = new FCKeditor('solution') ;
	$oFCKeditor->BasePath = $sBasePath ;
	$oFCKeditor->ToolbarSet = "Default" ;
	$oFCKeditor->Width = "530" ;
	$oFCKeditor->Height = "300" ;
	///if ( isset($_GET['Skin']) )
	///	$oFCKeditor->Config['SkinPath'] = $sBasePath . 'editor/skins/' . htmlspecialchars($_GET['Skin']) . '/' ;

	$oFCKeditor->Value =  @$solution;
	$oFCKeditor->Create() ;
?>
			</td>
			</tr>
			<tr>
		<td colspan="2" align="center"> 
<input type="submit" class="btn save" value="SAVE">  <input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close()">

</td>
			</tr>
</table>

</form>
<script language=javascript>

document.getElementById("spErr").innerHTML= "<?php echo $msg; ?>";

   </script>

</div>

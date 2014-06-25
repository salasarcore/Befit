<?php 
@session_start();
include("../../globalConfig.php");
include("../../functions/employee/dropdown.php");
include("../../functions/dropdown.php");
include("../../functions/common.php");
include('../check_session.php');
include("../../fckeditor/fckeditor.php") ;
$ua=getBrowser();
@session_start();
$Errs="";

?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../modules/js_css_common.php');
include '../modulemaster.php';

$id=option_news_list_add;
$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}
if(@$_GET['act']=="send")
{
	@$subj=	makeSafe($_POST['subj']);
	$repchar=array("'");
	@$cont=str_replace(@$repchar,"",trim(makeSafe($_POST['solution'])));
	@$today=date("jS-M-Y, g:i A");
		   
	 if(trim($subj)=="")
	 	$Errs= "<div class='error'>Please Enter Subject</div>";
	 else
		{ 
			$nid=getNextMaxId("news","nid")+1;
			$query = "insert into news(nid,br_id,ndate,subject,nbody,updated_by,date_updated) values('".$nid."',".$_SESSION['br_id'].",now(),'".$subj."','".$cont."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."', now())";
			$result = mysql_query($query) or die('Query failed: ');
			$Errs ="<DIV class='success'>Record Saved Successfully</DIV>";
		}

}//send

?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>NEWS/PRESS/MEDIA</title>
<link rel="shortcut icon" href="../saplimg/favicon.ico">
<script type="text/javascript"   src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/Ujquery-ui.min.js"></script>
<script type="text/javascript" src="../../js/ajax.js"></script>
<link rel="Stylesheet" type="text/css" href="../../css/jquery-ui.css" />


<script type="text/javascript">
/**
*This section gives an JavaScript error during runtime and it is not required, that's why commented
*/
/*function FCKeditor_OnComplete( editorInstance )
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
}*/
function fn(frm)
 {
    if(frm.subj.value.trim()=="")
   { 
     alert("Enter Subject");
     frm.subj.value="";
     frm.subj.focus();
     return false;
   }

   return true;
 }
</script>
	
</head>
<body leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0" style="text-align: left;">

<div id="middleWrap">
		<div class="head"><h2>NEWS/PRESS/MEDIA</h2></div>

<span id="spErr"><?php echo $Errs;?></span>

<form method="POST"  action="news.php?act=send" enctype="multipart/form-data" onsubmit="return fn(this);">
<table border="0" width="90%" id="table1" class="adminform" align="center" >
			<tr>
				<td align="right" class="redstar" width="20%">Subject :</td>
				<td><input type="text" name="subj" id="subj" size="40" tabindex="1"></td>
			</tr>
			<tr>
				<td align="right">Date :</td>
				<td><?php echo date("jS-M-Y, g:i A"); ?></td>
			</tr>
		<tr>
		<td  align="right" style="vertical-align:top;">Description :</td>
		<td  <?php if ( @$ua['name']=='Google Chrome') echo "style='border-bottom: 1px solid black;  border-top: 1px solid black;'";?> style=" border-top: 1px solid black;'">
				
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
		<input type="submit" class="btn save" value="Save">  <input type=button class="btn close" value="CLOSE" onClick="parent.emailwindow.close()">
		</td>
		</tr>
</table>
</form>
<script language=javascript>
document.getElementById("spErr").innerHTML= "<?php echo $Errs; ?>";
</script>
   
</div>

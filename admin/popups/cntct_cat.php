<?php 
@session_start();

include("../conn.php");
include('../check_session.php');
include("../functions/common.php");
?>
<link href="../css/classic.css" rel="stylesheet" type="text/css">
<?php
include('../php/js_css_common.php');
$act=makeSafe(@$_GET['act']);
/*include("../modulemaster.php");
if($act=="add")
	$id=option_contact_category_add;
elseif($act=="edit")
$id=option_contact_category_edit;
elseif($act=="delete")
$id=option_contact_category_delete;

$id_admin=$_SESSION['empid'];

$level=$_SESSION['access_level'];
if(($level!='Super Admin') && ($level!='Admin')){
	if(!isAccessModule($id_admin,$id)){
		echo "<div class='error' style='text-align:center;'>You are not authorised to view this page</div>";
		exit;
	}
}*/
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">

<title>CONTACT CATEGORY</title>
<link rel="shortcut icon" href="../saplimg/favicon.ico">



<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery-1.8.24.ui.min.js"></script> 

</head>
<body leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0" style="text-align: left">
<?php
$Errs="";

$catid=makeSafe(@$_GET['catid']);
$action=makeSafe(@$_POST['action']);
$catname=makeSafe(@$_POST['cattype']);

if(@$action=="SAVE" || @$action=="UPDATE")
{
	if($catname=="")
	{
		$Errs="<div class='error'>Invalid Category Name</div>";
	}
	else
	{
		if(@$action=="SAVE")
		{
			$query="Select * from school_contact_category where school_contact_cat_name='".$catname."'";
			$res=mysql_query($query);
			if(mysql_affected_rows($link)>0)
			{
				$Errs="<div class='error'>Duplicate Category name</div>";
			}
			else
			{
			$newcontactid=getNextMaxId("school_contact_category","school_contact_cat_id")+1;
				$sql="insert into school_contact_category(school_contact_cat_id,school_contact_cat_name,date_updated,updated_by) values (".$newcontactid.",'".$catname."','".date('Y-m-d H:i:s')."','".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."')";
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)>0)
					$Errs="<div class='success'>Record Saved Successfully</div>";
			}

		}
		else if(@$action=="UPDATE")
		{
			$query="Select * from school_contact_category where school_contact_cat_name='".$catname."' and school_contact_cat_id!='".$catid."'";
			$res=mysql_query($query);
			if(mysql_affected_rows($link)>0)
			{
				$Errs="<div class='error'>Duplicate Category name</div>";
			}
			else
			{
				$sql="update school_contact_category set school_contact_cat_name='".$catname."',updated_by='".$_SESSION['emp_name'].'['.$_SESSION['emp_id'].']'."' where school_contact_cat_id=".$catid;
				$res=mysql_query($sql,$link);
				if(mysql_affected_rows($link)==0)
					$Errs="<div class='success'>No Data Changed</div>";
				if(mysql_affected_rows($link)>0)
					$Errs="<div class='success'>Record Updated successfully</div>";
				if(mysql_affected_rows($link)<0)
					$Errs="<div class='error'>Record Not Updated successfully</div>";
			}
		}
	  }
}


if(@$action=="DELETE")
{
			   $query = "delete from school_contact_category  where school_contact_cat_id =".$catid;
				$result  = mysql_query($query);
				if(mysql_affected_rows($link)>0)
					{
					 	$Errs="<div class='success'>Category Deleted Successfully</div>";		
				 	}
				else
				{
			      		$Errs= "<div class='error'>Category Can't be Deleted </div>";
				}	
}

if(@$act=="edit" || @$act=="delete" )
{
		$catid=makeSafe(@$_GET['catid']);
		$sql="select * from school_contact_category where school_contact_cat_id=".$catid;
		$result  = mysql_query($sql);
		if(mysql_affected_rows($link)>0)
			{
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
			 	$contact_cat_name=@$row['school_contact_cat_name'];
			 	$updatedby=@$row['updated_by'];
			 	$updateddatetime=@$row['date_updated'];
			 	
			 	$temp1= new DateTime($updateddatetime);
				$time=$temp1->format('jS-M-Y, g:i A');
			}
	
}
?>
<div id="middleWrap">
		<div class="head"><h2>CONTACT CATEGORIES</h2></div>

<SCRIPT>
function validatecategory()
{
	var cattype=document.frmManu.cattype.value;
	
if(cattype.trim()=="")
{
	alert('Category name should not be blank');
	document.frmManu.cattype.value="";
	document.frmManu.cattype.focus();
	return false;
}
return true;
}

function ClearField(frm){
	  frm.cattype.value = "";
	 
	}



</SCRIPT>
 <span id="spErr"><?php echo $Errs;?></span>
<form action="cntct_cat.php?act=<?php echo makeSafe($act); ?>&catid=<?php echo makeSafe($catid);?>" method="post" name="frmManu" id="frmManu" onsubmit="return validatecategory();">
<table border="0" cellspacing="0" cellpadding="0" align="center" class="adminform">

	<tr>
	
	<td class="redstar">Category Name : </td><td><input type="text" name="cattype" id="cattype" maxlength="100" value="<?php if($act=="add") ""; elseif($act=="edit" || $act=="delete")  echo @$contact_cat_name;?>"  <?php if(@$act=="delete") echo "disabled"; ?> ></td>
	
	</tr>

	<tr>
					<td align="right">last Updated :</td>
					<td><?php echo @$time; ?></td>
				</tr>
				<tr>
					<td align="right">Updated By :</td>
					<td><?php echo @$updatedby; ?></td>
				</tr>
				
	<tr>
    <td colspan="2" style="padding-top:10px" align="center">
    		<input type="submit" class="btn save" value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "CONFIRM DELETE";
				 ?>' name="B1" />
			<?php if(@$act!="delete"){?>
				<input type="button" class="btn reset" value="RESET" id= "reset "name="reset" onClick="ClearField(this.form)" />
					<?php }	?>
			<input type=button  class="btn close" value="CLOSE" id="close" name="close" onClick="parent.emailwindow.close()" />
          	<input type='hidden' name='action' value='<?php 
	                if(@$act=="add") echo "SAVE";
	                if(@$act=="edit") echo "UPDATE";
	                if(@$act=="delete") echo "DELETE";
				 ?>' />
			<input type='hidden' name='catid' value='<?php echo @$catid; ?>'>
	</td>
</table>
</form>
</div>
<script language="javascript">
document.getElementById("spErr").innerHTML= "<?php echo $Errs;?>";
   </script>
</body>
</html>
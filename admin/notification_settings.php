<?php
 include_once("functions/common.php");
 /**
  * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
  
//include("modulemaster.php");
 if(in_array(module_notification_settings,$modules)){
    $id_admin=$_SESSION['empid'];
    $level=$_SESSION['access_level'];
     if(($level!='Super Admin') && ($level!='Admin'))
  {
         if(!isAccessModule($id_admin, module_notification_settings))
       {
               redirect("pages.php","You are not authorised to view this page");
        exit;
            }
   }
  }
  else{
   echo "<script>location.href='pages.php';</script>";
    exit;
  }
  */
$msg1="";
$msg2="";
  if (makeSafe ( isset ( $_POST ['SaveSMS'] ) )) {
  
  	$modulequery = "SELECT mm.* FROM notification_module_master mm INNER JOIN global_sms_templates t ON mm.module_id=t.module_id WHERE t.template_type='T' and t.approved='Y' and t.available_for_school='Y'";
  	$query2 = mysql_query($modulequery) or die("Query failed.");
  	$allmodules=array();
  	while($row=mysql_fetch_assoc($query2)){
  		$allmodules[]=$row['module_id'];}
   		$type="";
  		$modules=array();
  		if(isset($_POST['rdoIDS']))  $modules=$_POST['rdoIDS'];
  		 
  		foreach ($allmodules as $module){
  		
  			if(in_array($module, $modules))
  				$type='A';
  			else
  				$type='M';
  			$query="select * from notification_setting where module_id=".$module." and notification_type='S'";
  			$res=mysql_query($query);
  			$numrows=mysql_num_rows($res);
  			if($numrows>0)
  			{
  				$sql="update notification_setting set sending_type='".$type."' where notification_type='S' and module_id=".$module;
  				$result=mysql_query($sql);
  			}
  			else
  			{
  				$sql="insert into notification_setting (module_id,notification_type,sending_type) values(".$module.",'S','".$type."')";
  				$result=mysql_query($sql);
  			}
  		
  		}
  		if (mysql_affected_rows($link)>0){
  			$msg1= "<div class='success'>Details saved successfully</div>";
  		}
  		else if (mysql_affected_rows($link)==0){
  			$msg1= "<div class='success'>No data change</div>";
  		}
  		else {
  			$msg1 = "<div class='error'>Details not saved successfully</div>";
  		}
  
  }
  if (makeSafe ( isset ( $_POST ['SaveEmail'] ) )) {
  
  	$modulequery = "SELECT mm.* FROM notification_module_master mm INNER JOIN global_email_template t ON mm.module_id=t.email_module_id WHERE t.available_for_school='Y'";
  	$query2 = mysql_query($modulequery) or die("Query failed.");
  	$allmodules=array();
  	while($row=mysql_fetch_assoc($query2)){
  		$allmodules[]=$row['module_id'];}
  		$type="";
  		$modules=array();
  		  		
  		if(isset($_POST['rdoIDE'])) 
  			$modules=$_POST['rdoIDE'];
  		
  		foreach ($allmodules as $module){
  
  			if(in_array($module, $modules))
  				$type='A';
  			else
  				$type='M';
  			$query="select * from notification_setting where module_id=".$module." and notification_type='E'";
  			$res=mysql_query($query);
  			$numrows=mysql_num_rows($res);
  			if($numrows>0)
  			{
  				$sql="update notification_setting set sending_type='".$type."' where notification_type='E' and module_id=".$module;
  				$result=mysql_query($sql);
  			}
  			else
  			{
  				$sql="insert into notification_setting (module_id,notification_type,sending_type) values(".$module.",'E','".$type."')";
  				$result=mysql_query($sql);
  			}
  		
			
  			}
  			if (mysql_affected_rows($link)>0){
  				$msg2= "<div class='success'>Details saved successfully</div>";
  			}
  			else if (mysql_affected_rows($link)==0){
  				$msg2= "<div class='success'>No data change</div>";
  			}
  			else {
  				$msg2 = "<div class='error'>Details not saved successfully</div>";
  			}
  		}
  
  
  ?>
   <style>
   a:link 
   {
  text-decoration:none;
   }
   a:hover
    {
   text-decoration:underline;
   }
  .a:visited {color:#0000FF;} 
   .a{font-size:14px;font-family:sans-serif;margin-left:8px;text-decoration:none;}
   .app
    {
    margin-left:10px;
    color:#B80000;
    }
    .main_table:hover
   {
   background-color:#E5FFE5;
   
    }
    .main_table
    {
    margin-left: -20px;
    }
     
 .admintable 
 {
  border :1px solid #CCCCCC;
  }
 .admintable td {
    border :1px solid #CCCCCC;
    
    color: #000000;
    padding: 6px;}
    
 .admintable th {
      border: 1px solid #CCCCCC;
    
    color: #323232;
    padding: 10px;
    text-align: center;
       background-color: #DCE9F9;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.8) inset;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);}
    }
   </style>
    <script>
     $(function() {
      $( "#tabs" ).tabs();
   });
  
     function selectallid(val)
     {
     	var elms=document.getElementsByName("rdoID"+val+"[]");

     	if($('input[name=selectall'+val+']').is(':checked')) {
     		for (i=0;i<elms.length;i++){
     			if (elms[i].type="checkbox" ){
     				elms[i].checked = true;
     				}
     			}
     	}
     	else{
     		for (i=0;i<elms.length;i++){
     			if (elms[i].type="checkbox" ){
     				elms[i].checked = false;
     				
     				}
     			}
     	}
     }
   
     function getSelected(val)
     {
     	var elms=document.getElementsByName("rdoID"+val+"]");
     	for (i=0;i<elms.length;i++){
     		if (elms[i].type="checkbox" ){
     			if(!$('#'+elms[i].value).is(':checked')) {
     				$('#selectall'+val).attr('checked',false);
     			}
     		}
     }
     }
     
</script>
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
 <div class="page_head">
 <div id="navigation"><a href="pages.php"> Home</a><a> Utilities</a> <a>Email/SMS Notification</a><span style="color: #000000;">Notification Settings </span></div>
 </div>
 <div id="details" style="width: 1040px; height: auto; margin: auto;" >
 <div id="tabs">
  <ul>
 <li><a id='tb1' href="#tabs-1" style='font-family:arial;color:#0E864E;'>SMS</a></li>
 <li><a id='tb2' href="#tabs-2" style='font-family:arial;color:#0E864E;'>Email </a></li>
   <!--<li><a id='tb3' href="#tabs-3" style='font-family:arial;color:#0E864E;'>SMS History</a></li>  -->
  </ul>
   <div id="tabs-1">
  <div style='border:1px solid black;border-radius:7px;margin-bottom:5px;margin-right:5px;height:30px;width:100%;display: inline-block;text-align: center;padding-top: 4px;padding-bottom: 4px;'>
  <b>SMS Settings</b></div>
   <form action="" method="post">
   <span id="spErr"><?php echo @$msg1;?> </span>
   <table  cellspacing="1" width="100%" class="admintable">
   <thead >
   <th>Module Name</th>
   <th >Auto Sending</th>
   </thead>
   <tbody>
   
   <tr><td align="center" ><b style="color: red;">Select All</b></td><td align="center"><input type='checkbox'  id='selectall' name='selectallS' onclick="javascript:selectallid('S');"></td></tr>
    <?php  
    $modulequery = "SELECT mm.* FROM notification_module_master mm INNER JOIN global_sms_templates t ON mm.module_id=t.module_id WHERE t.template_type='T' and t.approved='Y' and t.available_for_school='Y'";
    $query2 = mysql_query($modulequery) or die("Query failed.");
    $checkedmodules=array();
    $selectquery="select * from notification_setting where notification_type='S' and sending_type='A' ";
    $query1 = mysql_query($selectquery) or die("Query failed.");
    while($row1 = mysql_fetch_assoc($query1))
    {

    	$checkedmodules[]=$row1['module_id'];
    }

while($row3 = mysql_fetch_assoc($query2))
{
	?>
<tr><td style="text-align: center;"><?php echo @$row3['module_name']; ?></td>
<td align="center"><input type="checkbox" name="rdoIDS[]"  value='<?php echo @$row3['module_id'];?>' onclick="getSelected('S');" <?php if(in_array($row3['module_id'],$checkedmodules)) echo "checked='checked'";?>></td></tr>
	<?php
}?>
</tbody>
</table>
<div align="center" style="padding-top: 10px;">
	<input type="submit" align="center" name="SaveSMS" class="btn save" value="SAVE" id="SaveSMS" />

	</div>
</form>
  </div>
   <div id="tabs-2">
  <div style='border:1px solid black;border-radius:7px;margin-bottom:5px;margin-right:5px;height:30px;width:100%;display: inline-block;text-align: center;padding-top: 4px;padding-bottom: 4px;'>
  <b>Email Settings</b></div>
   	<form action="" method="post">
   	 <span id="spErr"><?php echo @$msg2;?> </span>
   <table  cellspacing="1" width="100%" class="admintable">
    <tr>
   <th>Module Name</th>
   <th>Auto Sending</th>
   </tr>
     <tr><td align="center"><b style="color: red;">Select All</b></td><td align="center"><input type='checkbox'  id='selectall' name='selectallE' onclick="javascript:selectallid('E');" ></td></tr>
    <?php   $modulequery = "SELECT mm.* FROM notification_module_master mm INNER JOIN global_email_template t ON mm.module_id=t.email_module_id WHERE t.available_for_school='Y'";

$query2 = mysql_query($modulequery) or die("Query failed.");
$checkedmodules=array();
$selectquery="select * from notification_setting where notification_type='E' and sending_type='A' ";
$query1 = mysql_query($selectquery) or die("Query failed.");
while($row1 = mysql_fetch_assoc($query1))
{

	$checkedmodules[]=$row1['module_id'];
}
while($row3 = mysql_fetch_assoc($query2))
{
	?>
<tr><td style="text-align: center;"><?php echo @$row3['module_name']; ?></td>
<td align="center"><input type="checkbox" name="rdoIDE[]"  value='<?php echo @$row3['module_id'];?>' onclick="getSelected('E');" <?php if(in_array($row3['module_id'],$checkedmodules)) echo "checked='checked'";?> ></td></tr>
	<?php
}?>
</table>
<div align="center" style="padding-top: 10px;">
	<input type="submit" align="center" name="SaveEmail" class="btn save" value="SAVE" id="SaveEmail" />

	</div>
	</form>
   </div>
    <!--  <div id="tabs-3">
   <?php include("sms/sms_purchase_history.php");?>
    
    </div>-->
   </div> </div>
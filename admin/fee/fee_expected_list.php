<?php
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_fee_expected_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin' )
	{
		if(!isAccessModule($id_admin, module_fee_expected_list))
		{
			redirect("pages.php","You are not authorised to view this page");
			exit;
		}
	}
}
else{
	echo "<script>location.href='pages.php';</script>";
	exit;
}*/
?>
<script>
function selectID(objChk)
{
document.getElementById("expectedID").value=objChk;
}

function SubmitPage(Page)
{
document.getElementById("txtPage").value=Page;
document.fees.submit();
}
function validatefeeexp(frm)
{
	  if(frm.txtFilter.value.trim()=="" && frm.adtstart.value.trim()=="" && frm.adtend.value.trim()=="")
		{
			alert('Enter Atleast one criteria for filter');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
	  if(frm.txtFilter.value.trim()=="" && frm.adtstart.value.trim()=="" && frm.adtend.value.trim()!="")
		{
			alert('Enter start date for filter');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
	  if(frm.txtFilter.value.trim()=="" && frm.adtstart.value.trim()!="" && frm.adtend.value.trim()=="")
		{
			alert('Enter end date for filter');
			frm.txtFilter.value="";
			frm.txtFilter.focus();
			return false;
		}
	
	  if (Date.parse($.trim($('#adtend').val())) < Date.parse($.trim($('#adtstart').val())))
	 	{
		    alert('Last date should be greater than Start date  ');
		    return false;
	 	}   

}
function ActionScript(act)
{

	if(document.getElementById("expectedID").value=="" && act !="add")
	  	alert("Please select a Fee Expected");
	else if(act =="edit"){
		url="fee/popups/edit_fee_expected.php?act="+act+"&expectedID="+document.getElementById("expectedID").value;
		open_modal(url,600,400,"EDIT FEE EXPECTED")
		return true;
	}
	else if(act =="editsection"){
		url="fee/popups/edit_fee_expected_sections.php?act="+act+"&expectedID="+document.getElementById("expectedID").value;
		open_modal(url,700,560,"EDIT FEE EXPECTED")
		return true;
	}
	else
	{
		url="fee/popups/fee_expected.php?act="+act+"&expectedID="+document.getElementById("expectedID").value;
		open_modal(url,700,550,"FEE EXPECTED")
		return true;
	}
	
}
</script>
<div class="page_head">
<div id="navigation">
<a href="pages.php"> Home </a><a> Fee</a><span style="color: #000000;"> Fee Expected List</span>
</div>

<form name="frm" action="pages.php?src=fee/fee_expected_list.php" method="POST" onsubmit="return validatefeeexp(this);" autocomplete="off">
	<table width="100%" class="adminform1">
		<tr>
			<td>
			<h2>Fees Expected List</h2>
			</td>
				<td>
				<div id="option_menu">
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('edit');">Edit</a>
					<a class="btn btn-info" href="javascript:void(0);" onClick="javascript:ActionScript('editsection');">Edit Section Details</a>
					<a class="btn btn-danger" href="javascript:void(0);" onClick="javascript:ActionScript('delete');">Delete</a>
				</div>
			</td>
		</tr>
	</table>
	</div>
	<div class="search_bar">
	<?php  @$name_filter= makeSafe($_POST['txtFilter']); @$startdate=makesafe($_POST['adtstart']); @$enddate=makesafe($_POST['adtend']);?>
			Fee Expected Name :<input type="text" name="txtFilter" id="txtFilter" value="<?php echo @$name_filter;?>" maxlength="200" />
			Start Date :<input name="adtstart" type="text" class="date"	id="adtstart" size="11" style="max-width: 8em;" value="<?php echo @$startdate;?>" readonly />
     			   <script type="text/javascript">
					  $(function() {
						$( "#adtstart" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});
					});
				  </script>
				  To End Date:	<input name="adtend" type="text" class="date"	 style="max-width: 8em;" id="adtend" size="11" value="<?php echo @$enddate;?>" readonly />
     			   <script type="text/javascript">
					  $(function() {
						$( "#adtend" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							minDate: new Date()
						});
					});

				  </script> 
			<input type="submit" name="btnGo" value="Go" class="btn btn-info"/>
	</div>
	<br />
	<table width="100%" border="0" class="table table-bordered" style="cursor: pointer;">
		<thead>
			<tr>
				<th width="2%">#</th>
				<th width="10%">FEE EXPECTED NAME</th>
				<th width="10%">FEE PARTICULAR NAME</th>
				<th width="8%">START DATE</th>
				<th width="8%">LAST DATE</th>
				<th width="8%">DUE DATE</th>
				<th width="5%">DEPARTMENT - SECTION</th>
				<th width="10%">CREATED AT</th>
				<th width="10%">UPDATED AT</th>
				<th width="10%">UPDATED BY <br>Employee Name[Emp_Code]</th>
			</tr>
		</thead>
		<tbody>
			<?php

// by default we show first page
		$pageNum = 1;
	$rowsPerPage=20;

	if(makeSafe(isset($_POST['txtPage'])))	    $pageNum = makeSafe($_POST['txtPage']);
// counting the offset
		$offset = ($pageNum - 1) * $rowsPerPage;
		if($offset<0) $offset=0;
			

			$sql="SELECT fe.*, fp.name as particular_name from fee_expected as fe, fee_particulars as fp where fe.fee_particulars_id=fp.fee_particulars_id and fp.fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")";
			if(@$name_filter!="")
			$sql=$sql." and fe.name LIKE '%".$name_filter."%'";
			if(@$startdate!="" && @$enddate!="")
			$sql=$sql." and (fe.start_date BETWEEN '".@$startdate."' and '" .@$enddate."') and (fe.last_date BETWEEN '".@$startdate."' and '" .@$enddate."')";
			$sql=$sql." order by fe.updated_at desc LIMIT $offset, $rowsPerPage";
			
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$i=0;
		

			while($row=mysql_fetch_array($res))
			{
				$secstr="";
				$secids=explode(",",$row['section_id']);
				foreach($secids as $sec)
				{
					$getdet=getDetailsById("session_section","session_id",$sec);
					$getdeptdet=getDetailsById("mst_departments","department_id",$getdet['department_id']);
					$secstr.=$getdeptdet['department_name']." - ".$getdet['section'].", ";
				}
				$secstr=rtrim($secstr,", ");
				$i=$i+1;
				?>
			<tr class=<?php if($i%2==0) echo "row0"; else echo "row1"; ?> onclick="selectID('<?php echo base64_encode($row['fee_expected_id']);?>')">
				<td align="center"><input type="radio" name="rdoID" value="<?php echo $row['fee_expected_id']; ?>" id="rdoID" /></td>
				<td align="center"><?php echo $row['name'];?></td>
				<td align="center"><?php echo $row['particular_name'];?></td>
				<td align="center"><?php echo date("jS-M-Y",strtotime($row['start_date']));?></td>
				<td align="center"><?php echo date("jS-M-Y",strtotime($row['last_date']));?></td>
				<td align="center"><?php echo date("jS-M-Y",strtotime($row['due_date']));?></td>
				<td align="center"><?php echo $secstr;?></td>
				<td align="center"><?php echo date("jS-M-Y, g:iA",strtotime($row['created_at']));?></td>
				<td align="center"><?php echo date("jS-M-Y, g:iA",strtotime($row['updated_at']));?></td>
				<td align="center"><?php echo $row['updated_by'];?></td>
			</tr>
			<?php
	  }
	  ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="12">
			 
	 <?php
				$sql="SELECT  COUNT(fee_expected_id) as numrows from fee_expected as fe, fee_particulars as fp where fe.fee_particulars_id=fp.fee_particulars_id and fp.fee_categories_id in (select fee_categories_id from fee_categories where br_id=".$_SESSION['br_id'].")";
				if(@$name_filter!="")
				$sql=$sql." and fe.name LIKE '%".$name_filter."%'";
				if(@$startdate!="" && @$enddate!="")
				$sql=$sql." and (fe.start_date BETWEEN '".@$startdate."' and '" .@$enddate."') and (fe.last_date BETWEEN '".@$startdate."' and '" .@$enddate."')";
				$sql=$sql." order by fe.name LIMIT $offset, $rowsPerPage";
				
				$result  = mysql_query($sql) or die('Error, query failed');
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				$numrows = $row['numrows'];
				if($numrows>0){

					// how many pages we have when using paging?
					$maxPage = ceil($numrows/$rowsPerPage);
					echo "TOTAL RECORDS :".$numrows." | PAGE(S) : ".$maxPage;

					// print the link to access each page

					$nav  = '';
					//for($page = 1; $page <= $maxPage; $page++)
					for($page =$pageNum-4;  $page <= $pageNum+4 ; $page++)
					{
						if($page>0)
							if ($page == $pageNum)
							{
								$nav .= " $page "; // no need to create a link to current page
							}
							else
							{
									
								$nav .= " <a class='mylink' href=\"javascript:SubmitPage('$page')\">$page</a> ";
							}
							if($page>=$maxPage) break;
					}

					// creating previous and next link
					// plus the link to go straight to
					// the first and last page

					if ($pageNum > 1)
					{
						$page  = $pageNum - 1;
						$prev  = " <a class='mylink' href=\"javascript:SubmitPage('$page')\" >[Prev]</a> ";

						$first = " <a class='mylink' href=\"javascript:SubmitPage('1')\" >[First Page]</a> ";
					}
					else
					{
						$prev  = '&nbsp;'; // we're on page one, don't print previous link
						$first = '&nbsp;'; // nor the first page link
					}

					if ($pageNum < $maxPage)
					{
						$page = $pageNum + 1;
						$next = " <a class='mylink' href=\"javascript:SubmitPage('$page')\" >[Next]</a> ";

						$last = " <a class='mylink' href=\"javascript:SubmitPage('$maxPage')\" >[Last Page]</a> ";
					}
					else
					{
						$next = '&nbsp;'; // we're on the last page, don't print next link
						$last = '&nbsp;'; // nor the last page link
					}

					echo $first . $prev . $nav . $next . $last;
					echo "";
				}
				else
					echo "NO RECORDS FOUND";
				?>
				
				</th>
			</tr>
		</tfoot>
	</table>
	<div style="clear: both"></div>
	<input type="hidden" name="expectedID" id="expectedID" value="" />
	<input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>' />
</form>
<script>
document.frm.txtFilter.focus();
</script>

<?php
include_once("functions/common.php");

/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 
//include("modulemaster.php");
if(in_array(module_sms_transaction_log,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_sms_transaction_log))
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
?>
<script>

function SubmitPage(page)
{
document.getElementById("txtPage").value=page;
document.records.submit();
}

$(document).ready(function(){
	$('#mobile_num').keyup(function(){
		var value = $('#this').val();
		if(this.value.match(/[^0-9-]/g))
		{
			this.value = this.value.replace(/[^0-9-]/g,'');
		}
	 });
});
function check()
{
	if(($.trim(($('#mobile_num').val()))=="") && ($('#date').val()==""))
	{
		alert('Please select atleast one search parameter.');
		return false;
	}
	else
		return true;
}
</script>
<?php
makeSafe(extract(@$_REQUEST));
?>
<div class="page_head">
<div id="navigation">
	<a href="pages.php">Home</a><a> SMS</a>  <span style="color: #000000;">SMS Log</span>
</div>
<h2>SMS Log</h2>
</div>

<form name="records" action="pages.php?src=sms/sms_log.php"	method="POST">
<div class="search_bar">
	<table width="100%" class="adminform1">
		<tr>
			<td>
			Mobile Number : <input type="text" name="mobile_num" maxlength="10" id="mobile_num" value="<?php echo @$mobile_num;?>" maxlength="200" />
				Sent Date : <input name="date" type="text" class="date" id="date" size="11" value="<?php echo @$date;?>"  />
				<script type="text/javascript">
					  $(function() {
						$( "#date" ).datepicker({
							numberOfMonths: [1,2],
							dateFormat: 'yy-mm-dd',
							maxDate: new Date()
						});
					});
				  </script> <input type="submit" name="btnGo" value="Search" id="search" class="btn btn-info" onclick="return check();"/>
			</td>
		</tr>

	</table>
	</div>
	<br />
	<table width="100%" border="0" class="table table-bordered">
		<thead>
			<tr>
				<th>SENT TO</th>
				<th>TYPE</th>
				<th>NAME</th>
				<th>MESSAGE</th>
				<th>MOBILE NO.</th>
				<th>STATUS</th>
				<th>TRANSACTION ID</th>
				<th>LOG MESSAGE</th>
				<th>SENT ON</th>
			</tr>
			</thead>
		
		
		<tbody>
			<?php
			// by default we show first page
			$pageNum = 1;
			$rowsPerPage=20;

			if(makeSafe(isset($_POST['txtPage'])))	    $pageNum = makeSafe($_POST['txtPage']);

			$offset = ($pageNum - 1) * $rowsPerPage;
			if($offset<0) $offset=0;

			$sqlWhere="";
			$sql = "SELECT * FROM sms_transaction_log WHERE 1=1";
			if(@$mobile_num!="")
				$sqlWhere .= " AND mobile_no like '%$mobile_num%'";
			if(@$date!="")
				$sqlWhere .= " AND date(date_sent) like '%$date%'";
			$limit=" order by date_sent desc LIMIT $offset, $rowsPerPage";
			$sql=$sql." ".$sqlWhere.$limit;
			$res=mysql_query($sql,$link) or die("Unable to connect to Server, We are sorry for inconvienent caused");
			$i=0;

			if(mysql_num_rows($res)>0)
			{
				while($row=mysql_fetch_array($res))
				{

					$i=$i+1;
					?>
			<tr>
				<td><?php echo $row['flag'];?></td>
				<td><?php if($row['sms_type']=="T") echo "Transactional"; else echo "Promotional";?></td>
				<td><?php echo $row['name'];?></td>
				<td style="width: 50%;"><?php echo $row['message'];?></td>
				<td><?php echo $row['mobile_no'];?></td>
				<td><?php echo $row['status'];?></td>
				<td><?php echo $row['transaction_id'];?></td>
				<td><?php if($row['log_text']=="" || $row['log_text']==null) echo '-'; else echo $row['log_text'];?></td>
				<td><?php echo date('jS-M-Y, g:iA',strtotime($row['date_sent']));?></td>
			</tr>
			<?php
				}
				?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="15"><?php 
				$sql="SELECT count(*) as numrows FROM sms_transaction_log WHERE 1=1 ";
				$sql=$sql." ".$sqlWhere;
				$result  = mysql_query($sql,$link) or die('Error, query failed');
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				$numrows = $row['numrows'];


				// how many pages we have when using paging?
				$maxPage = ceil($numrows/$rowsPerPage);
				echo "TOTAL RECORDS :".$numrows." | PAGE(S) : ".$maxPage;

				// print the link to access each page
				$self = $_SERVER['PHP_SELF'];
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
							$nav .= "<a class='mylink' href=\"javascript:SubmitPage('$page')\">$page</a> ";
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
			{
				echo "<tr><th colspan='9'>NO RECORDS FOUND</th></tr>";?> <script>
				$('.edit').hide();
				$('.delete').hide();
				</script> <?php }
				?><input type="hidden" name="rdID" id="rdID" value='' /> <input type="hidden" name="txtPage" id="txtPage" value='<?php echo makeSafe(@$_POST['txtPage']);?>' />
				</th>
			</tr>
		</tfoot>
	</table>
	<div style="clear: both"></div>
</form>

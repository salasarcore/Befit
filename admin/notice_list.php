
<?php 

if(@$_GET['aStr']=='Y' || @$_GET['aStr']=='N')
{
$sql="update  notice_board set published='".makeSafe(@$_GET['aStr'])."'  WHERE nid=".makeSafe($_GET['nid']);
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
if(mysql_affected_rows()>0)
echo "<div class='success'>Notice updated successfully</div>";

}
?>
<script>
function Publish(act,nid)
{
var spid="pub_"+nid;
var tab="notice_board";
var field="published";
var condition_field="nid";
var poststr = "id="+nid+"&act="+act+"&spid="+spid+"&field="+field+"&tab="+tab+"&condition_field="+condition_field ;
makePOSTRequest('pub_unpub.php', poststr,spid);
}

function SubmitPage(Page)
{

document.getElementById("txtPage").value=Page;
document.records.submit();
}

function selectID(objChk)
{
document.getElementById("NID").value=objChk.value;
}
function ActionScript(act)
{
	if(document.getElementById("NID").value=="" && act !="add")
	{
	  alert("Please select an Employee");
	  
	}
	else
	{
		url="popups/notice.php?act="+act+"&NID="+document.getElementById("NID").value;
		open_modal(url,730,600,"NOTICE")
		return true;
	  }
}
</script>
<?php 
$name_filter= makeSafe(@$_POST['txtFilter']);
?>

	<div id="navigation"><a href="pages.php">Home</a><a> Utility</a>  <span style="color: #000000;">Notice List </span></div>

	<form name="records" action="pages.php?src=notice_list.php" method="POST">
		 <table width="100%" cellspacing="0" class="adminform1">
		<tr>
		<td>
		<h2 class="common">Notice List</h2>
	</td>
			<td>
				<div id="option_menu">
					<a  class="addnew" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
					</div>
			</td>
		</tr>	
		</table>
	
		<div class="search_bar">
			Enter Subject: <input type="text" name="txtFilter" id="txtFilter" value="<?php echo $name_filter;?>" /> 
		<input type="submit" name="btnGo" value="Go" class="btn search">
		</div>
		<br />
		<table class="adminlist" style="cursor: pointer;">
				 <thead>
				<tr>
					<th>DATE</th>
					<th>SUBJECT</th>
					<th>DESCRIPTION</th>
					<th>ATTACHMENT</th>
					<th>PUBLISHED?</th>
					<th>UPDATED BY <br>Employee Name[Emp_Code]</th>
				</tr>
				</thead>
				<tbody>
				<?php
			$rowsPerPage = 20;
			$pageNum = 1;

			if(isset($_POST['txtPage']))	    $pageNum = makeSafe($_POST['txtPage']);
			$offset = ($pageNum - 1) * $rowsPerPage;
			

						$i=0;
							$sql="select nid , ntype ,nbody, ndate , subject,mime,published,updated_by  from notice_board where br_id=".$_SESSION['br_id']." and  (subject like '%".trim($name_filter)."%' ) order by nid desc LIMIT $offset, $rowsPerPage ";
							$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");

							while($row=mysql_fetch_array($res))
							{
								$i=$i+1;
								
								
								echo "<tr class=";
								  if($i%2==0) echo "row0"; else echo "row1";
								  echo ">";
									
									echo "<td align='center'>".date('jS-M-Y, g:i A', strtotime($row['ndate']))."</td>";
								?>
								<td><a class='mylink' href="javascript:open_modal('popups/read_notice.php?nid=<?php echo base64_encode($row['nid']);?>',600,400,'NOTICE');"><?php echo $row['subject'];?></a></td>
								<?php
								$str="";
								if(trim($row['nbody'])!="") 
								$str=strip_tags(substr($row['nbody'],0,100))."....";
									echo "<td>".$str."</td>";
									echo "<td align='center'>";
									if($row['mime']!="")
										echo "<a target='_blank' href='../site_img/notice/".DOMAIN_IDENTIFIER."_".base64_encode($row['nid']).".".$row['mime']."'><img src='../images/message_attachment.png' height='25' width='25'/></a>";
									
									echo "</td>";
									
									?>
									<td align="center"><span id="pub_<?php echo @$row['nid'];?>"> <a href ="javascript:Publish('<?php if($row['published']=='Y') echo 'N'; else echo 'Y';?>',<?php echo $row['nid'];?>)"><img src="../images/<?php if($row["published"]=="Y") echo "publish.png"; else  echo "publish_x.png";?>"/></a></span></td>
								<?php
								echo "<td align='center'>".$row['updated_by']."</td>";
								echo "</tr>";

								
						 }

						 echo "<tfoot><tr><th colspan=6>";
						 

		// how many rows we have in database
		$query   = "SELECT COUNT(nid) AS numrows FROM notice_board where br_id=".$_SESSION['br_id'];
		$result  = mysql_query($query) or die('Error, query failed');
		$row     = mysql_fetch_array($result, MYSQL_ASSOC);
		$numrows = $row['numrows'];
		if($numrows>0){
		$maxPage = ceil($numrows/$rowsPerPage);
		echo "TOTAL : ".$numrows."  PAGES : ";
		// how many pages we have when using paging?
		$maxPage = ceil($numrows/$rowsPerPage);

		// print the link to access each page
		$self = $_SERVER['PHP_SELF'];
		$nav  = '';
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
}
else 
	echo "NO RECORDS FOUND";
						 
		?>
		<input type="hidden" name="txtPage" id="txtPage" value='<?php echo @$_POST['txtPage'];?>'/>
				<input type="hidden" name="page" id="page" value="<?php echo $pageNum;?>" />		
				<input type="hidden" name="NID" id="NID" value="" />		

		</th></tr>
		</tfoot>

		</table>
		</form>


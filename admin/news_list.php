
<?php 
include_once("../functions/common.php");
/**
 * Commented include("modulemaster.php"); as it is used in menu.php file.
 * DO not uncomment it. It is being commented for future reference.
 */
//include("modulemaster.php");
if(in_array(module_news_list,$modules)){
	$id_admin=$_SESSION['empid'];
	$level=$_SESSION['access_level'];
	if($level!='Super Admin' && $level!='Admin')
	{
		if(!isAccessModule($id_admin, module_news_list))
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

$br_id=makeSafe(@$_POST['branches']);
?>
<?php 

if(@$_GET['aStr']=='Y' || @$_GET['aStr']=='N')
{
	$sql="update  news set published='".makeSafe(@$_GET['aStr'])."'  WHERE nid=".makeSafe($_GET['nid']);
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	if(mysql_affected_rows()>0)
		echo "<div class='success'>Notice updated successfully</div>";

}
?>
<script>
function validatenews(frm)
{
	if(frm.txtFilter.value.trim()=="")
	{
		alert('Enter Subject Name');
		frm.txtFilter.value="";
		frm.txtFilter.focus();
		return false;
	}
}

function Publish(act,nid)
{
var spid="pub_"+nid;
var tab="news";
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
	  alert("Please select an News");
	  
	}
	else
	{
		url="popups/news.php?act="+act+"&NID="+document.getElementById("NID").value;
		open_modal(url,740,600,"NEWS")
		return true;
	  }
}
</script>
<?php
$name_filter=@$_POST['txtFilter'];

?>
<div class="page_head">
<div id="navigation">
	<a href="pages.php"> Home</a><a> Utility</a> <span style="color: #000000;"> News/Press/Media List</span>
</div>

<form name="records"
	action="pages.php?src=news_list.php"
	method="POST" onsubmit="return validatenews(this);">
	
		<table width="100%" cellspacing="0" class="adminform1">
			<tr>
				<td>
				<h2 class="common">News/Press/Media List</h2>
				</td>
				<td>
					<div id="option_menu">
						<a class="addnew" href="javascript:void(0);" onClick="javascript:ActionScript('add');">Add New</a>
					</div>
				</td>
			</tr>
		</table>
		</div>
		<div class="search_bar">
		Enter Subject Name: <input type="text" name="txtFilter"	id="txtFilter" value="<?php echo $name_filter;?>" />
				<input	type="submit" name="btnGo" value="Go" class="btn search">
		</div>
		<br />
		<table class="adminlist" style="cursor: pointer;">
			<thead>
				<tr>
					<th width='200px'>DATE</th>
					<th>SUBJECT</th>
					<th>DESCRIPTION</th>
					<!-- <th>ATTACHMENT</th> -->
					<th width='100px'>PUBLISHED?</th>
					<th width='200px'>UPDATED BY <br>Employee Name[Emp_Code]</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$rowsPerPage = 20;
				$pageNum = 1;
				
				if(isset($_POST['txtPage']))	    $pageNum = makeSafe($_POST['txtPage']);
				$offset = ($pageNum - 1) * $rowsPerPage;
				$i=0;
				$sql="select nid , nbody, ndate , subject,published,updated_by  from news where br_id=".$_SESSION['br_id']." and (subject like '%".trim($name_filter)."%' ) order by date_updated desc LIMIT $offset, $rowsPerPage ";
				$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error($link));
				if(mysql_num_rows($res)>0){
					while($row=mysql_fetch_array($res))
					{
						$i=$i+1;
						echo "<tr class=";
						if($i%2==0) echo "row0"; else echo "row1";
						echo ">";
						$tmp=new DateTime($row['ndate']);
						echo "<td align='center'>".$tmp->format('jS-M-Y, g:i A')."</td>";
						?>
				<td><a class='mylink'
					href="javascript:open_modal('popups/read_news.php?nid=<?php echo base64_encode($row['nid']);?>',600,400,'NEWS');"><?php echo $row['subject'];?>
				</a>
				</td>
				<?php
				$str="";
								if(trim($row['nbody'])!="") 
								$str=strip_tags(substr($row['nbody'],0,100))."....";
									echo "<td>".$str."</td>";
				//echo "<td>".strip_tags(substr($row['nbody'],0,100))."....</td>";
					
				?>
				<td align="center"><span id="pub_<?php echo @$row['nid'];?>"> <a
						href="javascript:Publish('<?php if($row['published']=='Y') echo 'N'; else echo 'Y';?>',<?php echo $row['nid'];?>)"><img
							src="../images/<?php if($row["published"]=="Y") echo "publish.png"; else  echo "publish_x.png";?>" />
					</a>
				</span>
				</td>
				<?php
				echo "<td align='center'>".$row['updated_by']."</td>";
				echo "</tr>";


					}

					echo "</tbody>";
					echo "<tfoot><tr><th colspan=5>";
					// how many rows we have in database
					$query   = "SELECT COUNT(nid) AS numrows FROM news where br_id=".$_SESSION['br_id'];
					$result  = mysql_query($query) or die('Error, query failed');
					$row     = mysql_fetch_array($result, MYSQL_ASSOC);
					$numrows = $row['numrows'];
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
					echo "</tbody><tfoot><tr><th colspan=5>NO RECORDS FOUND";

				?>
				</th>
				</tr></table>
		
	
			<input type="hidden" name="txtPage" id="txtPage" value='<?php echo @$_POST['txtPage'];?>'/>
				<input type="hidden" name="page" id="page"	value="<?php echo @$pageNum;?>" />
				<input type="hidden" name="NID" id="NID" value="" />


</form>
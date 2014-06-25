<?php 
function session_default($session_id)
{
if(@$session_id=="") $session_id=0;
echo "<select size=\"1\" name=\"session\" id=\"session\">";
$sql="select session_id,session_name  from session order by session_id desc";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['session_name']."'";
		if(@$_SESSION['d_session']==$row1['session_name']) echo "selected"; echo">".$row1['session_name']."</option>";
	}
echo"</select>";
}
?>

<?php 
function branch_default($branch_id)
{
if(@$branch_id=="") $branch_id=0;
echo "<select size=\"1\" name=\"branch\" id=\"branch\">";
$sql="select br_id,br_name from mst_branch order by br_name";
$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['br_id']."'";
		if(@$_SESSION['br_id']==$row1['br_id']) echo "selected"; echo">".$row1['br_name']."</option>";
	}
echo"</select>";
}
?>
<?php
	function dd_session_section($session_id)
{	
echo "<select size=\"1\" name=\"session_section\" id=\"session_section\">";


		$sql="select d.department_name,s.session,s.section,s.session_id,s.freeze from mst_departments d, session_section s where d.department_id=s.department_id and s.freeze='N' and s.session='".$_SESSION['d_session']."' order by department_name,session_id desc";
			$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused");	
			while($row=mysql_fetch_array($res))
			{
							echo"<option value='".$row['session_id']."' onclick=\"populate_student(".$row['session_id'].",'".$row['freeze']."','stuList');\"";
				if(@$session_id==$row['session_id']) echo "selected"; echo">".$row['session']."-".$row['department_name']."-".$row['section']."</option>";
		  }
		  
		 echo"</select>";
}
 /**
  * a getDetailsById function is taking three arguments and returning an array of resultset.
  * @param table_name database table name
  * @param col_name column name of that table
  * @param id matching column name of that table
  * @return array containing resultset
  */

function getDetailsById($table_name,$col_name="1",$id="1")
{
	$query="select * from ".$table_name." WHERE ".$col_name."='".$id."' order by ".$col_name;
	$result=mysql_query($query);
	if(mysql_num_rows($result)>1){
		while($row[]=mysql_fetch_assoc($result));
		unset($row[count($row)-1]);
	}	
		else 
			$row=mysql_fetch_assoc($result);
	return $row;
}

/**
 * a contains_array function is taking one arguments and returning an boolean value.
 * @param array array of values
 * @return boolean value depending on the conditions 
 */

function contains_array($array){
	foreach($array as $value){
		if(is_array($value)) {
			return true;
		}
	}
	return false;
}
?>
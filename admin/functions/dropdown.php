
<?php 
function sex($sex)
{

echo "<select size=\"1\" name=\"sex\"  id=\"sex\">";
echo "<option value=\"0\" selected>--Select Gender--</option>";
echo "<option "; if($sex=="MALE") echo "selected"; echo" value=\"MALE\">MALE</option>";
echo "<option "; if($sex=="FEMALE") echo "selected"; echo" value=\"FEMALE\">FEMALE</option>";
			
echo"</select>";
}	

function category()
{

	echo "<select size=\"1\" name=\"category\" id=\"category\" style=\"width:255px\">";

	$sql="select contact_cat_name from contact_category";
	$res=mysql_query($sql) or die("Unable to connect to Server, We are sorry for inconvienent caused".mysql_error());
	while($row1=mysql_fetch_array($res))
	{
		echo"<option value='".$row1['contact_cat_name']."'>".$row1['contact_cat_name']. "</option>";
	}
	echo"</select>";
}

function country($countryname="")
{
	$sql=mysql_query("select * from countries");
	while($row=mysql_fetch_array($sql))
	{
		echo "<option value='".$row['countries_id']."'";
		if(@$countryname==$row['countries_name']) echo "selected"; echo ">".$row['countries_name']."</option>";
	}
}

   /**
	 * A timing function will run on basis of $time parameter.
	 * If $time has assigned value then it will show the dropdown as per value is assigned else it will give default dropdown values within the range of 1AM-12AM.
	 * @param $time is optional parameter, which will have time to be selected while editing the record in dropdown.
	 */
function timing($time="")
{
	for($i = 1; $i <= 24; $i++):
	if($i<10) $i="0".$i;
	    echo "<option value='".$i.":00:00'";
	    if(@$time==$i.":00:00") echo "selected"; echo ">".date('g.iA', strtotime($i.':00'))."</option>";
	endfor;
}

function time_selected($timesel)
{
	$start = strtotime('7:00:00');
	$end = strtotime('21:00:00');
	
	for ($i = $start; $i <= $end; $i+=60)
	{
			
		if($i==strtotime($timesel))
		{
			echo '<option value="'.date('h:i A', $i).'" selected>' .date('h:i A', $i).'</option>';
		}
		else
		{
			echo '<option value="'.date('h:i A', $i).'">' . date('h:i A', $i).'</option>';
		}
	}
}

function handicapped($handicapped)
{
echo "<select size=\"1\" name=\"handicapped\"  id=\"handicapped\">";
echo "<option value=\"0\" selected>--Select Option--</option>";
echo "<option "; if($handicapped=="No") echo "selected"; echo" value=\"No\">No</option>";
echo "<option "; if($handicapped=="Yes") echo "selected"; echo" value=\"Yes\">Yes</option>";
echo"</select>";
}

function cast($cust)
{

	echo "<select  size=\"1\" name=\"cust\" id=\"cust\">";
	echo "<option value=\"0\" selected>--Select Option--</option>";
	echo "<option "; if($cust=="SC") echo "selected"; echo" value=\"SC\">SC</option>";
	echo "<option "; if($cust=="ST") echo "selected"; echo" value=\"ST\">ST</option>";
	echo "<option "; if($cust=="MINORITY") echo "selected"; echo" value=\"MINORITY\">MINORITY</option>";
	echo "<option "; if($cust=="GENERAL") echo "selected"; echo" value=\"GENERAL\">GENERAL</option>";
	echo"</select>";

}
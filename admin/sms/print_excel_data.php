<div align="center">
<table  border="1" class="adminlist" style="cursor: pointer;table-layout:fixed;width: 40%;">
<thead>
<tr>
<th style="width:20px;">#</th>
<th>NAME</th>
<th>MOBILE NUMBER</th>
</tr>
</thead>
<tbody>
<?php
$i=0;
global $ExcelData;

$dataValid=0;
foreach( $ExcelData as $rowData ) 
{ 
  $i=$i+1;
?>
<tr class="<?php if($i%2==0) echo "row1"; else echo "row0"; ?>">
<td  align="center"><?php echo $i; ?></td>
  <td align="center"><?php echo $rowData['name']; ?></td>
  <td align="center"><?php echo $rowData['number']; ?></td>
</tr>
<?php 
}
?>
</tbody>
</table>
</div>
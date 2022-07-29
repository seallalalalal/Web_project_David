<html>
<meta charset='utf-8'>
<table>
<tr><td>欄位名稱</td><td>類型</td><td>NULL</td><td>Key</td><td>預設值</td><td>extra</td></tr>
<?php
include ('cfg.php');
if (isSet($_GET['tbl']))
	$tbl_name=$_GET['tbl'];
else
	$tbl_name='item_type';
if (!$result=$db->query("describe $tbl_name"))
	die ('-DB');
while ($row=$result->fetch_assoc()){
	echo '<tr><td>',$row['Field'],'</td>';
	echo '<td>',$row['Type'],'</td>';
	echo '<td>',$row['Null'],'</td>';
	echo '<td>',$row['Key'],'</td>';
	echo '<td>',is_null($row['Default'])?'NULL':$row['Default'],'</td>';
	echo '<td>',$row['Extra'],"</td></tr>\n";	
}
?>
</table>
</html>
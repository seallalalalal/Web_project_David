<html>
<?php
include ('cfg.php');
if (empty($_GET['od_id']))
	die ('-訂單編號');
$od_id=$_GET['od_id'];
if (!is_numeric($od_id))
	die('-編號錯誤');

if (!$result=$db->query("select * from orders where od_id=$od_id")){
	die('-DB');
}
if (!$row=$result->fetch_assoc())
	die('-編號錯誤');
echo "店家名稱：",$row['vd_name'],"<br>";
echo "取餐編號：",$row['ticket_no'],"<br>";
echo "成立時間：",$row['od_date']," ",$row['od_time'],"<br>";
echo "總金額：",$row['tot_price'],"<br>";
echo "明細為：<br>";
if (!$result=$db->query("select * from od_detail where od_id=$od_id")){
	die();
}
echo '<table><tr><td>no.</td><td>商品名稱</td><td>數量</td><td>小計</td></tr>';
while ($row=$result->fetch_assoc()){
	echo "<tr><td>",$row['serial_no'],"</td>";
	echo "<td>",$row['item_name'],"</td>";
	echo "<td>",$row['qty'],"</td>";
	echo "<td>",$row['sub_total'],"</td></tr>\n";
}
echo '</table>';
?>
</html>
<?php
require_once('cfg.php');
$result=$db->query('select vd_name, phone_number, position, id from vendor_list');
if (!$result)
	die ('-DB');
while($row=$result->fetch_row()){
	$vd_name[]=$row[0];				
	$phone_number[$row[0]]=$row[1];	//將電話號碼的key設為店家名稱
	$position[$row[0]]=$row[2];		//將位置的key設為店家名稱
	$vd_id[$row[0]]=$row[3];		//將店家id的key設為店家名稱
}

function generateStoreName(){
	global $vd_name;	//商店清單
	if (!isSet($vd_name))
		die ('-Store name');
	for ($i=0;$i<count($vd_name);$i++){
		if ($i==0)
			echo "\n<nav><ul>\n<li class='tm-paging-item'><a onclick='getMenu(",'"',$vd_name[$i],'"',");' class='tm-paging-link sw active'>";
		else
			echo "\n<li class='tm-paging-item'><a onclick='getMenu(",'"',$vd_name[$i],'"',");' class='tm-paging-link sw'>";
		echo $vd_name[$i],'</a></li>';
	}
	echo "\n</ul></nav>\n";
}

function getPhoneNumber($vd_name){
	global $phone_number;
	if (is_null($phone_number[$vd_name]))
		echo '<font color=#BFBFBF>(店家沒有提供電話！)</font>';
	else
		echo "<font>$phone_number[$vd_name]</font>";
}

function getPosition($vd_name){
	global $position;
	if (is_null($position[$vd_name]))
		echo '<font color=#DF02EA>未知(???)</font>';
	else 
		echo "<font color=blue>$position[$vd_name]</font>";
}
?>
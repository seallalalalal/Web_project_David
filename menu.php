<?php
require_once('stores.php');

if (!isSet($_GET['storeName']))
	die ('-PARAM');
$store_name=$_GET['storeName'];

//撈DB
if (!$result=$db->query('SELECT vd_name,item_name,type_name,type_id,price FROM menu
	inner join vendor_list on vd_id=vendor_list.id
	left join item_type on item_type=type_id and menu.vd_id=item_type.vd_id
	where vendor_list.vd_name='.quoteStr($store_name)
	.' order by type_id, item_name')){
	die ('-DB');
}
if ($result->num_rows==0)
	die ('這家店還沒有菜單:(SPLIT');	//沒有菜單的話，SPLIT後的storeInfo不顯示任何東西

//用$row來顯示對應的html語法
$prevType=0;	//前一個type_id
$isFirstType=true;		//是否還是第一個種類
while ($row=$result->fetch_row()){
	//生成餐點種類(因為是用type_id left join且type_id和type_name皆不可為null，可知若type_id非null則type_name也必不為null)
	if (!is_null($row[3]) && $row[3]!=$prevType){	
		if (!$isFirstType){
			echo '</div></div>';
		}
		$isFirstType=false;
		$prevType=$row[3];	//更新prevType存的type_id,當有異動時才會再顯示新種類
		
		echo '<div class = "col-lg-12 col-md-12 col-sm-12 col-12 tm">';
		echo "<h4 class='tm-site-title'>$row[2]</h4>";
		echo '</div>';
	}

	//生成品項
	echo '<article class="col-lg-3 col-md-4 col-sm-6 col-12 tm-gallery-item">';
	echo '<figure>';
	echo '<img src="img/gallery/04.jpg" alt="Image" class="img-fluid tm-gallery-img" />';
	echo '<figcaption>';
	echo "<h4 id='itemName_$row[0]_$row[1]' class='tm-gallery-title'>$row[1]</h4>";	//生成餐點名稱
//	echo '<p class="tm-gallery-description">xxx</p>';				//生成餐點描述
	echo '<button onclick="addOrReduceQty(',"'$row[0]_$row[1]'",',false);">-</button>';
	echo " <span id='qty_$row[0]_$row[1]' name='item_qty'>0</span> ";	//餐點數量
	echo '<button onclick="addOrReduceQty(',"'$row[0]_$row[1]'",',true);">+</button>';
	echo "<p class='tm-gallery-price'>$<font id='price_$row[0]_$row[1]'>",$row[4],'</font></p>';	//生成餐點價錢
	echo '</figcaption></figure></article>';
}
	echo 'SPLIT';		//區分菜單部分和其他
	echo "店家電話: ",getPhoneNumber($store_name),"<br>";
	echo "這家店在地餐進門的",getPosition($store_name),"位置";

?>
<?php
require_once('stores.php');
if (!isSet($_GET['vd_name']))
	die('-店家名字');
if (!isSet($_GET['items']))
	die('-商品清單');
if (!isSet($_GET['totPrice']))
	die('-總價');
$storeName=$_GET['vd_name'];
$storeId=$vd_id[$storeName];
$items=$_GET['items'];
$totPrice=$_GET['totPrice'];

foreach ($items as $name=>$qty){
	$selectedItem[]="item_name='".$name."'";
}
$itemCond=implode(" or ",$selectedItem);

//需先計算總價是否和前端傳來的一致
//把price(單價)抓回來再由php端計算
if(!$result=$db->query("SELECT item_name,price FROM menu inner join vendor_list on menu.vd_id=vendor_list.id
	where vendor_list.vd_name='$storeName' and ($itemCond)")){		
	die('-DB');
}	
if($result->num_rows==0)
	die('店名或商品名稱和資料庫端不相符');

$cal=0;	//計算總價
while($row=$result->fetch_row()){
	$tmp=$items[$row[0]]*$row[1]; //數量*價格=小記
	$sub_total[$row[0]]=$tmp;	//以商品名當key，先記下來後面會用到
	$cal+=$tmp;				
}
if ($cal!=$totPrice)
	die ("總價不符,$cal");	//總價和前端計算不符，並以分隔字元分隔金額和錯誤訊息交由前端處理


//將該訂單寫入DB
$db->begin_transaction();	//全部sql包在transaction裡面，有異動失敗記得rollback

//取得當前日期時間
$od_date=date('Y-m-d');	
$od_time=date('H:i:s');

//取得ticket_no(訂單編號)
if (!$result=$db->query("select MAX(ticket_no) from orders inner join vendor_list where orders.vd_id=$storeId and orders.od_date='$od_date'"))
	die('訂單編號獲取失敗');
$ticket_no=$result->fetch_row()[0];
if (is_null($ticket_no))
	$ticket_no=1;
else
	$ticket_no++;

//試著新增資料到master table
if (!$result=$db->query("insert into orders (ticket_no,vd_id,vd_name,tot_price,od_date,od_time) values
	($ticket_no,$storeId,'$storeName',$cal,'$od_date','$od_time')")){
	$db->rollback();
	die("寫入訂單失敗");
}

if (!$result=$db->query("select od_id from orders where vd_id=$storeId and od_date='$od_date' and ticket_no=$ticket_no"))
	die('獲取訂單編號失敗');
$od_id=$result->fetch_row()[0];

//新增至master table成功，試著新增一至數筆明細至detail table
//因為一定會跟master join起來一起看，所以master有的東西除了join用的od_id(如vd_id)就不用在detail table再寫一次ㄌ

$insert_str="";
$sn=1;	//seriel_no
foreach ($items as $name=>$qty){
	if ($sn>1)
		$insert_str.=",";
	$insert_str.="($od_id,$sn,(select item_id from menu where menu.vd_id=$storeId and menu.item_name='$name'),'$name',$qty,$sub_total[$name])";
	$sn++;
}

if (!$result=$db->query("insert into od_detail (od_id,serial_no,item_id,item_name,qty,sub_total) values $insert_str")){	
	$db->rollback();
	die("寫入明細失敗");
}

$db->commit();		//結束transaction
echo $ticket_no,',',$od_id;	//回傳取餐號碼
?>
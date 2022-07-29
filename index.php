<?php include('stores.php');
?><!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>學生訂餐系統</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400" rel="stylesheet" />    
	<link href="css/templatemo-styles-v2-5.css" rel="stylesheet" />
</head>
<!--

Simple House

https://templatemo.com/tm-539-simple-house

-->
<body onload='getMenu("<?php echo $vd_name[0]?>");'>

	<a class="fixedButton" href='#'>
		<div class="roundedFixedBtn">↑<i class="fa fa-phone"></i></div>
	</a>
	
	<div class="navbar">
	  <div class="navbar-right">
		<a>$總金額 <font id='totPriceTxt' color='FF604D' >0</font></a>
		<a id='submit' class="active" onclick='viewOrder()'>結帳去</a>
		<a class='otherActive' onclick='clearShoppingCart(true)'>清空購物車</a>
	  </div>
	</div>
	
	<div class="container">
	<!-- Top box -->
		<!-- Logo & Site Name -->
		<div class="placeholder">
			<div class="parallax-window" style='background-image: url("img/simple-house-01.jpg");'>
				<div class="tm-header">
					<div class="row tm-header-inner">
						<div class="col-md-6 col-12">
							
							<div class="tm-site-text-box">
								<h1 class="tm-site-title">學生訂餐系統</h1>
							</div>
						</div>
						<nav class="col-md-6 col-12 tm-nav">
							<ul class="tm-nav-ul">
								<li class="tm-nav-li"><a class="tm-nav-link" onclick='returnHome();'>Home</a></li>
							</ul>
						</nav>	
					</div>
				</div>
			</div>
		</div>

		<main>
		
			<header class="row tm-welcome-section">
				<h2 class="col-12 text-center tm-section-title">歡迎使用學生訂餐系統</h2>
				<p class="col-12 text-center">
					點選店家，訂購您喜愛的美食。<br>
					<font color='#BFBFBF'>(不支援一次訂購多店家)</font>
				</p>
			</header>
						
			<div class="tm-paging-links"> 
				<!-- 這裡生成店家名-->
				<?php generateStoreName();?>
			</div>
			
			<div id='feedback' align='center'>
			</div>
			
			<div class="row tm-gallery">
				<!--菜單會顯示在這裡-->
				<div id='menuTxt' class="tm-gallery-page">	
				</div>
			</div>
			
			
		</main>

		<footer class="tm-footer text-center">
			<div id='storeInfo'>
			</div>
			<p>Copyright &copy; 2020 Simple House 
            
            | Design: <a rel="nofollow" href="https://templatemo.com">TemplateMo</a></p>
		</footer>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/parallax.min.js"></script>
	<script>
	
		var menuTxt=document.getElementById('menuTxt');
		var storeInfo=document.getElementById('storeInfo');
		var submit=document.getElementById('submit');
		var totPriceTxt=document.getElementById('totPriceTxt');
		var feedback=document.getElementById('feedback');
		
		var itemQty=document.getElementsByName('item_qty');	//餐點數量陣列，element為<span>
		
		var curStoreName=null;
		var orderList={};	//一個prototype Object的Dictionary

		const itemNamePrefix='itemName_';
		const qtyPrefix='qty_';
		const pricePrefix='price_';
		
		
		$(document).ready(function(){
			//切換選單按鈕active
			$('.sw').click(function(e){
				e.preventDefault();
				
				$('.sw').removeClass('active');
				$(this).addClass("active");
			});
		});
		
		function getMenu(vd_name){
			returnMenu(false);	//確保購物車顯示畫面被清空
			if (curStoreName!=null && curStoreName==vd_name)//使用者重複按當前店家的按鈕
				return;
			menuTxt.innerHTML='菜單取得中，請稍候...';
			if (vd_name==null){
				menuTxt.innerHTML='菜單取得失敗:(';
				return;
			}
			//ajax
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					if (this.status == 200){
						var str=this.responseText.split("SPLIT");
						menuTxt.innerHTML=str[0];
						storeInfo.innerHTML=str[str.length-1];
						
						//寫入當前vd_name至curStoreName，以免不必要的重複發送請求
						curStoreName=vd_name;
						setQtyAndTotPrice(vd_name);	//還原之前點選過的數量及總價
					}
					else
						menuTxt.innerHTML="error code: "+this.status;
				}
			};
			xhttp.open("GET", "menu.php?storeName="+encodeURIComponent(vd_name), true);
			xhttp.send();
		}
		
		function setQtyAndTotPrice(vd_name){
			var totPrice=0;
			Object.entries(orderList).forEach(entry=>{
				const [key, value]=entry;	//將entry拆成key value
				//key:店家名_餐點名；value:數量
				const storeName=key.split('_')[0];
				if (storeName!=vd_name)		//檢查該orderList中該品項是不是這家店的，是才還原
					return;
				document.getElementById(qtyPrefix+key).innerHTML=value;	//還原數量
				var price=document.getElementById(pricePrefix+key).innerHTML;
				totPrice+=price*value;			//計算總價
			});
			totPriceTxt.innerHTML=totPrice;		//還原總價
		}
		
		function addOrReduceQty(id,isAdd){
			//該id目前格式為"店家名_餐點名"
			var qtyTxt=document.getElementById(qtyPrefix+id);		//取得單品數量的html element
			var itemQty=parseInt(qtyTxt.innerHTML,10);				//數量文字轉數字

			var itemPrice=parseInt(document.getElementById(pricePrefix+id).innerHTML,10);	//單價文字轉數字
			var totPrice=parseInt(totPriceTxt.innerHTML,10);	//總價文字轉數字
			
			if (isAdd){	//增加數量
				itemQty++;
				totPrice+=itemPrice;
			}
			else {		//減少數量
				if (itemQty==0)
					return;
				itemQty--;
				totPrice-=itemPrice;
			}
			qtyTxt.innerHTML=itemQty;	//將數量寫回原本的地方
			totPriceTxt.innerHTML=totPrice;	//更新總價
			if (itemQty==0)
				delete orderList[id];		//=0的把它刪掉，之後才不會被iterate
			else
				orderList[id]=itemQty;		//更新/寫入orderList
		}
	
		function viewOrder(){
			if (totPriceTxt.innerHTML=='0'){
				alert('您尚未在該店點任何餐點，請繼續選購！');
				return;
			}
			var str="您已點選的餐點如下：<br>";
			var items=[];
			var totPrice=0;
			
			//寫入店家名稱
			str+="<p><h1>"+curStoreName+"</h1></p><br>";
			str+="<table>";
			//將顯示的內容換成已選擇品項
			Object.entries(orderList).forEach(entry=>{
				const [key,value]=entry;
				//key:店家名_餐點名；value:數量
				const names=key.split('_');
				if (names[0]!=curStoreName)	//訂單內店家名和當前店家不符
					return;
				//顯示餐點名和數量
				const itemPrice=document.getElementById(pricePrefix+key).innerHTML;
				const subTot=value*itemPrice;
				totPrice+=subTot;
				str+="<tr><td>"+names[1]+"</td><td>$</td><td>"+itemPrice+"</td>";
				str+="<td> * "+value+"</td><td>= </td><td>$</td><td>"+subTot+"</td></tr>";
			});
			//顯示總價
			str+="</table><br><p>總共: <font color='red'>$"+totPrice+"</font></p><br>";
			str+="若餐點都無誤，請點選送出鈕。<br><br>";
			str+="<ul><li class='tm-paging-item'><a class='tm-paging-link' onclick='submitOrder("+totPrice+");'>送出</a></li>";
			str+="<li class='tm-paging-item'><a class='tm-paging-link' onclick='returnMenu(true);'>返回</a></li></ul>";
			//把結果字串秀出來
			$('#menuTxt').addClass('hidden');
			feedback.innerHTML=str;
		}
		
		
		function submitOrder(totPrice){
			//將計算出來的商店、商品品項、數量和總價傳給後端
			//並由後端計算總價是否和前端傳來的相符
			var url="submitOrder.php?vd_name="+encodeURIComponent(curStoreName);
			Object.entries(orderList).forEach(entry=>{
				const [key,value]=entry;
				//key:店家名_餐點名；value:數量
				const names=key.split('_');	
				if (names[0]!=curStoreName)	//訂單內店家名和當前店家不符
					return;
				url+="&items["+encodeURIComponent(names[1])+"]="+encodeURIComponent(value);
			});
			url+="&totPrice="+totPrice;
			
			//用ajax送url(GET)
			feedback.innerHTML="訂單送出中，請稍後......";
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					if (this.status == 200){						
						//要處理前後端總價不符的情況
						const result=this.responseText.split(',');
						if (result[0]=='總價不符'){
							const rtn=confirm('總金額計算和資料庫紀錄不相符，將以資料庫為準，金額為'+result[1]+'，是否仍要購買？');
							if (!rtn){	//不想買ㄌ
								returnMenu(true);
								return;
							}
						}
						var tmp="<h1>"+curStoreName+"</h1><br>";
						tmp+="已收到您的訂單，<BR>訂單編號為：<br><h1>"+result[1]+"</h1><br>";
						tmp+="您的取餐編號為：<br><h1>"+result[0]+"</h1><br>";
						tmp+="<ul><li class='tm-paging-item'><a class='tm-paging-link' onclick='returnMenu(true);clearShoppingCart(false);'>返回</a></li></ul>";
						feedback.innerHTML=tmp;
					}
					else
						feedback.innerHTML="error code: "+this.status;
				}
			};
			xhttp.open("GET", url, true);
			xhttp.send();
		}
		
		function returnMenu(jumpToTop){
			//從預覽購物車回到菜單頁
			$('#menuTxt').removeClass('hidden');
			feedback.innerHTML="";
			if (jumpToTop)
				window.scrollTo(0, 0);
		}
		
		
		function returnHome(){
			//又名:你想refreshㄇ
			var rtn=confirm("按下去會清空購物車，是否繼續？");
			if (rtn)
				location.reload();
		}
		
		function clearShoppingCart(showConfirm){
			if ($('#menuTxt').hasClass("hidden")){	//購物車已在預覽或送訂單中，此時不該再清除購物車
				alert('此時不能清除購物車！');
				return;
			}
			if (showConfirm){
				const rtn=confirm('確定要清空該商店的購物車嗎？');
				if (!rtn)
					return;
			}
			//把該店的品項delete掉
			Object.keys(orderList).forEach(name=>{
				if (name.split('_')[0]==curStoreName)	//店家名_商品名
					delete orderList[name];
			});
			//把數量再改回0
			itemQty.forEach(qtyEle=>{
				qtyEle.innerHTML=0;
			});
			//把總價歸0
			totPriceTxt.innerHTML=0;
		}
		
	</script>
</body>
</html>	
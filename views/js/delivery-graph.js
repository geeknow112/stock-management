

function cmd_search() {
	document.forms.method = 'get';
	document.forms.action = "{{ admin_url() }}admin.php?page={{$formPage}}&action=search"
	document.forms.cmd.value = 'search';
	document.forms.submit();
}

/**
 * [注文]ボタン押下時の処理 (class 0)
 * 
 **/
function change_repeat_order(oid = null) {
	var r_order_id = 'r_order_' + oid;
	var cars_class_id = 'cars_class_' + oid;
	var cars_tank_id = 'cars_tank_' + oid;
	var delivery_dt_id = 'delivery_dt_' + oid;
	var warehouse_id = 'r_warehouse_' + oid;
	var arrival_dt_id = 'r_arrival_dt_' + oid;

	var cars_class = document.getElementById(cars_class_id).value;
	var cars_tank = document.getElementById(cars_tank_id).value;
	var delivery_dt = document.getElementById(delivery_dt_id).value;
	var warehouse = document.getElementById(warehouse_id).value;
	var arrival_dt = document.getElementById(arrival_dt_id).value;


	if (window.confirm('車種、槽、配送予定日 を変更しますか？')) {
		document.forms.method = 'post';
		document.forms.action.value = 'regist';
		//document.forms.oid.value = '1';
		document.getElementById(r_order_id).value = r_order_id;
		document.forms.class.value = cars_class;
		document.forms.cars_tank.value = cars_tank;
		document.forms.change_delivery_dt.value = delivery_dt;
		document.forms.r_warehouse.value = warehouse;
		document.forms.r_arrival_dt.value = arrival_dt;

	/*
		document.forms.r_delivery_dt.value = <?php echo $row->delivery_dt; ?>;
		document.forms.r_class.value = <?php echo $row->class; ?>;
		document.forms.r_tank.value = '{{$row->cars_tank}}';
		document.forms.base_sales.value = '1';
		document.forms.cmd.value = 'regist';
	*/	document.forms.submit();
	}

}

/**
 * [直取分]ボタン押下時の処理 (class 7)
 * 
 **/
function change_repeat_order_direct_delivery(oid = null) {
console.log(oid);
	var r_order_id = 'r_order_' + oid;
	var cars_class_id = 'cars_class_' + oid;
	var cars_tank_id = 'cars_tank_' + oid;
	var delivery_dt_id = 'delivery_dt_' + oid;
	var warehouse_id = 'r_warehouse_' + oid;
	var arrival_dt_id = 'r_arrival_dt_' + oid;

	var cars_class = document.getElementById(cars_class_id).value;
	var cars_tank = document.getElementById(cars_tank_id).value;
	var delivery_dt = document.getElementById(delivery_dt_id).value;
	var warehouse = document.getElementById(warehouse_id).value;
	var arrival_dt = document.getElementById(arrival_dt_id).value;


	if (window.confirm('この直取分を 【 完了 】 にしますか？')) {
		document.forms.method = 'post';
//		document.forms.action.value = 'regist';
		document.forms.action.value = 'set_direct_delivery';
		//document.forms.oid.value = '1';
		document.getElementById(r_order_id).value = r_order_id;
		document.forms.class.value = cars_class;
		document.forms.cars_tank.value = cars_tank;
		document.forms.change_delivery_dt.value = delivery_dt;
		document.forms.r_warehouse.value = warehouse;
		document.forms.r_arrival_dt.value = arrival_dt;
		document.forms.submit();
	}
}

/**
 * 確定列の[直取分]ボタン押下時の処理 (class 7)
 * 
 **/
function complete_order_direct_delivery(oid = null, sales = null) {
console.log(sales);

	var cars_class_id = 'cars_class_' + oid;

	var cars_class = document.getElementById(cars_class_id).value;

	if (window.confirm('この直取分を 【 処理済 】 にしますか？')) {
		document.forms.method = 'post';
		document.forms.action.value = 'complete_direct_delivery';
		document.forms.class.value = cars_class;
		document.forms.sales.value = sales;
		document.forms.submit();
	}
}

/**
 * [更新]ボタン押下時の処理
 * 
 **/
function change_order(sales = null, repeat_fg = null, use_stock = null) {
	const change_qty_id = 'change_qty_' + sales;
	const change_qty = document.getElementById(change_qty_id).value;

	const change_ship_addr_id = 'change_ship_addr_' + sales;
	const change_ship_addr = document.getElementById(change_ship_addr_id).value;

	const ship_addr_text_id = 'ship_addr_text_' + sales;
	const ship_addr_text = document.getElementById(ship_addr_text_id).value;

	if (change_qty != false) {
		if (window.confirm('更新しますか？')) {
			document.forms.method = 'post';
			document.forms.action.value = 'order_update';
			document.forms.sales.value = sales;
			document.forms.repeat_fg.value = repeat_fg;
			document.forms.use_stock.value = use_stock;
			document.forms.change_qty.value = change_qty;
			document.forms.change_ship_addr.value = change_ship_addr;
			document.forms.ship_addr_text.value = ship_addr_text;
			document.forms.submit();
		}
	}
}

/**
 * 受領書受取の確認
 * 
 **/
function check_status(sales = null, goods = null, repeat_fg = null, use_stock = null) {
	const rec = document.getElementById('check-receipt_' + sales).checked;
	console.log(rec);
	if (rec == true) {
		if (window.confirm('一連の処理を 【 完了 】 にしますか？')) {
			document.forms.method = 'post';
			document.forms.action.value = 'set_receipt';
			document.forms.sales.value = sales;
			document.forms.repeat_fg.value = repeat_fg;
			document.forms.use_stock.value = use_stock;
			document.forms.submit();
		}
	} else {
		//alert('受領書の受取をチェックしてください。');

		// ロット登録画面へ遷移
		to_lot_regist(sales, goods);
	}
}

/**
 * 受領書受取のチェックボックス切替
 * 
 **/
function switch_receipt(sales) {
	const ret = document.getElementById('check-receipt_' + sales);
	if (ret.checked == true) {
		ret.checked = false;
	} else {
		ret.checked = true;
	}
}

/**
 * ロット登録欄作成のための確認
 * 
 **/
function confirm_make_lot_space(sales = null, goods = null, repeat_fg = null, use_stock = null, role = null) {
	console.log(role);
	const message = (role == 'administrator') ? 'ロット登録欄を作成しますか？' : '確定する';
	if (window.confirm(message)) {
		document.forms.method = 'post';
		document.forms.action.value = 'make_lot_space';
		document.forms.sales.value = sales;
		document.forms.repeat_fg.value = repeat_fg;
		document.forms.use_stock.value = use_stock;
		document.forms.submit();
	}
}

/**
 * ロット登録画面へ遷移
 * 
 **/
function to_lot_regist(sales = null, goods = null) {
	const sdt = document.getElementById('user-search-input').value; // 開始日付を付加
	window.location = '{{ admin_url() }}admin.php?page=lot-regist&s[sdt]=' + sdt + '&sales=' + sales + '&goods=' + goods + '&action=save';
}

var unescapeHtml = function(str) {
	if (typeof str !== 'string') return str;

	var patterns = {
		'&lt;'   : '<',
		'&gt;'   : '>',
		'&amp;'  : '&',
		'&quot;' : '"',
		'&#x27;' : '\'',
		'&#x60;' : '`'
	};

	return str.replace(/&(lt|gt|amp|quot|#x27|#x60);/g, function(match) {
		return patterns[match];
	});
};

function setResult(oid) {
	const data = {
		oid: oid, 
		customer: document.getElementById("customer_" + oid).value, 
		goods: document.getElementById("goods_" + oid).value, 
		qty: document.getElementById("qty_" + oid).value, 
		ship_addr: document.getElementById("ship_addr_" + oid).value, 
		outgoing_warehouse: document.getElementById("outgoing_warehouse_" + oid).value
	};
	console.log(data);

	if (data['goods'] == "0" || !data['qty'] || data['outgoing_warehouse'] == "0") {
		alert('品名、数量(t)、出庫倉庫の入力に誤りがあります。');
	} else {
		var ret = window.confirm(oid + ' の結果を登録しますか？');
		if (ret) {
			document.forms.cmd.value = 'cmd_set_result';
			document.forms.method = 'post';
			document.forms.action.value = 'set_result';
			document.forms.oid.value = oid;
			document.forms.odata.value = JSON.stringify(data);
			document.forms.submit();
		} else {
		}
	}
}
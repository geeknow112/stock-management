	<p class="">
	<legend>【注文登録】</legend>
	</p>

{{--	@if ($get->action != '')	--}}
	<div class="row mb-3">
		<label for="sales" class="col-sm-2 col-form-label w-5">注文番号</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="sales" name="sales" aria-describedby="salesHelp" value="{{$rows->sales}}" readonly>
	</div>
{{--	@endif	--}}

	<div class="row mb-3">
		<label for="customer" class="col-sm-2 col-form-label w-5">氏名　<span class="badge text-bg-danger">必須</span></label>
		<select class="form-select w-75" aria-label="customer" id="customer" name="customer" onchange="createSelectBox(); createSelectBoxGoods();" @if ($cur_user->roles[0] != 'administrator') disabled @endif>
			@foreach($initForm['select']['customer'] as $customer => $d)
				@if ($customer == '0')
				<option value=""></option>
				@else
				<option value="{{$customer}}" @if ($customer == $rows->customer) selected @endif >{{$customer}} : {{$d}}</option>
				@endif
			@endforeach
		</select>
		@if ($cur_user->roles[0] != 'administrator')
			<input type="hidden" name="customer" value="{{$rows->customer}}" />
		@endif
<!--		<div id="orderName" class="form-text">氏名を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="carModel" class="col-sm-2 col-form-label">車種　<span class="badge text-bg-danger">必須</span></label>
		<select class="form-select w-75" aria-label="carModel" id="class" name="class" onchange="disp_class_detail();">
			@if ($rows->class != 8 && $rows->class != 9 && $rows->class != 10)
				@foreach($initForm['select']['car_model'] as $i => $d)
					@if ($i == '0')
					<option value=""></option>
					@else
					<option value="{{$i}}" @if ($i == $rows->class) selected @endif @if (in_array($i, $initForm['select']['car_model_limit'])) style="color: red;" @endif>{{$d}}</option>
					@endif
				@endforeach
			@else
					<option value="{{$rows->class}}">6t-{{$rows->class}}</option>
			@endif
		</select>
	</div>

	@if ($get->action != '')
	<div class="row mb-3">
		<label for="class_detail" class="col-sm-2 col-form-label hide-area">　{{$rows->delivery_dt}}の内訳<span id="limit_alert"></span></label>
		<table class="table table-bordered text-nowrap class-detail-textarea hide-area" id="class_detail" name="class_detail">
			<tr class="table-light border-dark">
				<th>品名</th>
				<th>量(t)</th>
				<th>顧客名</th>
			</tr>
<!--
			<tr>
				<td>みやび肥育</td>
				<td>4</td>
				<td>能勢農場</td>
			</tr>
-->
		</table>
	</div>
	@endif

		<script>
			function disp_class_detail() {
				// 更新画面以外は処理を終了する
				var action = "{{$get->action}}";
				if (!action) {
//					console.log(action);
					return;
				}

				// 明細出力エリアを表示
				var get_area = document.getElementsByClassName("hide-area");
				Object.keys(get_area).forEach(function(i) {
//					console.log(get_area[i]);
					get_area[i].style.display = 'block';
				});

				const ddt_value = document.getElementById("delivery_dt").value.replaceAll('-', '');
				const class_value = document.getElementById("class").value;

				//連想配列の配列
				var cd = "{{$class_detail}}";
				var class_data = JSON.parse(unescapeHtml(cd));
//				console.log(class_data);

				const class_detail = document.getElementById("class_detail");
				const chead = '<tr class="table-light border-dark"><th>品名</th><th>量(t)</th><th>顧客名</th></tr>';

console.log(ddt_value);
console.log(class_value);

				var d = class_data[ddt_value][class_value];
				var cbody = '';

	if (d != undefined) {
		console.log('data : ' + d);
	}
	var cnt = Object.keys(d).length;
	console.log('cnt : ' + cnt);

	var qtys = [];
	for (var i=0; i<=cnt; i++) {
		if (d[i] != undefined) {
			console.log(d[i]);
			cbody += '<tr><td>' + d[i]['goods_name'] + '</td><td>' + d[i]['qty'] + '</td><td>' + d[i]['customer_name'] + '</td></tr>';
			qtys.push(d[i]['qty']);
		}
	}

	const limit = 6; // 限界値(6t)
	var sum_qty = qtys.reduce((pre_value, cur_value) => parseFloat(pre_value) + parseFloat(cur_value), 0);
	console.log(sum_qty);
	const limit_alert = document.getElementById('limit_alert');
	if (sum_qty >= limit) {
		limit_alert.style.color = 'red';
		limit_alert.innerHTML = "<br>　⚠️6tを超えています";
	} else {
		limit_alert.innerHTML = "";
	}


//				for (var i=1; i<=Object.keys(d).length; i++) {
//					cbody += '<tr><td>' + d[i]['goods'] + '</td><td>' + d[i]['qty'] + '</td><td>' + d[i]['customer'] + '</td></tr>';
//				}
				class_detail.innerHTML = chead + cbody;
			}
		</script>

	<div class="row mb-3">
		<label for="carsTank" class="col-sm-2 col-form-label">槽　（※ ６ｔ車のタンクの番号）</label>
		<select class="form-select w-75" aria-label="carsTank" id="cars_tank" name="cars_tank">
			@foreach($initForm['select']['cars_tank'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->cars_tank) selected @endif >{{$d}}</option>
			@endforeach
		</select>
	</div>

	<div class="row mb-3">
		<label for="goodsName" class="col-sm-2 col-form-label">品名　<span class="badge text-bg-danger">必須</span></label>
		<select class="form-select w-75" aria-label="goodsName" id="goods" name="goods" onchange="createSelectBox();" @if ($get->action == '' || $cur_user->roles[0] != 'administrator') disabled @endif>
			@if ($post->customer)
				@foreach($initForm['select']['goods_name'][$post->customer] as $i => $d)
					<option value="{{$i}}" @if ($i == $rows->goods) selected @endif >{{$d}}</option>
				@endforeach
			@else
				@foreach($initForm['select']['goods_name'][$rows->customer] as $i => $d)
					<option value="{{$i}}" @if ($i == $rows->goods) selected @endif >{{$d}}</option>
				@endforeach
			@endif
		</select>
		@if ($cur_user->roles[0] != 'administrator')
			<input type="hidden" name="goods" value="{{$rows->goods}}" />
		@endif
		<span id="" class="manual-text form-text">※ 「<b>氏名</b>」を選択後、プルダウンが有効になります。</span>
	</div>
<script>
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

function createSelectBox(){
	var customer = document.forms.customer.value;
	var goods = document.forms.goods.value;
	//console.log('c: ' + customer);
	//console.log('g: ' + goods);

	//連想配列の配列
	var ar = "{{$test_ship_addr}}";
	var json = JSON.parse(unescapeHtml(ar));
	console.log(json[customer]);
	var arr = json[customer];

	// selectの初期化
	const sel = document.getElementById("ship_addr");
	sel.disabled = (goods) ? (customer) ? false : true : true; // 非活性化
	console.log(sel.childNodes.length);
	for (var i=sel.childNodes.length-1; i>=0; i--) {
		sel.removeChild(sel.childNodes[i]);
	}

	if (arr !== undefined) {
		//連想配列をループ処理で値を取り出してセレクトボックスにセットする
		for (var i=0; i<arr.length; i++) {
			if (i != 0 && arr[i] == '') { continue; }
			let op = document.createElement("option");
			op.value = i;  //value値
			op.text = arr[i];   //テキスト値
			document.getElementById("ship_addr").appendChild(op);
		}
	}
};

function createSelectBoxGoods(){
	var customer = document.forms.customer.value;
	//連想配列の配列
	var ar = "{{$gnames}}";
	var json = JSON.parse(unescapeHtml(ar));
	console.log(json[customer]);
	var arr = json[customer];

	// selectの初期化
	const sel = document.getElementById("goods");
	sel.disabled = (customer) ? false : true; // 非活性化
	console.log(sel.childNodes.length);
	for (var i=sel.childNodes.length-1; i>=0; i--) {
		sel.removeChild(sel.childNodes[i]);
	}

	if (arr !== undefined) {
		//連想配列をループ処理で値を取り出してセレクトボックスにセットする
		for (let goods in arr) {
			let op = document.createElement("option");
			if (goods != 0) {
				op.value = goods;  //value値
				op.text = goods + ' : ' + arr[goods];   //テキスト値
			}
			document.getElementById("goods").appendChild(op);
		}
	}
};
</script>

	<div class="row mb-3">
		<label for="shipAddr" class="col-sm-2 col-form-label">配送先　（※ 顧客の槽（タンク））</label>
		@if ($rows->class != 8 && $rows->class != 9)
			<select class="form-select w-75" aria-label="shipAddr" id="ship_addr" name="ship_addr" @if ($get->action == '') disabled @endif>
				@if ($post->customer)
					@foreach($initForm['select']['ship_addr'][$post->customer] as $i => $d)
						<option value="{{$i}}" @if ($i == $rows->ship_addr) selected @endif >{{$d}}</option>
					@endforeach
				@else
					@foreach($initForm['select']['ship_addr'][$rows->customer] as $i => $d)
						<option value="{{$i}}" @if ($i == $rows->ship_addr) selected @endif >{{$d}}</option>
					@endforeach
				@endif
			</select>
			<span id="" class="manual-text form-text">※ 「<b>品名</b>」を選択後、プルダウンが有効になります。</span>
		@else
			<input type="text" class="w-auto" id="field1" name="field1" value="{{$rows->field1}}" /><!-- ship_addr (結果入力の際は、field1に登録となる) -->
		@endif
	</div>

	<div class="row mb-3">
		<label for="shipAddrText" class="col-sm-2 col-form-label">　　　　（※ テキスト入力）</label>
		@if ($rows->class != 8 && $rows->class != 9)
			<textarea class="ship-addr-textarea" id="field1" name="field1">{{$rows->field1}}</textarea><!-- ship_addr (結果入力の際は、field1に登録となる) -->
		@endif
	</div>

	<div class="row mb-3">
		<label for="qty" class="col-sm-2 col-form-label">量(t)　<span class="badge text-bg-danger">必須</span></label>
		@if ($rows->class != 8 && $rows->class != 9 && $rows->class != 10)
			<select class="form-select w-75" aria-label="qty" id="qty" name="qty">
				@foreach($initForm['select']['qty'] as $i => $d)
					@if ($i == '0')
					<option value=""></option>
					@else
					<option value="{{$i}}" @if ($i == $rows->qty) selected @endif >{{$d}}</option>
					@endif
				@endforeach
			</select>
<!--			<div id="qtyHelp" class="form-text">量(t)を入力してください。</div>-->
		@else
			<input type="number" class="w-auto" id="qty" name="qty" min="0" max="30" step="0.5" value="{{$rows->qty}}" />
		@endif
	</div>

	<div class="">
		<label for="use_stock" class="col-sm-2 col-form-label"><!--在庫から配送--></label>
		<!--<input type="checkbox" class="col-sm-2 form-check-input" id="use_stock" name="use_stock" onchange="check_use_stock();" @if ($cur_user->roles[0] != 'administrator') disabled @endif>-->

		<input type="checkbox" class="btn-check" id="use_stock" name="use_stock" autocomplete="off" onchange="check_use_stock();" @if ($cur_user->roles[0] != 'administrator') disabled @endif>
		<label class="btn btn-outline-primary" for="use_stock">在庫から配送</label>
<br>
<br>

	</div>

	<div class="row mb-3">
		<label for="delivery_dt" class="col-sm-2 col-form-label">配送予定日　<span class="badge text-bg-danger">必須</span></label>
		<input type="date" class="col-sm-6 col-form-control w-auto" id="delivery_dt" name="delivery_dt" aria-describedby="deliveryDtHelp" value="{{$rows->delivery_dt}}" onchange="setArrivalDt(); setRepeatSDt();">
<!--		<div id="arrivalDtHelp" class="form-text">入庫予定日を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="arrival_dt" class="col-sm-2 col-form-label">入庫予定日</label>
		<input type="date" class="col-sm-6 col-form-control w-auto" id="arrival_dt" name="arrival_dt" aria-describedby="arrivalDtHelp" value="{{$rows->arrival_dt}}" @if ($cur_user->roles[0] != 'administrator') disabled @endif>
		<span id="" class="manual-text form-text">※ 入力がない場合、「配送予定日」の<b>3日前</b>の日付を自動入力します。</span>
	</div>

	<div class="row mb-3">
		<label for="outgoing_warehouse" class="col-sm-2 col-form-label">出庫倉庫　<span class="badge text-bg-danger">必須</span></label>
		<select class="form-select w-75" aria-label="outgoing_warehouse" id="outgoing_warehouse" name="outgoing_warehouse" @if ($cur_user->roles[0] != 'administrator') disabled @endif>
			@foreach($initForm['select']['outgoing_warehouse'] as $i => $d)
				@if ($i == '0')
				<option value=""></option>
				@else
				<option value="{{$i}}" @if ($i == $rows->outgoing_warehouse) selected @endif >{{$d}}</option>
				@endif
			@endforeach
		</select>
		@if ($cur_user->roles[0] != 'administrator')
			<input type="hidden" name="outgoing_warehouse" value="{{$rows->outgoing_warehouse}}" />
		@endif
	</div>

	<div class="">
		@if (($cur_user->roles[0] != 'administrator') || ($rows->class == 8 || $rows->class == 9 || $rows->class == 10))
			<label for="repeat_tmp" class="col-sm-2 col-form-label"><!--繰り返し予定を設定する--></label>
			<input type="checkbox" class="btn-check" id="repeat_tmp" name="repeat_tmp" autocomplete="off" value="" @if ($rows->repeat_fg) checked @endif disabled>
			<label class="btn btn-outline-primary" for="repeat_tmp">繰り返し予定を設定する</label>
			<input type="hidden" class="btn-check" id="repeat_fg" name="repeat_fg" value="" onload="check_repeat();" @if ($cur_user->roles[0] != 'editor') disabled @endif>
		@else
			<label for="repeat_fg" class="col-sm-2 col-form-label"><!--繰り返し予定を設定する--></label>
			<input type="checkbox" class="btn-check" id="repeat_fg" name="repeat_fg" autocomplete="off" value="" onchange="check_repeat(); checkRepeat();">
			<label class="btn btn-outline-primary" for="repeat_fg">繰り返し予定を設定する</label>
		@endif

		@if ($rows->class == 8 || $rows->class == 9 || $rows->class == 10)
		<span id="" class="manual-text form-text" style="color: red;">※ 「結果入力」した注文からの「繰り返し予定設定」はできません。</span>
		@endif
	</div>

<script>
/**
 * 「入庫予定日」の自動入力
 *    - 「配送予定日」の3日前
 **/
function setArrivalDt() {
	// 「配送予定表③」からの遷移を検知
	const page = "{{$get->page}}";
	const ref = "{{$_SESSION['sales-search']}}";
//		console.log(ref);
	var result = unescapeHtml(ref);
//		console.log(result);

	const regex = /page=delivery-graph/g;
	const ret = result.search(regex);

//	console.log('search : ' + ret);

	// 「配送予定表③」からの遷移以外の処理
	if (ret < 0) {
		console.log(page);
//		window.location = "/wp-admin/admin.php?page=" + page;

		/**
		 * 「配送予定日」の3日前の日付を自動入力する処理
		 **/
		const delivery_dt = document.getElementById('delivery_dt').value;
		//console.log('delivery_dt : ' + delivery_dt);

		const dt = new Date(delivery_dt);
		dt.setDate(dt.getDate() -3); // 3日後に設定

		const m = parseInt(dt.getMonth()) + 1; // TODO: dt.getMonth()が、なぜか月が-1減算されるため、+1で設定
		//console.log(m);
		const month = m.toString().padStart(2, "0"); // 0埋め
		//console.log(month);
		const date = dt.getDate().toString().padStart(2, "0"); // 0埋め
		//console.log(date);

		const arrival_dt = dt.getFullYear() + '-' + month + '-' + date;
		//console.log(arrival_dt);

		document.getElementById('arrival_dt').value = arrival_dt;
	}
}

/**
 * 
 **/
initCheckbox();
function initCheckbox() {
	const use_stock = '{{$rows->use_stock}}';
	if (use_stock == 'on' || use_stock == 1) {
		document.getElementById('use_stock').checked = true;
	}

	const repeat = '{{$rows->repeat_fg}}';
	if (repeat == 'on' || repeat == 1) {
		document.getElementById('repeat_fg').checked = true;
		document.getElementById('repeat_fg').value = 1; // true
	}
}

function check_use_stock() {
	if (document.getElementById('use_stock').checked) {
		document.getElementById('use_stock').value = 1; // true
	}
}

function check_repeat() {
	if (document.getElementById('repeat_fg').checked) {
		document.getElementById('repeat_fg').value = 1; // true
	}
}

/**
 * 「繰返 開始日」の自動入力
 *    - 「配送予定日」変更時
 **/
function setRepeatSDt() {
	const delivery_dt = document.getElementById('delivery_dt').value;
	document.getElementById('repeat_s_dt').value = delivery_dt;	

	const cur_user = '{{$cur_user->roles[0]}}';
	if (cur_user != 'administrator') {
		document.getElementById('disp_repeat_s_dt').value = delivery_dt;
	}
}
</script>

<style>
.manual-text {
	width: 400px;
	padding-left: 10px;
	#background: gray;
}

.ship-addr-textarea {
	width: 300px;
	height: 200px;
	padding-left: 10px;
}

.class-detail-textarea {
	width: 300px;
	height: 200px;
	padding-left: 10px;
}

.hide-area {
	display: none;
}
</style>
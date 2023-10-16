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
		<label for="customer" class="col-sm-2 col-form-label w-5">氏名</label>
		<select class="form-select w-75" aria-label="customer" id="customer" name="customer" onchange="createSelectBox(); createSelectBoxGoods();">
			@foreach($initForm['select']['customer'] as $customer => $d)
				@if ($customer == '')
				<option value=""></option>
				@else
				<option value="{{$customer}}" @if ($customer == $rows->customer) selected @endif >{{$customer}} : {{$d}}</option>
				@endif
			@endforeach
		</select>
<!--		<div id="orderName" class="form-text">氏名を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="carModel" class="col-sm-2 col-form-label">車種</label>
		<select class="form-select w-75" aria-label="carModel" id="class" name="class">
			@foreach($initForm['select']['car_model'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->class) selected @endif >{{$d}}</option>
			@endforeach
		</select>
	</div>

	<div class="row mb-3">
		<label for="carsTank" class="col-sm-2 col-form-label">槽</label>
		<select class="form-select w-75" aria-label="carsTank" id="cars_tank" name="cars_tank">
			@foreach($initForm['select']['cars_tank'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->cars_tank) selected @endif >{{$d}}</option>
			@endforeach
		</select>
	</div>

	<div class="row mb-3">
		<label for="goodsName" class="col-sm-2 col-form-label">品名</label>
		<select class="form-select w-75" aria-label="goodsName" id="goods" name="goods">
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
	console.log(customer);
	//連想配列の配列
	var ar = "{{$test_ship_addr}}";
	var json = JSON.parse(unescapeHtml(ar));
	console.log(json[customer]);
	var arr = json[customer];

	// selectの初期化
	const sel = document.getElementById("ship_addr");
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
	console.log(sel.childNodes.length);
	for (var i=sel.childNodes.length-1; i>=0; i--) {
		sel.removeChild(sel.childNodes[i]);
	}

	if (arr !== undefined) {
		//連想配列をループ処理で値を取り出してセレクトボックスにセットする
		for (let goods in arr) {
			let op = document.createElement("option");
			op.value = goods;  //value値
			if (goods != 0) {
				op.text = goods + ' : ' + arr[goods];   //テキスト値
			}
			document.getElementById("goods").appendChild(op);
		}
	}
};
</script>

	<div class="row mb-3">
		<label for="shipAddr" class="col-sm-2 col-form-label">配送先</label>
		<select class="form-select w-75" aria-label="shipAddr" id="ship_addr" name="ship_addr">
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
<!--		<div id="shipAddHelp" class="form-text">配送先を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="qty" class="col-sm-2 col-form-label">量(t)</label>
		<select class="form-select w-75" aria-label="qty" id="qty" name="qty">
			@foreach($initForm['select']['qty'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->qty) selected @endif >{{$d}}</option>
			@endforeach
		</select>
<!--		<div id="qtyHelp" class="form-text">量(t)を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="use_stock" class="col-sm-2 col-form-label">在庫から配送</label>
		<input type="checkbox" class="col-sm-2 form-check-input" id="use_stock" name="use_stock" onchange="check_use_stock();">
	</div>

	<div class="row mb-3">
		<label for="arrival_dt" class="col-sm-2 col-form-label">入庫予定日</label>
		<input type="date" class="col-sm-6 col-form-control w-auto" id="arrival_dt" name="arrival_dt" aria-describedby="arrivalDtHelp" value="{{$rows->arrival_dt}}">
<!--		<div id="arrivalDtHelp" class="form-text">入庫予定日を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="delivery_dt" class="col-sm-2 col-form-label">配送予定日</label>
		<input type="date" class="col-sm-6 col-form-control w-auto" id="delivery_dt" name="delivery_dt" aria-describedby="deliveryDtHelp" value="{{$rows->delivery_dt}}">
<!--		<div id="arrivalDtHelp" class="form-text">入庫予定日を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="outgoing_warehouse" class="col-sm-2 col-form-label">出庫倉庫</label>
		<select class="form-select w-75" aria-label="outgoing_warehouse" id="outgoing_warehouse" name="outgoing_warehouse">
			@foreach($initForm['select']['outgoing_warehouse'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->outgoing_warehouse) selected @endif >{{$d}}</option>
			@endforeach
		</select>
	</div>

	<div class="row mb-3">
		<label for="repeat_fg" class="col-sm-2 col-form-label">繰り返し予定を設定する</label>
<!--		<input type="checkbox" class="col-sm-2 form-check-input" id="repeat" name="repeat" onchange="changeCheckBox('repeat') && checkRepeat();">-->
		<input type="checkbox" class="col-sm-2 form-check-input" id="repeat_fg" name="repeat_fg" onchange="check_repeat();">
	</div>

<br /><br /><hr>

<script>
initCheckbox();
function initCheckbox() {
	const use_stock = '{{$rows->use_stock}}';
	if (use_stock == 'on' || use_stock == 1) {
		document.getElementById('use_stock').checked = true;
	}

	const repeat = '{{$rows->repeat_fg}}';
	if (repeat == 'on' || repeat == 1) {
		document.getElementById('repeat_fg').checked = true;
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
</script>
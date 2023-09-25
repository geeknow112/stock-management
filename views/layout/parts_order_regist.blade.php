	<p class="">
	<legend>【注文登録】</legend>
	</p>

	@if ($get->action != '')
	<div class="row mb-3">
		<label for="sales" class="col-sm-2 col-form-label w-5">注文番号</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="sales" name="sales" aria-describedby="salesHelp" value="{{$rows->sales}}" readonly>
	</div>
	@endif

	<div class="row mb-3">
		<label for="order_name" class="col-sm-2 col-form-label w-5">氏名</label>
		<select class="form-select w-75" aria-label="order_name" id="order_name" name="order_name">
			@foreach($initForm['select']['order_name'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->name) selected @endif >{{$d}}</option>
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
		<label for="goodsName" class="col-sm-2 col-form-label">品名</label>
		<select class="form-select w-75" aria-label="goodsName" id="goods" name="goods">
			@foreach($initForm['select']['goods_name'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->goods) selected @endif >{{$d}}</option>
			@endforeach
		</select>
	</div>

	<div class="row mb-3">
		<label for="shipAddr" class="col-sm-2 col-form-label">配送先</label>
		<select class="form-select w-75" aria-label="shipAddr" id="ship_addr" name="ship_addr">
			@foreach($initForm['select']['ship_addr'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->ship_addr) selected @endif >{{$d}}</option>
			@endforeach
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
		<input type="checkbox" class="col-sm-2 form-check-input" id="use_stock">
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
		<select class="form-select w-75" aria-label="outgoing_warehouse">
			@foreach($initForm['select']['outgoing_warehouse'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->outgoing_warehouse) selected @endif >{{$d}}</option>
			@endforeach
		</select>
	</div>

	<div class="row mb-3">
		<label for="repeat" class="col-sm-2 col-form-label">繰り返し予定を設定する</label>
		<input type="checkbox" class="col-sm-2 form-check-input" id="repeat" onclick="checkRepeat();">
	</div>

<br /><br /><hr>

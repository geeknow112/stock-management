	<p class="">
	<legend>【商品登録】</legend>
	</p>

	@if ($get->action != '' && $get->action != 'save')
	<div class="row mb-3">
		<label for="goods" class="col-sm-2 col-form-label">商品番号</label>
		<input type="text" class="col-sm-2 col-form-control" id="goods" name="goods" aria-describedby="goodsHelp" value="{{$rows->id}}" readonly>
	</div>
	@endif

	<div class="row mb-3">
		<label for="goodsName" class="col-sm-2 col-form-label">商品名</label>
		<input type="text" class="col-sm-2 col-form-control" id="goods_name" name="goods_name" aria-describedby="goodsNameHelp" value="{{$rows->name}}">
<!--		<div id="orderName" class="form-text">商品名を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="qty" class="col-sm-2 col-form-label">内容量(t)</label>
		<input type="text" class="col-sm-2 col-form-control" id="qty" name="qty" aria-describedby="qtyHelp" value="{{$rows->qty}}">
	</div>

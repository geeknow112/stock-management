	<p class="">
	<legend>【商品登録】</legend>
	</p>

{{--	@if ($get->action != '' && $get->action != 'save')--}}
	<div class="row mb-3">
		<label for="goods" class="col-sm-2 col-form-label w-5">商品番号</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="goods" name="goods" aria-describedby="goodsHelp" value="{{$rows->goods}}" readonly>
	</div>
{{--	@endif--}}

	<div class="row mb-3">
		<label for="goods_name" class="col-sm-2 col-form-label w-5">商品名</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="goods_name" name="goods_name" aria-describedby="goodsNameHelp" value="{{$rows->goods_name}}" @if ($get->action != '' && $get->action != 'save' && $get->action != 'edit') readonly @endif>
<!--		<div id="orderName" class="form-text">商品名を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="qty" class="col-sm-2 col-form-label w-5">内容量(t)</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="qty" name="qty" aria-describedby="qtyHelp" value="{{$rows->qty}}" @if ($get->action != '' && $get->action != 'save' && $get->action != 'edit') readonly @endif>
	</div>

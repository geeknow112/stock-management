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

	<div class="">
		<label for="separately_fg" class="col-sm-2 col-form-label"><!--バラ売り--></label>
		<input type="checkbox" class="btn-check" id="separately_fg" name="separately_fg" autocomplete="off" value="" onchange="check_separately();">
		<label class="btn btn-outline-primary" for="separately_fg">バラ売り</label>
		<br /><br />
	</div>

	<div class="row mb-3">
		<label for="remark" class="col-sm-2 col-form-label w-5">備考</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="remark" name="remark" aria-describedby="remarkHelp" value="{{$rows->remark}}" @if ($get->action != '' && $get->action != 'save' && $get->action != 'edit') readonly @endif>
	</div>

<script>
/**
 * 確認画面でform要素をreadOnlyにする
 *
 **/
window.onload = function() {
	const action = "{{$get->action}}";
	if (action == 'confirm') {
		document.getElementById('goods').readOnly = true;
		document.getElementById('goods_name').readOnly = true;
		document.getElementById('qty').readOnly = true;
	}

	const separately = '{{$rows->separately_fg}}';
	if (separately == 'on' || separately == 1) {
		document.getElementById('separately_fg').checked = true;
		document.getElementById('separately_fg').value = 1; // true
	}
}

function check_separately() {
	const separately = document.getElementById('separately_fg');
	if (separately.checked) {
		separately.value = 1; // true
	} else {
		separately.value = 0; // false
	}
}
</script>
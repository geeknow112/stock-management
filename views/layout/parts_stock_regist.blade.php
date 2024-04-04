	<p class="">
	<legend>【在庫登録】</legend>
	</p>

{{--	@if ($get->action != '')	--}}
	<div class="row mb-3">
		<label for="stock" class="col-sm-2 col-form-label w-5">在庫番号</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="stock" name="stock" aria-describedby="stockHelp" value="{{$rows->stock}}" readonly>
	</div>
{{--	@endif	--}}

	<div class="row mb-3">
		<label for="goodsName" class="col-sm-2 col-form-label">品名　<span class="badge text-bg-danger">必須</span></label>
		<select class="form-select w-75" aria-label="goodsName" id="goods" name="goods" @if ($get->action == '') disabled @endif>
			@foreach($initForm['select']['goods_name'] as $i => $d)
				<option value="{{$i}}" @if ($i == $rows->goods) selected @endif >{{$i}} : {{$d}}</option>
			@endforeach
		</select>
	</div>

	<div class="row mb-3">
		<label for="qty" class="col-sm-2 col-form-label">量(kg)　</label>
		500
	</div>

	<div class="row mb-3">
		<label for="goods_total" class="col-sm-2 col-form-label">個数　<span class="badge text-bg-danger">必須</span></label>
		<input type="number" min="0" class="tx-right w-auto" id="goods_total" name="goods_total" value="{{$rows->goods_total}}" onchange="calcWeight();" @if($get->action != '' && $get->action != 'save' && $get->action != 'edit') readonly @endif>
	</div>

	<div class="row mb-3">
		<label for="subtotal" class="col-sm-2 col-form-label">合計量(kg)　</label>
		<input type="text" min="0" class="tx-right w-auto" id="subtotal" name="subtotal" value="{{number_format($rows->subtotal)}}" readonly>
	</div>

	<div class="row mb-3">
		<label for="arrival_dt" class="col-sm-2 col-form-label">入庫予定日</label>
		<input type="date" class="col-sm-6 col-form-control w-auto" id="arrival_dt" name="arrival_dt" aria-describedby="arrivalDtHelp" value="{{$rows->arrival_dt}}">
	</div>

	<div class="row mb-3">
		<label for="warehouse" class="col-sm-2 col-form-label">倉庫　<span class="badge text-bg-danger">必須</span></label>
		<select class="form-select w-75" aria-label="warehouse" id="warehouse" name="warehouse">
			@foreach($initForm['select']['outgoing_warehouse'] as $i => $d)
				@if ($i == '0')
				<option value=""></option>
				@else
				<option value="{{$i}}" @if ($i == $rows->warehouse) selected @endif >{{$d}}</option>
				@endif
			@endforeach
		</select>
		<input type="hidden" id="outgoing_warehouse" name="outgoing_warehouse" value="{{$rows->warehouse}}" />
	</div>

<script>
/**
 * 確認画面でform要素をreadOnlyにする
 *
 **/
window.onload = function() {
	const action = "{{$get->action}}";
	if (action == 'confirm') {

		document.getElementById('arrival_dt').readOnly = true;

	}

	if (action == 'complete') {

		document.getElementById('arrival_dt').readOnly = true;
//		document.getElementById('text_outgoing_warehouse').readOnly = true;

	}
}

/**
 * 重量の計算
 * 
 * 「個数」* 500(kg) = 重量(kg)
 **/
function calcWeight() {
	const qty = document.getElementById('goods_total').value;
	const weight = qty * 500;
	document.getElementById('subtotal').value = weight.toLocaleString(); // 3桁カンマ区切り
}

/**
 * 
 **/
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
</script>

<style>
.manual-text {
	width: 400px;
	padding-left: 10px;
	#background: gray;
}
</style>
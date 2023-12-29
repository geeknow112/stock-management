	<p class="">
	<legend>【転送処理】</legend>
	</p>

	<div class="row mb-3">
		<label for="arrival_dt" class="col-sm-2 col-form-label">入庫予定日</label>
		<input type="date" class="col-sm-6 col-form-control w-auto" id="arrival_dt" name="arrival_dt" aria-describedby="arrivalDtHelp" value="{{$rows->arrival_dt}}" @if($get->action == 'confirm') readonly @endif>
	</div>


<br />
<hr>
<div id="wpbody-content">
	<div class="wrap">
		<div class="tablenav top">
			<br class="clear">
		</div>
		
	<div class="table-responsive">
		<div id="title">■ 転送処理</div>
		<div>※ 基本情報を入力後、ロット番号へのリンクが表示されます。</div>
		<div>
			<table class="table table-bordered text-nowrap">
				<thead class="table-light">
					<tr>
						<th class=""></th>
						<th class="">品名</th>
						<th class="">荷姿・容量(kgTB)</th>
						<th class="">個数</th>
						<th class="">数量(kg)</th>
						<th class="">出庫倉庫</th>
						<th class="">入庫倉庫</th>
						<th class="">ロット番号</th>
					</tr>
				</thead>

				<tbody id="the-list" data-wp-lists="list:user">
					<input type="hidden" id="sales" name="sales" value="{{$get->sales}}">
					<input type="hidden" id="goods" name="goods" value="{{$get->goods}}">
					@for ($i = 0; $i < 5; $i++)
					<tr id="user-1">
						<td class="">&emsp;</td>
						<td class="">
							@if(!$rows->goods_list)
							<select class="form-select w-75" aria-label="goodsName" id="goods_{{$i}}" name="goods_list[]">
								@foreach($initForm['select']['goods_name'] as $goods => $gname)
									<option value="{{$goods}}">{{$goods}} : {{$gname}}</option>
								@endforeach
							</select>
							@else
								<input type="text" id="text_goods_list_{{$i}}" name="text_goods_list[{{$i}}]" value="{{$rows->goods_list[$i]}} : {{$initForm['select']['goods_name'][$rows->goods_list[$i]]}}" />
								<input type="hidden" name="goods_list[]" value="{{$rows->goods_list[$i]}}" />
							@endif
						</td>
						<td class="tx-right">500</td>
						<td class="tx-right"><input type="number" min="0" class="tx-center w-50" id="qty_{{$i}}" name="qty_list[]" value="{{$rows->qty_list[$i]}}" onchange="calcWeight({{$i}}); sumRows();"></td>
						<td class="tx-right"><input type="text" class="tx-right w-75" id="weight_{{$i}}" name="weight_list[]" value="{{$rows->weight_list[$i]}}" readonly></td>
						<td class="tx-right">
							<select class="form-select w-75" id="outgoing_warehouse_{{$i}}" name="outgoing_warehouse[]" onchange="setReceiveWarehouse({{$i}});">
								<option value=""></option>
								<option value="1" @if ($rows->outgoing_warehouse[$i] == 1) selected @endif>内藤SP</option>
								<option value="2" @if ($rows->outgoing_warehouse[$i] == 2) selected @endif>丹波SP</option>
							</select>
						</td>
						<td class="tx-right"><input type="text" class="tx-right w-75" id="text_receive_warehouse_{{$i}}" name="text_receive_warehouse[]" value="{{$rows->text_receive_warehouse[$i]}}" readonly></td>
						<input type="hidden" id="receive_warehouse_{{$i}}" name="receive_warehouse[]" value="{{$rows->receive_warehouse[$i]}}">
						<td class="tx-right">@if($get->stock)<a href="/wp-admin/admin.php?page=stock-lot-regist&stock={{$get->stock}}">入力画面へ</a>@else - @endif</td>
						</td>
					</tr>
					@endfor
				</tbody>

				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>
	<!-- end -->
</div>



<script>
function set_trunsfer() {
	document.getElementById('cmd').value = 'cmd_transfer';
}

/**
 * 確認画面でform要素をreadOnlyにする
 *
 **/
window.onload = function() {
	const action = "{{$get->action}}";
	if (action == 'confirm') {

//		document.getElementById('text_outgoing_warehouse').readOnly = true;

		for (let i = 0; i < 5; i++) {
				document.getElementById('text_goods_list_' + i).readOnly = true;
				document.getElementById('qty_' + i).readOnly = true;
		}
	}

	if (action == 'complete') {

		document.getElementById('arrival_dt').readOnly = true;
		document.getElementById('text_outgoing_warehouse').readOnly = true;

		for (let i = 0; i < 5; i++) {
				document.getElementById('text_goods_list_' + i).readOnly = true;
				document.getElementById('qty_' + i).readOnly = true;
		}
	}
}

/**
 * 重量の計算
 * 
 * 「個数」* 500(kg) = 重量(kg)
 **/
function calcWeight(num = null) {
	const obj_qty = "qty_" + num;
	const obj_weight = "weight_" + num;
	const qty = document.getElementById(obj_qty).value;
	const weight = qty * 500;
	document.getElementById(obj_weight).value = weight.toLocaleString(); // 3桁カンマ区切り
}

/**
 * 列の集計
 * 
 * 「個数」列の集計
 **/
function sumRows() {
	const qtys = [];

	for (let i = 0; i < 20; i++) {
			var q = parseInt(document.getElementById('qty_' + i).value);
			if (!isNaN(q)) { qtys.push(q); }
	}

	let sum_qty = qtys.reduce(function(a, b) {
	  return a + b;
	});

	console.log(qtys);
	console.log(sum_qty);

	document.getElementById('sum_qty').value = sum_qty;
	const sum_weight = sum_qty * 500;
	document.getElementById('sum_weight').value = sum_weight.toLocaleString(); // 3桁カンマ区切り

}

/**
 * 入庫倉庫の表示 : 「転送処理」用
 * 
 * 「出庫倉庫」の反対の倉庫を表示
 **/
function setReceiveWarehouse(num = null) {
	const out_sp = document.getElementById('outgoing_warehouse_' + num).value;
	var rec_sp = '';
	switch (out_sp) {
		default :
			rec_sp = '';
			break;
		case '1' :
			rec_sp = '2';
			break;
		case '2' :
			rec_sp = '1';
			break;
	}
//{{$initForm['select']['outgoing_warehouse'][$rows->receive_warehouse[$i]]}}

	const whs = ['', '内藤SP', '丹波SP'];
	document.getElementById('receive_warehouse_' + num).value = rec_sp;
	document.getElementById('text_receive_warehouse_' + num).value = whs[rec_sp];
}
</script>

<style>
.tx-right {
	text-align: right;
}

.tx-center {
	text-align: center;
}
</style>
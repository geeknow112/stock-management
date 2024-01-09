	<p class="">
	<legend>【在庫登録】</legend>
	</p>

	<div class="row mb-3">
		<label for="arrival_dt" class="col-sm-2 col-form-label">入庫予定日</label>
		<input type="date" class="col-sm-6 col-form-control w-auto" id="arrival_dt" name="arrival_dt" aria-describedby="arrivalDtHelp" value="{{$rows->arrival_dt}}" @if($get->action == 'confirm') readonly @endif>
	</div>

	<div class="row mb-3">
		<label for="outgoing_warehouse" class="col-sm-2 col-form-label">出庫倉庫</label>
		@if($get->action != 'confirm' && $get->action != 'complete')
		<select class="form-select w-75" aria-label="outgoing_warehouse" id="outgoing_warehouse" name="outgoing_warehouse" onchange="cahngeTitleWarehouse();">
			@foreach($initForm['select']['outgoing_warehouse'] as $i => $d)
				@if ($i == '0')
				<option value=""></option>
				@else
				<option value="{{$i}}" @if ($i == $rows->outgoing_warehouse) selected @endif >{{$d}}</option>
				@endif
			@endforeach
		</select>
		@else
		<input type="text" class="col-sm-6 col-form-control w-auto" id="text_outgoing_warehouse" name="text_outgoing_warehouse" value="{{$initForm['select']['outgoing_warehouse'][$rows->outgoing_warehouse]}}">
		<input type="hidden" id="outgoing_warehouse" name="outgoing_warehouse" value="{{$rows->outgoing_warehouse}}">
		@endif
	</div>

<br />
<hr>
<div id="wpbody-content">
	<div class="wrap">
		<div class="tablenav top">
			<br class="clear">
		</div>
		
		<div class="table-responsive">
			<div id="title_warehouse">■ </div>
			<div>※ 基本情報を入力後、ロット番号へのリンクが表示されます。</div>
			<div>
				<table class="table table-bordered text-nowrap">
					<thead class="table-light">
						<tr>
							<th class="">在庫ID</th>
							<th class="">品名</th>
							<th class="">容量(kg)</th>
							<th class="">個数</th>
							<th class="">重量(kg)</th>
							<th class="">ロット番号</th>
						</tr>
					</thead>

					<tbody id="the-list" data-wp-lists="list:user">
						<input type="hidden" id="pre_cmd" name="pre_cmd" value="{{$post->cmd}}">
						@for($i = 0; $i<20; $i++)
						<tr>
							<td class="">{{$rows->stock_list[$i]}}</td>
							<input type="hidden" id="stock_list" name="stock_list[]" value="{{$rows->stock_list[$i]}}">
							<td class="">
								@if(!$rows->goods_list)
								<select class="form-select w-75" aria-label="goodsName" id="goods_{{$i}}" name="goods_list[]">
									@foreach($initForm['select']['goods_name'] as $goods => $gname)
										<option value="{{$goods}}">{{$goods}} : {{$gname}}</option>
									@endforeach
								</select>
								@else
<!--									<input type="text" id="text_goods_list_{{$i}}" name="text_goods_list_{{$i}}" value="{{$rows->goods_list[$i]}} : {{$initForm['select']['goods_name'][$rows->goods_list[$i]]}}" />
									<input type="hidden" name="goods_list[]" value="{{$rows->goods_list[$i]}}" />
-->
								<select class="form-select w-75" aria-label="goodsName" id="goods_{{$i}}" name="goods_list[]">
									@foreach($initForm['select']['goods_name'] as $goods => $gname)
										<option value="{{$goods}}" @if($goods == $rows->goods_list[$i]) selected @endif>{{$goods}} : {{$gname}}</option>
									@endforeach
								</select>
								@endif
							</td>
							<td class="tx-right">500</td>
							<td class="tx-right"><input type="number" min="0" class="tx-center w-50" id="qty_{{$i}}" name="qty_list[]" value="{{$rows->qty_list[$i]}}" onchange="calcWeight({{$i}}); sumRows();" @if($get->action != '' && $get->action != 'save' && $get->action != 'edit') readonly @endif></td>
							<td class="tx-right"><input type="text" class="tx-right w-75" id="weight_{{$i}}" name="weight_list[]" value="{{$rows->weight_list[$i]}}" readonly></td>
							<td class="tx-right">
								@if(!$rows->stock_list[$i] || $get->action == 'confirm')
								 - 
								@else
									<a href="/wp-admin/admin.php?page=stock-lot-regist&stock={{$rows->stock_list[$i]}}&goods={{$rows->goods_list[$i]}}&arrival_dt={{$get->arrival_dt}}&warehouse={{$get->warehouse}}">入力画面へ</a>
								@endif
							</td>
						</tr>
						@endfor
					</tbody>

					<tfoot>
						<tr>
							<th class="">&emsp;</th>
							<th class="">&emsp;</th>
							<th class="">&emsp;</th>
							<th class="">&emsp;</th>
							<th class="">&emsp;</th>
							<th class="">&emsp;</th>
						</tr>
						<tr>
							<th class="" colspan="2"></th>
							<th class="table-light">合計</th>
							<td class="tx-right"><input type="text" class="tx-center w-50" id="sum_qty" name="sum_qty" value="{{$rows->sum_qty}}" readonly></td>
							<td class="tx-right"><input type="text" class="tx-right w-75" id="sum_weight" name="sum_weight" value="{{$rows->sum_weight}}" readonly></td>
							<td class="">&emsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>

<!--
			<div class="row mb-3" id="customerTankRow">
				@if ($rows_tanks)
					@foreach($rows_tanks as $i => $d)
							<div>
							<label class="col-sm-2 col-form-label w-5">配送先 槽（タンク）: {{$d->detail}}</label>
							<input type="text" class="col-sm-2 col-form-control w-auto" id="tank_{{$i}}" name="tank[]" aria-describedby="tankHelp" value="{{$d->tank}}">&emsp;
							@if ($i == $rows_tanks_count - 1)
							<input type="button" class="col-sm-2 col-form-control w-auto" id="add_tank_{{$rows_tanks_count}}" name="add_tank_{{$rows_tanks_count}}" value="追加" onclick="addCustomerTankRow({{$rows_tanks_count}})">
							@endif
							</div>
					@endforeach
				@else
					<div>
						<label class="col-sm-2 col-form-label w-5">配送先 槽（タンク）: 新規登録 </label>
						<input type="text" class="col-sm-2 col-form-control w-auto" id="tank_0" name="tank[]" aria-describedby="tankHelp" value="">&emsp;
						<input type="button" class="col-sm-2 col-form-control w-auto" id="add_tank_0" name="add_tank_0" value="追加" onclick="addCustomerTankRow(0)">
					</div>
				@endif
			</div>
-->
		</div>
	</div>

<script>
/**
 * 確認画面でform要素をreadOnlyにする
 *
 **/
window.onload = function() {
	const action = "{{$get->action}}";
	if (action == 'confirm') {

		document.getElementById('text_outgoing_warehouse').readOnly = true;

		for (let i = 0; i < 20; i++) {
				document.getElementById('text_goods_list_' + i).readOnly = true;
				document.getElementById('qty_' + i).readOnly = true;
		}
	}

	if (action == 'complete') {

		document.getElementById('arrival_dt').readOnly = true;
		document.getElementById('text_outgoing_warehouse').readOnly = true;

		for (let i = 0; i < 20; i++) {
				document.getElementById('text_goods_list_' + i).readOnly = true;
				document.getElementById('qty_' + i).readOnly = true;
		}
	}
}

/**
 * 
 **/
function cahngeTitleWarehouse() {
	var warehouse = document.getElementById('outgoing_warehouse').value;

	switch (warehouse) {
		case '1' :
			var title = '■ 内藤SP';
			break;

		default :
		case '2' :
			var title = '■ 丹波SP';
			break;
	}
	document.getElementById('title_warehouse').innerHTML = title;
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
 * addCustomerTankRow: テーブルに行を追加
 **/
function addCustomerTankRow(cnt = null)
{
	const cRow = document.getElementById("customerTankRow");
	if (!cRow) return;

	cnt = parseInt(cnt) + 1;
	console.log(cnt);

	cRow.innerHTML += '<div id="addRow' + cnt + '"></div>';

	const addRow = document.getElementById("addRow" + cnt);
	addRow.innerHTML += '	<label class="col-sm-2 col-form-label w-5" id="label_' + cnt + '">配送先 槽（タンク）: ' + cnt + '</label>';
	addRow.innerHTML += '	<input type="text" class="col-sm-2 col-form-control w-auto" id="tank_' + cnt + '" name="tank[]" aria-describedby="tankHelp" value="">&emsp;';
	addRow.innerHTML += '	<input type="button" class="col-sm-2 col-form-control w-auto" id="add_tank_' + cnt + '" name="add_tank_' + cnt + '" value="追加" onclick="addCustomerTankRow(' + cnt + ')">&emsp;';

	did = parseInt(cnt) - 1;
	console.log(did);
	document.getElementById("add_tank_" + did).remove();
}

/**
 * addCustomerAddrRow: テーブルに行を追加
 **/
function addCustomerAddrRow(cnt = null)
{
	const cRow = document.getElementById("customerAddrRow");
	if (!cRow) return;

	cnt = parseInt(cnt) + 1;
	console.log(cnt);

	cRow.innerHTML += '<div id="addRow' + cnt + '"></div>';

	const addRow = document.getElementById("addRow" + cnt);
	addRow.innerHTML += '	<label class="col-sm-2 col-form-label w-5" id="label_' + cnt + '">住所: ' + cnt + '</label>';
	addRow.innerHTML += '	<input type="text" class="col-sm-2 col-form-control w-auto" id="pref_' + cnt + '" name="pref[]" aria-describedby="prefHelp" value="">&emsp;';
	addRow.innerHTML += '	<input type="text" class="col-sm-2 col-form-control w-auto" id="addr1_' + cnt + '" name="addr1[]" aria-describedby="addr1Help" value="">&emsp;';
	addRow.innerHTML += '	<input type="text" class="col-sm-2 col-form-control w-auto" id="addr2_' + cnt + '" name="addr2[]" aria-describedby="addr2Help" value="">&emsp;';
	addRow.innerHTML += '	<input type="text" class="col-sm-2 col-form-control w-auto" id="addr3_' + cnt + '" name="addr3[]" aria-describedby="addr3Help" value="">&emsp;';
//	addRow.innerHTML += '	<input type="button" class="col-sm-2 col-form-control w-auto" id="del' + cnt + '" name="del' + cnt + '" value="削除" onclick="delCustomerAddrRow(' + cnt + ')">&emsp;';
	addRow.innerHTML += '	<input type="button" class="col-sm-2 col-form-control w-auto" id="add' + cnt + '" name="add' + cnt + '" value="追加" onclick="addCustomerAddrRow(' + cnt + ')">&emsp;';

	did = parseInt(cnt) - 1;
	console.log(did);
	document.getElementById("add" + did).remove();
}

/**
 * delCustomerAddrRow: 削除ボタン該当行を削除
 **/
function delCustomerAddrRow(cnt)
{
    // 確認
	if (!confirm("この行を削除しますか？")) { return; }
//	const cAddr = document.getElementById("customerAddr");

	var e = [];
//	e.push(document.getElementById("customerAddr"));
	e.push(document.getElementById("label_" + cnt));
	e.push(document.getElementById("pref_" + cnt));
	e.push(document.getElementById("addr1_" + cnt));
	e.push(document.getElementById("addr2_" + cnt));
	e.push(document.getElementById("addr3_" + cnt));
	e.push(document.getElementById("add" + cnt));
	e.push(document.getElementById("del" + cnt));

//	if (!cAddr) { return; }
//console.log(cAddr);

//	if (!obj) { return; }
//console.log(obj);

	//console.log(e);
	e.forEach((element) => element.remove());
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
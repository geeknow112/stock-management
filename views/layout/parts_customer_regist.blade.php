	<p class="">
	<legend>【顧客登録】</legend>
	</p>

{{--	@if ($get->action != '')	--}}
	<div class="row mb-3">
		<label for="customer" class="col-sm-2 col-form-label w-5">顧客番号</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="customer" name="customer" aria-describedby="customerHelp" value="{{$rows->customer}}" readonly>
	</div>
{{--	@endif	--}}

	<div class="row mb-3">
		<label for="customer_name" class="col-sm-2 col-form-label w-5">顧客名</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="customer_name" name="customer_name" aria-describedby="customerNameHelp" value="{{$post->customer_name}}">
<!--		<div id="orderName" class="form-text">顧客名を入力してください。</div>-->
	</div>

<br />
<hr>
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

<!--
<br />
<hr>
	<div class="row mb-3" id="customerAddrRow">
	@if ($rows_addrs)
		@foreach($rows_addrs as $i => $d)
				<div>
				<label class="col-sm-2 col-form-label w-5">住所: {{$d->detail}}</label>
				<input type="text" class="col-sm-2 col-form-control w-auto" id="pref_{{$i}}" name="pref[]" aria-describedby="prefHelp" value="{{$d->pref}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control w-auto" id="addr1_{{$i}}" name="addr1[]" aria-describedby="addr1Help" value="{{$d->addr1}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control w-auto" id="addr2_{{$i}}" name="addr2[]" aria-describedby="addr2Help" value="{{$d->addr2}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control w-auto" id="addr3_{{$i}}" name="addr3[]" aria-describedby="addr3Help" value="{{$d->addr3}}">&emsp;
				@if ($i == $rows_addrs_count - 1)
				<input type="button" class="col-sm-2 col-form-control w-auto" id="add{{$rows_addrs_count}}" name="add{{$rows_addrs_count}}" value="追加" onclick="addCustomerAddrRow({{$rows_addrs_count}})">
				@endif
				</div>
		@endforeach
	@else
		<div>
			<label class="col-sm-2 col-form-label w-5">住所: 新規登録 </label>
			<input type="text" class="col-sm-2 col-form-control w-auto" id="pref_0" name="pref[]" aria-describedby="prefHelp" value="">&emsp;
			<input type="text" class="col-sm-2 col-form-control w-auto" id="addr1_0" name="addr1[]" aria-describedby="addr1Help" value="">&emsp;
			<input type="text" class="col-sm-2 col-form-control w-auto" id="addr2_0" name="addr2[]" aria-describedby="addr2Help" value="">&emsp;
			<input type="text" class="col-sm-2 col-form-control w-auto" id="addr3_0" name="addr3[]" aria-describedby="addr3Help" value="">&emsp;
			<input type="button" class="col-sm-2 col-form-control w-auto" id="add0" name="add0" value="追加" onclick="addCustomerAddrRow(0)">
		</div>
	@endif
	</div>
-->

<script>
/**
 * 確認画面でform要素をreadOnlyにする
 *
 **/
window.onload = function() {
	const action = "{{$get->action}}";
	if (action == 'confirm') {
		document.getElementById('customer').readOnly = true;
		document.getElementById('customer_name').readOnly = true;
	}
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

<br />
<hr>
	@if ($goods_list)
		<div class="row mb-3">
			<div>
			<label class="col-sm-2 col-form-label w-5">商品:</label>
			@foreach($goods_list as $goods => $goods_name)
				<label>
				@if (!is_null($cust_goods))
				<input type="checkbox" class="" id="goods" name="goods_s[]" aria-describedby="prefHelp" value="{{$goods}}" @if (in_array($goods, $cust_goods)) checked @endif>{{$goods_name}}&emsp;
				@else
				<input type="checkbox" class="" id="goods" name="goods_s[]" aria-describedby="prefHelp" value="{{$goods}}">{{$goods_name}}&emsp;
				@endif
				</label>
				@if ($loop->index != 0 && $loop->index % 5 == 0)
					<br />
					<label class="col-sm-2 col-form-label w-5">&emsp;</label>
				@endif
			@endforeach
			</div>
		</div>
	@else
		<div class="row mb-3">
			<label class="col-sm-2 col-form-label">商品: </label>
			商品がありません。
		</div>
	@endif

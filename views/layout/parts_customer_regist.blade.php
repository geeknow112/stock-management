	<p class="">
	<legend>【顧客登録】</legend>
	</p>

	@if ($get->action != '')
	<div class="row mb-3">
		<label for="customer" class="col-sm-2 col-form-label w-5">顧客番号</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="customer" name="customer" aria-describedby="customerHelp" value="{{$post->customer}}" readonly>
	</div>
	@endif

	<div class="row mb-3">
		<label for="customer_name" class="col-sm-2 col-form-label w-5">顧客名</label>
		<input type="text" class="col-sm-2 col-form-control w-auto" id="customer_name" name="customer_name" aria-describedby="customerNameHelp" value="{{$post->customer_name}}">
<!--		<div id="orderName" class="form-text">顧客名を入力してください。</div>-->
	</div>

<br />
<hr>
	@if ($rows)
		@foreach($rows as $i => $d)
			<div class="row mb-3" id="customerAddrRow">
				<label class="col-sm-2 col-form-label w-5">住所: {{$d->detail}}</label>
				<input type="text" class="col-sm-2 col-form-control w-auto" id="pref_{{$i}}" name="pref[]" aria-describedby="prefHelp" value="{{$d->pref}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control w-auto" id="addr1_{{$i}}" name="addr1[]" aria-describedby="addr1Help" value="{{$d->addr1}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control w-auto" id="addr2_{{$i}}" name="addr2[]" aria-describedby="addr2Help" value="{{$d->addr2}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control w-auto" id="addr3_{{$i}}" name="addr3[]" aria-describedby="addr3Help" value="{{$d->addr3}}">&emsp;
				<input type="button" class="col-sm-2 col-form-control w-auto" id="add" name="add" value="追加" onclick="addCustomerAddrRow({{$i}})">
				<input type="hidden" id="cnt" name="cnt" value="">
			</div>
		@endforeach
	@else
		<div class="row mb-3">
			<label class="col-sm-2 col-form-label w-5">住所: 追加</label>
			<input type="text" class="col-sm-2 col-form-control w-auto" id="pref_0" name="pref[]" aria-describedby="prefHelp" value="">&emsp;
			<input type="text" class="col-sm-2 col-form-control w-auto" id="addr1_0" name="addr1[]" aria-describedby="addr1Help" value="">&emsp;
			<input type="text" class="col-sm-2 col-form-control w-auto" id="addr2_0" name="addr2[]" aria-describedby="addr2Help" value="">&emsp;
			<input type="text" class="col-sm-2 col-form-control w-auto" id="addr3_0" name="addr3[]" aria-describedby="addr3Help" value="">&emsp;
		</div>
	@endif

<script>
/**
 * addCustomerAddrRow: テーブルに行を追加
 **/
function addCustomerAddrRow()
{
	const cRow = document.getElementById("customerAddrRow");
	if (!cRow) return;

	const oCnt = document.getElementById("cnt");
	cnt = (oCnt.value) ? oCnt.value : 5;
	cnt = parseInt(cnt) + 1;
	console.log(cnt);
console.log(document.getElementById("customerAddr"));

	cRow.innerHTML += '<div class="row mb-3" id="customerAddr" name="customerAddr[]">';
	cRow.innerHTML += '	<label class="col-sm-2 col-form-label w-5" id="label_1">住所: {{$d->detail}}</label>';
	cRow.innerHTML += '	<input type="text" class="col-sm-2 col-form-control w-auto" id="pref_1" name="pref[]" aria-describedby="prefHelp" value="{{$d->pref}}">&emsp;';
	cRow.innerHTML += '	<input type="text" class="col-sm-2 col-form-control w-auto" id="addr1_1" name="addr1[]" aria-describedby="addr1Help" value="{{$d->addr1}}">&emsp;';
	cRow.innerHTML += '	<input type="text" class="col-sm-2 col-form-control w-auto" id="addr2_1" name="addr2[]" aria-describedby="addr2Help" value="{{$d->addr2}}">&emsp;';
	cRow.innerHTML += '	<input type="text" class="col-sm-2 col-form-control w-auto" id="addr3_1" name="addr3[]" aria-describedby="addr3Help" value="{{$d->addr3}}">&emsp;';
	cRow.innerHTML += '	<input type="button" class="col-sm-2 col-form-control w-auto" id="delBtn1" name="" value="削除" onclick="delCustomerAddrRow(this)">&emsp;';
	cRow.innerHTML += '</div>';

	oCnt.value = cnt;

}

/**
 * delCustomerAddrRow: 削除ボタン該当行を削除
 **/
function delCustomerAddrRow(obj)
{
    // 確認
	if (!confirm("この行を削除しますか？")) { return; }
	const cAddr = document.getElementById("customerAddr");

	var e = [];
	e.push(document.getElementById("customerAddr"));
	e.push(document.getElementById("label_1"));
	e.push(document.getElementById("pref_1"));
	e.push(document.getElementById("addr1_1"));
	e.push(document.getElementById("addr2_1"));
	e.push(document.getElementById("addr3_1"));
	e.push(document.getElementById("delBtn1"));

	if (!cAddr) { return; }
console.log(cAddr);

	if (!obj) { return; }
console.log(obj);

	//console.log(e);
	e.forEach((element) => element.remove());
}
</script>

<br />
<hr>

	@if (current($rows_goods)->goods)
		@foreach($rows_goods as $i => $goods)
			<div class="row mb-3">
				<label class="col-sm-2 col-form-label w-5">商品: {{$d->detail}}</label>
				<input type="text" class="col-sm-2 col-form-control w-auto" id="goods_{{$i}}" name="goods[]" aria-describedby="prefHelp" value="{{$goods->goods}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control w-auto" id="goods_name_{{$i}}" name="goods_name[]" aria-describedby="addr1Help" value="{{$goods->name}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control w-auto" id="qty_{{$i}}" name="qty[]" aria-describedby="addr2Help" value="{{$goods->qty}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control w-auto" id="remark_{{$i}}" name="remark[]" aria-describedby="addr3Help" value="{{$goods->remark}}">&emsp;
			</div>
		@endforeach
	@else
		<div class="row mb-3">
			<label class="col-sm-2 col-form-label">商品: </label>
			関連する商品がありません。
		</div>
	@endif

	<p class="">
	<legend>【顧客登録】</legend>
	</p>

	@if ($get->action != '')
	<div class="row mb-3">
		<label for="customer" class="col-sm-2 col-form-label">顧客番号</label>
		<input type="text" class="col-sm-2 col-form-control" id="customer" name="customer" aria-describedby="customerHelp" value="{{$post->customer}}" readonly>
	</div>
	@endif

	<div class="row mb-3">
		<label for="customer_name" class="col-sm-2 col-form-label">顧客名</label>
		<input type="text" class="col-sm-2 col-form-control" id="customer_name" name="customer_name" aria-describedby="customerNameHelp" value="{{$post->customer_name}}">
<!--		<div id="orderName" class="form-text">顧客名を入力してください。</div>-->
	</div>

	<br />

	@foreach($rows as $i => $d)
		<div class="row mb-3">
			<label class="col-sm-2 col-form-label">住所: {{$d->detail}}</label>
			<input type="text" class="col-sm-2 col-form-control" id="pref_{{$i}}" name="pref[]" aria-describedby="prefHelp" value="{{$d->pref}}">&emsp;
			<input type="text" class="col-sm-2 col-form-control" id="addr1_{{$i}}" name="addr1[]" aria-describedby="addr1Help" value="{{$d->addr1}}">&emsp;
			<input type="text" class="col-sm-2 col-form-control" id="addr2_{{$i}}" name="addr2[]" aria-describedby="addr2Help" value="{{$d->addr2}}">&emsp;
			<input type="text" class="col-sm-2 col-form-control" id="addr3_{{$i}}" name="addr3[]" aria-describedby="addr3Help" value="{{$d->addr3}}">&emsp;
		</div>
	@endforeach
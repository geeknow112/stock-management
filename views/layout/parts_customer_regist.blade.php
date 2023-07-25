	<p class="">
	<legend>【顧客登録】</legend>
	</p>

	@if ($get->action != '')
	<div class="row mb-3">
		<label for="customer" class="col-sm-2 col-form-label">顧客番号</label>
		<input type="text" class="col-sm-2 col-form-control" id="customer" name="customer" aria-describedby="customerHelp" value="{{$rows->id}}" readonly>
	</div>
	@endif

	<div class="row mb-3">
		<label for="customerName" class="col-sm-2 col-form-label">顧客名</label>
		<input type="text" class="col-sm-2 col-form-control" id="customer_name" name="customer_name" aria-describedby="customerNameHelp" value="{{$rows->name}}">
<!--		<div id="orderName" class="form-text">顧客名を入力してください。</div>-->
	</div>

	<div class="row mb-3">
		<label for="pref" class="col-sm-2 col-form-label">都道府県</label>
		<input type="text" class="col-sm-2 col-form-control" id="pref" name="pref" aria-describedby="prefHelp" value="{{$rows->pref}}">
	</div>

	<div class="row mb-3">
		<label for="addr1" class="col-sm-2 col-form-label">住所１</label>
		<input type="text" class="col-sm-2 col-form-control" id="addr1" name="addr1" aria-describedby="addr1Help" value="{{$rows->addr1}}">
	</div>

	<div class="row mb-3">
		<label for="addr2" class="col-sm-2 col-form-label">住所２</label>
		<input type="text" class="col-sm-2 col-form-control" id="addr2" name="addr2" aria-describedby="addr2Help" value="{{$rows->addr2}}">
	</div>

	<div class="row mb-3">
		<label for="addr3" class="col-sm-2 col-form-label">住所３</label>
		<input type="text" class="col-sm-2 col-form-control" id="addr3" name="addr3" aria-describedby="addr3Help" value="{{$rows->addr3}}">
	</div>
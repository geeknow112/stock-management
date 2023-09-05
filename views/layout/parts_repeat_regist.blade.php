<div id="repeat_info" style="display: none">

	<p class="">
	<legend>【繰り返し情報登録】</legend>
	</p>

  <div class="row mb-3">
    <label for="repeat_every" class="col-sm-2 col-form-label">繰り返し</label>
	<select class="form-select" aria-label="repeat_every">
		<option value=""></option>
		<option value=""></option>
		<option value="1" selected>毎日</option>
		<option value="2">毎週</option>
		<option value="3">毎月</option>
		<option value="4">毎年</option>>
	</select>
  </div>

  <div class="row mb-3">
    <label for="interval" class="col-sm-2 col-form-label">繰り返す間隔</label>
	<select class="form-select" aria-label="interval">
		<option value=""></option>
		@for ($i = 1; $i <= 31; $i++)
		<option value="{{$i}}">{{$i}}</option>
		@endfor
	</select>
  </div>

  <div class="row mb-3">
    <label class="form-check-label" for="week">曜日</label>
  </div>
  <div class="mb-3">
		<input type="checkbox" name="week_M" id="week_M" class="form-check-imput" />
		<label class="form-check-label" for="week_M">月</label>
		<input type="checkbox" name="week_Tu" id="week_Tu" class="form-check-imput" />
		<label class="form-check-label" for="week_Tu">火</label>
		<input type="checkbox" name="week_W" id="week_W" class="form-check-imput" />
		<label class="form-check-label" for="week_W">水</label>
		<input type="checkbox" name="week_Th" id="week_Th" class="form-check-imput" />
		<label class="form-check-label" for="week_Th">木</label>
		<input type="checkbox" name="week_F" id="week_F" class="form-check-imput" />
		<label class="form-check-label" for="week_F">金</label>
		<input type="checkbox" name="week_St" id="week_St" class="form-check-imput" />
		<label class="form-check-label" for="week_St">土</label>
		<input type="checkbox" name="week_Su" id="week_Su" class="form-check-imput" />
		<label class="form-check-label" for="week_Su">日</label>
  </div>

  <div class="row mb-3">
    <label for="repeat_s_dt" class="col-sm-2 col-form-label">開始日</label>
    <input type="date" class="col-sm-6 col-form-control" id="repeat_s_dt" aria-describedby="repeatSDtHelp">
<!--    <div id="repeatSDtHelp" class="form-text">開始日を入力してください。</div>-->
  </div>

  <div class="row mb-3">
    <label for="repeat_e_dt" class="col-sm-2 col-form-label">終了日</label>
    <input type="date" class="col-sm-6 col-form-control" id="repeat_e_dt" aria-describedby="repeatEDtHelp">
<!--    <div id="repeatEDtHelp" class="form-text">終了日を入力してください。</div>-->
  </div>

</div>
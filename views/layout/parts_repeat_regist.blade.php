	<p class="">
	<legend>【繰り返し情報登録】</legend>
	</p>

  <div class="mb-3">
    <label for="carModel" class="form-label">繰り返し</label>
	<select class="form-select" aria-label="carModel">
		<option value=""></option>
		<option value=""></option>
		<option value="1" selected>毎日</option>
		<option value="2">毎週</option>
		<option value="3">毎月</option>
		<option value="4">毎年</option>>
	</select>
  </div>

  <div class="mb-3">
    <label for="goodsName" class="form-label">繰り返す間隔</label>
	<select class="form-select" aria-label="goodsName">
		<option value=""></option>
		@for ($i = 1; $i <= 31; $i++)
		<option value="{{$i}}">{{$i}}</option>
		@endfor
	</select>
  </div>

  <div class="mb-3">
    <label class="form-check-label" for="repeat">曜日</label>
  </div>
  <div class="mb-3">
		<input type="checkbox" name="repeatM" id="repeatM" class="form-check-imput" />
		<label class="form-check-label" for="repeatM">月</label>
		<input type="checkbox" name="repeatTu" id="repeatTu" class="form-check-imput" />
		<label class="form-check-label" for="repeatTu">火</label>
		<input type="checkbox" name="repeatW" id="repeatW" class="form-check-imput" />
		<label class="form-check-label" for="repeatW">水</label>
		<input type="checkbox" name="repeatTh" id="repeatTh" class="form-check-imput" />
		<label class="form-check-label" for="repeatTh">木</label>
		<input type="checkbox" name="repeatF" id="repeatF" class="form-check-imput" />
		<label class="form-check-label" for="repeatF">金</label>
		<input type="checkbox" name="repeatSt" id="repeatSt" class="form-check-imput" />
		<label class="form-check-label" for="repeatSt">土</label>
		<input type="checkbox" name="repeatSu" id="repeatSu" class="form-check-imput" />
		<label class="form-check-label" for="repeatSu">日</label>
  </div>

  <div class="mb-3">
    <label for="arrivalDt" class="form-label">開始日</label>
    <input type="date" class="form-control" id="arrivalDt" aria-describedby="arrivalDtHelp">
    <div id="arrivalDtHelp" class="form-text">開始日を入力してください。</div>
  </div>

  <div class="mb-3">
    <label for="arrivalDt" class="form-label">終了日</label>
    <input type="date" class="form-control" id="arrivalDt" aria-describedby="arrivalDtHelp">
    <div id="arrivalDtHelp" class="form-text">終了日を入力してください。</div>
  </div>

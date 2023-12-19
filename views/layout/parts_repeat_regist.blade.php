<!--<div id="repeat_info">-->
<div id="repeat_info" style="display: none">

	<p class="">
	<legend>【繰り返し情報登録】</legend>
	</p>

  <div class="row mb-3">
    <label for="repeat_every" class="col-sm-2 col-form-label">繰り返し</label>
	<select class="form-select w-75" aria-label="repeat_every" id="period" name="period">
		@foreach($initForm['select']['period'] as $i => $d)
			<option value="{{$i}}" @if ($i == $rows->period) selected @endif >{{$d}}</option>
		@endforeach
	</select>
  </div>

  <div class="row mb-3">
    <label for="interval" class="col-sm-2 col-form-label">繰り返す間隔</label>
	<select class="form-select w-75" aria-label="interval" id="span" name="span">
		@foreach($initForm['select']['span'] as $i => $d)
			<option value="{{$i}}" @if ($i == $rows->span) selected @endif >{{$d}}</option>
		@endforeach
	</select>
  </div>

	<div class="row mb-3">
		<label class="form-check-label" for="week">曜日</label>
	</div>
	<div class="mb-3">
		@foreach($initForm['select']['week'] as $i => $d)
			@if ($rows->week)
			<input type="checkbox" name="week[{{$i}}]" id="week_{{$i}}" name="week" class="form-check-imput" @if (in_array($i, $rows->week)) checked @endif />
			@else
			<input type="checkbox" name="week[{{$i}}]" id="week_{{$i}}" name="week" class="form-check-imput" />
			@endif
			<label class="form-check-label" for="week_{{$i}}">{{$d}}</label>
		@endforeach

<!--
		<input type="checkbox" name="week[1]" id="week_M" class="form-check-imput" /><label class="form-check-label" for="week_M">月</label>
		<input type="checkbox" name="week[2]" id="week_Tu" class="form-check-imput" /><label class="form-check-label" for="week_Tu">火</label>
		<input type="checkbox" name="week[3]" id="week_W" class="form-check-imput" /><label class="form-check-label" for="week_W">水</label>
		<input type="checkbox" name="week[4]" id="week_Th" class="form-check-imput" /><label class="form-check-label" for="week_Th">木</label>
		<input type="checkbox" name="week[5]" id="week_F" class="form-check-imput" /><label class="form-check-label" for="week_F">金</label>
		<input type="checkbox" name="week[6]" id="week_St" class="form-check-imput" /><label class="form-check-label" for="week_St">土</label>
		<input type="checkbox" name="week[7]" id="week_Su" class="form-check-imput" /><label class="form-check-label" for="week_Su">日</label>
-->
	</div>

  <div class="row mb-3">
    <label for="repeat_s_dt" class="col-sm-2 col-form-label">開始日</label>
    <input type="date" class="col-sm-6 col-form-control w-auto" id="repeat_s_dt" name="repeat_s_dt" aria-describedby="repeatSDtHelp" value="{{$rows->repeat_s_dt}}">
<!--    <div id="repeatSDtHelp" class="form-text">開始日を入力してください。</div>-->
  </div>

  <div class="row mb-3">
    <label for="repeat_e_dt" class="col-sm-2 col-form-label">終了日</label>
    <input type="date" class="col-sm-6 col-form-control w-auto" id="repeat_e_dt" name="repeat_e_dt" aria-describedby="repeatEDtHelp" value="{{$rows->repeat_e_dt}}">
<!--    <div id="repeatEDtHelp" class="form-text">終了日を入力してください。</div>-->
  </div>

</div>
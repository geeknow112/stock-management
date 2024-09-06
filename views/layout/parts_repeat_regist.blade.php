<div id="repeat_info">
	<br /><br /><hr>
<!--<div id="repeat_info" style="display: none">-->

	<p class="">
	<legend>【繰り返し情報登録】</legend>
	</p>

  <div class="row mb-3">
    <label for="repeat_every" class="col-sm-2 col-form-label">繰り返し</label>
	<select class="form-select w-75" aria-label="repeat_every" id="period" name="period" onchange="displayCustom();" @if ($cur_user->roles[0] != 'administrator') disabled @endif>
		@foreach($initForm['select']['period'] as $i => $d)
			<option value="{{$i}}" @if ($i == $rows->period) selected @endif >{{$d}}</option>
		@endforeach
	</select>
	@if ($cur_user->roles[0] != 'administrator')
		<input type="hidden" name="period" value="{{$rows->period}}" />
	@endif
  </div>

	<div id="repeat_custom">
  <div class="row mb-3">
    <label for="interval" class="col-sm-2 col-form-label">繰り返す間隔</label>
	<select class="form-select w-25" aria-label="interval" id="span" name="span" @if ($cur_user->roles[0] != 'administrator') disabled @endif>
		@foreach($initForm['select']['span'] as $i => $d)
			<option value="{{$i}}" @if ($i == $rows->span) selected @endif >{{$d}}</option>
		@endforeach
	</select>
	@if ($cur_user->roles[0] != 'administrator')
		<input type="hidden" name="span" value="{{$rows->span}}" />
	@endif
	<span id="" class="manual-text form-text">　日</span>
  </div>

<!--
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
	</div>
-->

  <div class="row mb-3">
    <label for="repeat_s_dt" class="col-sm-2 col-form-label">開始日</label>
	@if ($cur_user->roles[0] == 'administrator')
		<input type="date" class="col-sm-6 col-form-control w-auto" id="repeat_s_dt" name="repeat_s_dt" aria-describedby="repeatSDtHelp" value="{{$rows->repeat_s_dt}}">
	@else
		<input type="date" class="col-sm-6 col-form-control w-auto" id="disp_repeat_s_dt" name="disp_repeat_s_dt" aria-describedby="repeatSDtHelp" value="{{$rows->repeat_s_dt}}" disabled>
		<input type="hidden" id="repeat_s_dt" name="repeat_s_dt" value="{{$rows->repeat_s_dt}}" />
	@endif
<!--    <div id="repeatSDtHelp" class="form-text">開始日を入力してください。</div>-->
  </div>

  <div class="row mb-3">
    <label for="repeat_e_dt" class="col-sm-2 col-form-label">終了日</label>
    <input type="date" class="col-sm-6 col-form-control w-auto" id="repeat_e_dt" name="repeat_e_dt" aria-describedby="repeatEDtHelp" value="{{$rows->repeat_e_dt}}" @if ($cur_user->roles[0] != 'administrator') disabled @endif>
	@if ($cur_user->roles[0] != 'administrator')
		<input type="hidden" name="repeat_e_dt" value="{{$rows->repeat_e_dt}}" />
	@endif
<!--    <div id="repeatEDtHelp" class="form-text">終了日を入力してください。</div>-->
  </div>
	</div>
</div>

<script>
/**
 * 「繰返 開始日」の自動入力
 *    - 初期画面での「配送予定日」との連動
 **/
setRepeatSDt();
</script>
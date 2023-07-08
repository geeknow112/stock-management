	<p class="">
	<legend>【注文登録】</legend>
	</p>

  <div class="mb-3">
    <label for="carModel" class="form-label">車種</label>
	<select class="form-select" aria-label="carModel">
		<option value=""></option>
		<option value="6t_1" selected>6t-1</option>
		<option value="6t_2">6t-2</option>
		<option value="6t_3">6t-3</option>
		<option value="6t_4">6t-4</option>
		<option value="6t_5">6t-5</option>
	</select>
  </div>

  <div class="mb-3">
    <label for="goodsName" class="form-label">品名</label>
	<select class="form-select" aria-label="goodsName">
		<option value=""></option>
		<option value="1" selected>商品①</option>
		<option value="2">商品②</option>
		<option value="3">商品③</option>
		<option value="4">商品④</option>
		<option value="5">商品⑤</option>
	</select>
  </div>

  <div class="mb-3">
    <label for="shipAdd" class="form-label">配送先</label>
    <input type="text" class="form-control" id="shipAdd" aria-describedby="shipAddHelp">
    <div id="shipAddHelp" class="form-text">配送先を入力してください。</div>
  </div>

  <div class="mb-3">
    <label for="qty" class="form-label">量(t)</label>
    <input type="text" class="form-control" id="qty" aria-describedby="qtyHelp">
    <div id="qtyHelp" class="form-text">量(t)を入力してください。</div>
  </div>

  <div class="mb-3">
    <label for="arrivalDt" class="form-label">入庫予定日</label>
    <input type="date" class="form-control" id="arrivalDt" aria-describedby="arrivalDtHelp">
    <div id="arrivalDtHelp" class="form-text">入庫予定日を入力してください。</div>
  </div>

  <div class="mb-3">
    <label for="orderName" class="form-label">氏名</label>
    <input type="text" class="form-control" id="orderName" aria-describedby="orderNameHelp">
    <div id="orderName" class="form-text">氏名を入力してください。</div>
  </div>



  <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="repeat">
    <label class="form-check-label" for="repeat">繰り返し予定を設定する</label>
  </div>

<br /><br /><hr>

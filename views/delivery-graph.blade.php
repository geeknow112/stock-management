<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【配送予定表③】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">

		<form name="forms" id="forms" action="" method="" enctype="multipart/form-data">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}

			<div class="message">
				@foreach($msg as $k => $error)
					<p>【 {{$k}} 】 {{$error}}</p>
				@endforeach
			</div>

			<div class="search-box">
				<label class="screen-reader-text" for="user-search-input">申込者を検索:</label>
<!--
				No. ：<input type="search" id="user-search-input" name="s[no]" value="<?php echo htmlspecialchars($get->s['no']); ?>">&emsp;&emsp;&emsp;
				社名：<input type="search" id="user-search-input" name="s[company_name]" value="<?php echo htmlspecialchars($get->s['company_name']); ?>"><br /><br />
-->
				開始：<input type="date" id="user-search-input" name="s[sdt]" value="<?php echo htmlspecialchars($get->s['sdt']); ?>" placeholder="2020-11-01">&emsp;
<!--				開始：<input type="date" id="user-search-input" name="s[sdt]" value="<?php echo htmlspecialchars($get->s['sdt']); ?>" placeholder="2020-11-01">&emsp;～&emsp;	-->
<!--				終了：<input type="date" id="user-search-input" name="s[edt]" value="<?php echo htmlspecialchars($get->s['edt']); ?>" placeholder="2022-12-01">&emsp;	-->

				<input type="button" id="search-submit" class="btn btn-primary" onclick="cmd_search();" value="検索">

				<script>
				function cmd_search() {
					document.forms.method = 'get';
					document.forms.action = "/wp-admin/admin.php?page=delivery-graph&action=search"
					document.forms.cmd.value = 'search';
					document.forms.submit();
				}
				</script>
			</div>

			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="delivery-graph">
			<input type="hidden" name="action" value="search">
			<input type="hidden" name="cmd" value="">

			<input type="hidden" name="delivery_dt" value="">
			<input type="hidden" name="class" value="">
			<input type="hidden" name="cars_tank" value="">
			<input type="hidden" name="change_delivery_dt" value="">

			<input type="hidden" name="base_sales" value="">
{{--			@endif	--}}

		<div class="table-responsive">
<!--
			<table class="wp-list-table widefat fixed striped table-view-list users">
-->
<!--
			<table class="striped" style="border-collapse: collapse;">
-->
			<table class="table table-bordered text-nowrap">
				<thead class="table-light">
				</thead>

			@if (isset($rows) && count($rows))
				<tbody id="the-list" data-wp-lists="list:user">
			@else
				<td class="colspanchange" colspan="7">検索対象は見つかりませんでした。</td>
			@endif
				</tbody>

				<tfoot>
				</tfoot>
			</table>


		<div>
			<table class="table table-bordered text-nowrap">
				<thead class="table-light">
					<tr>
						<th class=""></th>
						<th class=""></th>
						<th class=""></th>
						@if ($cur_user->roles[0] != 'subscriber')
						<th class="" colspan="6">6t ⓪</th>
						@endif
						<th class="" colspan="6">6t ①</th>
						<th class="" colspan="6">6t ②</th>
						<th class="" colspan="6">6t ③</th>
						<th class="" colspan="6">6t ④</th>
						<th class="" colspan="6">6t ⑤</th>
						<th class="" colspan="6">6t ⑥</th>
						<th class="" colspan="6">6t ⑦</th>
						<th class="" colspan="6">6t ⑧ (太田畜産 専用：結果入力欄)</th>
						<th class="" colspan="6">7.5t ⑨ (村上畜産 専用：結果入力欄)</th>
						<th class="" colspan="6">6t ⑩ (山忠商事(直取) 専用：結果入力欄)</th>
					</tr>

					<tr>
						<th class="" scope="col" id="username"></th>
						<th class="" scope="col" id="username">曜</th>
						<th class="" scope="col" id="username">日</th>
						@for ($i = 0; $i <= 10; $i++)
							@if ($cur_user->roles[0] == 'subscriber' && $i == 0)
								@php continue; @endphp
							@endif
						<th class="" style="width: 7rem;">品名</th>
						<th class="" style="width: 3rem;">量(t)</th>
						<th class="" style="width: 7rem;">配送先</th>
						<th class="" style="width: 5rem;">入庫予定日</th>
						<th class="" style="width: 5rem;">氏名</th>
						<th class="" style="width: 5rem;">確認</th>
						@endfor
					</tr>
				</thead>
<style>
	.message {
		width: 100%;
		height: 150px;
		margin-top: 1.5em;
		margin-bottom: 1.5em;
		border: 1px solid #c0c0c0;
		overflow: auto;
	}

	.message p {
		color: red;
		font-size: 24px;
	}

	.inner_box {
		width: 8rem; background: #eeeeee; border-right: 1px solid #d3d3d3;
		font-size: 24px;
	}

	.inner_box_repeat {
		width: 8rem; background: #ff69b4; border-right: 1px solid #ffffff; color: #ffffff;
		font-size: 24px;
	}
</style>

<?php	function innerTable($delivery_dt, $list, $class, $sumTanks = null, $carsTank = null, $initForm = null) {	?>
		<div style="width: 40rem;">
<!--	<div class="card" style="width: 40rem;">-->
		<?php foreach ($list as $sales => $d) { ?>
			<?php foreach ($d as $id => $row) { ?>
				<?php if ($row->class == $class && $row->cars_tank == $carsTank) { ?>
					<div class="d-flex flex-row bd-highlight mb-3">
						@if ($row->repeat_fg != 1)
							@if ($row->upuser != 'ceo')
							<div class="text-wrap text-center inner_box" style="width: 8rem;"><a href='/wp-admin/admin.php?page=sales-detail&sales={{$row->sales}}&goods={{$row->goods}}&repeat={{$row->repeat}}&action=edit'>{{$row->goods_name}}</a></div>
							@else
							<div class="text-wrap text-center inner_box" style="width: 8rem; background: yellow;"><a href='/wp-admin/admin.php?page=sales-detail&sales={{$row->sales}}&goods={{$row->goods}}&repeat={{$row->repeat}}&action=edit'>{{$row->goods_name}}</a></div>
							@endif
						@else
							<div class="text-wrap text-center inner_box_repeat" style="width: 8rem;"><a href='/wp-admin/admin.php?page=sales-detail&sales={{$row->sales}}&goods={{$row->goods}}&repeat={{$row->repeat}}&action=edit'>{{$row->goods_name}}</a></div>
						@endif
						<div class="text-wrap text-center inner_box" style="width: 3.5rem;"><?php echo $row->qty; ?></div>
						<div class="text-wrap text-center inner_box" style="width: 9rem;">
						<?php
//							echo sprintf('[ ');
//							echo sprintf('(%s', $row->ship_addr);
							foreach ($sumTanks[$row->sales][$row->goods] as $i => $d) {
								if (!empty(current($d))) {
									echo sprintf(' %s (t) <br>', implode(' : ', $d));
								}
							}
//							echo sprintf(' ]');
							echo ($row->outgoing_warehouse == 1) ? '<span style="color: red;">(内)</span>' : '';
						?>
						</div>
						<div class="text-wrap text-center inner_box" style="width: 7.5rem;"><?php echo $row->delivery_dt; ?></div>
						<div class="text-wrap text-center inner_box" style="width: 6.5rem;"><?php echo $row->customer_name; ?></div>
						@if ($row->lot_fg == 0)
							@if (isset($row->base_sales))
							<div>
						<?php
						$oid = $row->sales. "_". $row->goods. "_". $row->repeat. "_". str_replace('-', '', $delivery_dt);
						?>
								<select class="" id="cars_class_{{$oid}}" name="">
						{{--
									@foreach($initForm['select']['car_model'] as $i => $d)
										<option value="{{$i}}">{{$d}}</option>
									@endforeach
						--}}
										<option value="1">6t-1</option>
										<option value="2">6t-2</option>
										<option value="3">6t-3</option>
										<option value="4">6t-4</option>
										<option value="5">6t-5</option>
										<option value="6">6t-6</option>
										<option value="7">6t-7</option>
								</select>
								<br />
								<select class="" id="cars_tank_{{$oid}}" name="">
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
								</select>
								<br />
								<input type="date" class="col-sm-6 col-form-control w-auto" id="delivery_dt_{{$oid}}" name="" value="">
								<br />
								<input type="hidden" id="r_order_{{$oid}}" name="r_order[]" value="">
								<input type="button" class="btn btn-primary text-center" value="注文" onclick="change_repeat_order('{{$oid}}');">
							</div>

<!--							<a href="" class="btn btn-secondary text-center" onClick="window.prompt('車種、槽を入力してください。', ''); return false;">未注文</a>	-->
							@else
							<a href="" class="btn btn-secondary text-center" onClick="window.location = '/wp-admin/admin.php?page=lot-regist&sales=<?php echo htmlspecialchars($row->sales); ?>&goods=<?php echo htmlspecialchars($row->goods); ?>&action=save'; return false;">未作成</a>
							@endif
						@elseif ($row->lot_fg == 1)
						<a href="" class="btn btn-warning text-center" onClick="window.location = '/wp-admin/admin.php?page=lot-regist&sales=<?php echo htmlspecialchars($row->sales); ?>&goods=<?php echo htmlspecialchars($row->goods); ?>&action=save'; return false;">未登録</a>
						@else
						<a href="" class="btn btn-success text-center" onClick="window.location = '/wp-admin/admin.php?page=lot-regist&sales=<?php echo htmlspecialchars($row->sales); ?>&goods=<?php echo htmlspecialchars($row->goods); ?>&action=edit'; return false;">登録済</a>
						<input type="checkbox" class="btn-check" id="btn-check-outlined" autocomplete="off"><label class="btn btn-outline-primary" for="btn-check-outlined">受領書</label><!-- 受領書の受取確認用 -->
						@endif
					</div>
	<!--
					<div class="card-body border mb-1">
						<h5 class="card-title">品名：<?php if ($row->repeat_fg != 1) { echo $row->goods_name; } else { echo '<span style="color:red;">'. $row->goods_name. '</span>'; } ?></h5>
						<p class="card-text">配送先：<?php echo $row->ship_addr; ?></p>
						<a href="" class="btn btn-primary" onClick="window.location = '/wp-admin/admin.php?page=lot-regist&sales=<?php echo htmlspecialchars($row->id); ?>&goods=<?php echo htmlspecialchars($row->goods); ?>'; return false;">未登録</a>
					</div>
	-->
	<!--
					<div class="card mb-3" style="max-width: 540px;">
						<div class="row no-gutters">
							<div class="col-md-4">
								<svg class="bd-placeholder-img" width="100%" height="250" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: Image"><title>Placeholder</title><rect fill="#868e96" width="100%" height="100%"/><text fill="#dee2e6" dy=".3em" x="50%" y="50%">Image</text></svg>
							</div>
							<div class="col-md-8">
								<div class="card-body">
									<h5 class="card-title">品名：<?php if ($row->repeat_fg != 1) { echo $row->goods_name; } else { echo '<span style="color:red;">'. $row->goods_name. '</span>'; } ?></h5>
									<p class="card-text">配送先：<?php echo $row->ship_addr; ?></p>
									<p class="card-text">量(t)：<?php echo $row->qty; ?></p>
									<p class="card-text">入庫予定日：<?php echo $row->arrival_dt; ?></p>
									<p class="card-text">氏名：<?php echo $row->name; ?></p>
									<a href="" class="btn btn-primary" onClick="window.location = '/wp-admin/admin.php?page=lot-regist&sales=<?php echo htmlspecialchars($row->id); ?>&goods=<?php echo htmlspecialchars($row->goods); ?>'; return false;">未登録</a>
								</div>
							</div>
						</div>
					</div>
	-->
				<?php }	?>
			<?php }	?>
		<?php }	?>
	</div>
<?php	}	?>

<script>
function change_repeat_order(oid) {
	var r_order_id = 'r_order_' + oid;
	var cars_class_id = 'cars_class_' + oid;
	var cars_tank_id = 'cars_tank_' + oid;
	var delivery_dt_id = 'delivery_dt_' + oid;

	var cars_class = document.getElementById(cars_class_id).value;
	var cars_tank = document.getElementById(cars_tank_id).value;
	var delivery_dt = document.getElementById(delivery_dt_id).value;

	if (window.confirm('車種、槽、配送予定日 を変更しますか？')) {
		document.forms.method = 'post';
		document.forms.action.value = 'regist';
		//document.forms.oid.value = '1';
		document.getElementById(r_order_id).value = r_order_id;
		document.forms.class.value = cars_class;
		document.forms.cars_tank.value = cars_tank;
		document.forms.change_delivery_dt.value = delivery_dt;

	/*
		document.forms.r_delivery_dt.value = <?php echo $row->delivery_dt; ?>;
		document.forms.r_class.value = <?php echo $row->class; ?>;
		document.forms.r_tank.value = '{{$row->cars_tank}}';
		document.forms.base_sales.value = '1';
		document.forms.cmd.value = 'regist';
	*/	document.forms.submit();
	}

}
</script>

<?php	function innerTableFixed($delivery_dt, $list, $class, $sumTanks = null, $carsTank = null, $initForm = null) {	?>
{{$delivery_dt}}_{{$class}}_{{$carsTank}}
		<div style="width: 40rem;" id="app1" class="container">
			<div class="d-flex flex-row bd-highlight mb-3">
				<input type="text" class="text-wrap text-center inner_box" style="width: 8rem;" placeholder="商品名" value="">
				<input type="text" class="text-wrap text-center inner_box" style="width: 3.5rem;" placeholder="量(t)" value="">
				<input type="text" class="text-wrap text-center inner_box" style="width: 9rem;" placeholder="配送先" value="">
				<input type="text" class="text-wrap text-center inner_box" style="width: 7.5rem;" placeholder="入庫予定日" value="">
				<input type="text" class="text-wrap text-center inner_box" style="width: 6.5rem;" placeholder="氏名" value="">
				<a href="" class="btn btn-primary text-center" onClick="">入力</a>
			</div>
		</div>
<?php	}	?>

			@if (isset($rows) && count($rows))
				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($rows as $delivery_dt => $list)
					<tr id="user-1">
						<td class="" colspan="3">
							<a href="/wp-admin/admin.php?page=sum-day-goods">{{$delivery_dt}}</a><br />
							<p>　1槽</p>
						</td>

						<!-- 6t 0 -->
						@if ($cur_user->roles[0] != 'subscriber')
						<td class="" colspan="6">
							@php innerTable($delivery_dt, $repeat_list[$delivery_dt], 0, $sumTanks, 1, $initForm); @endphp
						</td>
						@endif

						<!-- 6t 1 -->
						<td class="" colspan="6">
							@php innerTable($delivery_dt, $list, 1, $sumTanks, 1); @endphp
						</td>

						<!-- 6t 2 -->
						<td class="" colspan="6">
							@php innerTable($delivery_dt, $list, 2, $sumTanks, 1); @endphp
						</td>

						<!-- 6t 3 -->
						<td class="" colspan="6">
							@php innerTable($delivery_dt, $list, 3, $sumTanks, 1); @endphp
						</td>

						<!-- 6t 4 -->
						<td class="" colspan="6">
							@php innerTable($delivery_dt, $list, 4, $sumTanks, 1); @endphp
						</td>
						<!-- 6t 5 -->
						<td class="" colspan="6">
							@php innerTable($delivery_dt, $list, 5, $sumTanks, 1); @endphp
						</td>
						<!-- 6t 6 -->
						<td class="" colspan="6">
							@php innerTable($delivery_dt, $list, 6, $sumTanks, 1); @endphp
						</td>
						<!-- 6t 7 -->
						<td class="" colspan="6">
							@php innerTable($delivery_dt, $list, 7, $sumTanks, 1); @endphp
						</td>
						<!-- 6t 8 -->
						<td class="" colspan="6">
							@php innerTableFixed($delivery_dt, $list, 8, $sumTanks, 1); @endphp
						</td>
						<!-- 6t 9 -->
						<td class="" colspan="6">
							@php innerTableFixed($delivery_dt, $list, 9, $sumTanks, 1); @endphp
						</td>
						<!-- 6t 10 -->
						<td class="" colspan="6">
							@php innerTableFixed($delivery_dt, $list, 10, $sumTanks, 1); @endphp
						</td>
					</tr>
					<tr id="user-1">
						<td class="" colspan="3">
							<p>　2槽</p>
						</td>

						<!-- 6t 0 -->
						@if ($cur_user->roles[0] != 'subscriber')
						<td colspan="6">
							@php innerTable($delivery_dt, $repeat_list[$delivery_dt], 0, $sumTanks, 2); @endphp
						</td>
						@endif

						<!-- 6t 1 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 1, $sumTanks, 2); @endphp
						</td>

						<!-- 6t 2 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 2, $sumTanks, 2); @endphp
						</td>

						<!-- 6t 3 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 3, $sumTanks, 2); @endphp
						</td>

						<!-- 6t 4 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 4, $sumTanks, 2); @endphp
						</td>
						<!-- 6t 5 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 5, $sumTanks, 2); @endphp
						</td>
						<!-- 6t 6 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 6, $sumTanks, 2); @endphp
						</td>
						<!-- 6t 7 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 7, $sumTanks, 2); @endphp
						</td>
						<!-- 6t 8 -->
						<td colspan="6">
							@php innerTableFixed($delivery_dt, $list, 8, $sumTanks, 2); @endphp
						</td>
						<!-- 6t 9 -->
						<td colspan="6">
							@php innerTableFixed($delivery_dt, $list, 9, $sumTanks, 2); @endphp
						</td>
						<!-- 6t 10 -->
						<td colspan="6">
							@php innerTableFixed($delivery_dt, $list, 10, $sumTanks, 2); @endphp
						</td>
					</tr>
					<tr id="user-1" style="border-bottom: solid 1px gray;">
						<td class="" colspan="3">
							<p>　3槽</p>
						</td>

						<!-- 6t 0 -->
						@if ($cur_user->roles[0] != 'subscriber')
						<td colspan="6">
							@php innerTable($delivery_dt, $repeat_list[$delivery_dt], 0, $sumTanks, 3); @endphp
						</td>
						@endif

						<!-- 6t 1 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 1, $sumTanks, 3); @endphp
						</td>

						<!-- 6t 2 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 2, $sumTanks, 3); @endphp
						</td>

						<!-- 6t 3 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 3, $sumTanks, 3); @endphp
						</td>

						<!-- 6t 4 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 4, $sumTanks, 3); @endphp
						</td>
						<!-- 6t 5 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 5, $sumTanks, 3); @endphp
						</td>
						<!-- 6t 6 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 6, $sumTanks, 3); @endphp
						</td>
						<!-- 6t 7 -->
						<td colspan="6">
							@php innerTable($delivery_dt, $list, 7, $sumTanks, 3); @endphp
						</td>
						<!-- 6t 8 -->
						<td colspan="6">
							@php innerTableFixed($delivery_dt, $list, 8, $sumTanks, 3); @endphp
						</td>
						<!-- 6t 9 -->
						<td colspan="6">
							@php innerTableFixed($delivery_dt, $list, 9, $sumTanks, 3); @endphp
						</td>
						<!-- 6t 10 -->
						<td colspan="6">
							@php innerTableFixed($delivery_dt, $list, 10, $sumTanks, 3); @endphp
						</td>
					</tr>
					@endforeach
			@else
				<td class="colspanchange" colspan="7">検索対象は見つかりませんでした。</td>
			@endif
				</tbody>

				<tfoot>
				</tfoot>
			</table>
</div>

		</div>

			<div class="tablenav bottom">
				<br class="clear">
			</div>
		</form>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>

<script>
function init_status(applicant = null) {
	if (applicant == "" || applicant == null) {
		alert("No. がありません。");
		exit;
	}

	var str = "No. 【" + applicant + "】 の「登録状況」を初期化しますか？";
	if (window.confirm(str)) {
		//alert("初期化しました。");
		location.href = location.protocol + "//" + location.hostname + "/wp-admin/admin.php?page=shop-list&post=" + applicant + "&action=init-status";
	}
}
</script>

<!--
<table>
<tr>
<td>
<div id="app" class="container">
  <div id="vbox1" class="vbox">
    <draggable v-model="items" item-key="no" tag="ul" group="ITEMS" class="ul_tag">
      <template #item="{ element, index }">
        <li class="li_tag">@{{element.goods_name}}-(No.@{{element.id}})</li>
      </template>
    </draggable>
  </div>
  <div id="vbox2" class="vbox">
    <draggable v-model="items2" item-key="no" tag="ul" group="ITEMS" class="ul_tag">
      <template #item="{ element, index }">
        <li class="li_tag">@{{element.goods_name}}-(No.@{{element.id}})</li>
      </template>
    </draggable>
  </div>
</div>
</td>
</tr>

<tr>
<td>
<div id="app1" class="container">
  <div id="vbox1" class="vbox">
    <draggable v-model="items" item-key="no" tag="ul" group="ITEMS" class="ul_tag">
      <template #item="{ element, index }">
        <li class="li_tag">@{{element.goods_name}}-(No.@{{element.id}})</li>
      </template>
    </draggable>
  </div>
  <div id="vbox2" class="vbox">
    <draggable v-model="items2" item-key="no" tag="ul" group="ITEMS" class="ul_tag">
      <template #item="{ element, index }">
        <li class="li_tag">@{{element.goods_name}}-(No.@{{element.id}})</li>
      </template>
    </draggable>
  </div>
</div>
</td>
</tr>
</table>
-->

<style>
.vbox {
  width: 50%;
  float: left;
  padding: 20px 0;
}
#vbox1 {
  background-color: #fdd;
}
#vbox2 {
  background-color: #ddf;
}
.ul_tag {
  list-style-type: none;
      padding-right: 2rem;
}
.li_tag {
  cursor:pointer;
  padding: 10px;
  border: solid #ddd 1px;
  background-color: #fff;
}
</style>

<!-- bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<!-- bootstrap end -->
<!-- Vue 3.2.26 -->
<script src="https://unpkg.com/vue@3.2.26/dist/vue.global.js"></script>
<!-- Vue 3.2.26 end -->
<!-- CDNJS :: Sortable (https://cdnjs.com/) -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.10.2/Sortable.min.js"></script>
<!-- CDNJS :: Vue.Draggable (https://cdnjs.com/) -->
<script src="https://cdn.jsdelivr.net/npm/vuedraggable@4.0.2/dist/vuedraggable.umd.min.js"></script>

<script>
var r = @json($r);
const draggable = window['vuedraggable'];
const App = {
    data() {
      return {
        items:r,
//        items:[ 
//          {no:1, name:'goods1', categoryNo:'1'}, 
//          {no:2, name:'goods2', categoryNo:'2'} 
//        ], 
        items2:[ 
          {id:5, goods_name:'goods3', categoryNo:'1'},
          {id:6, goods_name:'goods4', categoryNo:'2'} 
        ] 
      }
    },
    components: {
      draggable: draggable
    },
  }

  Vue.createApp(App).mount('#app');
  Vue.createApp(App).mount('#app1');
</script>
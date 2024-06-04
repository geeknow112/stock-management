<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, user-scalable=yes">

<link href="<?php echo home_url(); ?>/wp-content/plugins/stock-management/views/css/style.css" rel="stylesheet" />
<script src="<?php echo home_url(); ?>/wp-content/plugins/stock-management/views/js/delivery-graph.js" integrity="" crossorigin=""></script>

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

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【配送予定表】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">

		<form name="forms" id="forms" action="" method="" enctype="multipart/form-data">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}

			@if ($cur_user->roles[0] != 'subscriber')
			<div class="message">
				@foreach($msg as $k => $error)
					<p>【 {{$k}} 】 {{$error}}</p>
				@endforeach
			</div>
			@endif

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

				<span class="pc">&emsp;&emsp;&emsp;&emsp;</span>
				<span class="sp"><br /></br /></span>
				<span id="jump_link">
					@if ($cur_user->roles[0] != 'subscriber')
					<span><a href="#table_top"><input type="button" class="btn btn-primary" value="繰返"></a><span>
					&emsp;
					@endif

					@if ($cur_user->roles[0] != 'subscriber')
					<span><a href="#car_model_0"><input type="button" class="btn btn-info" value="未確定"></a><span>
					&emsp;
					@endif

					<span><a href="#car_model_1"><input type="button" class="btn btn-info" value="①"></a><span>
					&emsp;

					<span><a href="#car_model_2"><input type="button" class="btn btn-info" value="②"></a><span>
					&emsp;

					<span><a href="#car_model_3"><input type="button" class="btn btn-primary" value="③"></a><span>
					&emsp;

					<span><a href="#car_model_4"><input type="button" class="btn btn-info" value="④"></a><span>
					&emsp;

					<span><a href="#car_model_5"><input type="button" class="btn btn-info" value="⑤"></a><span>
					&emsp;

					@if ($cur_user->roles[0] == 'administrator')
					<span><a href="#input_result"><input type="button" class="btn btn-primary" value="結果入力欄"></a><span>
					&emsp;

					<span><a href="#input_result_end" name="input_result_end"><input type="button" class="btn btn-info" value=">>"></a><span>
					@endif

				</span>
				<span class="sp"><br /></br /></span>
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
			<input type="hidden" name="r_arrival_dt" value="">
			<input type="hidden" name="r_warehouse" value="">

			<input type="hidden" name="sales" value="">
			<input type="hidden" name="base_sales" value="">
			<input type="hidden" name="repeat_fg" value="">
			<input type="hidden" name="use_stock" value="">

			<input type="hidden" name="oid" value="">
			<input type="hidden" name="odata" value="">

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

		<?php $colspan = 6; ?>
		<div id="table_top">
			<table class="table table-bordered text-nowrap">
				<thead class="table-light">
					<tr>
						<th class="_sticky" colspan="3"></th>
						@if ($cur_user->roles[0] != 'subscriber')
						<th class="" colspan="{{$colspan}}">繰返</th>
						<th class="" colspan="{{$colspan}}" id="car_model_0">未確定</th>
						@endif
						<th class="" colspan="{{$colspan}}" id="car_model_1">6t ①</th>
						<th class="" colspan="{{$colspan}}" id="car_model_2">6t ②</th>
						<th class="" colspan="{{$colspan}}" id="car_model_3">6t ③</th>
						<th class="" colspan="{{$colspan}}" id="car_model_4">6t ④</th>
						<th class="" colspan="{{$colspan}}" id="car_model_5">6t ⑤</th>
						@if ($cur_user->roles[0] == 'administrator')
						<th class="" colspan="{{$colspan}}">6t ⑦ (山忠商事(直取) 専用：繰り返し注文表示欄)</th>
						<th class="" colspan="{{$colspan}}" id="input_result">6t ⑧ (太田畜産 専用：結果入力欄)</th>
						<th class="" colspan="{{$colspan}}">7.5t ⑨ (村上畜産 専用：結果入力欄)</th>
						<th class="" colspan="{{$colspan}}" id="input_result_end">6t ⑩ (山忠商事(直取) 専用：結果入力欄)</th>
						@endif
					</tr>

					<tr>
						<th class="_sticky" scope="col" colspan="3">日</th>
						@for ($i = 0; $i <= 10; $i++)
							@if ($cur_user->roles[0] == 'subscriber' && $i == 0)
								@php continue; @endphp
							@endif

							@if ($cur_user->roles[0] == 'editor' && $i < 4)
								@php continue; @endphp
							@endif

							@if ($cur_user->roles[0] == 'subscriber' && $i < 6)
								@php continue; @endphp
							@endif

						<th class="" style="width: 7rem;">品名</th>
						<th class="" style="width: 3rem;">量(t)</th>
						<th class="" style="width: 7rem;">配送先</th>

							@if ($i != 8 && $i != 9 && $i != 10)
						<th class="" style="width: 5rem;">入庫予定日</th>
							@else
						<th class="" style="width: 5rem;">出庫倉庫</th>
							@endif

						<th class="" style="width: 5rem;">氏名</th>

						<th class="" style="width: 5rem;">確認</th>

						@endfor
					</tr>
				</thead>

<?php	function innerTable($delivery_dt, $list, $class, $carsTank = null, $initForm = null, $cur_user = null) {	?>
		<?php $w_box = ($cur_user->roles[0] == 'administrator') ? '40rem' : '34rem'; ?>
		<div style="width: {{$w_box}};">
<!--	<div class="card" style="width: 40rem;">-->
		<?php foreach ($list as $sales => $d) { ?>
			<?php foreach ($d as $id => $row) { ?>
				<?php if ($row->class == $class && $row->cars_tank == $carsTank) { ?>
					<div class="d-flex flex-row bd-highlight mb-3">

						<!-- 「品名」 表示エリア -->
						@if ($row->repeat_fg != 1)
							@if ($row->upuser != 'ceo')
							<div class="text-wrap text-center inner_box" style="width: 8rem;"><a href='/wp-admin/admin.php?page=sales-detail&sales={{$row->sales}}&goods={{$row->goods}}&repeat={{$row->repeat}}&action=edit'>{{$row->goods_name}} @if ($row->separately_fg == true) （バラ） @endif</a></div>
							@else
							<div class="text-wrap text-center inner_box" style="width: 8rem; background: yellow;"><a href='/wp-admin/admin.php?page=sales-detail&sales={{$row->sales}}&goods={{$row->goods}}&repeat={{$row->repeat}}&action=edit'>{{$row->goods_name}} @if ($row->separately_fg == true) （バラ） @endif</a></div>
							@endif
						@else
							<div class="text-wrap text-center inner_box_repeat" style="width: 8rem;"><a href='/wp-admin/admin.php?page=sales-detail&sales={{$row->sales}}&goods={{$row->goods}}&repeat={{$row->repeat}}&action=edit'>{{$row->goods_name}} @if ($row->separately_fg == true) （バラ） @endif</a></div>
						@endif

						<!-- 「量(t)」 表示エリア -->
						@if ($row->class >= 1 && $row->class < 7) {{-- 未確定列と、①～⑤のみ --}}
							<input class="text-wrap text-center inner_box" style="width: 4.0rem;" type="number" id="_qty_{{$oid}}" min="0" max="30" step="0.5" value="<?php echo $row->qty; ?>" />
						@else
							<div class="text-wrap text-center inner_box" style="width: 4.0rem;"><?php echo $row->qty; ?></div>
						@endif

						<!-- 「配送先」 表示エリア -->
						<div class="text-wrap text-center inner_box" style="width: 9rem;">
							@if (in_array($row->class, array(8,9)))
								@if ($row->field1)
									{{$row->field1}}
								@else
									-
								@endif

							@else
								@if ($row->tank_name)
									<select class="w-100" id="" name="">
										<option></option>
										<option @if($row->tank_name) selected @endif >{{$row->tank_name}}</option>
									</select>
								@else
									<select class="w-100" id="" name="">
										<option></option>
									</select>
								@endif
							@endif
							<br>

							@if ($row->outgoing_warehouse == 1)
								<span style="color: red;">(内)</span>
							@else
								&emsp;
							@endif

							@if (in_array($row->class, array(1,2,3,4,5,6)))
								<span><input type="button" class="btn btn-secondary text-center" value="更新" onclick="window.confirm('更新しますか？');"></span>
							@endif
						</div>

						<!-- 「入庫予定日」|「出庫倉庫」 表示エリア -->
						@if ($row->class <= 7) {{-- ⑧、⑨、⑩の場合、出庫倉庫を表示 --}}
							@if ($row->delivery_dt <= $row->arrival_dt)
								<div class="text-wrap text-center inner_box bg-danger text-light" style="width: 7.5rem;"><?php echo date('m/d', strtotime($row->arrival_dt)); ?></div>
							@else
								<?php
									if ($row->class != 0 && $row->remark && !$row->use_stock) {
										$bg_arrival_dt = 'bg-info text-light';
									} elseif ($row->class != 0 && $row->use_stock) {
										$bg_arrival_dt = 'bg-success text-light';
									} else {
										$bg_arrival_dt = '';
									}
								?>
								<div class="text-wrap text-center inner_box {{$bg_arrival_dt}}" style="width: 7.5rem;">@if (!$row->use_stock)<?php echo date('m/d', strtotime($row->arrival_dt)); ?>@endif</div>
							@endif
						@else
							<div class="text-wrap text-center inner_box" style="width: 7.5rem;">{{$initForm['select']['outgoing_warehouse'][$row->outgoing_warehouse]}}</div>
						@endif

						<!-- 「(顧客)氏名」 表示エリア -->
						<div class="text-wrap text-center inner_box" style="width: 6.5rem;"><?php echo str_replace('　', '', $row->customer_name); ?></div>

						<!-- 操作ボタン等 表示エリア -->
						@if ($row->class != 7)
							@if ($row->lot_fg == 0)
								@if (isset($row->base_sales))
								<div>
							<?php
							$oid = $row->sales. "_". $row->goods. "_". $row->repeat. "_". str_replace('-', '', $delivery_dt);
							?>
									<input type="date" class="col-sm-6 col-form-control w-auto" id="delivery_dt_{{$oid}}" name="" value="">
									<input type="hidden" class="" id="r_arrival_dt_{{$oid}}" name="" value="{{$row->arrival_dt}}">
									<input type="hidden" class="" id="r_warehouse_{{$oid}}" name="" value="{{$row->outgoing_warehouse}}">
									<br />
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
<!--											<option value="7">6t-7</option>	-->
									</select>
									<select class="" id="cars_tank_{{$oid}}" name="">
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
									</select>
									<input type="hidden" id="r_order_{{$oid}}" name="r_order[]" value="">
									<input type="button" class="btn btn-primary text-center" value="注文" onclick="change_repeat_order('{{$oid}}');">
								</div>

	<!--							<a href="" class="btn btn-secondary text-center" onClick="window.prompt('車種、槽を入力してください。', ''); return false;">未注文</a>	-->
								@else
									@if ($cur_user->roles[0] == 'administrator')
										<a href="#" class="btn btn-secondary text-center" onclick="confirm_make_lot_space({{$row->sales}}, {{$row->goods}}, {{$row->repeat_fg}}, {{$row->use_stock}}, 'administrator');">未作成</a>
									@elseif ($cur_user->roles[0] == 'editor')
										<a href="#" class="btn btn-secondary text-center" onclick="confirm_make_lot_space({{$row->sales}}, {{$row->goods}}, {{$row->repeat_fg}}, {{$row->use_stock}}, 'editor');">&emsp;&emsp;&emsp;</a>
									@else
										<span class="btn btn-secondary text-center">&emsp;&emsp;&emsp;</span>
									@endif
								@endif
							@elseif ($row->lot_fg == 1)
									@if ($cur_user->roles[0] == 'administrator')
										<a href="#" class="btn text-center" id="btn_unregist" onclick="to_lot_regist({{$row->sales}}, {{$row->goods}});">未登録</a>
									@else
										<span class="btn text-center" id="btn_unregist">&emsp;&emsp;&emsp;</span>
									@endif
							@else
								@if ($row->receipt_fg != 1)
									@if ($cur_user->roles[0] == 'administrator')
										<a href="#" class="btn btn-success text-center" onclick="check_status({{$row->sales}}, {{$row->goods}}, {{$row->repeat_fg}}, {{$row->use_stock}});">登録済</a>
										<input type="checkbox" class="btn-check" id="check-receipt_{{$row->sales}}" autocomplete="on"><label class="btn btn-outline-primary" onclick="switch_receipt({{$row->sales}});">受領書</label><!-- 受領書の受取確認用 -->
<!--										<input type="checkbox" class="btn-check" id="check-receipt_{{$row->sales}}" autocomplete="off"><label class="btn btn-outline-primary" for="check-receipt_{{$row->sales}}">受領書</label>--><!-- 受領書の受取確認用 -->
									@else
										<span class="btn btn-success text-center">&emsp;&emsp;&emsp;</span>
									@endif
								@else
									@if ($cur_user->roles[0] == 'administrator')
										<a href="#" class="btn btn-danger text-center" onclick="to_lot_regist({{$row->sales}}, {{$row->goods}});">&emsp;完了&emsp;</a>
									@else
										<span class="btn btn-danger text-center">&emsp;&emsp;&emsp;</span>
									@endif
								@endif
							@endif
						@else
							@if ($row->receipt_fg != 1)
								<div>
							<?php
							$oid = $row->sales. "_". $row->goods. "_". $row->repeat. "_". str_replace('-', '', $delivery_dt);
							?>
									<input type="date" class="col-sm-6 col-form-control w-auto" id="delivery_dt_{{$oid}}" name="" value="">
									<input type="hidden" class="" id="r_arrival_dt_{{$oid}}" name="" value="{{$row->arrival_dt}}">
									<input type="hidden" class="" id="r_warehouse_{{$oid}}" name="" value="{{$row->outgoing_warehouse}}">
									<br />
									<select class="" id="cars_class_{{$oid}}" name="">
							{{--
										@foreach($initForm['select']['car_model'] as $i => $d)
											<option value="{{$i}}">{{$d}}</option>
										@endforeach
							--}}
											<option value="7">6t-7</option>
									</select>
									<select class="" id="cars_tank_{{$oid}}" name="">
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
									</select>
									<input type="hidden" id="r_order_{{$oid}}" name="r_order[]" value="">
									<input type="button" class="btn btn-info text-center text-light" value="直取分" onclick="change_repeat_order_direct_delivery('{{$oid}}');">
								</div>
							@else
								<a href="#" class="btn btn-danger text-center" onclick="to_lot_regist({{$row->sales}}, {{$row->goods}});">&emsp;完了&emsp;</a>
							@endif
						@endif
					</div>
				<?php }	?>
			<?php }	?>
		<?php }	?>
	</div>
<?php	}	?>

<?php	function innerTableFixed($delivery_dt, $list, $class, $carsTank = null, $initForm = null, $cur_user = null) { ?>
	<?php $oid = sprintf("%s%02d%02d", str_replace('-', '', $delivery_dt), $class, $carsTank); // echo $oid; ?>

	<?php if ($cur_user->roles[0] == 'administrator') { // 管理者以外非表示 START ?>
		<div style="width: 40rem;" id="app1" class="container">
			<div class="d-flex flex-row bd-highlight mb-3">

				<!-- 「商品」 選択欄 -->
				<?php if ($class == 8) { // 太田畜産用 ?>
					<select class="w-25" id="goods_{{$oid}}" name="">
						<?php foreach ($initForm['fix_customer'][17]['goods'] as $customer => $goods_list) { ?>
							<?php foreach ($initForm['fix_customer'][17]['goods'][17] as $goods => $goods_name) { ?>
							<option value="<?php echo $goods; ?>"><?php echo sprintf("%s : %s", $goods, $goods_name); ?></option>
							<?php } ?>
						<?php } ?>
					</select>

				<?php } elseif ($class == 9) { // 村上養鶏場用 ?>
					<select class="w-25" id="goods_{{$oid}}" name="">
						<?php foreach ($initForm['fix_customer'][31]['goods'] as $customer => $goods_list) { ?>
							<?php foreach ($initForm['fix_customer'][31]['goods'][31] as $goods => $goods_name) { ?>
							<option value="<?php echo $goods; ?>"><?php echo sprintf("%s : %s", $goods, $goods_name); ?></option>
							<?php } ?>
						<?php } ?>
					</select>

				<?php } else { // その他 ?>
					<select class="w-25" id="goods_{{$oid}}" name="">
					</select>
				<?php } ?>

				<!-- 「数量」 入力欄 -->
				<input type="number" id="qty_{{$oid}}" min="0" max="30" step="0.5" value="" />

				<!-- 「配送先」 入力欄 -->
				<?php if (in_array($class, array(8,9))) { // 太田畜産用 // 村上養鶏場用 ?>
					<input type="text" class="w-25" id="ship_addr_{{$oid}}" value="" />
				<?php } else { // その他 ?>
					<select class="w-25" id="ship_addr_{{$oid}}" name="">
					</select>
				<?php } ?>

				<!-- 「出庫倉庫」 選択欄 -->
				<select class="" id="outgoing_warehouse_{{$oid}}" name="">
					<?php foreach ($initForm['select']['outgoing_warehouse'] as $i => $outgoing_warehouse) { ?>
						<option value="<?php echo $i; ?>"><?php echo $outgoing_warehouse; ?></option>
					<?php } ?>
				</select>

				<!-- 「(顧客)氏名」 選択欄 -->
				<?php if ($class == 8) { // 太田畜産用 ?>
					<select class="" id="customer_{{$oid}}" name="customer_{{$oid}}">
						<?php foreach ($initForm['fix_customer'][17]['customer'] as $customer => $customer_name) { ?>
							<option value="<?php echo $customer; ?>"><?php echo sprintf("%s : %s", $customer, $customer_name); ?></option>
						<?php } ?>
					</select>

				<?php } elseif ($class == 9) { // 村上養鶏場用 ?>
					<select class="" id="customer_{{$oid}}" name="customer_{{$oid}}">
						<?php foreach ($initForm['fix_customer'][31]['customer'] as $customer => $customer_name) { ?>
							<option value="<?php echo $customer; ?>"><?php echo sprintf("%s : %s", $customer, $customer_name); ?></option>
						<?php } ?>
					</select>

				<?php } else { // その他 ?>
					<select class="" id="customer_{{$oid}}" name="customer_{{$oid}}" onchange="createSelectBox(<?php echo $oid; ?>); createSelectBoxGoods(<?php echo $oid; ?>);">
						<?php foreach ($initForm['select']['customer'] as $customer => $customer_name) { ?>
							<option value="<?php echo $customer; ?>"><?php echo sprintf("%s : %s", $customer, $customer_name); ?></option>
						<?php } ?>
					</select>
				<?php } ?>

				<a href="#" class="btn btn-primary text-center" onClick="setResult(<?php echo $oid; ?>);">入力</a>
			</div>
		</div>
	<?php	} // 管理者以外非表示 END ?>
<?php	}	?>

			@if (isset($rows) && count($rows))
				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($rows as $delivery_dt => $list)
					<tr id="user-1">
						<td class="_sticky" id="sticky" colspan="3">
							<a href="#">{{$delivery_dt}}</a><br />
<!--							<p>　1槽</p>-->
						</td>

						<!-- 6t 0 -->
						@if ($cur_user->roles[0] != 'subscriber')
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $repeat_list[$delivery_dt], 0, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $repeat_list[$delivery_dt], 0, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $repeat_list[$delivery_dt], 0, 3, $initForm, $cur_user); @endphp
						</td>
						@endif

						<!-- 6t 1 -->
						@if ($cur_user->roles[0] != 'subscriber')
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 1, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 1, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 1, 3, $initForm, $cur_user); @endphp
						</td>
						@endif

						<!-- 6t 2 -->
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 2, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 2, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 2, 3, $initForm, $cur_user); @endphp
						</td>

						<!-- 6t 3 -->
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 3, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 3, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 3, 3, $initForm, $cur_user); @endphp
						</td>

						<!-- 6t 4 -->
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 4, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 4, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 4, 3, $initForm, $cur_user); @endphp
						</td>
						<!-- 6t 5 -->
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 5, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 5, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 5, 3, $initForm, $cur_user); @endphp
						</td>
						<!-- 6t 6 -->
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 6, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 6, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 6, 3, $initForm, $cur_user); @endphp
						</td>
						<!-- 6t 7 -->
						@if ($cur_user->roles[0] == 'administrator')
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 7, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $repeat_list[$delivery_dt], 7, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 7, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $repeat_list[$delivery_dt], 7, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 7, 3, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $repeat_list[$delivery_dt], 7, 3, $initForm, $cur_user); @endphp
						</td>
						<!-- 6t 8 -->
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 8, 1, $initForm, $cur_user); @endphp
							@php innerTableFixed($delivery_dt, $list, 8, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 8, 2, $initForm, $cur_user); @endphp
							@php innerTableFixed($delivery_dt, $list, 8, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 8, 3, $initForm, $cur_user); @endphp
							@php innerTableFixed($delivery_dt, $list, 8, 3, $initForm, $cur_user); @endphp
						</td>
						<!-- 6t 9 -->
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 9, 1, $initForm, $cur_user); @endphp
							@php innerTableFixed($delivery_dt, $list, 9, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 9, 2, $initForm, $cur_user); @endphp
							@php innerTableFixed($delivery_dt, $list, 9, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 9, 3, $initForm, $cur_user); @endphp
							@php innerTableFixed($delivery_dt, $list, 9, 3, $initForm, $cur_user); @endphp
						</td>
						<!-- 6t 10 -->
						<td class="" colspan="{{$colspan}}">
							@php innerTable($delivery_dt, $list, 10, 1, $initForm, $cur_user); @endphp
							@php innerTableFixed($delivery_dt, $list, 10, 1, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 10, 2, $initForm, $cur_user); @endphp
							@php innerTableFixed($delivery_dt, $list, 10, 2, $initForm, $cur_user); @endphp
							@php innerTable($delivery_dt, $list, 10, 3, $initForm, $cur_user); @endphp
							@php innerTableFixed($delivery_dt, $list, 10, 3, $initForm, $cur_user); @endphp
						</td>
						@endif
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

<script>
function createSelectBox(oid) {
	console.log(oid);
	var customer = document.getElementById("customer_" + oid).value;
//	var goods = document.forms.goods.value;
	console.log('c: ' + customer);
//	console.log('g: ' + goods);

	//連想配列の配列
	var ar = "{{$test_ship_addr}}";
	var json = JSON.parse(unescapeHtml(ar));
	console.log(json[customer]);
	var arr = json[customer];

	// selectの初期化
	const sel = document.getElementById("ship_addr_" + oid);
//	sel.disabled = (goods) ? (customer) ? false : true : true; // 非活性化
	console.log(sel.childNodes.length);
	for (var i=sel.childNodes.length-1; i>=0; i--) {
		sel.removeChild(sel.childNodes[i]);
	}

	if (arr !== undefined) {
		//連想配列をループ処理で値を取り出してセレクトボックスにセットする
		for (var i=0; i<arr.length; i++) {
			if (i != 0 && arr[i] == '') { continue; }
			let op = document.createElement("option");
			op.value = i;  //value値
			op.text = arr[i];   //テキスト値
			sel.appendChild(op);
		}
	}
}

function createSelectBoxGoods(oid) {
	console.log(oid);
	var customer = document.getElementById("customer_" + oid).value;
	//連想配列の配列
	var ar = "{{$gnames}}";
	var json = JSON.parse(unescapeHtml(ar));
	console.log(json[customer]);
	var arr = json[customer];

	// selectの初期化
	const sel = document.getElementById("goods_" + oid);
//	sel.disabled = (customer) ? false : true; // 非活性化
	console.log(sel.childNodes.length);
	for (var i=sel.childNodes.length-1; i>=0; i--) {
		sel.removeChild(sel.childNodes[i]);
	}

	if (arr !== undefined) {
		//連想配列をループ処理で値を取り出してセレクトボックスにセットする
		for (let goods in arr) {
			let op = document.createElement("option");
			if (goods != 0) {
				op.value = goods;  //value値
				op.text = goods + ' : ' + arr[goods];   //テキスト値
			}
			sel.appendChild(op);
		}
	}
}
</script>
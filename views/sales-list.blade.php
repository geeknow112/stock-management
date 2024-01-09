<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【注文検索】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">

		<form name="forms" id="forms" action="" method="" enctype="multipart/form-data">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}
			<div class="search-box">
				<label for="sales" class="col-sm-2 col-form-label">No. ：</label>
					<input type="search" id="sales" name="s[no]" value="<?php echo htmlspecialchars($get->s['no']); ?>"><br /><br />

				<label for="goods_name" class="col-sm-2 col-form-label">商品名：</label>
					<input type="search" id="goods_name" name="s[goods_name]" value="<?php echo htmlspecialchars($get->s['goods_name']); ?>"><br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">車種：</label>
					<select type="search" id="user-search-input" name="s[car_model]" class="col-form-select" aria-label="car_model" id="car_model">
						@foreach($initForm['select']['car_model'] as $i => $d)
							@if (isset($get->s['car_model']))
								<option value="{{$i}}" @if($i == $get->s['car_model']) selected @endif>{{$i}} : {{$d}}</option>
							@else
								<option value="{{$i}}">{{$d}}</option>
							@endif
						@endforeach
					</select>
					<br /><br />

				<label for="lot" class="col-sm-2 col-form-label">ロット番号：</label>
					<input type="search" id="lot" name="s[lot]" value="<?php echo htmlspecialchars($get->s['lot']); ?>"><br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">状態：</label>
					<select type="search" id="user-search-input" name="s[status]" class="col-form-select" aria-label="status" id="status">
						@foreach($initForm['select']['status'] as $i => $d)
							@if (isset($get->s['status']))
								<option value="{{$i}}" @if($i == $get->s['status']) selected @endif>{{$d}}</option>
							@else
								<option value="{{$i}}">{{$d}}</option>
							@endif
						@endforeach
					</select>
					<br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">出庫倉庫：</label>
					<select type="search" id="user-search-input" name="s[outgoing_warehouse]" class="col-form-select" aria-label="outgoing_warehouse" id="outgoing_warehouse">
						@foreach($initForm['select']['outgoing_warehouse'] as $i => $d)
							@if (isset($get->s['outgoing_warehouse']))
								<option value="{{$i}}" @if($i == $get->s['outgoing_warehouse']) selected @endif>{{$d}}</option>
							@else
								<option value="{{$i}}">{{$d}}</option>
							@endif
						@endforeach
					</select>
					<br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">注文日：</label>
					<input type="date" id="user-search-input" name="s[order_s_dt]" value="<?php echo htmlspecialchars($get->s['order_s_dt']); ?>" placeholder="2020-11-01">&emsp;～&emsp;
				<input type="date" id="user-search-input" name="s[order_e_dt]" value="<?php echo htmlspecialchars($get->s['order_e_dt']); ?>" placeholder="2022-12-01"><br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">配送予定日：</label>
					<input type="date" id="user-search-input" name="s[delivery_s_dt]" value="<?php echo htmlspecialchars($get->s['delivery_s_dt']); ?>" placeholder="2020-11-01">&emsp;～&emsp;
				<input type="date" id="user-search-input" name="s[delivery_e_dt]" value="<?php echo htmlspecialchars($get->s['delivery_e_dt']); ?>" placeholder="2022-12-01"><br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">引取(入庫)予定日：</label>
					<input type="date" id="user-search-input" name="s[arrival_s_dt]" value="<?php echo htmlspecialchars($get->s['arrival_s_dt']); ?>" placeholder="2020-11-01">&emsp;～&emsp;
				<input type="date" id="user-search-input" name="s[arrival_e_dt]" value="<?php echo htmlspecialchars($get->s['arrival_e_dt']); ?>" placeholder="2022-12-01">&emsp;
<!--
				<input type="submit" id="search-submit" class="button" value="申込者を検索">
-->
				<input type="button" id="search-submit" class="btn btn-primary" onclick="cmd_search();" value="検索">

			</div>
			<br />

			<div class="search-box">
				<label for="cmd-select" class="col-sm-2 col-form-label">一括操作：</label>
				<select class="col-form-select" aria-label="orderName" id="change_status" name="change_status">
					@foreach($initForm['select']['status'] as $i => $d)
						@if (isset($get->s['change_status']))
							<option value="{{$i}}" @if($i == $get->s['change_status']) selected @endif>{{$d}}</option>
						@else
							<option value="{{$i}}">{{$d}}</option>
						@endif
					@endforeach
				</select>

				&emsp;
				<button type="button" class="btn btn-primary" onclick="confirm_bulk_operation();">適用</button>

				<script>
				function cmd_search() {
					document.forms.method = 'get';
					document.forms.action = "/wp-admin/admin.php?page=sales-list&sales={{$get->sales}}&goods={{$get->goods}}&action=search"
					document.forms.cmd.value = 'search';
					document.forms.submit();
				}

				function confirm_bulk_operation() {
					var ret = window.confirm('チェックした注文の状態を一括操作しますか？');
					if (ret) {
						document.forms.method = 'post';
						document.forms.action = "/wp-admin/admin.php?page=sales-list&sales={{$get->sales}}&goods={{$get->goods}}"
						document.forms.cmd.value = 'edit';
						document.forms.submit();
					} else {
					}
				}
				</script>
			</div>
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="sales-list">
			<input type="hidden" name="action" value="search">
			<input type="hidden" name="cmd" value="">
{{--			@endif	--}}


{{ $wp_list_table->display() }}

			<div class="tablenav top">
				<br class="clear">
			</div>
			
			<h2 class="screen-reader-text">ユーザー一覧</h2>

		<div class="table-responsive">
<!--
			<table class="wp-list-table widefat fixed striped table-view-list users">
-->
<!--
			<table class="striped" style="border-collapse: collapse;">
-->

<!--
		<div>
			<table class="table table-bordered text-nowrap">
				<thead class="table-light">
					<tr>
						<th scope="col" id="username" class="">No.</th>
						<th scope="col" id="username" class="">注文番号</th>
						<th scope="col" id="username" class="">注文日</th>
						<th scope="col" id="username" class="">注文者</th>
						<th scope="col" id="username" class="">商品</th>
						<th scope="col" id="username" class="">個数</th>
						<th scope="col" id="username" class="">出庫倉庫</th>
						<th scope="col" id="username" class="">引取(入庫)予定日</th>
						<th scope="col" id="username" class="">配送予定日</th>
						<th scope="col" id="username" class="">状態(確定｜未確定｜削除)</th>
					</tr>
				</thead>

			@if (isset($rows) && count($rows))
				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($rows as $i => $list)
					<tr id="user-1">
						<td>
							<input type="checkbox" id="no" name="no[]" value="{{$list->id}}" />
							<input type="hidden" id="arr_goods" name="arr_goods[{{$list->id}}]" value="{{$list->goods}}" />
							<input type="hidden" id="arr_qty" name="arr_qty[{{$list->id}}]" value="{{$list->qty}}" />
							<input type="hidden" id="arr_repeat" name="arr_repeat[]" value="{{$list->repeat}}" />
							<input type="hidden" id="arr_delivery_dt" name="arr_delivery_dt[]" value="{{$list->delivery_dt}}" />
						</td>

						<td>
							<a href="/wp-admin/admin.php?page=sales-detail&sales={{$list->id}}&action=edit">{{ $list->id }}</a>
						</td>
						<td>{{ $list->rgdt }}</td>
						<td>{{ $list->name }}</td>
						<td @if ($list->repeat_fg == 1) style="background: #ff69b4;" @endif >{{ $list->goods_name }} @if ($list->rep_i) : {{ $list->rep_i }} @endif</td>
						<td>
							@if ($list->status == '0')
							{{ $list->qty }}
							@else
							<a href="/wp-admin/admin.php?page=lot-regist&sales={{$list->id}}&goods={{$list->goods}}&action=save">{{ $list->qty }}</a>
							@endif
						</td>
						<td></td>
						<td>{{ $list->arrival_dt }}</td>
						<td>{{ $list->delivery_dt }}</td>
						@if ($list->status == '0')
						<td><span class="text-danger">{{ $initForm['select']['status'][$list->status] }}</span></td>
						@else
						<td><span class="text-success">{{ $initForm['select']['status'][$list->status] }}</span></td>
						@endif
					</tr>
					@endforeach
			@else
				<td class="colspanchange" colspan="10">検索対象は見つかりませんでした。</td>
			@endif
				</tbody>

				<tfoot>
				</tfoot>
			</table>
</div>
-->

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
		location.href = location.protocol + "//" + location.hostname + "/wp-admin/admin.php?page=sales-list&post=" + applicant + "&action=init-status";
	}
}

/**
 *  - onchangeイベント発生時処理
 * 
 **/
document.getElementById('cb-select-all-1').onchange = function () {
	const ch_all = document.getElementById('cb-select-all-1').checked;
//	console.log(ch_all);
	checkbox_all_select(ch_all);
};

/**
 *  - onchangeイベント発生時処理
 * 
 **/
document.getElementById('cb-select-all-2').onchange = function () {
	const ch_all = document.getElementById('cb-select-all-2').checked;
//	console.log(ch_all);
	checkbox_all_select(ch_all);
};

/**
 * チェックボックス全選択
 * 
 **/
function checkbox_all_select(ch_all) {
	const ch_obj = document.querySelectorAll("*[id*='no_']"); // idに'no_'が含まれる要素をすべて取得する
	//console.log(ch_obj);
	//ch_obj.forEach((el) => console.log(el.id));

	if (ch_all == true) {
		ch_obj.forEach((el) => document.getElementById(el.id).checked = true); // chbox 全選択

	} else {
		ch_obj.forEach((el) => document.getElementById(el.id).checked = false); // chbox 全解除
	}
}
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【入庫予定日検索】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">

		<form name="forms" id="forms" action="" method="" enctype="multipart/form-data">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}
			<div class="search-box">

				<label for="arrival_dt" class="col-sm-2 col-form-label">引取(入庫)予定日：</label>
					<input type="date" id="user-search-input" name="s[arrival_s_dt]" value="<?php echo htmlspecialchars($get->s['arrival_s_dt']); ?>" placeholder="2020-11-01">&emsp;～&emsp;
					<input type="date" id="user-search-input" name="s[arrival_e_dt]" value="<?php echo htmlspecialchars($get->s['arrival_e_dt']); ?>" placeholder="2022-12-01">
					<br />
<!--
					<input class="form-check-input" type="radio" name="sum_span" id="sum_span_1" value="" @if ($get->sum_span == 'one' || $get->sum_span == '') checked @endif>
					<label class="form-check-label" for="sum_span_1">1日分</label>
					&emsp;

					<input class="form-check-input" type="radio" name="sum_span" id="sum_span_10" value="ten" @if ($get->sum_span == 'ten') checked @endif>
					<label class="form-check-label" for="sum_span_10">10日分</label>
-->
				<label for="" class="col-sm-2 col-form-label">&emsp;</label>
					<span id="" class="manual-text form-text">※ 日付の範囲が<b>100日</b>を超えた場合、エラーとなります。</span>
					<br />

				<label for="customer_name" class="col-sm-2 col-form-label">顧客名：</label>
					<input type="search" id="customer_name" name="s[customer_name]" value="{{$get->s['customer_name']}}"><br /><br />

				<label for="tank" class="col-sm-2 col-form-label">配送先：</label>
					<input type="search" id="customer_name" name="s[tank]" value="{{$get->s['tank']}}"><br /><br />

				<label for="goods_name" class="col-sm-2 col-form-label">品名：</label>
					<input type="search" id="goods_name" name="s[goods_name]" value="{{$get->s['goods_name']}}"><br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">出庫倉庫：</label>
<!--					<input type="search" id="user-search-input" name="s[outgoing_warehouse]" value="<?php echo htmlspecialchars($get->s['outgoing_warehouse']); ?>">-->
					<select class="" aria-label="outgoing_warehouse" id="outgoing_warehouse" name="s[outgoing_warehouse]">
						@foreach($initForm['select']['outgoing_warehouse'] as $i => $d)
							@if ($i == '0')
							<option value=""></option>
							@else
							<option value="{{$i}}" @if ($i == $get->s['outgoing_warehouse']) selected @endif >{{$d}}</option>
							@endif
						@endforeach
					</select>
					&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;

				<input type="button" id="search-submit" class="btn btn-primary" onclick="cmd_search();" value="検索">

				<script>
				function cmd_search() {
					document.forms.method = 'get';
					document.forms.action = "/wp-admin/admin.php?page=stock-receive&sales={{$get->sales}}&goods={{$get->goods}}&action=search"
					document.forms.cmd.value = 'search';
					document.forms.submit();
				}
				</script>
			</div>
			<br />

			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="stock-receive">
			<input type="hidden" name="action" value="search">
			<input type="hidden" name="cmd" value="">

			<input type="hidden" name="oid" value="">
			<input type="hidden" name="change_arrival_dt" value="">
{{--			@endif	--}}


{{-- $wp_list_table->display() --}}
<!-- start -->
<div id="wpbody-content">
	<div class="wrap">

		<form method="get">
			<div class="tablenav top">
				<br class="clear">
			</div>
			
		<div class="table-responsive">

		<div>
			<table class="table table-bordered text-nowrap">
				<thead class="table-light">
					<tr>
						<th class="">No.</th>
						<th class="col-md-4">品名</th>
						<th class="">量(t)</th>
						<th class="w-25">引取(入庫)予定日</th>
						<th class="">倉庫</th>
					</tr>
				</thead>

				@if (isset($sum_list) && count($sum_list))
				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($sum_list as $goods => $list)
						@foreach ($list as $arrival_dt => $row)
						<?php $a_dt = str_replace('-', '', $arrival_dt); ?>
						<tr id="">
							<td class="">&emsp;</td>
							<td class="" onclick="changeDisplay({{$goods}}, {{$a_dt}});" @if (isset($row->repeat)) style="background: pink;" @endif><span>{{$row->goods_name}}</span></td>
							<td class="tx-center">{{number_format(array_sum($row->qty),1)}}</td>
							<td class="w-25 tx-center">{{$arrival_dt}}</td>
							<td class="">{{$initForm['select']['outgoing_warehouse'][$row->outgoing_warehouse]}}</td>
						</tr>
							@foreach ($detail[$goods][$arrival_dt] as $customer => $data)
								@foreach ($data as $i => $d)
								<tr class="detail d_{{$goods}}_{{$a_dt}}" id="detail_{{$goods}}_{{$i}}">
									<td class="">&emsp;</td>
									<td class="table-light tx-center">　<b>- 顧客：</b>( {{$d->customer_name}} )：@if ($d->tank) {{$d->tank}} @endif</td>
									<td class="table-info tx-right">{{number_format($d->qty,1)}}</td>
									<td class="tx-right @if ($d->remark && !$d->use_stock) table-info @elseif ($d->use_stock) table-success @endif">
<!--										<input type="checkbox" class="btn-check" id="check-reservation_{{$d->sales}}" autocomplete="on"><label class="btn btn-outline-primary" onclick="switch_reservation({{$d->sales}});">入庫予約済</label>-->
<!-- 入庫予約確認用 -->
							<?php
							$oid = $d->sales. "_". $d->goods. "_". $d->repeat_fg. "_". str_replace('-', '', $d->arrival_dt);
							?>
										<input type="hidden" id="r_order_{{$oid}}" name="r_order[]" value="">
										<input type="date" class="col-sm-6 col-form-control w-auto" id="change_arrival_dt_{{$oid}}" name="" value="{{$d->arrival_dt}}">

										@if (!$d->remark)
											<input type="button" class="btn btn-primary text-center" value="確定" onclick="decide_receive_order('{{$oid}}');">
										@else
											<input type="button" class="btn btn-secondary text-center" value="解除" onclick="cancel_receive_order('{{$oid}}');">
										@endif
									</td>
									<td class="tx-right @if ($d->remark && !$d->use_stock) table-info @elseif ($d->use_stock) table-success @endif">
										<input type="checkbox" class="btn-check" id="use_stock_{{$oid}}" name="use_stock" autocomplete="off" onchange="check_use_stock('{{$oid}}');">
										<label class="btn btn-outline-primary text-center" for="use_stock_{{$oid}}">在庫から配送</label>
										<input type="button" class="btn btn-success text-right" value="確定" onclick=" decide_stock_order('{{$oid}}');">
									</td>
								</tr>
								@endforeach
							@endforeach
						@endforeach
					@endforeach
				@else
				<td class="colspanchange" colspan="7">検索対象は見つかりませんでした。</td>
				@endif
				</tbody>

<script>
/**
 * 初期状態: 表示
 * 
 **/
window.onload = function () {
	const className = 'detail';
	const targets = document.getElementsByClassName(className);
	Array.from(targets).forEach(target => {
		if (target.style.display != "none") {
			target.style.display = "";
		} else {
			target.style.display = "none";
		}
	});
}

/**
 * クリックで詳細を表示
 * 
 **/
function changeDisplay(goods = null, a_dt = null) {
	console.log(goods);
	const className = 'd_' + goods + '_' + a_dt;
	console.log(className);
	const targets = document.getElementsByClassName(className);
	Array.from(targets).forEach(target => {
		if (target.style.display == "none") {
			target.style.display = "";
		} else {
			target.style.display = "none";
		}
	});
}

/**
 * 入庫予約確認のチェックボックス切替
 * 
 **/
function switch_reservation(sales) {
	console.log(sales);
	const ret = document.getElementById('check-reservation_' + sales);
	if (ret.checked == true) {
		ret.checked = false;
	} else {
		ret.checked = true;
	}
}

/**
 * [入庫予定日]編集欄で日付編集後、[確定]ボタン押下時の処理
 * 
 **/
function decide_receive_order(oid) {
console.log(oid);

	var r_order_id = 'r_order_' + oid;
	var arrival_dt_id = 'change_arrival_dt_' + oid;
	var arrival_dt = document.getElementById(arrival_dt_id).value;
console.log(arrival_dt);
/*
	var cars_class_id = 'cars_class_' + oid;
	var cars_tank_id = 'cars_tank_' + oid;
	var delivery_dt_id = 'delivery_dt_' + oid;
	var warehouse_id = 'r_warehouse_' + oid;


	var cars_class = document.getElementById(cars_class_id).value;
	var cars_tank = document.getElementById(cars_tank_id).value;
	var delivery_dt = document.getElementById(delivery_dt_id).value;
	var warehouse = document.getElementById(warehouse_id).value;
*/

	if (window.confirm('入庫日 を確定しますか？')) {
		document.forms.method = 'post';
		document.forms.action.value = 'regist';
		//document.forms.oid.value = '1';
		document.getElementById(r_order_id).value = r_order_id;

		document.forms.change_arrival_dt.value = arrival_dt;

		document.forms.cmd.value = 'regist';
		document.forms.submit();
	}
}

/**
 * [入庫予定日]編集欄で日付編集後の[確定]をキャンセルする処理
 * 
 **/
function cancel_receive_order(oid) {
	var r_order_id = 'r_order_' + oid;

	if (window.confirm('入庫日 の確定を解除しますか？')) {
		document.forms.method = 'post';
		document.forms.action.value = 'cancel';
		//document.forms.oid.value = '1';
		document.getElementById(r_order_id).value = r_order_id;

		document.forms.cmd.value = 'cancel';
		document.forms.submit();
	}
}

/**
 * 「在庫から配送」チェックボックス切替
 * 
 **/
function check_use_stock(oid) {
	var use_stock = 'use_stock_' + oid;
	if (document.getElementById(use_stock).checked) {
		document.getElementById(use_stock).value = 1; // true
	}
}

/**
 * [在庫から配送]チェック後、[確定]ボタン押下時の処理
 * 
 **/
function decide_stock_order(oid) {
	var r_order_id = 'r_order_' + oid;
	var use_stock = 'use_stock_' + oid;
	console.log(use_stock);

	if (document.getElementById(use_stock).checked) {
		if (window.confirm('「在庫から配送」 で確定しますか？')) {
			document.forms.method = 'post';
			document.forms.action.value = 'regist';
			//document.forms.oid.value = '1';
			document.getElementById(r_order_id).value = r_order_id;
			document.forms.use_stock.value = true;
			document.forms.cmd.value = 'decide_use_stock';
			document.forms.submit();
		}
	}
}
</script>

				<tfoot>
					<tr>
						<th class="">&emsp;</th>
						<th class="">&emsp;</th>
						<th class="">&emsp;</th>
						<th class="">&emsp;</th>
						<th class="">&emsp;</th>
					</tr>
					<tr>
						<th class="table-light tx-right" colspan="2">合計</th>
						<td class="tx-right">{{number_format($total,1)}}</td>
						<td class="">&emsp;</td>
						<th class="">&emsp;</th>
					</tr>
				</tfoot>
			</table>
</div>
<!-- end -->
			<div class="tablenav top">
				<br class="clear">
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
</script>
<style>
.tx-right {
	text-align: right;
}

.tx-center {
	text-align: center;
}
</style>
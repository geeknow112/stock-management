<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【在庫証明書】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">
		<br />

		<form name="forms" id="forms" action="" method="" enctype="multipart/form-data">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}

			<div class="search-box">

				<label for="carModel" class="col-sm-2 col-form-label">引取(入庫)日： </label>
					<input type="date" id="user-search-input" name="s[arrival_e_dt]" value="{{$get->s['arrival_e_dt']}}" placeholder="2020-11-01"><br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">出庫倉庫：</label>
					<select class="" aria-label="outgoing_warehouse" id="outgoing_warehouse" name="s[outgoing_warehouse]">
						@foreach($initForm['select']['outgoing_warehouse'] as $i => $d)
							@if ($i == '0')
							<option value=""></option>
							@else
							<option value="{{$i}}" @if ($i == $get->s['outgoing_warehouse']) selected @endif >{{$d}}</option>
							@endif
						@endforeach
					</select>

				<div class="hide_print">
					<br />
					<label for="" class="col-sm-2 col-form-label">&emsp;</label>
					<input type="checkbox" class="btn-check" id="match_lot" name="match_lot" autocomplete="off" onchange="check_match_lot();">
					<label class="btn btn-outline-primary" for="match_lot">ロット番号での照合</label>

					<br /><br />
					<label for="" class="col-sm-2 col-form-label">ロット番号</label>
					<input type="button" id="disp_lot" class="btn btn-primary" onclick="disp_lots();" value=" 表示 ">&emsp;
					<input type="button" id="hide_lot" class="btn btn-primary" onclick="hide_lots();" value="非表示">
					&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;

					<input type="button" id="search-submit" class="btn btn-primary" onclick="cmd_search();" value="検索">
				</div>

				<script>
				function cmd_search() {
					document.forms.method = 'get';
					document.forms.action = "/wp-admin/admin.php?page=stock-export&action=search"
					document.forms.cmd.value = 'search';
					document.forms.submit();
				}

				window.onload = function () {
					const match_lot = '{{$get->match_lot}}';
					if (match_lot == 'on' || match_lot == 1) {
						document.getElementById('match_lot').checked = true;
					}
				}

				function check_match_lot() {
					if (document.getElementById('match_lot').checked) {
						document.getElementById('match_lot').value = 1; // true
					}
				}

				function disp_lots() {
					var lot_area = document.getElementsByClassName("lot_area");
//					lot_area[0].hidden = false;
					Object.keys(lot_area).forEach(function(i) {
//						console.log(i);
						lot_area[i].hidden = false;
					});

				}

				function hide_lots() {
					var lot_area = document.getElementsByClassName("lot_area");
//					lot_area[0].hidden = true;
					Object.keys(lot_area).forEach(function(i) {
//						console.log(i);
						lot_area[i].hidden = true;
					});
				}
				</script>
			</div>
			<br />

			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="stock-export">
			<input type="hidden" name="action" value="search">
			<input type="hidden" name="cmd" value="">
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
						<th class="col-md-1">No.</th>
						<th class="col-md-2">製品名</th>
						<th class="col-md-1">荷姿・容量(kgTB)</th>
						<th class="col-md-1">個数</th>
						<th class="col-md-1">数量(kg)</th>
						<th class="">備考</th>
					</tr>
				</thead>

				@if (isset($rows) && count($rows))
				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($rows as $i => $row)
					<tr id="">
						<td class="tx-center">{{$i+1}}</td>
						<td class="">{{$row->goods_name}}</td>
						<td class="tx-right">{{$row->qty}}</td>
						<td class="tx-right">{{number_format($row->cnt)}}</td>
						<td class="tx-right">{{number_format($row->stock_total)}}</td>
						<td class=""><span class="lot_area">{{$row->lots}}</span></td>
					</tr>
					@endforeach
				@else
				<td class="colspanchange" colspan="7">検索対象は見つかりませんでした。</td>
				@endif
				</tbody>

				<tfoot class="table-light">
					<tr>
						<th class="tx-right" colspan="3">合計</th>
						<th class="tx-right">{{number_format($stock_cnt)}}</th>
						<th class="tx-right">{{number_format($stock_sum)}}</th>
						<th class=""></th>
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

@media print {
	.hide_print {
		display: none;
	}
}
</style>
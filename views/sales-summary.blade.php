<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【注文集計】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">
		<br />

		<form name="forms" id="forms" action="" method="" enctype="multipart/form-data">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}
			<div class="search-box">

				<label for="customer_name" class="col-sm-2 col-form-label">注文者名：</label>
					<input type="search" id="customer_name" name="s[customer_name]" value="<?php echo htmlspecialchars($get->s['customer_name']); ?>"><br /><br />

				<label for="tank" class="col-sm-2 col-form-label">配送先：</label>
					<input type="search" id="customer_name" name="s[tank]" value="<?php echo htmlspecialchars($get->s['tank']); ?>"><br /><br />

				<label for="goods_name" class="col-sm-2 col-form-label">商品名：</label>
					<input type="search" id="goods_name" name="s[goods_name]" value="<?php echo htmlspecialchars($get->s['goods_name']); ?>"><br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">配送日：</label>
					<input type="date" id="user-search-input" name="s[delivery_s_dt]" value="<?php echo htmlspecialchars($get->s['delivery_s_dt']); ?>" placeholder="2020-11-01">&emsp;～&emsp;
					<input type="date" id="user-search-input" name="s[delivery_e_dt]" value="<?php echo htmlspecialchars($get->s['delivery_e_dt']); ?>" placeholder="2022-12-01"><br /><br />

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
					&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;

				<span class="">
					<input type="button" id="search-submit" class="btn btn-primary" onclick="cmd_search();" value="検索">
					&emsp;&emsp;
				</span>

			</div>
			<br />

			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="sales-summary">
			<input type="hidden" name="action" value="search">
			<input type="hidden" name="cmd" value="">
		</form>
{{--			@endif	--}}

		<!-- start -->
		<div class="table-responsive">
			<div class="title-box">■ ※<!--①～⑥、⑧、⑨ （※配送予定表の①～⑥、⑧、⑨を集計します。）--></div>
			@if (isset($rows) && count($rows))
			<table class="table table-bordered text-nowrap">
				<!-- thead -->
				<tr class="table-light">
					<th class="">No.</th>
					<th class="">品名</th>
					<th class="">容量</th>
					<th class="">量目(t)</th>
					<th class="">出庫倉庫</th>
					<th class="">備考</th>
					<th class="">配送先</th>
				</tr>

				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($rows as $i => $row)
					<tr id="">
						<td class="">{{$i+1}}</td>
						<td class="">{{$row->goods_name}}</td>
						<th class="tx-center">@if ($row->separately_fg == false)（T）@else（B）@endif</th>
						<td class="tx-right">{{number_format($row->sum_qty,1)}}</td>
						<td class="">{{$initForm['select']['outgoing_warehouse'][$row->outgoing_warehouse]}}</td>
						<td class="">{{$row->customer_name}}</td>
						<td class="">{{$row->tank}}　:{{$row->result_ship_addr}}</td>
					</tr>
					@endforeach
				</tbody>

				<!-- tfoot -->
				<tr>
					<th class="">&emsp;</th>
					<th class="">&emsp;</th>
					<th class="">&emsp;</th>
					<th class="">&emsp;</th>
					<th class="">&emsp;</th>
					<th class="">&emsp;</th>
					<th class="">&emsp;</th>
				</tr>
				<tr>
					<th class="table-light tx-right" colspan="3">合計</th>
					<td class="tx-right">{{number_format($total,1)}}</td>
					<th class="">&emsp;</th>
					<th class="">&emsp;</th>
					<th class="">&emsp;</th>
				</tr>

			</table>
			@endif
		</div>
		<!-- end -->
	</div>
</div>

<script>
/**
 * 検索実行
 **/
function cmd_search() {
	document.forms.method = 'get';
	document.forms.action = "/wp-admin/admin.php?page=sales-summary&sales={{$get->sales}}&goods={{$get->goods}}&action=search"
	document.forms.cmd.value = 'search';
	document.forms.submit();
}

/**
 * 画面ロード時の処理
 **/
window.onload = function () {
}

</script>

<style>
.tx-right {
	text-align: right;
}

.tx-center {
	text-align: center;
}

.title-box {
	margin-top: 30px;
}
</style>
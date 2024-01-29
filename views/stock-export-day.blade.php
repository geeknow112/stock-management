<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【倉出伝票】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">
		<br />

		<form name="forms" id="forms" action="" method="" enctype="multipart/form-data">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}
			<div class="search-box">

				<label for="carModel" class="col-sm-2 col-form-label">配送日：</label>
					<input type="date" id="user-search-input" name="s[delivery_s_dt]" value="<?php echo htmlspecialchars($get->s['delivery_s_dt']); ?>" placeholder="2020-11-01"><br /><br />

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
					&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;

				<span class="hide_print">
					<input type="button" id="search-submit" class="btn btn-primary" onclick="cmd_search();" value="検索">
					&emsp;&emsp;
					<input type="button" id="btn_print" class="btn btn-danger" onclick="exe_print();" value="印刷">
				</span>

			</div>
			<br />

			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="stock-export-day">
			<input type="hidden" name="action" value="search">
			<input type="hidden" name="cmd" value="">
		</form>
{{--			@endif	--}}

		<!-- start -->
		<div class="table-responsive">
			<div class="title-box">■ ※①～⑥、⑧、⑨ （※配送予定表の①～⑥、⑧、⑨を集計します。）</div>
			@if (isset($rows) && count($rows))
			<table class="table table-bordered text-nowrap">
				<!-- thead -->
				<tr class="table-light">
					<th class="">No.</th>
					<th class="">品名</th>
					<th class="">容量</th>
					<th class="">量目(t)</th>
					<th class="">備考</th>
				</tr>

				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($rows as $i => $row)
					<tr id="">
						<td class="">{{$i+1}}</td>
						<td class="">{{$row->goods_name}}</td>
						<th class="tx-center">@if ($row->separately_fg == false)（T）@else（B）@endif</th>
						<td class="tx-right">{{number_format($row->qty,1)}}</td>
						<th class="">{{$row->customer_name}}</th>
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
				</tr>
				<tr>
					<th class="table-light">運送会社</th>
					<th class="">内藤運送</th>
					<th class="" colspan="3"></th>
				</tr>

			</table>
			@endif
		</div>
		<!-- end -->

		<!-- start -->
		<div class="table-responsive">
			<div class="title-box">■ 【直取】　※⑩ （※配送予定表の⑩を集計します。）</div>
			@if (isset($jks) && count($jks))
			<table class="table table-bordered text-nowrap">
				<!-- thead -->
				<tr class="table-light">
					<th class="">No.</th>
					<th class="">品名</th>
					<th class="">容量</th>
					<th class="">量目(t)</th>
					<th class="">備考</th>
				</tr>

				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($jks as $i => $jk)
					<tr id="">
						<td class="">{{$i+1}}</td>
						<td class="">{{$jk->goods_name}}</td>
						<th class="tx-center">（T）</th>
						<td class="tx-right">{{number_format($jk->qty,1)}}</td>
						<th class="">{{$jk->customer_name}}</th>
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
				</tr>
				<tr>
					<th class="table-light">運送会社</th>
					<th class="">山忠商事</th>
					<th class="" colspan="3"></th>
				</tr>
			</table>
			@endif
		</div>
		<!-- end -->

		<!-- start -->
		<div class="table-responsive">
			<div class="title-box">■ 【転送】　丹波SP ➤ 内藤SP</div>
			@if (isset($trans_t_n) && count($trans_t_n))
			<table class="table table-bordered text-nowrap">
				<!-- thead -->
				<tr class="table-light">
					<th class="">No.</th>
					<th class="">品名</th>
					<th class="">容量</th>
					<th class="">量目(t)</th>
					<th class="">備考</th>
				</tr>

				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($trans_t_n as $i => $tf)
					<tr id="">
						<td class="">{{$i+1}}</td>
						<td class="">{{$tf->goods_name}}</td>
						<th class="tx-center">（T）</th>
						<td class="tx-right">{{number_format($tf->qty,1)}}</td>
						<th class="">{{$tf->customer_name}}</th>
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
				</tr>
				<tr>
					<th class="table-light">運送会社</th>
					<th class="">内藤運送</th>
					<th class="" colspan="3"></th>
				</tr>
			</table>
			@endif
		</div>
		<!-- end -->

		<!-- start -->
		<div class="table-responsive">
			<div class="title-box">■ 【転送】　内藤SP ➤ 丹波SP</div>
			@if (isset($trans_n_t) && count($trans_n_t))
			<table class="table table-bordered text-nowrap">
				<!-- thead -->
				<tr class="table-light">
					<th class="">No.</th>
					<th class="">品名</th>
					<th class="">容量</th>
					<th class="">量目(t)</th>
					<th class="">備考</th>
				</tr>

				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($trans_n_t as $i => $tf)
					<tr id="">
						<td class="">{{$i+1}}</td>
						<td class="">{{$tf->goods_name}}</td>
						<th class="tx-center">（T）</th>
						<td class="tx-right">{{number_format($tf->qty,1)}}</td>
						<th class="">{{$tf->customer_name}}</th>
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
				</tr>
				<tr>
					<th class="table-light">運送会社</th>
					<th class="">内藤運送</th>
					<th class="" colspan="3"></th>
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
	document.forms.action = "/wp-admin/admin.php?page=stock-export-day&sales={{$get->sales}}&goods={{$get->goods}}&action=search"
	document.forms.cmd.value = 'search';
	document.forms.submit();
}

/**
 * 画面ロード時の処理
 **/
window.onload = function () {
}

/**
 * 印刷時に不要なメニュー等を非表示
 **/
function exe_print() {
	// 印刷時に不要なパーツを非表示
	const menu = document.getElementById('adminmenumain');
	const wpfooter = document.getElementById('wpfooter');
	const footer = document.getElementById('footer-upgrade');
	const wpauth = document.getElementById('wp-auth-check-wrap');

	menu.classList.add('hide_print');
	wpfooter.classList.add('hide_print');
	footer.classList.add('hide_print');
	wpauth.classList.add('hide_print');

	// 左メニューを閉じる
	var cBtn = document.getElementById('collapse-button');
	//console.log(cBtn.ariaExpanded);
	if (cBtn.ariaExpanded == 'true') {
		//console.log('exc menu close.');
		cBtn.click();
	}

	window.print();
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

@media print {
	.hide_print {
		display: none;
	}

	body * {
		visibility: hidden;
	}

	#wpbody-content * {
		visibility: visible;
	}

	#wpbody-content {
		position: absolute;
		top: 0;
		left: 0;
	}
}
</style>
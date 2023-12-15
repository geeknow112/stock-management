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

				<label for="carModel" class="col-sm-2 col-form-label">引取(入庫)予定日：</label>
					<input type="date" id="user-search-input" name="s[arrival_s_dt]" value="<?php echo htmlspecialchars($get->s['arrival_s_dt']); ?>" placeholder="2020-11-01"><!--&emsp;～&emsp;
				<input type="date" id="user-search-input" name="s[arrival_e_dt]" value="<?php echo htmlspecialchars($g['s']['arrival_e_dt']); ?>" placeholder="2022-12-01">&emsp;--><br /><br />

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
					&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;

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
						<th class="">倉庫</th>
					</tr>
				</thead>

				@if (isset($sum_list) && count($sum_list))
				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($sum_list as $goods => $row)
					<tr id="">
						<td class="">&emsp;</td>
						<td class="" onclick="changeDisplay({{$goods}});" @if (isset($row->repeat)) style="background: pink;" @endif><a href="#">{{$row->goods_name}}</a></td>
						<td class="tx-center">{{number_format(array_sum($row->qty),1)}}</td>
						<td class="">{{$initForm['select']['outgoing_warehouse'][$row->outgoing_warehouse]}}</td>
					</tr>
						@foreach ($detail[$goods] as $customer => $data)
							@foreach ($data as $i => $d)
							<tr class="detail d_{{$goods}}" id="detail_{{$goods}}_{{$i}}">
								<td class="">&emsp;</td>
								<td class="table-light tx-center">　<b>- 顧客：</b>( {{$d->customer_name}} )</td>
								<td class="table-info tx-right">{{number_format($d->qty,1)}}</td>
								<td class=""></td>
							</tr>
							@endforeach
						@endforeach
					@endforeach
				@else
				<td class="colspanchange" colspan="7">検索対象は見つかりませんでした。</td>
				@endif
				</tbody>

<script>
/**
 * 初期状態: 非表示
 * 
 **/
window.onload = function () {
	const className = 'detail';
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
 * クリックで詳細を表示
 * 
 **/
function changeDisplay(goods = null) {
	console.log(goods);
	const className = 'd_' + goods;
	const targets = document.getElementsByClassName(className);
	Array.from(targets).forEach(target => {
		if (target.style.display == "none") {
			target.style.display = "";
		} else {
			target.style.display = "none";
		}
	});
}
</script>

				<tfoot>
					<tr>
						<th class="">&emsp;</th>
						<th class="">&emsp;</th>
						<th class="">&emsp;</th>
						<th class="">&emsp;</th>
					</tr>
					<tr>
						<th class="table-light tx-right" colspan="2">合計</th>
						<td class="tx-right">{{number_format($total,1)}}</td>
						<td class="">&emsp;</td>
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
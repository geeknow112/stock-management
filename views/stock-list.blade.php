<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【在庫管理】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">

		<form name="forms" id="forms" action="" method="" enctype="multipart/form-data">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}
			<div class="search-box">

				<label for="carModel" class="col-sm-2 col-form-label">引取(入庫)予定日：</label>
					<input type="date" id="user-search-input" name="s[arrival_s_dt]" value="<?php echo htmlspecialchars($g['s']['arrival_s_dt']); ?>" placeholder="2020-11-01"><!--&emsp;～&emsp;
				<input type="date" id="user-search-input" name="s[arrival_e_dt]" value="<?php echo htmlspecialchars($g['s']['arrival_e_dt']); ?>" placeholder="2022-12-01">&emsp;--><br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">出庫倉庫：</label>
					<input type="search" id="user-search-input" name="s[outgoing_warehouse]" value="<?php echo htmlspecialchars($g['s']['outgoing_warehouse']); ?>" disabled>&emsp;&emsp;&emsp;&emsp;

				<input type="button" id="search-submit" class="btn btn-primary" onclick="cmd_search();" value="検索">

			</div>
			<br />

			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="sales-list">
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
			<b>日時：2022-12-**</b>
			<table class="table table-bordered text-nowrap">
				<thead class="table-light">
					<tr>
						<th class="">コード</th>
						<th class="">品名</th>
						<th class="">容量(kg)</th>
						<th class="">個数</th>
						<th class="">重量(kg)</th>
						<th class="">ロット番号</th>
						<th class="">備考</th>
						<th class="">適用</th>
					</tr>
				</thead>

				<tbody id="the-list" data-wp-lists="list:user">
					<tr>
						<td class=""><input type="text" value="43530"></td>
						<td class=""><input type="text" value="すこぶる育成　カミ"></td>
						<td class=""><input type="text" value="20"></td>
						<td class=""><input type="text" value="35"></td>
						<td class=""><input type="text" value="700"></td>
						<td class=""><input type="text" value=""></td>
						<td class=""><input type="text" value="パレット"></td>
						<td class=""><input type="text" value=""></td>
					</tr>
					<tr>
						<td class=""><input type="text" value="43565"></td>
						<td class=""><input type="text" value="繁殖用マミー　カミ"></td>
						<td class=""><input type="text" value="20"></td>
						<td class=""><input type="text" value="15"></td>
						<td class=""><input type="text" value="300"></td>
						<td class=""><input type="text" value=""></td>
						<td class=""><input type="text" value="パレット"></td>
						<td class=""><input type="text" value=""></td>
					</tr>
					<tr>
						<td class=""><input type="text" value="49120"></td>
						<td class=""><input type="text" value="みやび後期　カミ"></td>
						<td class=""><input type="text" value="20"></td>
						<td class=""><input type="text" value="50"></td>
						<td class=""><input type="text" value="1,000"></td>
						<td class=""><input type="text" value=""></td>
						<td class=""><input type="text" value="パレット"></td>
						<td class=""><input type="text" value=""></td>
					</tr>

{{--
			@if (isset($rows) && count($rows))
				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($rows as $delivery_dt => $list)
					<tr id="user-1">
						<td>
						</td>
					</tr>
					@endforeach
			@else
				<td class="colspanchange" colspan="7">検索対象は見つかりませんでした。</td>
			@endif
--}}
				</tbody>

				<tfoot>
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

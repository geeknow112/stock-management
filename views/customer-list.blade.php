<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【顧客検索】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">

		<form method="get">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}
			<div class="search-box">
				<label for="customer" class="col-sm-2 col-form-label">顧客番号：</label>
					<input type="search" id="customer" name="s[no]" value="<?php echo htmlspecialchars($get->s['no']); ?>"><br /><br />

				<label for="customer_name" class="col-sm-2 col-form-label">顧客名：</label>
					<input type="search" id="customer_name" name="s[customer_name]" value="<?php echo htmlspecialchars($get->s['customer_name']); ?>"><br /><br />
<!--
				<input type="submit" id="search-submit" class="button" value="申込者を検索">
-->
				<input type="submit" id="search-submit" class="btn btn-primary" value="検索">

			</div>

{{ $wp_list_table->display() }}

			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="customer-list">
			<input type="hidden" name="action" value="search">
{{--			@endif	--}}

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
						<th scope="col" id="username" class="">顧客番号</th>
						<th scope="col" id="username" class="">顧客名</th>
						<th scope="col" id="username" class=""></th>
						<th scope="col" id="username" class=""></th>
						<th scope="col" id="username" class=""></th>
						<th scope="col" id="username" class="">備考</th>
					</tr>
				</thead>

			@if (isset($rows) && count($rows))
				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($rows as $i => $list)
					<tr id="user-1">
						<td colspan="1">
							<a href="/wp-admin/admin.php?page=goods-detail&customer={{$list->customer}}&action=edit">{{ $list->customer }}</a>
						</td>
						<td>{{ $list->customer_name }}</td>
						<td></td>
						<td></td>
						<td></td>
						<td>{{ $list->remark }}</td>

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
-->
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
		location.href = location.protocol + "//" + location.hostname + "/wp-admin/admin.php?page=customer-list&post=" + applicant + "&action=init-status";
	}
}
</script>

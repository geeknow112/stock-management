<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
<?php
$tb = new Postmeta;

$g = $_GET;
//var_dump($g['s']);
?>
	<div class="wrap">
		<h1 class="wp-heading-inline">【注文検索】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">

		<form method="get">
			@if ($tb->getCurUser()->roles[0] == 'administrator')
			<div class="search-box">
				<label class="screen-reader-text" for="user-search-input">申込者を検索:</label>
				No. ：<input type="search" id="user-search-input" name="s[no]" value="<?php echo htmlspecialchars($g['s']['no']); ?>"><br /><br />
				商品：<input type="search" id="user-search-input" name="s[company_name]" value="<?php echo htmlspecialchars($g['s']['company_name']); ?>"><br /><br />
				ロット番号：<input type="search" id="user-search-input" name="s[company_name]" value="<?php echo htmlspecialchars($g['s']['company_name']); ?>"><br /><br />
				状態：<input type="search" id="user-search-input" name="s[company_name]" value="<?php echo htmlspecialchars($g['s']['company_name']); ?>"><br /><br />
				出庫倉庫：<input type="search" id="user-search-input" name="s[company_name]" value="<?php echo htmlspecialchars($g['s']['company_name']); ?>"><br /><br />

				注文日：<input type="date" id="user-search-input" name="s[sdt]" value="<?php echo htmlspecialchars($g['s']['sdt']); ?>" placeholder="2020-11-01">&emsp;～&emsp;
				<input type="date" id="user-search-input" name="s[edt]" value="<?php echo htmlspecialchars($g['s']['edt']); ?>" placeholder="2022-12-01"><br /><br />

				引取予定日：<input type="date" id="user-search-input" name="s[sdt]" value="<?php echo htmlspecialchars($g['s']['sdt']); ?>" placeholder="2020-11-01">&emsp;～&emsp;
				<input type="date" id="user-search-input" name="s[edt]" value="<?php echo htmlspecialchars($g['s']['edt']); ?>" placeholder="2022-12-01">&emsp;
<!--
				<input type="submit" id="search-submit" class="button" value="申込者を検索">
-->
				<button type="button" class="btn btn-primary">検索</button>

			</div>
			<br />

			<div class="search-box">
				<label for="cmd-select">一括操作：</label>
				<select name="cmd" id="cmd-select">
					<option value=""></option>
					<option value="confirm">確定</option>
					<option value="pending">未確定</option>
					<option value="delete">削除</option>
				</select>

				&emsp;
				<button type="button" class="btn btn-primary">適用</button>

			</div>
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="shop-list">
			<input type="hidden" name="action" value="search">
			@endif

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
							<input type="checkbox" />
						</td>

						<td>
							<a href="" onClick="window.open('/wp-admin/admin.php?page=sum-day-goods', 'regist lot number', 'popup', 'left=200,top=100,width=420,height=520');">{{ $list->id }}</a>
						</td>
						<td>{{ $list->rgdt }}</td>
						<td>{{ $list->name }}</td>
						<td>{{ $list->goods_name }}</td>
						<td>{{ $list->qty }}</td>
						<td></td>
						<td>{{ $list->arrival_dt }}</td>
						<td>{{ $list->delivery_dt }}</td>
						<td></td>

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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
	<div class="wrap">
		<h1 class="wp-heading-inline">【在庫検索】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">

		<form name="forms" id="forms" action="" method="" enctype="multipart/form-data">
{{--			@if ($tb->getCurUser()->roles[0] == 'administrator')	--}}
			<div class="search-box">
				<label for="goods" class="col-sm-2 col-form-label">在庫番号：</label>
					<input type="search" id="goods" name="s[no]" value="<?php echo htmlspecialchars($get->s['no']); ?>"><br /><br />

				<label for="goods_name" class="col-sm-2 col-form-label">商品名：</label>
					<input type="search" id="goods_name" name="s[goods_name]" value="<?php echo htmlspecialchars($get->s['goods_name']); ?>"><br /><br />

				<label for="qty" class="col-sm-2 col-form-label">荷姿・容量：</label>
					<input type="search" id="qty" name="s[qty]" value="<?php echo htmlspecialchars($get->s['qty']); ?>"><br /><br />

				<label for="lot" class="col-sm-2 col-form-label">ロット番号：</label>
					<input type="search" id="lot" name="s[lot]" value="<?php echo htmlspecialchars($get->s['lot']); ?>"><br /><br />

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

					<label for="" class="col-sm-2 col-form-label">&emsp;</label>
					<input type="checkbox" class="btn-check" id="transfer_fg" name="s[transfer_fg]" autocomplete="off" onchange="check_transfer_fg();">
					<label class="btn btn-outline-primary" for="transfer_fg">転送を抽出</label>
					&emsp;&emsp;<span id="" class="manual-text form-text">※ ON(青い状態)で、転送のみを抽出します。OFF(白い状態)では、転送を含むすべてを抽出します。</span>
					<br /><br />

				<label for="carModel" class="col-sm-2 col-form-label">引取(入庫)日：</label>
					<input type="date" id="user-search-input" name="s[arrival_s_dt]" value="{{$get->s['arrival_s_dt']}}" placeholder="2020-11-01">&emsp;～&emsp;
					<input type="date" id="user-search-input" name="s[arrival_e_dt]" value="{{$get->s['arrival_e_dt']}}" placeholder="2022-12-01">&emsp;

				<input type="button" id="search-submit" class="btn btn-primary" onclick="cmd_search();" value="検索">

				<script>
				function cmd_search() {
					document.forms.method = 'get';
					document.forms.action = "/wp-admin/admin.php?page=stock-list&sales={{$get->sales}}&goods={{$get->goods}}&action=search"
					document.forms.cmd.value = 'search';
					document.forms.submit();
				}

				function cancel_transfer(stock = null) {
					var ret = window.confirm('この「転送」処理を取り消しますか？');
					if (ret) {
						document.forms.method = 'post';
						document.forms.action = "/wp-admin/admin.php?page=stock-list&stock=" + stock;
						document.forms.cmd.value = 'cmd_cancel_transfer';
						document.forms.submit();
					} else {
					}
				}
				</script>
			</div>
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="5647b2c250">
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/users.php">-->
			<input type="hidden" name="page" value="stock-list">
			<input type="hidden" name="action" value="search">
			<input type="hidden" name="cmd" value="">
{{--			@endif	--}}


{{ $wp_list_table->display() }}

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
/**
 * 画面ロード時の処理
 **/
window.onload = function () {
	const transfer_fg = '{{$get->s["transfer_fg"]}}';
	if (transfer_fg == 'on' || transfer_fg == 1) {
		document.getElementById('transfer_fg').checked = true;
		document.getElementById('transfer_fg').value = 1; // true
	}
}

/**
 * 「転送を抽出」をcheckedにする処理
 **/
function check_transfer_fg() {
	if (document.getElementById('transfer_fg').checked) {
		document.getElementById('transfer_fg').value = 1; // true
	}
}
</script>

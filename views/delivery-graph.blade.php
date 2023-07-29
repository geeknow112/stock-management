<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<div id="wpbody-content">
<?php
$tb = new Postmeta;

$g = $_GET;
//var_dump($g['s']);
?>
	<div class="wrap">
		<h1 class="wp-heading-inline">【配送予定表③】</h1>
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page={{$formPage}}&action=regist" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->
		<!--<a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=agreement" name="cmd_regist" id="cmd_regist" class="page-title-action">新規登録</a>-->

		<hr class="wp-header-end">

		<form method="get">
			@if ($tb->getCurUser()->roles[0] == 'administrator')
			<div class="search-box">
				<label class="screen-reader-text" for="user-search-input">申込者を検索:</label>
				No. ：<input type="search" id="user-search-input" name="s[no]" value="<?php echo htmlspecialchars($g['s']['no']); ?>">&emsp;&emsp;&emsp;
				社名：<input type="search" id="user-search-input" name="s[company_name]" value="<?php echo htmlspecialchars($g['s']['company_name']); ?>"><br /><br />
				開始：<input type="date" id="user-search-input" name="s[sdt]" value="<?php echo htmlspecialchars($g['s']['sdt']); ?>" placeholder="2020-11-01">&emsp;～&emsp;
				終了：<input type="date" id="user-search-input" name="s[edt]" value="<?php echo htmlspecialchars($g['s']['edt']); ?>" placeholder="2022-12-01">&emsp;
<!--
				<input type="submit" id="search-submit" class="button" value="申込者を検索">
-->
				<button type="button" class="btn btn-primary">Primary</button>
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
						<th class=""></th>
						<th class=""></th>
						<th class=""></th>
						<th class="" colspan="6">6t 1</th>
						<th class="" colspan="6">6t 2</th>
						<th class="" colspan="6">6t 3</th>
						<th class="" colspan="6">6t 4</th>
						<th class="" colspan="6">6t 5</th>
						<th class="" colspan="6"></th>
						<th class="" colspan="6"></th>
					</tr>

					<tr>
						<th scope="col" id="username" class=""></th>
						<th scope="col" id="username" class="">曜</th>
						<th scope="col" id="username" class="">日</th>
						@for ($i = 0; $i < 7; $i++)
						<th class="" style="width: 7rem;">品名</th>
						<th class="" style="width: 7rem;">配送先</th>
						<th class="" style="width: 3rem;">量(t)</th>
						<th class="" style="width: 5rem;">入庫予定日</th>
						<th class="" style="width: 5rem;">氏名</th>
						<th class="" style="width: 5rem;">確認</th>
						@endfor
					</tr>
				</thead>

<?php	function innerTables($rows) {
			foreach ($rows as $deli_dt => $list) {
				foreach ($list as $i => $row) {
					$ret[$deli_dt][$row->class][] = $row;
				}
			}

//			echo '<pre>';			print_r($ret);			echo '</pre>';


			foreach ($ret as $deli_dt => $list) {
				foreach ($list as $class => $row) {
					echo '<tr>';
//					echo '<td class="" colspan="3" rowspan="'. count($list). '">'. $deli_dt. ' : '. count($list). '</td>';

//			echo '<pre>';			print_r($row);			echo '</pre>';

					for ($j = 0; $j < count($row); $j++) {
						echo '<td class="" colspan="3">'. $deli_dt. ' : '. count($list). '</td>';
						for ($i = 0; $i < 7; $i++) {
							$obj = $row[$j];

							$setClass = $i + 1;
							if ($obj->class == $setClass) {
								echo '<td class="">'. $obj->goods. ':'. $obj->class. ':'. $setClass. '</td>';
								echo '<td class="">'. $obj->ship_addr. '</td>';
								echo '<td class="">'. $obj->qty. '</td>';
								echo '<td class="">'. $obj->arrival_dt. '</td>';
								echo '<td class="">'. $obj->name. '</td>';
							} else {
								echo '<td class=""></td>';
								echo '<td class=""></td>';
								echo '<td class=""></td>';
								echo '<td class=""></td>';
								echo '<td class=""></td>';
							}
						}
						echo '</tr>';
					}
					echo '</tr>';
				}
/*
				for ($class = 0; $class <= count($list); $class++) {
//echo '<pre>';	print_r($list);			echo '</pre>';
					echo '<td class="">';
					echo '<td class="">';
					echo '<td class="">'. $deli_dt. ' : '. count($list). '</td>';
					for ($i = 0; $i < 7; $i++) {
							$obj = $list[$class][$i];
							$setClass = $i + 1;
							if ($obj->class == $setClass) {
								echo '<td class="">'. $obj->goods. ':'. $obj->class. ':'. $setClass. '</td>';
								echo '<td class="">'. $obj->ship_addr. '</td>';
								echo '<td class="">'. $obj->qty. '</td>';
								echo '<td class="">'. $obj->arrival_dt. '</td>';
								echo '<td class="">'. $obj->name. '</td>';
							} else {
								echo '<td class=""></td>';
								echo '<td class=""></td>';
								echo '<td class=""></td>';
								echo '<td class=""></td>';
								echo '<td class=""></td>';
							}
					}
					echo '</tr>';
				}
*/
				echo '</tr>';
			}
		}
?>

{{--
			@if (isset($rows) && count($rows))
				<tbody id="the-list" data-wp-lists="list:user">
				@php innerTables($rows); @endphp
			@else
				<td class="colspanchange" colspan="7">検索対象は見つかりませんでした。</td>
			@endif
				</tbody>
--}}

<style>
	.inner_box {
		width: 8rem; background: #eeeeee; border-right: 1px solid #d3d3d3;
	}
</style>

<?php	function innerTable($list, $class) {	?>
		<div style="width: 40rem;">
<!--	<div class="card" style="width: 40rem;">-->
		<?php foreach ($list as $i => $row) { ?>
			<?php if ($row->class == $class) { ?>
				<div class="d-flex flex-row bd-highlight mb-3">
					<div class="text-wrap text-center inner_box" style="width: 8rem;"><?php if ($row->repeat_fg != 1) { echo $row->goods_name; } else { echo '<span style="color:red;">'. $row->goods_name. '</span>'; } ?></div>
					<div class="text-wrap text-center inner_box" style="width: 9rem;"><?php echo $row->ship_addr; ?></div>
					<div class="text-wrap text-center inner_box" style="width: 3.5rem;"><?php echo $row->qty; ?></div>
					<div class="text-wrap text-center inner_box" style="width: 7.5rem;"><?php echo $row->arrival_dt; ?></div>
					<div class="text-wrap text-center inner_box" style="width: 6.5rem;"><?php echo $row->name; ?></div>
					<a href="" class="btn btn-secondary text-center" onClick="window.location = '/wp-admin/admin.php?page=lot-regist&order=<?php echo htmlspecialchars($row->id); ?>&goods=<?php echo htmlspecialchars($row->goods); ?>'; return false;">未登録</a>
				</div>
<!--
				<div class="card-body border mb-1">
					<h5 class="card-title">品名：<?php if ($row->repeat_fg != 1) { echo $row->goods_name; } else { echo '<span style="color:red;">'. $row->goods_name. '</span>'; } ?></h5>
					<p class="card-text">配送先：<?php echo $row->ship_addr; ?></p>
					<a href="" class="btn btn-primary" onClick="window.location = '/wp-admin/admin.php?page=lot-regist&order=<?php echo htmlspecialchars($row->id); ?>&goods=<?php echo htmlspecialchars($row->goods); ?>'; return false;">未登録</a>
				</div>
-->
<!--
				<div class="card mb-3" style="max-width: 540px;">
					<div class="row no-gutters">
						<div class="col-md-4">
							<svg class="bd-placeholder-img" width="100%" height="250" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: Image"><title>Placeholder</title><rect fill="#868e96" width="100%" height="100%"/><text fill="#dee2e6" dy=".3em" x="50%" y="50%">Image</text></svg>
						</div>
						<div class="col-md-8">
							<div class="card-body">
								<h5 class="card-title">品名：<?php if ($row->repeat_fg != 1) { echo $row->goods_name; } else { echo '<span style="color:red;">'. $row->goods_name. '</span>'; } ?></h5>
								<p class="card-text">配送先：<?php echo $row->ship_addr; ?></p>
								<p class="card-text">量(t)：<?php echo $row->qty; ?></p>
								<p class="card-text">入庫予定日：<?php echo $row->arrival_dt; ?></p>
								<p class="card-text">氏名：<?php echo $row->name; ?></p>
								<a href="" class="btn btn-primary" onClick="window.location = '/wp-admin/admin.php?page=lot-regist&order=<?php echo htmlspecialchars($row->id); ?>&goods=<?php echo htmlspecialchars($row->goods); ?>'; return false;">未登録</a>
							</div>
						</div>
					</div>
				</div>
-->
			<?php }	?>
		<?php }	?>
	</div>
<?php	}	?>
			@if (isset($rows) && count($rows))
				<tbody id="the-list" data-wp-lists="list:user">
					@foreach ($rows as $delivery_dt => $list)
					<tr id="user-1">
						<td colspan="3">
							<a href="" onClick="window.open('/wp-admin/admin.php?page=sum-day-goods', 'regist lot number', 'popup', 'left=200,top=100,width=420,height=520');">{{$delivery_dt}}</a>
						</td>

						<!-- 6t 1 -->
						<td colspan="6">
							@php innerTable($list, 1); @endphp
						</td>

						<!-- 6t 2 -->
						<td colspan="6">
							@php innerTable($list, 2); @endphp
						</td>

						<!-- 6t 3 -->
						<td colspan="6">
							@php innerTable($list, 3); @endphp
						</td>

						<!-- 6t 4 -->
						<td colspan="6">
							@php innerTable($list, 4); @endphp
						</td>
						<!-- 6t 5 -->
						<td colspan="6">
							@php innerTable($list, 5); @endphp
						</td>
						<!--  -->
						<td colspan="6">
							@php innerTable($list, 6); @endphp
						</td>
						<!--  -->
						<td colspan="6">
							@php innerTable($list, 7); @endphp
						</td>
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

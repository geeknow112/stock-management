<p class="">
<legend>【在庫ロット番号登録】</legend>
</p>

<div class="tablenav top">
	<br class="clear">
</div>

@if (wp_get_current_user()->roles[0] == 'administrator' && wp_get_current_user()->user_login == 'user')
<div>
	<input type="text" id="input_lot" class="">
	<input type="number" min="0" id="times" class="">
	<input type="button" id="btn_bulk_lot_input" class="btn btn-success" onclick="bulk_lot_input();" value="ロット複数入力">
</div>
@endif

<div class="table-responsive">
	<div>
		<table class="table table-bordered text-nowrap">
			<thead class="table-light">
				<tr>
					<th class="">品名</th>
					<th class="">量(t)</th>
					<th class="">ロット番号</th>
				</tr>
			</thead>

			<tbody id="the-list" data-wp-lists="list:user">
				@if (!empty(current($rows)))
					<input type="hidden" id="arrival_dt" name="arrival_dt" value="{{$get->arrival_dt}}" />
					<input type="hidden" id="warehouse" name="warehouse" value="{{$get->warehouse}}" />
					<input type="hidden" id="stock" name="stock" value="{{$get->stock}}" />
					<input type="hidden" id="goods" name="goods" value="{{$get->goods}}" />
					<input type="hidden" id="goods" name="goods_name" value="{{current($rows)->goods_name}}" />
					@foreach ($rows as $i => $d)
					<tr id="user-1">
						<td class="">{{$d->goods_name}}</td>
						<td class="">0.5</td>
						<td class=""><input type="text" class="lots" id="lot" name="lot[{{$i}}]" value="{{$d->lot}}"></td>
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
		<input type="button" id="btn_back" class="btn btn-success" onclick="to_back();" value="◀ 戻る">
</div>

<script>
window.onload = function () {
	// 入庫日のhidden値 保持
	const p_sdt = "{{$post->arrival_dt}}";
	const g_sdt = "{{$get->arrival_dt}}";
	const r_sdt = "{{current($rows)->arrival_dt}}";
	var sdt = document.getElementById('arrival_dt');

	console.log('p_sdt:' + p_sdt);
	console.log('g_sdt:' + g_sdt);
	console.log('r_sdt:' + r_sdt);

	if (p_sdt) {
		sdt.value = p_sdt;
	} else {
		if (g_sdt) {
			sdt.value = g_sdt;
		} else {
			sdt.value = r_sdt;
		}
	}

	// 入庫倉庫のhidden値 保持
	const p_wh = "{{$post->warehouse}}";
	const g_wh = "{{$get->warehouse}}";
	const r_wh = "{{current($rows)->warehouse}}";
	var wh = document.getElementById('warehouse');

	console.log('p_wh:' + p_wh);
	console.log('g_wh:' + g_wh);
	console.log('r_wh:' + r_wh);

	if (p_wh) {
		wh.value = p_wh;
	} else {
		if (g_wh) {
			wh.value = g_wh;
		} else {
			wh.value = r_wh;
		}
	}
}

function to_back() {
	const ref = "{{$_SERVER['HTTP_REFERER']}}";
//	console.log(ref);
	var result = unescapeHtml(ref);
//	console.log(result);

	const regex = /page=stock-detail|page=stock-list/g;
	const ret = result.search(regex);

	console.log('search : ' + ret);

	if (ret < 0) {
		var arrival_dt = document.getElementById('arrival_dt').value;
		var warehouse = document.getElementById('warehouse').value;
		window.location = "/wp-admin/admin.php?page=stock-detail&arrival_dt=" + arrival_dt + "&warehouse=" + warehouse + "&action=edit";

	} else {
		window.location = result;

	}
}

/**
 * HTML文字列をアンエスケープ
 * @param {string} str エスケープされたHTML文字列
 * @return {string} アンエスケープされたHTML文字列を返す
 */
var unescapeHtml = function(str) {
	if (typeof str !== 'string') return str;

	var patterns = {
		'&lt;'   : '<',
		'&gt;'   : '>',
		'&amp;'  : '&',
		'&quot;' : '"',
		'&#x27;' : '\'',
		'&#x60;' : '`'
	};

	return str.replace(/&(lt|gt|amp|quot|#x27|#x60);/g, function(match) {
		return patterns[match];
	});
};

function bulk_lot_input() {
	const input_lot = document.getElementById('input_lot').value;
	const times = document.getElementById('times').value;
	const lots = document.getElementsByClassName('lots');
	console.log(input_lot);
	console.log(times);
	for (var i=0; i<times; i++) {
		lots[i].value = input_lot;
	}
}
</script>
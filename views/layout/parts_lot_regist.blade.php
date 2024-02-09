<p class="">
<legend>【ロット番号登録】</legend>
</p>

<div class="tablenav top">
	<br class="clear">
</div>

<div>
	<input type="text" id="input_lot" class="">
	<input type="number" min="0" id="times" class="">&emsp;&emsp;
	<input type="button" id="btn_bulk_lot_input" class="btn btn-success" onclick="bulk_lot_input();" value="ロット複数入力">
	<br><br>

	@if (wp_get_current_user()->roles[0] == 'administrator' && wp_get_current_user()->user_login == 'user')
	<textarea id="input_barcode" class="" style="width: 400px; height: 100px;"></textarea>&emsp;
	<input type="button" id="btn_bulk_barcode_input" class="btn btn-success" onclick="bulk_barcode_input();" value="バーコード複数入力">
	<br><br>
	@endif
</div>

<div class="table-responsive">
	<div>
		<table class="table table-bordered text-nowrap">
			<thead class="table-light">
				<tr>
					<th class="">品名</th>
					<th class="">配送先</th>
					<th class="">量(t)</th>
					<th class="">入庫予定日</th>
					<th class="">氏名</th>
					<th class="hide_area">タンク番号</th>
					<th class="">ロット番号</th>
					<th class="">ロット(バーコード)</th>
				</tr>
			</thead>

			<tbody id="the-list" data-wp-lists="list:user">
				<input type="hidden" id="sales" name="sales" value="{{$get->sales}}">
				<input type="hidden" id="goods" name="goods" value="{{$get->goods}}">
				<input type="hidden" id="sdt" name="sdt" value="">
				@if (isset($rows) && count($rows))
					@foreach ($rows as $i => $d)
					<tr id="user-1">
						<input type="hidden" id="lot_tmp_id" name="lot_tmp_id[]" value="{{$d->lot_tmp_id}}">
						<td>{{$d->goods_name}}</td>
						<td>{{$initForm['select']['ship_addr'][$d->customer][$d->ship_addr]}}</td>
						<td>{{$d->goods_qty}}</td>
						<td>{{$d->arrival_dt}}</td>
						<td>{{$d->customer_name}}</td>
						<td class="hide_area">
						<input type="text" class="" id="tank" name="tank[{{$d->lot_tmp_id}}]" value="{{$d->tank}}">
						</td>
						<td class="">
						<input type="text" class="lots" id="lot" name="lot[{{$d->lot_tmp_id}}]" value="{{$d->lot}}">
						</td>
						<td class="">
						<input type="text" class="barcodes" id="barcode" name="barcode[{{$d->lot_tmp_id}}]" value="{{$d->barcode}}">
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
		<div>
			<input type="button" id="btn_back" class="btn btn-success" onclick="to_back();" value="◀ 戻る">
<script>
window.onload = function () {
	const p_sdt = "{{$post->sdt}}";
	const g_sdt = "{{$get->s['sdt']}}";
	var sdt = document.getElementById('sdt');
	console.log('p_sdt:' + p_sdt);
	console.log('g_sdt:' + g_sdt);
	if (p_sdt) {
		sdt.value = p_sdt;
	} else {
		sdt.value = g_sdt;
	}
}

function to_back() {
	const ref = "{{$_SERVER['HTTP_REFERER']}}";
//	console.log(ref);
	var result = unescapeHtml(ref);
//	console.log(result);

	const regex = /page=delivery-graph|page=sales-list/g;
	const ret = result.search(regex);

	console.log('search : ' + ret);

	if (ret < 0) {
		var sdt = document.getElementById('sdt').value;
		window.location = "/wp-admin/admin.php?s[sdt]=" + sdt + "&page=delivery-graph&action=search&cmd=search";

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

/**
 * ロット複数入力
 *  - ロット番号、ループ回数を入力することで、回数分連続登録することが可能
 **/
function bulk_lot_input() {
	// ロット番号
	const input_lot = document.getElementById('input_lot').value;
	// ループ回数
	const times = document.getElementById('times').value;

	// 代入用配列
	const lots = document.getElementsByClassName('lots');
//	console.log(input_lot);
//	console.log(times);

	// 1. 入力用配列を作る(入力コード * ループ回数 をもとに)
	const inputs = [];
	for (var i=0; i<times; i++) {
		inputs.push(input_lot);
	}

	// 2. 代入用配列を欄数回ループする
	for (var i=0; i<lots.length; i++) {

		// 代入用欄が空白かチェック
		if (lots[i].value === '') {
			// 2-1. 空白の場合、入力用配列を一つ代入し、代入したものは入力用配列から削除する。
			lots[i].value = inputs.shift(); // 先頭を代入 + 先頭を削除
		} else {
			// 2-2. 空白以外の場合、スキップする。
		}

		// 3. 入力用配列が空になったら処理を終える。
//		console.log(inputs);
		if (inputs.length == 0) {
			return false;
		}
	}
}

/**
 * バーコード複数入力
 *  - バーコード一覧を入力することで、回数分連続登録することが可能
 **/
function bulk_barcode_input() {
	const input_barcode = document.getElementById('input_barcode').value;
	const barcodes = document.getElementsByClassName('barcodes');
	const sp = input_barcode.split('\n');
//	console.log(sp.length);
	for (var i=0; i<sp.length; i++) {
		if (!sp[i]) { continue; }
		barcodes[i].value = sp[i];
	}
}
</script>
		</div>
	</div>
</div>

<style>
.hide_area {
	display: none;
}
</style>
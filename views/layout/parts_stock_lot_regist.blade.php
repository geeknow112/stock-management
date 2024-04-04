<p class="">
<legend>【在庫ロット番号登録】</legend>
</p>

<div class="tablenav top">
	<br class="clear">
</div>

<div>
	<p class="">■ 入力補助機能：ロット番号繰り返し入力</p>
	<input type="text" id="input_lot" class="" placeholder="ロット番号">
	<input type="number" min="0" id="times" class="" placeholder="回数">&emsp;&emsp;
	<input type="button" id="btn_bulk_lot_input" class="btn btn-success" onclick="bulk_lot_input();" value="ロット複数入力">
	<br>
	<span id="" class="manual-text form-text">※ ロット番号と、回数を入力して「ロット複数入力」をクリックすると、下記のロット番号登録欄に回数分コピーします。<br>　また、再入力の場合は、空欄からコピーを開始します。</span>
	<br><br>
</div>

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
		<input type="button" id="btn_back" class="btn btn-success" onclick="to_search();" value="◀ 検索画面へ戻る">
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

/**
 * [戻る]ボタン用の処理
 * 
 **/
function to_back() {
	const ref = "{{$_SERVER['HTTP_REFERER']}}";
//	console.log(ref);
	var result = unescapeHtml(ref);
//	console.log(result);

	const regex = /page=stock-bulk|page=stock-list/g;
	const ret = result.search(regex);

	console.log('search : ' + ret);

	if (ret < 0) {
		var arrival_dt = document.getElementById('arrival_dt').value;
		var warehouse = document.getElementById('warehouse').value;
		window.location = "/wp-admin/admin.php?page=stock-bulk&arrival_dt=" + arrival_dt + "&warehouse=" + warehouse + "&action=edit";

	} else {
		window.location = result;

	}
}

/**
 * [検索画面へ戻る]ボタン用の処理
 * 
 **/
function to_search() {
	const page = 'stock-list';
	const ref = "{{$_SESSION['stock-list']}}";
//	console.log(ref);
	var result = unescapeHtml(ref);
//	console.log(result);

	if (result) {
		window.location = result;
	} else {
		window.location = "/wp-admin/admin.php?page=" + page;
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
</script>
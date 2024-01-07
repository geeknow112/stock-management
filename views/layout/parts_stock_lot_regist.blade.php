<p class="">
<legend>【在庫ロット番号登録】</legend>
</p>

<div class="tablenav top">
	<br class="clear">
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
						<td class=""><input type="text" id="lot" name="lot[{{$i}}]" value="{{$d->lot}}"></td>
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
function to_back() {
	const ref = "{{$_SERVER['HTTP_REFERER']}}";
//	console.log(ref);
	var result = unescapeHtml(ref);
//	console.log(result);
	window.location = result;
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
</script>
@extends ('layout.common')

<!--<h1>登録　ステップ１</h1>-->

@section ('contents2')
	@include ('layout.parts_stock_regist', ['rows' => $rows])

@endsection

@section ('contents4')
	<br /><br /><hr>
	@if ($get->action != '' && $_SESSION['stock-list'] != '')
	<div class="table-responsive">
<!--		<input type="button" id="btn_back" class="btn btn-success" onclick="to_back();" value="◀ 戻る">-->
		<input type="button" id="btn_back" class="btn btn-success" onclick="to_search();" value="◀ 検索画面へ戻る">
	</div>
	@endif

@endsection

<script>
	function to_next() {
		document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=stock-detail&action=confirm";

//		var ch = document.getElementById('cmd_confirm').checked;
//		document.forms.cmd.value = (ch != true) ? "cmd_regist" : "cmd_confirm";
		document.forms.cmd.value = "cmd_confirm";
		document.forms.submit();
	}
</script>
<script>
	/**
	 * [戻る]ボタン用の処理
	 * 
	 **/
	function to_back() {
		const ref = "{{$_SERVER['HTTP_REFERER']}}";
//		console.log(ref);
		var result = unescapeHtml(ref);
//		console.log(result);

//		const regex = /page=stock-detail|page=stock-list/g;
		const regex = /page=stock-bulk/g;
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
		const page = "{{$get->page}}";
		const ref = "{{$_SESSION['stock-list']}}";
//		console.log(ref);
		var result = unescapeHtml(ref);
//		console.log(result);

		const regex = /page=stock-list/g;
		const ret = result.search(regex);

		console.log('search : ' + ret);

		if (ret < 0) {
			console.log(page);
			window.location = "/wp-admin/admin.php?page=" + page;
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
</script>
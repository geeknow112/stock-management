@extends ('layout.common')

<!--<h1>登録　ステップ１</h1>-->

@section ('contents2')
	@include ('layout.parts_stock_regist', ['rows' => $rows])

@endsection

@section ('contents9')
{{--	@include ('layout.parts_transfer_process', ['rows' => $rows])--}}
	<div class="table-responsive">
		<div id="title">■ 転送処理</div>
		　<a href="{{home_url()}}/wp-admin/admin.php?page=stock-transfer">転送処理画面へ</a>
		<hr>
	</div>
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
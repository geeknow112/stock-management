@extends ('layout.common')

<!--<h1>登録　ステップ１</h1>-->

@section ('contents2')
	@include ('layout.parts_transfer_process', ['rows' => $rows])

@endsection

<script>
	function to_next() {
		document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=stock-transfer&action=confirm";

//		var ch = document.getElementById('cmd_confirm').checked;
//		document.forms.cmd.value = (ch != true) ? "cmd_regist" : "cmd_confirm";
		document.forms.cmd.value = "cmd_confirm";
		document.forms.submit();
	}
</script>
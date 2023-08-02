@extends ('layout.common')

<!--<h1>登録　ステップ１</h1>-->

@section ('contents2')
	@include ('layout.parts_lot_regist', ['rows' => $rows])

@endsection

<script>
	function to_next() {
		document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=lot-regist&sales={{$get->sales}}&goods={{$get->goods}}&action=confirm";
		document.forms.cmd.value = "cmd_confirm";
		document.forms.submit();
	}
</script>

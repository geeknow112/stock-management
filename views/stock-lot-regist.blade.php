@extends ('layout.common')

<!--<h1>登録　ステップ１</h1>-->

@section ('contents2')
	@include ('layout.parts_stock_lot_regist', ['rows' => $rows])

@endsection

<script>
	function to_next() {
		document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=stock-lot-regist&stock={{$get->stock}}&goods={{$get->goods}}&arrival_dt={{$get->arrival_dt}}&warehouse={{$get->warehouse}}&action=confirm";
		document.forms.cmd.value = "cmd_confirm";
		document.forms.submit();
	}
</script>

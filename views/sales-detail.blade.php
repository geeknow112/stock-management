@extends ('layout.common')

<!--<h1>登録　ステップ１</h1>-->

@section ('contents2')
	@include ('layout.parts_order_regist', ['rows' => $rows])

@endsection

@section ('contents3')
	@include ('layout.parts_repeat_regist', ['rows' => $rows])

@endsection
<!--
<script>
	function to_next() {
		document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=step1";

		var ch = document.getElementById('cmd_confirm').checked;
		document.forms.cmd.value = (ch != true) ? "cmd_regist" : "cmd_confirm";
		document.forms.submit();
	}
</script>
-->
<script>
	function checkRepeat() {
		//alert('test');
		const p1 = document.getElementById("repeat_info");

		if(p1.style.display=="block"){
			// noneで非表示
			p1.style.display ="none";
		}else{
			// blockで表示
			p1.style.display ="block";
		}
	}
</script>
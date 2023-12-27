@extends ('layout.common')

<!--<h1>登録　ステップ１</h1>-->

@section ('contents2')
	@include ('layout.parts_order_regist', ['rows' => $rows])

@endsection

@section ('contents3')
	@include ('layout.parts_repeat_regist', ['rows' => $rows])

@endsection
<script>
	function to_next() {
		document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=sales-detail&action=confirm";

//		var ch = document.getElementById('cmd_confirm').checked;
//		document.forms.cmd.value = (ch != true) ? "cmd_regist" : "cmd_confirm";
		document.forms.cmd.value = "cmd_confirm";
		document.forms.submit();
	}
</script>
<script>
	function checkRepeat() {
		const fg = document.getElementById("repeat_fg");

		if (fg) {
			console.log(fg.value);
			const p1 = document.getElementById("repeat_info");

			const rep = '{{$rows->repeat_fg}}';
			console.log(rep);

			if (p1.style.display == "block" && rep !== 1) {
				// noneで非表示
				p1.style.display = "none";
			} else {
				// blockで表示
				p1.style.display = "block";
			}

		}
	}

	// 初期状態：繰り返し設定　非表示
	//  ※windows.onloadは一画面1個制約がある
	window.onload = function() {
		const p1 = document.getElementById("repeat_info");
		const p2 = document.getElementById("repeat_custom");

		const rep = '{{$rows->repeat_fg}}';
		console.log(rep);

		const period = '{{$rows->period}}';
		console.log(period);

		p1.style.display = "none";

		p2.style.display = "none";

		if (rep != 1) {
			// noneで非表示
			p1.style.display = "none";
		} else {
			// blockで表示
			p1.style.display = "block";
		}

		if (period != 9) {
			// noneで非表示
			p2.style.display = "none";
		} else {
			// blockで表示
			p2.style.display = "block";
		}

		/**
		 * 確認画面でform要素をreadOnlyにする
		 * 
		 **/
		const action = "{{$get->action}}";
		if (action == 'confirm') {
			document.getElementById('delivery_dt').readOnly = true;
			document.getElementById('arrival_dt').readOnly = true;
		}

		if (action == 'complete') {
			document.getElementById('delivery_dt').readOnly = true;
			document.getElementById('arrival_dt').readOnly = true;
		}
	}

	// 繰り返しカスタム設定　表示
	function displayCustom() {
		const period = document.getElementById("period").value;
		console.log(period);
		const p2 = document.getElementById("repeat_custom");
		if (period != 9) {
			p2.style.display = "none";
		} else {
			p2.style.display = "block";
		}
	}
</script>
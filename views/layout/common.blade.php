<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

<script>
	function exec_action(cmd = null) {
		var page = "{{$get->page}}";
		switch (cmd) {
			case 'edit':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page={{$get->page}}&sales={{$post->sales}}&goods={{$post->goods}}&customer={{$post->customer}}&action=edit";
				document.forms.cmd.value = 'edit';
				document.forms.target = '';
				document.forms.submit();
				break;
			case 'edit-exe':
				if (page == 'customer-detail') {
					document.forms.action = "{{home_url()}}/wp-admin/admin.php?page={{$get->page}}&customer={{$post->customer}}&action=edit-exe";

				} else if (page == 'sales-detail') {
					document.forms.action = "{{home_url()}}/wp-admin/admin.php?page={{$get->page}}&sales={{$post->sales}}&action=edit-exe";

				} else {
					document.forms.action = "{{home_url()}}/wp-admin/admin.php?page={{$get->page}}&goods={{$post->goods}}&action=edit-exe";
				}
				document.forms.cmd.value = 'update';
				document.forms.target = '';
				document.forms.submit();
				break;
			case 'save':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page={{$get->page}}&action=save";
				document.forms.cmd.value = 'save';
				document.forms.target = '';
				document.forms.submit();
				break;
		}
	}
</script>

<div class="mesasge">
	@foreach($msg as $k => $error)
		<p style="color: red;">【 {{$k}} 】 {{$error}}</p>
	@endforeach
</div>

<br />

<form name="forms" id="forms" action="" method="post" enctype="multipart/form-data">
	<span id="msg" style="color: red;"></span>
	<input type="hidden" name="cmd" value="" />
	<input type="hidden" name="step" id="step" value="" />
	<input type="hidden" name="sales" value="{{$get->sales}}" />
	<input type="hidden" name="your_email" value="<?php echo htmlspecialchars($rows->_field_your_email); ?>" />
	<div class="mesasge">
		<p style="color: red;"><?php if (!empty($_POST['message']['error'])) { echo htmlspecialchars(current($_POST['message']['error'])); } ?></p>
	</div>
	<br />

<div class="container-fluid">
	@yield ('contents2')

	@yield ('contents3')

	@yield ('contents4')

	@yield ('contents5')

	@yield ('contents6')

	@yield ('contents7')

	@yield ('contents8')
</div>

<input type="hidden" name="your-subject" id="your-subject" value="" />
<input type="hidden" name="your-name" id="your-name" value="" />
<input type="hidden" name="your-email" id="your-email" value="" />

<label for="cmd_confirm">
<!--<input type="checkbox" name="cmd_confirm" id="cmd_confirm" class=""> 確定（保存後に当ステップの編集ができなくなります）&emsp;&emsp; -->
</label>
&emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp;
&emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp;
&emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp; 
<!--<input type="button" name="cmd_regist" id="cmd_regist" class="common_btn" value="登録" onclick="to_next();">-->
<!--<input type="button" name="cmd_regist" id="cmd_regist" class="btn btn-primary" value="登録" onclick="to_next();">-->
<!--<button type="submit" class="btn btn-primary">Submit</button>-->

<div class="d-flex flex-column align-items-center">
<!--<div class="d-flex flex-column align-items-end mx-5">-->
	{{$get->action}}
	@if ($get->action == '' || $get->action == 'save' || $get->action == 'edit')
	<input type="button" name="cmd_regist" id="cmd_regist" class="btn btn-primary" value="確認" onclick="to_next();">
	@elseif ($get->action == 'confirm' && ($post->btn == 'update' || $rows->btn == 'update'))
	<input type="button" name="cmd_update" id="cmd_update" class="mb-3 btn btn-primary" value="更新" onclick="confirm_update();">
	<input type="button" name="cmd_return" id="cmd_return" class="mb-3 btn btn-primary" value="編集" onclick="exec_action('edit');">
	@elseif ($get->action == 'confirm')
	<input type="button" name="cmd_regist" id="cmd_regist" class="mb-3 btn btn-primary" value="登録" onclick="confirm_regist();">
	<input type="button" name="cmd_return" id="cmd_return" class="mb-3 btn btn-primary" value="編集" onclick="exec_action('edit');">
	@else
	<input type="button" name="cmd_return" id="cmd_return" class="mb-3 btn btn-primary" value="編集" onclick="exec_action('edit');">
	@endif

	<script>
	function confirm_regist() {
		var ret = window.confirm('登録しますか？');
		if (ret) {
			exec_action('save');
		} else {
		}
	}

	function confirm_update() {
		var ret = window.confirm('更新しますか？');
		if (ret) {
			exec_action('edit-exe');
		} else {
		}
	}
	</script>
</dvi>

</form>

<hr class="yjSeparation">
<!-- フォームボタン -->

<!--[submit "確認画面へ進む →"]-->
<!--[multistep multistep-916 first_step skip_save "http://localhost:81/shop-confirmed"]-->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

<script>
	function exec_action(cmd = null) {
		switch (cmd) {
			case 'edit-exe':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page={{$get->page}}&sales={{$_GET['sales']}}&action=edit-exe"
				document.forms.cmd.value = 'save';
				document.forms.target = '';
				document.forms.submit();
				break;
			case 'save':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page={{$get->page}}&sales={{$_GET['sales']}}&action=edit-exe"
				document.forms.cmd.value = 'save';
				document.forms.target = '';
				document.forms.submit();
				break;
			case 'cancel':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page={{$get->page}}&sales={{$_GET['sales']}}&action=cancel"
				document.forms.target = '';
				document.forms.submit();
				break;
			case 'preview':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page={{$get->page}}&sales={{$_GET['sales']}}&action=preview"
				document.forms.cmd.value = 'preview';
				document.forms.target = '_blank';
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

@if ($get->action == '' || $get->action == 'save')
<input type="button" name="cmd_regist" id="cmd_regist" class="btn btn-primary" value="登録" onclick="exec_action('save');">
@else
<input type="button" name="cmd_update" id="cmd_update" class="btn btn-primary" value="更新" onclick="exec_action('edit-exe');">
@endif

</form>

<hr class="yjSeparation">
<!-- フォームボタン -->

<!--[submit "確認画面へ進む →"]-->
<!--[multistep multistep-916 first_step skip_save "http://localhost:81/shop-confirmed"]-->

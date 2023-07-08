<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

<div style="width: 950px;"><!-- body class="yj950-1" -->
	<div id="wrapper">
		<div id="header">
			<div id="sh">
				<span class="yjGuid"><a name="yjPagetop" id="yjPagetop"></a><img src="https://s.yimg.jp/yui/jp/tmpl/1.1.0/clear.gif" width="1" height="1" alt="このページの先頭です"></span>
				<span class="yjSkip"><a href="#yjContentsStart"><img src="https://s.yimg.jp/yui/jp/tmpl/1.1.0/clear.gif" alt="このページの本文へ" width="1" height="1"></a></span>
<!--
	    <div id="masthead">
	      <div class="yjmth">
	        <div class="yjmthproplogoarea"><a href="#"><img height="36" width="243" border="0" alt="Developers Club" src=""></a></div>
	        <div class="yjmthcmnlnkarea">
	        </div>
	      </div>
	    </div>
-->

	<div><!-- EMG3 noResult --></div>
	<div><!-- EMG2 noResult --></div>
	<div><!-- EMG noResult --></div>

			</div>
		</div>
		<hr class="yjSeparation">

		<div id="contents">
			<div id="yjContentsBody">
				<span class="yjGuid"><a name="yjContentsStart" id="yjContentsStart"></a><img src="#" width="1" height="1" alt="??common.text.startOfMainText_ja??"></span>

				<div id="yjMain">
					<div class="yjMainGrid">
						@if ($prm->action == 'regist')
						<div class="ClearFix" id="stepnavi">
							<div>
								<div class="floatL <?php echo ($prm->step == '1st') ? 'active' : ''; ?>">
									<p>お申込情報の入力</p>
									<div class="line"><span>&nbsp;</span></div>
								</div>
								<div class="floatL <?php echo ($prm->step == '2nd') ? 'active' : ''; ?>">
									<p>請求先情報の入力</p>
									<div class="line"><span>&nbsp;</span></div>
								</div>
								<div class="floatL <?php echo ($prm->step == '3rd') ? 'active' : ''; ?>">
									<p>商品情報の確認</p>
									<div class="line"><span>&nbsp;</span></div>
								</div>
								<div class="floatL <?php echo ($prm->step == '4th') ? 'active' : ''; ?> last">
									<p>決済代行会社からの確認</p>
									<div class="line"><span>&nbsp;</span></div>
								</div>
<!--
								<div class="floatL <?php echo ($prm->step == 'confirm') ? 'active' : ''; ?>">
									<p>入力の確認</p>
									<div class="line"><span>&nbsp;</span></div>
								</div>
								<div class="floatL last">
									<p>お申込の完了</p>
									<div class="line"><span>&nbsp;</span></div>
								</div>
-->
							</div>
						</div>
						@endif

						<div id="yjPageTitle" class="ClearFix">
							<div class="ClearFix">
								<div class="pageTitle_l">
									<p><em class="yjMastMark">必須</em>がついている項目は、すべて入力必須項目です。</p>
<!--
									<div>
										<dl class="modIDConnect" style="margin: 15px 0 0;width: 720px;">
											<dt>入力の前にご確認ください。</dt>
											<dd><p class="Txt">お申込にはフォーム入力用仮IDと仮パスワードが必要です。ID/パスワードをご確認のうえ、お申込ください。</p>
												<div class="ctrlHolder ClearFix">
													<div class="AreaL">
														<p><label>仮ID/PW</label></p>
													</div>
													<div class="AreaR">
														<p><span class="inactive">メールでご案内したものです。</span></p>
													</div>
												</div>
												<div class="ctrlHolder">
												</div>
											</dd>
										</dl>
									</div>
-->
								</div>
<!--/.pageTitle_l-->
							</div>
<!--
							<span class="line">&nbsp;</span>
-->
						</div>

<!--
  <form name="Form01" action="" method="post" class="FromStyle" id="Form01" _lpchecked="1">
-->
<style>
.common_btn {
	margin: 15px 0 0;
	width: 220px;
	border: 1px solid #2c4b79;
	color: #FFF;
	background: #2c4b79;
}
</style>

<script>
	function exec_action(cmd) {
		switch (cmd) {
			case 'edit-exe':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=shop-list&post={{$_GET['post']}}&action=edit-exe"
				document.forms.target = '';
				document.forms.submit();
				break;
			case 'save':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=shop-list&post={{$_GET['post']}}&action=edit-exe"
				document.forms.cmd.value = 'save';
				document.forms.target = '';
				document.forms.submit();
				break;
			case 'cancel':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=shop-list&post={{$_GET['post']}}&action=cancel"
				document.forms.target = '';
				document.forms.submit();
				break;
			case 'preview':
				document.forms.action = "{{home_url()}}/wp-admin/admin.php?page=shop-list&post={{$_GET['post']}}&action=preview"
				document.forms.cmd.value = 'preview';
				document.forms.target = '_blank';
				document.forms.submit();
				break;
		}
	}
</script>

<?php
/*
	switch ($prm->action) {
		case 'edit':
			?><input type="button" name="cmd_edit" id="cmd_edit" class="page-title-action common_btn" value="編集" onclick="exec_action('edit-exe');"><?php
			break;
		case 'edit-exe':
			?><input type="button" name="cmd_save" id="cmd_save" class="page-title-action common_btn" value="更新" onclick="exec_action('save');">&emsp;&emsp;<?php
			?><input type="button" name="cmd_cnacel" id="cmd_cancel" class="page-title-action common_btn" value="キャンセル" onclick="exec_action('cancel');">&emsp;&emsp;<?php
			?><input type="button" name="cmd_preview" id="cmd_preview" class="page-title-action common_btn" value="プレビュー" onclick="exec_action('preview');"><?php
			break;
		case 'regist':
			break;
		default:
			?><input type="button" name="cmd_edit" id="cmd_edit" class="page-title-action common_btn" value="編集" onclick="exec_action('edit-exe');"><?php
			break;
	}
*/
?>

<div class="mesasge">
	@foreach($msg as $k => $error)
	<p style="color: red;">【 {{$aliases[$k]}} 】 {{$error}}</p>
	@endforeach
</div>

<br />
<div>{{$rows->applicant}}</div>

<form name="forms" id="forms" action="" method="post" enctype="multipart/form-data">
	<span id="msg" style="color: red;"></span>
	<input type="hidden" name="cmd" value="" />
	<input type="hidden" name="step" id="step" value="" />
	<input type="hidden" name="applicant" value="{{$prm->post}}" />
	<input type="hidden" name="your_email" value="<?php echo htmlspecialchars($rows->_field_your_email); ?>" />
	<div class="mesasge">
		<p style="color: red;"><?php if (!empty($_POST['message']['error'])) { echo htmlspecialchars(current($_POST['message']['error'])); } ?></p>
	</div>
	<br />
						<div>
						</div>


<div>

  <div id="fcom">
							@yield ('contents2')
    
    <fieldset class="ClearFix">
      
<!-- js 1 -->
      
    </fieldset>
    

							@yield ('contents3')
  </div>
</div>

<div>
  <a name="yjPayTop"></a>
							@yield ('contents4')
</div>
    

<div>
							@yield ('contents5')

  <input name="acc_select_type" type="hidden" value="A">
</div>

<div>
							@yield ('contents6')
  

  <div id="fstr">
							@yield ('contents7')
  </div>

</div>

<div>
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

</form>

<hr class="yjSeparation">
<!-- フォームボタン -->

<!--[submit "確認画面へ進む →"]-->
<!--[multistep multistep-916 first_step skip_save "http://localhost:81/shop-confirmed"]-->

<div class="mesasge">
	@foreach($msg as $k => $error)
		<p style="color: red;">【 {{$k}} 】 {{$error}}</p>
	@endforeach
</div>

<br />

	<div class="mesasge">
		<p style="color: red;"><?php if (!empty($_POST['message']['error'])) { echo htmlspecialchars(current($_POST['message']['error'])); } ?></p>
	</div>
	<br />

<div class="container-fluid">
	<!-- start -->
	<div class="tablenav top">
		<br class="clear">
	</div>
		
	<div class="table-responsive">
		<div id="title">■ 転送処理</div>
		<div>※ 基本情報を入力後、ロット番号へのリンクが表示されます。</div>
		<div>
			<table class="table table-bordered text-nowrap">
				<thead class="table-light">
					<tr>
						<th class=""></th>
						<th class="">品名</th>
						<th class="">荷姿・容量(kgTB)</th>
						<th class="">個数</th>
						<th class="">数量(kg)</th>
						<th class="">出庫倉庫</th>
						<th class="">入庫倉庫</th>
						<th class="">ロット番号</th>
					</tr>
				</thead>

				<tbody id="the-list" data-wp-lists="list:user">
					<input type="hidden" id="sales" name="sales" value="{{$get->sales}}">
					<input type="hidden" id="goods" name="goods" value="{{$get->goods}}">
					@for ($i = 0; $i < 5; $i++)
					<tr id="user-1">
						<td class="">&emsp;</td>
						<td class="">
							@if(!$rows->goods_list)
							<select class="form-select w-75" aria-label="goodsName" id="goods_{{$i}}" name="goods_list[]">
								@foreach($initForm['select']['goods_name'] as $goods => $gname)
									<option value="{{$goods}}">{{$goods}} : {{$gname}}</option>
								@endforeach
							</select>
							@else
								<input type="text" id="text_goods_list_{{$i}}" name="text_goods_list_{{$i}}" value="{{$rows->goods_list[$i]}} : {{$initForm['select']['goods_name'][$rows->goods_list[$i]]}}" />
								<input type="hidden" name="goods_list[{{$rows->goods_list[$i]}}]" value="{{$rows->goods_list[$i]}}" />
							@endif
						</td>
						<td class="tx-right">500</td>
						<td class="tx-right"><input type="number" min="0" class="tx-center w-75" id="t_qty_{{$i}}" name="t_qty_list[]" value="{{$rows->t_qty_list[$i]}}" onchange="calcTransferWeight({{$i}});"></td>
						<td class="tx-right"><input type="text" class="tx-right w-75" id="t_weight_{{$i}}" name="t_weight_list[]" value="{{$rows->t_weight_list[$i]}}" readonly></td>
						<td class="tx-right">
							<select class="form-select w-75" id="t_outgoing_warehouse_{{$i}}" name="t_outgoing_warehouse[]" onchange="setReceiveWarehouse({{$i}});">
								<option value=""></option>
								<option value="1">内藤SP</option>
								<option value="2">丹波SP</option>
							</select>
						</td>
						<td class="tx-right"><input type="text" class="tx-right w-75" id="t_receive_warehouse_{{$i}}" name="t_receive_warehouse[]" value="" readonly></td>
						<td class="tx-right">@if($get->stock)<a href="/wp-admin/admin.php?page=stock-lot-regist">入力画面へ</a>@else - @endif</td>
						</td>
					</tr>
					@endfor
				</tbody>

				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>
	<!-- end -->
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
	<input type="button" name="cmd_regist" id="cmd_regist" class="btn btn-primary" value="確認 🌟" onclick="set_trunsfer(); to_next();">
	@elseif ($get->action == 'confirm' && ($post->btn == 'update' || $rows->btn == 'update'))
	<input type="button" name="cmd_update" id="cmd_update" class="mb-3 btn btn-primary" value="更新" onclick="confirm_update();">
	<input type="button" name="cmd_return" id="cmd_return" class="mb-3 btn btn-primary" value="編集" onclick="exec_action('edit');">
	@elseif ($get->action == 'confirm')
	<input type="button" name="cmd_regist" id="cmd_regist" class="mb-3 btn btn-primary" value="登録" onclick="confirm_regist();">
	<input type="button" name="cmd_return" id="cmd_return" class="mb-3 btn btn-primary" value="編集" onclick="exec_action('edit');">
	@else
	<input type="button" name="cmd_return" id="cmd_return" class="mb-3 btn btn-primary" value="編集" onclick="exec_action('edit');">
	@endif

	<input type="hidden" name="cmd" id="cmd" class="btn btn-primary" value="">
</div>

<script>
function set_trunsfer() {
	document.getElementById('cmd').value = 'cmd_transfer';
}
</script>

<style>
.tx-right {
	text-align: right;
}

.tx-center {
	text-align: center;
}
</style>
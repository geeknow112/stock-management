	<p class="">
	<legend>【転送処理】</legend>
	</p>

	<div class="row mb-3">
		<label for="arrival_dt" class="col-sm-2 col-form-label">入庫予定日</label>
		<input type="date" class="col-sm-6 col-form-control w-auto" id="arrival_dt" name="arrival_dt" aria-describedby="arrivalDtHelp" value="{{$rows->arrival_dt}}" @if($get->action == 'confirm') readonly @endif>
	</div>


<br />
<hr>
<div id="wpbody-content">
	<div class="wrap">
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
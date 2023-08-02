<p class="">
<legend>【ロット番号登録】</legend>
</p>

<div class="tablenav top">
	<br class="clear">
</div>
	
<div class="table-responsive">
	<div>
		<table class="table table-bordered text-nowrap">
			<thead class="table-light">
				<tr>
					<th class="">品名</th>
					<th class="">配送先</th>
					<th class="">量(t)</th>
					<th class="">入庫予定日</th>
					<th class="">氏名</th>
					<th class="">タンク番号</th>
					<th class="">ロット番号</th>
				</tr>
			</thead>

			<tbody id="the-list" data-wp-lists="list:user">
				@if (isset($rows) && count($rows))
					@foreach ($rows as $i => $d)
					<tr id="user-1">
						<td>{{$d->goods_name}}</td>
						<td>{{$d->ship_addr}}</td>
						<td>{{$d->goods_qty}}</td>
						<td>{{$d->arrival_dt}}</td>
						<td>{{$d->name}}</td>
						<td class="">
						<input type="text" class="" id="tank" name="tank" value="{{$d->tank}}">
						</td>
						<td class="">
						<input type="text" class="" id="lot" name="lot" value="{{$d->lot}}">
						</td>
					</tr>
					@endforeach
				@else
				<td class="colspanchange" colspan="7">検索対象は見つかりませんでした。</td>
				@endif
			</tbody>

			<tfoot>
			</tfoot>
		</table>
	</div>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<h1>@yield ('title') | BladeOne</h1>
{!!$title!!}<br>
<h2>escape</h2>
{!!$fugafuga!!}<br>

@foreach ($msg as $k => $ms)
	{{$k}} : {{$ms}} <br>
@endforeach

<hr>

<div class="d-grid gap-2">
<div class="d-grid gap-2 col-6 mx-auto">
	<input type="button" id="menu_btn" class="btn btn-primary" value="商品登録" onclick="window.location = '/wp-admin/admin.php?page=goods-detail';">
	<input type="button" id="menu_btn" class="btn btn-primary" value="顧客登録" onclick="window.location = '/wp-admin/admin.php?page=customer-detail';">
	<input type="button" id="menu_btn" class="btn btn-primary" value="注文登録" onclick="window.location = '/wp-admin/admin.php?page=sales-detail';">
</div>
<br>
<div class="d-grid gap-2 col-6 mx-auto">
	<input type="button" id="menu_btn" class="btn btn-danger" value="商品検索" onclick="window.location = '/wp-admin/admin.php?page=goods-list';">
	<input type="button" id="menu_btn" class="btn btn-danger" value="顧客検索" onclick="window.location = '/wp-admin/admin.php?page=customer-list';">
	<input type="button" id="menu_btn" class="btn btn-danger" value="注文検索" onclick="window.location = '/wp-admin/admin.php?page=sales-list';">
</div>
</div>
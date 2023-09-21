	<p class="">
	<legend>【顧客登録】</legend>
	</p>

	@if ($get->action != '')
	<div class="row mb-3">
		<label for="customer" class="col-sm-2 col-form-label">顧客番号</label>
		<input type="text" class="col-sm-2 col-form-control" id="customer" name="customer" aria-describedby="customerHelp" value="{{$post->customer}}" readonly>
	</div>
	@endif

	<div class="row mb-3">
		<label for="customer_name" class="col-sm-2 col-form-label">顧客名</label>
		<input type="text" class="col-sm-2 col-form-control" id="customer_name" name="customer_name" aria-describedby="customerNameHelp" value="{{$post->customer_name}}">
<!--		<div id="orderName" class="form-text">顧客名を入力してください。</div>-->
	</div>

	<br />

	@if ($rows)
		@foreach($rows as $i => $d)
			<div class="row mb-3">
				<label class="col-sm-2 col-form-label">住所: {{$d->detail}}</label>
				<input type="text" class="col-sm-2 col-form-control" id="pref_{{$i}}" name="pref[]" aria-describedby="prefHelp" value="{{$d->pref}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control" id="addr1_{{$i}}" name="addr1[]" aria-describedby="addr1Help" value="{{$d->addr1}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control" id="addr2_{{$i}}" name="addr2[]" aria-describedby="addr2Help" value="{{$d->addr2}}">&emsp;
				<input type="text" class="col-sm-2 col-form-control" id="addr3_{{$i}}" name="addr3[]" aria-describedby="addr3Help" value="{{$d->addr3}}">&emsp;
			</div>
		@endforeach
	@else
		<div class="row mb-3">
			<label class="col-sm-2 col-form-label">住所: 追加</label>
			<input type="text" class="col-sm-2 col-form-control" id="pref_0" name="pref[]" aria-describedby="prefHelp" value="">&emsp;
			<input type="text" class="col-sm-2 col-form-control" id="addr1_0" name="addr1[]" aria-describedby="addr1Help" value="">&emsp;
			<input type="text" class="col-sm-2 col-form-control" id="addr2_0" name="addr2[]" aria-describedby="addr2Help" value="">&emsp;
			<input type="text" class="col-sm-2 col-form-control" id="addr3_0" name="addr3[]" aria-describedby="addr3Help" value="">&emsp;
		</div>
	@endif

<form id="frm" name="frm" method="GET" action="">
    <div>新しい行を追加：<input type="button" id="add" name="add" value="追加" onclick="appendRow()"></div>
    <table border="1" id="tbl">
    <tr>
        <th style="text-align:right; width:40px;">番号</th>
        <th style="">入力文字</th>
        <th style="background-color: green; width:40px;"> </th>
        <th style="background-color: red; width:40px;"> </th>
    </tr>
    <tr>
        <td style="text-align:right; width:40px;"><span class="seqno">1</span></td>
        <td style=""><input class="inpval" type="text" id="txt1" name="txt1" value="blah blah blah" size="30" readonly style="border:none"></td>
        <td style="background-color: green; width:40px;"><input class="edtbtn" type="button" id="edtBtn1" value="編集" onclick="editRow(this)"></td>
        <td style="background-color: red; width:40px;"><input class="delbtn" type="button" id="delBtn1" value="削除" onclick="deleteRow(this)"></td>
    </tr>
    </table>
    <input type="submit" value="送信">
</form>

<script>
// https://www.northwind.mydns.jp/samples/table_sample1.php
/*
 * appendRow: テーブルに行を追加
 */
function appendRow()
{
    var objTBL = document.getElementById("tbl");
    if (!objTBL)
        return;
    
    var count = objTBL.rows.length;
    
    // 最終行に新しい行を追加
    var row = objTBL.insertRow(count);

    // 列の追加
    var c1 = row.insertCell(0);
    var c2 = row.insertCell(1);
    var c3 = row.insertCell(2);
    var c4 = row.insertCell(3);

    // 各列にスタイルを設定
    c1.style.cssText = "text-align:right; width:40px;";
    c2.style.cssText = "";
    c3.style.cssText = "background-color: green; width:40px;";
    c4.style.cssText = "background-color: red; width:40px;";
    
    // 各列に表示内容を設定
    c1.innerHTML = '<span class="seqno">' + count + '</span>';
    c2.innerHTML = '<input class="inpval" type="text"   id="txt' + count + '" name="txt' + count + '" value="" size="30" style="border:1px solid #888;">';
    c3.innerHTML = '<input class="edtbtn" type="button" id="edtBtn' + count + '" value="確定" onclick="editRow(this)">';
    c4.innerHTML = '<input class="delbtn" type="button" id="delBtn' + count + '" value="削除" onclick="deleteRow(this)">';
    
    // 追加した行の入力フィールドへフォーカスを設定
    var objInp = document.getElementById("txt" + count);
    if (objInp)
        objInp.focus();
}

/*
 * deleteRow: 削除ボタン該当行を削除
 */
function deleteRow(obj)
{
    // 確認
    if (!confirm("この行を削除しますか？"))
        return;

    if (!obj)
        return;

    var objTR = obj.parentNode.parentNode;
    var objTBL = objTR.parentNode;
    
    if (objTBL)
        objTBL.deleteRow(objTR.sectionRowIndex);
    
    // <span> 行番号ふり直し
    var tagElements = document.getElementsByTagName("span");
    if (!tagElements)
        return false;

    var seq = 1;
    for (var i = 0; i < tagElements.length; i++)
    {
        if (tagElements[i].className.match("seqno"))
            tagElements[i].innerHTML = seq++;
    }

    // id/name ふり直し
    var tagElements = document.getElementsByTagName("input");
    if (!tagElements)
        return false;

    // <input type="text" id="txtN">
    var seq = 1;
    for (var i = 0; i < tagElements.length; i++)
    {
        if (tagElements[i].className.match("inpval"))
        {
            tagElements[i].setAttribute("id", "txt" + seq);
            tagElements[i].setAttribute("name", "txt" + seq);
            ++seq;
        }
    }

    // <input type="button" id="edtBtnN">
    seq = 1;
    for (var i = 0; i < tagElements.length; i++)
    {
        if (tagElements[i].className.match("edtbtn"))
        {
            tagElements[i].setAttribute("id", "edtBtn" + seq);
            ++seq;
        }
    }

    // <input type="button" id="delBtnN">
    seq = 1;
    for (var i = 0; i < tagElements.length; i++)
    {
        if (tagElements[i].className.match("delbtn"))
        {
            tagElements[i].setAttribute("id", "delBtn" + seq);
            ++seq;
        }
    }
}

/*
 * editRow: 編集ボタン該当行の内容を入力・編集またモード切り替え
 */
function editRow(obj)
{
    var objTR = obj.parentNode.parentNode;
    var rowId = objTR.sectionRowIndex;
    var objInp = document.getElementById("txt" + rowId);
    var objBtn = document.getElementById("edtBtn" + rowId);

    if (!objInp || !objBtn)
        return;
    
    // モードの切り替えはボタンの値で判定   
    if (objBtn.value == "編集")
    {
        objInp.style.cssText = "border:1px solid #888;"
        objInp.readOnly = false;
        objInp.focus();
        objBtn.value = "確定";
    }
    else
    {
        objInp.style.cssText = "border:none;"
        objInp.readOnly = true;
        objBtn.value = "編集";
    }
}
</script>

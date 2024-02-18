<?php
/**
 * StockTransfer.php short discription
 *
 * long discription
 *
 */
require_once(dirname(__DIR__). '/library/Ext/Model/Base.php');
/**
 * StockClass short discription
 *
 * long discription
 *
 */
class StockTransfer extends Stock {

	/**
	 * 
	 **/
	public function getValidElement($step_num = null) {

		$step1 = array(
			'rules' => array(
				'arrival_dt'				=> 'required|max:100',
//				'outgoing_warehouse'		=> 'required|max:100',
/*
				'apply_service'				=> 'required|max:100',
				'apply_plan'				=> 'required|max:100',

				'biz_fg'					=> 'required|max:100',
				'biz_number'				=> 'required|regex:/^[0-9]{13}+$/i',
				'company_name'				=> 'required|max:100',
				'company_name_kana'			=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'zip'						=> 'required|max:100',
				'pref'						=> 'required|max:100',
				'addr'						=> 'required|max:100',
				'addr2'						=> 'required|max:100',
				'addr3'						=> 'max:100',
				'addr_kana'					=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'tel'						=> 'required|max:100',
				'fax'						=> 'max:100',
				'est_dt'					=> 'required|max:100',
				'num_employ'				=> 'required|max:100',
				'capital'					=> 'required|max:100',
				'annual_sales'				=> 'max:100',
				'goods_class'				=> 'required|max:100',
				'goods'						=> 'required|max:100',
				'delivery_company'			=> 'max:100',
				'url'						=> 'max:100',

				'name'                  => 'required|max:2',
				'email'                 => 'required|email',
				'password'              => 'required|min:6',
				'confirm_password'      => 'required|same:password',
				'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
				'skills'                => 'array',
				'skills.*.id'           => 'required|numeric',
				'skills.*.percentage'   => 'required|numeric'
*/
			), 
			'messages' => array(
				'name.required' => 'ユーザー名を入力してください',
				'name.string' => '正しい形式で入力してください',
				'name.max' => '文字数をオーバーしています。',
				'email.required' => 'メールアドレスを入力してください。',
				'email.email' => '正しい形式でメールアドレスを入力してください',
				'email.max' => '文字数をオーバーしています。',
				'email.unique' => '登録済みのユーザーです',
				'password.required' => 'パスワードを入力してください',
				'password.min' => 'パスワードは8文字以上で入力してください。',
				'password.confirmed' => 'パスワードが一致しません。',
			)
		);

		return $step1;
	}

	/**
	 * 在庫「転送」情報登録
	 * 
	 **/
	public function regDetail($get = null, $post = null) {
		$post->transfer_fg = true; // 転送処理フラグ
		$rows = parent::regDetail($get, $post);

		/** 
		 * 【丹波SPの減少：yc_stock_detailへの反映】
		 * 
		 * 「転送」時に、
		 * 丹波SP → 内藤SP のケースで、
		 * 丹波SPの在庫を減数するための処理
		 * 
		 * 内藤SP → 丹波SPのケースは、
		 * 通常の在庫が増えるパターンと同様なので、処理不要
		 **/
//		$this->vd($rows);
		foreach ($rows->goods_list as $i => $goods) {
			$qty = $rows->qty_list[$i];
			$rwh = $rows->receive_warehouse[$i];

			if (empty($goods)) { continue; }
			if (empty($qty)) { continue; }
			if (empty($rwh) || ($rwh == '2')) { continue; } // 丹波SPの増加になるため、除外

			$stocks[$i] = $this->getDetailByGoodsCode($goods, $qty); // goods, qty
		}

		foreach ($stocks as $i => $stock) {
			foreach ($stock as $i => $d) {
				$ret[] = $this->updTransferFg($d->id);
			}
		}

		return $rows;
	}

	/**
	 * 在庫情報詳細取得
	 * - 商品コード(goods)から抽出
	 * 
	 **/
	public function getDetailByGoodsCode($goods = null, $qty = null) {
		global $wpdb;

		$sql  = "SELECT st.*, std.id, std.lot, std.barcode, std.transfer_fg FROM ". $this->getTableName(). " as st ";
		$sql .= "LEFT JOIN yc_stock_detail as std ON st.stock = std.stock ";
		$sql .= sprintf("WHERE st.goods = '%s' ", $goods);
		$sql .= "AND st.warehouse = '2' "; // = 丹波SP
		$sql .= "AND std.transfer_fg != '1' ";
		$sql .= sprintf("LIMIT 0, %s ", $qty);

		$rows = $wpdb->get_results($sql);
		return (object) $rows;
	}

	/**
	 * 在庫情報詳細「転送」フラグ更新
	 **/
	public function updTransferFg($id = null) {
		$post = (object) $post;
		global $wpdb;

		$data['transfer_fg'] = true;
		$data['updt'] = date('Y-m-d H:i:s');

		$ret = $wpdb->update(
			'yc_stock_detail', 
			$data, 
			array('id' => $id)
		);

		return $ret;
	}
}
?>

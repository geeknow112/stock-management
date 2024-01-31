<?php
/**
 * Stock.php short discription
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
class Stock extends Ext_Model_Base {
	protected $_name = 'yc_stock';

	/**
	 * 
	 **/
	public function getValidElement($step_num = null) {

		$step1 = array(
			'rules' => array(
				'arrival_dt'				=> 'required|max:100',
				'outgoing_warehouse'		=> 'required|max:100',
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
	 * 商品情報一覧取得
	 **/
	public function getList($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		$sql  = "SELECT g.*, g.name AS goods_name ";
		$sql .= "FROM yc_goods AS g ";
		$sql .= "WHERE g.goods is not null ";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
				if (!empty($get->s['no'])) { $sql .= sprintf("AND g.goods = '%s' ", $get->s['no']); }
				if (!empty($get->s['goods_name'])) { $sql .= sprintf("AND g.name LIKE '%s%s' ", $get->s['goods_name'], '%'); }
//				$sql .= "ORDER BY g.goods desc";
				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}
		$rows = $wpdb->get_results($sql);
		return $rows;
	}

	/**
	 * 商品情報詳細取得
	 **/
	public function getDetail($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		// 商品IDで検索して商品情報を取得するSQL
		$sql  = "SELECT s.* FROM yc_sales as s ";
		$sql .= "WHERE s.id = '". $get->sales. "'";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		$sql .= "LIMIT 1;";
		$rows = $wpdb->get_results($sql);

/*
		// 配列整形
		foreach ($rows as $i => $d) {
			$ret[str_replace('-', '_', $d->meta_key)] = $d->meta_value;
		}
*/
		return $rows[0];
	}

	/**
	 * 在庫情報詳細取得
	 * - 在庫コード(stock)から抽出
	 **/
	public function getDetailByStockCode($goods = null) {
		global $wpdb;

		$sql  = "SELECT st.* FROM ". $this->getTableName(). " as st ";
		$sql .= sprintf("WHERE st.stock = '%s' ", $goods);

		$rows = $wpdb->get_results($sql);


		$ret['arrival_dt'] = $rows[0]->arrival_dt;

		return (object) $rows;
	}

	/**
	 * 在庫情報詳細 複数取得
	 * - 在庫コード複数(stocks)から抽出
	 **/
	public function getDetailByStockCodes($stocks = null) {
		global $wpdb;

		$sql  = "SELECT st.* FROM ". $this->getTableName(). " as st ";
		$sql .= sprintf("WHERE st.stock IN (%s); ", implode(',', $stocks));

		$rows = $wpdb->get_results($sql);
		return $rows;
	}

	/**
	 * 在庫情報詳細ロット番号欄取得
	 * - 在庫コード(stock)から抽出
	 **/
	public function getDetailLotByStockCode($stock = null) {
		global $wpdb;

		$sql  = "SELECT st.*, std.*, g.name AS goods_name FROM yc_stock as st ";
		$sql .= "LEFT JOIN yc_stock_detail as std ON st.stock = std.stock ";
		$sql .= "LEFT JOIN yc_goods as g ON st.goods = g.goods ";
		$sql .= sprintf("WHERE std.stock = '%s' ", $stock);
		$sql .= sprintf("AND std.upuser is null ");

		$rows = $wpdb->get_results($sql);
		return (object) $rows;
	}

	/**
	 * 対象注文のロット番号一覧取得
	 **/
	public function getLotNumberListByOrder($prm = null) {
		$prm = (object) $prm;
		global $wpdb;
		$cur_user = wp_get_current_user();
		//var_dump($cur_user->user_login);
		//var_dump($cur_user->user_email);

		$sql  = "SELECT o.id, o.ship_addr, o.arrival_dt, o.name, g.goods, g.name as goods_name, g.qty as goods_qty, gd.lot, gd.tank ";
		$sql .= "FROM yc_sales as o ";
		$sql .= "LEFT JOIN yc_goods as g ON o.goods = g.goods ";
		$sql .= "LEFT JOIN yc_goods_detail as gd on o.id = gd.order ";
		$sql .= "WHERE o.id is not null ";
		$sql .= "AND gd.id is not null ";
		$sql .= sprintf("AND o.id = %d and g.goods = %d ", $prm->order, $prm->goods);

		$rows = $wpdb->get_results($sql);

//$this->vd($rows);
		return $rows;
	}

	/**
	 * 在庫情報詳細取得
	 * - 入庫日(arrival_dt)から抽出
	 **/
	public function getDetailByArrivalDt($arrival_dt = null, $warehouse = null) {
		global $wpdb;

		$sql  = "SELECT st.* FROM ". $this->getTableName(). " as st ";
		$sql .= sprintf("WHERE st.arrival_dt = '%s' ", $arrival_dt);
		$sql .= sprintf("AND st.warehouse = '%s' ", $warehouse);

		$rows = $wpdb->get_results($sql);

		// 表示用に成形
		foreach ($rows as $i => $d) {
			$ret['stock_list'][] = $d->stock;
			$ret['goods_list'][] = $d->goods;
			$ret['qty_list'][] = $d->goods_total;
			$ret['weight_list'][] = $d->subtotal;
		}
		return (object) $ret;
	}

	/**
	 * 在庫情報登録
	 **/
	public function regDetail($get = null, $post = null) {
		global $wpdb;

		$exist_columns = $wpdb->get_col("DESC ". $this->getTableName(). ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!empty($post->$col)) {
				$data[$col] = $post->$col;
			}
		}

//		$data['name'] = $post->goods_name;

//$this->vd($post);
//$this->vd($exist_columns);

		foreach ($post->goods_list as $i => $goods) {
			if ($goods == '0') { continue; }
			$data['stock']       = null;
			$data['goods']       = $goods;
			$data['arrival_dt']  = $post->arrival_dt;
			$data['warehouse']   = ($post->transfer_fg == true) ? $post->receive_warehouse[$i] : $post->outgoing_warehouse;
			$data['goods_total'] = $post->qty_list[$i];
			$data['subtotal']    = str_replace(',', '', $post->weight_list[$i]);
			$data['transfer_fg'] = ($post->transfer_fg == true) ? true : '';
			$data['rgdt']        = date('Y-m-d H:i:s');
			$datas[] = $data;
		}

		foreach ($datas as $i => $data) {
			$ret[] = $wpdb->insert(
				$this->getTableName(), 
				$data
				//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
			);

			// 登録したIDを取得
			$stocks[] = $wpdb->insert_id;
		}

		// 登録情報を再取得
		$rows = $this->getDetailByStockCodes($stocks);

		foreach ($rows as $i => $d) {
			$count = $d->goods_total; // 個数 (500kg/個) = ロット番号入力レコード生成数
			for ($j = 0; $j < $count; $j++) {
				$ret_detail[] = $wpdb->insert(
					'yc_stock_detail', 
					array(
						'id' => null, 
						'stock' => $d->stock, 
						'lot' => null, 
						'barcode' => null, 
						'rgdt' => date('Y-m-d H:i:s')
					)
					//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
				);
			}
		}

		// 表示用に整形
/*
		foreach ($rows as $i => $d) {
		}
*/
		$ret = $post; // TODO:

		return $ret;
	}

	/**
	 * 在庫情報更新
	 * 
	 **/
	public function updDetail($get = null, $post = null) {
		$post = (object) $post;
		global $wpdb;

		$cur_user = wp_get_current_user();

		foreach ($post->stock_list as $i => $stock) {
			if (empty($stock)) { continue; }
			$stocks[$i] = $stock;
			$data = array(
				'goods' => $post->goods_list[$i], 
				'arrival_dt' => $post->arrival_dt, 
				'warehouse' => $post->outgoing_warehouse, 
				'goods_total' => $post->qty_list[$i], 
				'subtotal' => str_replace(',', '', $post->weight_list[$i]), 
				'updt' => date('Y-m-d H:i:s'), 
				'upuser' => $cur_user->user_login
			);

			$ret[] = $wpdb->update(
				$this->getTableName(), 
				$data, 
				array('stock' => $stock)
			);
		}

		// 更新情報を再取得
		$rows = $this->getDetailByStockCodes($stocks);

		// 表示用に成形
		foreach ($rows as $i => $row) {
			$result['arrival_dt'] = $row->arrival_dt;
			$result['outgoing_warehouse'] = $row->warehouse;
			$result['warehouse'] = $row->warehouse;
			$result['stock_list'][$i] = $row->stock;
			$result['goods_list'][$i] = $row->goods;
			$result['qty_list'][$i] = $row->goods_total;
			$result['weight_list'][$i] = $row->subtotal;
		}

		return (object) $result;
	}

	/**
	 * 在庫情報更新
	 *  - ロット番号登録
	 **/
	public function updLotNumber($get = null, $post = null) {
		$post = (object) $post;
		global $wpdb;

		$table_name = 'yc_stock_detail';

		$exist_columns = $wpdb->get_col("DESC ". $table_name. ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!empty($post->$col)) {
				$data[$col] = $post->$col;
			}
		}

		// 更新対象の取得
		$rows = $this->getDetailIdsByStockCode($post->stock);

		foreach ($rows as $i => $d) {
			$v['lot'] = $data['lot'][$i];
			$ret[] = $wpdb->update(
				$table_name, 
				$v, 
				array(
					'id' => $d->id, 
					'stock' => $post->stock
				)
			);
		}

		// 更新情報を再取得
		$rows = $this->getDetailIdsByStockCode($post->stock);
		return $rows;
	}

	/**
	 * 在庫情報更新対象の取得
	 * 
	 * 
	 **/
	public function getDetailIdsByStockCode($stock = null) {
		global $wpdb;

		$sql  = "SELECT std.*, g.goods, g.name AS goods_name FROM yc_stock_detail as std ";
		$sql .= "LEFT JOIN yc_stock AS st ON std.stock = st.stock ";
		$sql .= "LEFT JOIN yc_goods AS g ON g.goods = st.goods ";
		$sql .= sprintf("WHERE std.stock = '%s' ", $stock);

		$rows = $wpdb->get_results($sql);

		// 表示用に成形
/*
		foreach ($rows as $i => $d) {
			$ret['stock_list'][] = $d->stock;
			$ret['goods_list'][] = $d->goods;
			$ret['qty_list'][] = $d->goods_total;
			$ret['weight_list'][] = $d->subtotal;
		}
*/
		return (object) $rows;
	}

	/**
	 * 在庫証明書 情報一覧取得
	 * 
	 **/
	public function getStockExportList($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

//		$sql  = "SELECT st.goods, g.name AS goods_name, (g.qty * 1000) AS qty, count(*) AS cnt, (count(*) * 500) AS stock_total ";
		$sql  = "SELECT st.goods, g.name AS goods_name, g.qty, g.separately_fg, g.remark, std.id, std.lot, std.barcode, std.transfer_fg, st.stock AS stock ";
		$sql .= "FROM yc_stock AS st ";
		$sql .= "LEFT JOIN yc_stock_detail AS std ON st.stock = std.stock ";
		$sql .= "LEFT JOIN yc_goods AS g ON g.goods = st.goods ";
		$sql .= "WHERE st.stock is not null ";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "GROUP BY st.goods ";
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
//				if (!empty($get->s['no'])) { $sql .= sprintf("AND g.goods = '%s' ", $get->s['no']); }
//				if (!empty($get->s['goods_name'])) { $sql .= sprintf("AND g.name LIKE '%s%s' ", $get->s['goods_name'], '%'); }
//				$sql .= "ORDER BY g.goods desc";

				if (!empty($get->s['arrival_e_dt'])) { $sql .= sprintf("AND st.arrival_dt <= '%s 23:59:59' ", $get->s['arrival_e_dt']); }

				// 転送処理をした場合、st.warehouseには転送先のSPが登録されるため、通常の検索で「転送」による増加は処理される。
				if (!empty($get->s['outgoing_warehouse'])) { $sql .= sprintf("AND st.warehouse = '%s' ", $get->s['outgoing_warehouse']); }

//				$sql .= "GROUP BY st.goods ";
//				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}

		$d_sql = $sql;
		$n_sql = $sql. "AND (std.transfer_fg is null OR std.transfer_fg <> '1');"; // 「転送」処理分を含めない
		$stocks = $wpdb->get_results($n_sql);

		// 「転送」処理分 取得のためSQLを整形
		$warehouse = ($get->s['outgoing_warehouse'] == 1) ? 2 : 1; // 倉庫SPを反転
		$d_sql = preg_replace("/AND st.warehouse = '(.*)'/", "", $d_sql);
		$d_sql .= sprintf("AND st.warehouse = '%s' ", $warehouse);
		$d_sql .= "AND st.transfer_fg = 1;"; // 「転送」処理分を取得
		$d_stocks = $wpdb->get_results($d_sql);

		$rows = $this->decreaseStockByTransfer($get, $stocks, $d_stocks);

		return $rows;
	}

	/**
	 * 在庫証明書
	 * 「転送」による在庫の減少を実施
	 * 
	 **/
	public function decreaseStockByTransfer($get = null, $stocks = null, $d_stocks = null) {

		foreach ($stocks as $i => $stock) {
			foreach ($d_stocks as $j => $del) {

				// 商品別で数量による除外
				if ($stock->goods == $del->goods) {
					if ($get->match_lot != true) {
						unset($stocks[$i]);
						unset($d_stocks[$j]);
						break;

					} else {
						// ロット番号による除外
						if ($stock->lot == $del->lot) {
							unset($stocks[$i]);
							unset($d_stocks[$j]);
							break;
						}
					}
				}

			}
		}
		return $stocks;
	}

	/**
	 * 在庫証明書 情報一覧取得
	 * 「注文」で除外(配送済み)分 取得
	 * 
	 **/
	public function getSalesDeliveredList($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

//		$sql  = "SELECT st.goods, g.name AS goods_name, (g.qty * 1000) AS qty, count(*) AS cnt, (count(*) * 500) AS stock_total ";
		$sql  = "SELECT s.*, gd.*, g.name AS goods_name, s.goods AS goods ";
		$sql .= "FROM yc_sales AS s ";
		$sql .= "LEFT JOIN yc_goods_detail AS gd ON s.sales = gd.sales ";
		$sql .= "LEFT JOIN yc_goods AS g ON g.goods = gd.goods ";
		$sql .= "WHERE s.sales is not null AND s.status <> 9 ";
		$sql .= "AND s.class NOT IN (0, 7) "; // 「繰り返し」分を除外
//		$sql .= "AND s.outgoing_warehouse = '2' ";
		$sql .= "AND s.status = '1' ";
//		$sql .= "AND std.transfer_fg != '1' "; // 「転送」処理分の減少

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "GROUP BY st.goods ";
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
//				if (!empty($get->s['no'])) { $sql .= sprintf("AND g.goods = '%s' ", $get->s['no']); }
//				if (!empty($get->s['goods_name'])) { $sql .= sprintf("AND g.name LIKE '%s%s' ", $get->s['goods_name'], '%'); }
//				$sql .= "ORDER BY g.goods desc";

				if (!empty($get->s['arrival_e_dt'])) { $sql .= sprintf("AND s.delivery_dt <= '%s 23:59:59' ", $get->s['arrival_e_dt']); }
				if (!empty($get->s['outgoing_warehouse'])) { $sql .= sprintf("AND s.outgoing_warehouse = '%s' ", $get->s['outgoing_warehouse']); }

//				$sql .= "GROUP BY st.goods ";
				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}
//$this->vd($sql);
		$rows = $wpdb->get_results($sql);
		return $rows;
	}

	/**
	 * 倉出伝票 情報一覧取得
	 * 
	 * @ $jk_flag: 「直取」フラグ: yc_sales.class = 10 のみを抽出
	 **/
	public function getStockExportListDay($get = null, $jk_flag = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		$sql  = "SELECT s.delivery_dt, s.goods, s.class, s.cars_tank, s.qty, g.name AS goods_name, g.separately_fg, s.customer AS customer, c.name AS customer_name ";
		$sql .= "FROM yc_sales AS s ";
		$sql .= "LEFT JOIN yc_goods AS g ON g.goods = s.goods ";
		$sql .= "LEFT JOIN yc_customer AS c ON c.customer = s.customer ";
		$sql .= "WHERE s.sales is not null AND s.status <> 9 ";

		if (is_null($jk_flag)) {
			$sql .= "AND s.class NOT IN (0, 7, 10) ";
		} else {
			$sql .= "AND s.class IN (10) ";
		}


		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
				if (!empty($get->s['delivery_s_dt'])) { $sql .= sprintf("AND s.delivery_dt = '%s' ", $get->s['delivery_s_dt']); }
				if (!empty($get->s['outgoing_warehouse'])) { $sql .= sprintf("AND s.outgoing_warehouse = '%s' ", $get->s['outgoing_warehouse']); }
//				$sql .= "GROUP BY s.goods, s.customer ";
//				$sql .= "ORDER BY g.goods desc";
//				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}

		$sql_sub  = "SELECT *, sum(t.qty) AS qty FROM (". $sql. ") AS t ";
		$sql_sub .= "GROUP BY t.goods, t.customer ";
		$sql_sub .= "ORDER BY t.class, t.cars_tank asc ";
		$sql_sub .= ";";

		$rows = $wpdb->get_results($sql_sub);
		return $rows;
	}

	/**
	 * 倉出伝票 「転送」処理分 取得
	 * 
	 * @ $warehouse: 「転送」先の倉庫の番号
	 **/
	public function getStockTransferList($get = null, $warehouse = null) {
		$get = (object) $get;
		if (empty($get->s['delivery_s_dt'])) { return; }
		global $wpdb;
		$cur_user = wp_get_current_user();

		$sql  = "SELECT st.*, st.goods_total AS qty, g.name AS goods_name ";
		$sql .= "FROM yc_stock AS st ";
		$sql .= "LEFT JOIN yc_goods AS g ON g.goods = st.goods ";
		$sql .= "WHERE st.stock is not null ";
		$sql .= "AND st.transfer_fg = 1 ";
		if (!empty($warehouse)) { $sql .= sprintf("AND st.warehouse = %s ", $warehouse); }

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
				if (!empty($get->s['delivery_s_dt'])) { $sql .= sprintf("AND st.arrival_dt = '%s' ", $get->s['delivery_s_dt']); }
//				if (!empty($get->s['outgoing_warehouse'])) { $sql .= sprintf("AND st.warehouse = '%s' ", $get->s['outgoing_warehouse']); }
//				$sql .= "ORDER BY g.goods desc";
//				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}

		$rows = $wpdb->get_results($sql);
		return $rows;
	}

	/**
	 * 
	 **/
	public function getInitForm() {
		return array(
			'select' => array(
				'outgoing_warehouse' => $this->getPartsOutgoingWarehouse(), 
				'goods_name' => $this->getPartsGoodsName(), 
			)
		);
	}

	/**
	 * 「出庫倉庫」
	 **/
	private function getPartsOutgoingWarehouse() {
		return array(
			0 => '', 
			1 => '内藤SP', 
			2 => '丹波SP',
		);
	}

	/**
	 * 「品名」
	 **/
	private function getPartsGoodsName() {
		global $wpdb;
		$sql  = "SELECT g.goods, g.name, g.separately_fg FROM yc_goods as g ";
		$sql .= ";";
		$rows = $wpdb->get_results($sql);

		// 配列整形
		$ret[0] = '';
		$separately = null;
		foreach ($rows as $i => $d) {
			$ret[$d->goods][0] = '';
			if ($d->separately_fg == true) { $separately = " （バラ）"; }
			$ret[$d->goods] = sprintf("%s%s", $d->name, $separately);
		}

		return $ret;
	}
}
?>

<?php
class Sales {
	protected $_name = 'yc_sales';

	/**
	 *
	 **/
	function getTableName() {
		return $this->_name;
	}

	/**
	 *
	 **/
	public function getCurUser() {
		$cur_user = wp_get_current_user();
		return $cur_user;
	}

	/**
	 * 
	 **/
	public function getValidElement($step_num = null) {

		$messages = array(
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
		);

		$step1 = array(
			'rules' => array(
				'customer'					=> 'required|min:2',
				'qty'						=> 'required|max:100',

/*
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
			'messages' => $messages
		);

		$step2 = array(
			'rules' => array(
				'tank'				=> 'required|max:100',
				'lot'				=> 'required|max:100',
			), 
			'messages' => $messages
		);

		switch ($step_num) {
			default:
			case 1:
				return $step1;
				break;

			case 2:
				return $step2;
				break;
		}
	}

	/**
	 * 受注情報一覧取得
	 **/
	public function getList($get = null, $un_convert = null) {
		$get = (object) $get;
// $this->vd($get);
		global $wpdb;
		$cur_user = wp_get_current_user();

		// your_name, your_emailで検索してIDを取得するSQL
		//$rows = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_field_your-email'");
		$sql  = "SELECT s.*, sc.repeat, sc.period, sc.span, sc.day_of_week, sc.st_dt, sc.ed_dt, g.name as goods_name ";
		$sql .= "FROM yc_sales as s ";
		$sql .= "LEFT JOIN yc_schedule_repeat as sc ON s.sales = sc.sales ";
		$sql .= "LEFT JOIN yc_goods as g ON s.goods = g.goods ";
		$sql .= "WHERE s.sales is not null ";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
//			$sql .= ";";

		} else {
			if ($get->action == 'search') {
				if (!empty($get->s['no'])) { $sql .= sprintf("AND s.sales = '%s' ", $get->s['no']); }
				if (!empty($get->s['goods_name'])) { $sql .= sprintf("AND g.name LIKE '%s%s' ", $get->s['goods_name'], '%'); }

				if (!empty($get->s['order_s_dt'])) { $sql .= sprintf("AND s.rgdt >= '%s 00:00:00' ", $get->s['order_s_dt']); }
				if (!empty($get->s['order_e_dt'])) { $sql .= sprintf("AND s.rgdt <= '%s 23:59:59' ", $get->s['order_e_dt']); }

				if (!empty($get->s['arrival_s_dt'])) { $sql .= sprintf("AND s.arrival_dt >= '%s 00:00:00' ", $get->s['arrival_s_dt']); }
				if (!empty($get->s['arrival_e_dt'])) { $sql .= sprintf("AND s.arrival_dt <= '%s 23:59:59' ", $get->s['arrival_e_dt']); }

				$sql .= "ORDER BY s.rgdt desc";
				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}
//$this->vd($sql);
		$rows = $wpdb->get_results($sql);

		$days10 = array(
			'2023-07-17', '2023-07-18', '2023-07-19', '2023-07-20', '2023-07-21', '2023-07-22',
			'2022-12-20', '2022-12-21', '2022-12-22', '2022-12-23', '2022-12-24', '2022-12-25'
		);


		// リピート注文除外リスト:TEST ※コピー元のsales, repeat, rep_iで2, 5を除外
		$e_list = array(2, 5);

		foreach ($rows as $i => $row) {
			if ($row->repeat_fg == true) {
				// repeat分
				if ($row->period == 1) { // 毎週 (元注文の配送予定日を起点にn回繰り返す処理)
					for ($i=1; $i<=5; $i++) {
						if (in_array($i, $e_list)) { continue; } // 除外
						$r = clone $row;
						$r->sales = NULL;
						$r->goods_name = 'rep:'. $r->goods_name;
						$r->status = 0;
						$date = new DateTime($r->delivery_dt);
						$date->modify(sprintf('+%d day', $i));
						$r->delivery_dt = $date->format('Y-m-d');
						$r->rgdt = NULL;
						$r->rep_i = $i;
						$rows[] = $r;
					}
				}
			}
		}

		// sales-listの処理
		if ($un_convert == true) {
//$this->vd($rows);
			// 配送予定日でソート
			$dts = [];
			foreach ($rows as $row) {
				$dts[] = $row->delivery_dt;
			}
			array_multisort($dts, SORT_DESC, $rows);
			return $rows;

		} else {
		// delivery-graphの処理
			// convert
			foreach ($rows as $i => $row) {
				$tmp[$row->delivery_dt][$row->sales] = $row;
			}

			foreach ($days10 as $i => $day) {
				$t = $tmp;
				$t->delivery_dt = $day;
				// repeat分
				$result[$day][] = $t;
				// 個別分
				if (!empty($tmp[$day])) {
					foreach ($tmp[$day] as $i => $list) {
						$result[$day][] = $list;
					}
				}
			}
			return $result;
		}
	}

	/**
	 * 受注情報詳細取得
	 **/
	public function getDetail($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		// 受注IDで検索して受注情報を取得するSQL
		$sql  = "SELECT s.* FROM yc_sales as s ";
		$sql .= "WHERE s.sales = '". $get->sales. "'";

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
	 * 受注情報詳細取得
	 * - 注文コード(sales)から抽出
	 **/
	public function getDetailBySalesCode($sales = null) {
		global $wpdb;

		$sql  = "SELECT s.* FROM ". $this->getTableName(). " as s ";
		$sql .= sprintf("WHERE s.sales = '%s' ", $sales);
		$sql .= "LIMIT 1;";

		$rows = $wpdb->get_results($sql);
		return $rows[0];
	}

	/**
	 * 受注情報詳細取得
	 * - 受注コード(applicant)から抽出
	 **/
	public function getDetailByApplicantCode($applicant = null) {
		global $wpdb;
//$this->vd($applicant);exit;
		$sql  = "SELECT ap.* FROM ".$wpdb->prefix."applicant as ap ";
		$sql .= sprintf("WHERE ap.applicant = '%s' ", $applicant);
		$sql .= "LIMIT 1;";

		$rows = $wpdb->get_results($sql);
		return $rows[0];
	}

	/**
	 * 対象注文のロット番号一覧取得
	 **/
	public function getLotNumberListBySales($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		$sql  = "SELECT s.sales, s.ship_addr, s.arrival_dt, s.name, g.goods, g.name as goods_name, g.qty as goods_qty, gd.id as lot_tmp_id, gd.lot, gd.tank ";
		$sql .= "FROM yc_sales as s ";
		$sql .= "LEFT JOIN yc_goods as g ON s.goods = g.goods ";
		$sql .= "LEFT JOIN yc_goods_detail as gd on s.sales = gd.sales ";
		$sql .= "WHERE s.sales is not null ";
		$sql .= "AND gd.id is not null ";

		if (in_array($get->action, array('save', 'confirm', 'edit', 'complete'))) {
			$sql .= sprintf("AND s.sales = %d and g.goods = %d ", $get->sales, $get->goods);
		}

		$rows = $wpdb->get_results($sql);

		// convert
		foreach ($rows as $i => $row) {
			$conv[$row->lot_tmp_id] = $row;
		}
//$this->vd($conv);
		return $conv;
	}

	/**
	 * 受注情報登録
	 **/
	public function regDetail($get = null, $post = null) {
		$post = (object) $post;
		global $wpdb;
		$cur_user = $this->getCurUser();

		// checkboxの初期化
		$post->use_stock = ($post->use_stock == 'on') ? 1 : 0;
		$post->repeat_fg = ($post->repeat_fg == 'on') ? 1 : 0;

//		$p->updt = date('Y-m-d H:i:s'); // updt

		$exist_columns = $wpdb->get_col("DESC ". $this->getTableName(). ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!is_null($post->$col)) {
				$data[$col] = $post->$col;
			}
		}

		$data['rgdt'] = date('Y-m-d H:i:s');

		$ret = $wpdb->insert(
			$this->getTableName(), 
			$data
			//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
		);

		// 登録したIDを取得
		$sales = $wpdb->insert_id;

		// 登録情報を再取得
		$rows = $this->getDetailBySalesCode($sales);
		$rows->sales = $rows->sales;
		return $rows;
	}

	/**
	 * 受注情報更新
	 **/
	public function updDetail($get = null, $post = null) {
		$post = (object) $post;
		global $wpdb;

		// checkboxの初期化
		$post->use_stock = ($post->use_stock == 'on') ? 1 : 0;
		$post->repeat_fg = ($post->repeat_fg == 'on') ? 1 : 0;

		$exist_columns = $wpdb->get_col("DESC ". $this->getTableName(). ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!is_null($post->$col)) {
				$data[$col] = $post->$col;
			}
		}

		$data['updt'] = date('Y-m-d H:i:s');

		$ret = $wpdb->update(
			$this->getTableName(), 
			$data, 
			array('sales' => $post->sales)
		);

		// schedule_repeat関連値登録
		// upsert
		$targetId = $wpdb->get_var($wpdb->prepare("SELECT sales FROM yc_sales WHERE sales = %s ", $post->sales));
		if (is_null($targetId)) {
			$ret[] = $wpdb->insert(
				'yc_schedule_repeat', 
				array(
					'repeat' => null, 
					'sales' => null, 
					'period' => $post->period, 
					'rgdt' => date('Y-m-d H:i:s')
				)
				//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
			);
		} else {
/*
			$ret[] = $wpdb->update(
				'yc_customer_detail', 
				array(
					'pref' => $d->pref, 
					'addr1' => $d->addr1, 
					'addr2' => $d->addr2, 
					'addr3' => $d->addr3, 
					'updt' => date('Y-m-d H:i:s')
				),
				array(
					'customer' => $post->customer, 
					'detail' => $detail
				)
			);
*/
		}

		// 更新情報を再取得
		$rows = $this->getDetailBySalesCode($post->sales);
		return $rows;
	}

	/**
	 * ロット情報更新
	 **/
	public function updLotDetail($get = null, $post = null) {
		$post = (object) $post;
		global $wpdb;

		foreach ($post->lot_tmp_id as $id) {
			$data[$id] = array(
				'id' => $id, 
				'tank' => $post->tank[$id], 
				'lot' => $post->lot[$id]
			);
		}

//$this->vd($data);exit;

		foreach ($data as $id => $d) {
			$ret[] = $wpdb->update(
				'yc_goods_detail', 
				$d, 
				array('id' => $id)
			);
		}
		return true;
	}

	/**
	 * ロット情報登録領域作成
	 **/
	public function makeLotSpace($get = null, $post = null) {
		$post = (object) $post;
		global $wpdb;

		// 一括操作が「確定」以外の場合は処理終了
		$exec_status = (int) array_search('確定', $this->getPartsStatus());
		$curr_status = (int) $post->change_status;
		if ($exec_status !== $curr_status) { return false; }
$this->vd($post);exit;
		foreach ($post->no as $i => $sales) {
			// 注文IDがNULLの場合、リピート注文のため、元注文をコピーして新規登録する
			if (empty($sales)) {
				// 1. リピートIDで、yc_schdule_repeat.salesで元注文の注文IDを取得
				$rep_sqls[]  = sprintf("SELECT * FROM yc_schedule_repeat AS sc LEFT JOIN yc_sales AS s ON sc.sales = s.sales WHERE sc.repeat = %d LIMIT 1;", $post->arr_repeat[$i]);
			}
		}

		// 2. yc_sales.sales = yc_schdule_repeat.salesで元注文の注文情報を取得
		foreach ($rep_sqls as $i => $rep_sql) {
			$rep_rets[] = current($wpdb->get_results($rep_sql));
		}

		// 3. regDetail()で新規登録
		foreach ($rep_rets as $i => $d) {
			$d->sales = NULL; // sales = NULLで新規登録
			$d->delivery_dt = $post->arr_delivery_dt[$i]; // リピート注文からdelivery_dtをコピー
			$reg_rets[] = $this->regDetail($get, $d);
		}

		// 4. $postにmerge
		foreach ($reg_rets as $i => $data) {
			array_push($post->no, $data->id);
		}

		foreach ($post->no as $i => $sales) {
			$sqls[$sales]  = sprintf("SELECT count(*) as count FROM yc_goods_detail as gd WHERE gd.sales = %s AND gd.goods = %s LIMIT 1;", $sales, $post->arr_goods[$sales]);
		}

		foreach ($sqls as $sales => $sql) {
			$rets[$sales] = current($wpdb->get_results($sql));
		}

		foreach ($rets as $sales => $d) {
			if ($d->count == 0) {
				// 数量(t)/0.5(t)=レコード数
				$loop = (int) $post->arr_qty[$sales] / 0.5;
				for ($j=0; $j<$loop; $j++) {
					$results[$sales][] = $wpdb->insert(
						'yc_goods_detail', 
						array(
							'id' => NULL, 
							'sales' => $sales, 
							'goods' => $post->arr_goods[$sales], 
							'lot' => NULL, 
							'tank' => NULL, 
							'rgdt' => NULL, 
							'updt' => NULL, 
							'upuser' => NULL, 
						),
						array('%s', '%s', '%d', '%s')
					);
				}
			}
		}

//$this->vd($results);
		// ロット登録領域を生成したら、yc_sales.lot_fgを変更する。(0:未作成 → 1:未登録)
		foreach ($results as $sales => $ret) {
			$upd_ret[$sales] = $wpdb->update(
				$this->getTableName(), 
				array(
					'id' => $sales,
					'lot_fg' => array_search('未登録', $this->getPartsLotFg()),
				), 
				array('id' => $sales)
			);
		}
$this->vd($upd_ret);
		return true;
	}

	/**
	 * ロットフラグの変更
	 * 
	 * yc_sales.lot_fg を変更する処理：
	 * - ロット番号が全登録済の場合: (2:登録済)
	 * - ロット番号に空欄がある場合: (1:未登録)
	 **/
	public function updLotFg($rows = null) {
		global $wpdb;
		foreach ($rows as $tmp_lot_id => $d) {
			$sales = $d->id;
			$check_arr[] = $d->lot;
		}

//		$this->vd($check_arr);
		if (in_array(0, $check_arr) || in_array(NULL, $check_arr)) {
			$lot_fg = 1;
		} else {
			$lot_fg = 2;
		}

		$ret = $wpdb->update(
			$this->getTableName(), 
			array(
				'id' => $sales,
				'lot_fg' => $lot_fg,
			), 
			array('id' => $sales)
		);

		return $ret;
	}

	/**
	 * 状態変更
	 * 
	 * $change_status : 変更後の状態番号
	 * $object_no : 対象の注文番号
	 **/
	public function changeStatus($change_status = null, $object_no = null) {
		global $wpdb;

		foreach ($object_no as $i => $sales) {
			$data[$sales] = array(
				'id' => $sales, 
				'status' => $change_status
			);
		}

//$this->vd($data);exit;

		foreach ($data as $sales => $d) {
			$ret[] = $wpdb->update(
				$this->getTableName(), 
				$d, 
				array('id' => $sales)
			);
		}
		return true;
	}

	/**
	 * 
	 **/
	public function vd($d) {
//return false;
		global $wpdb;
		$cur_user = wp_get_current_user();
		if (current($cur_user->roles) == 'administrator') {
			echo '<div class="border border-success mb-3">';
			echo '<pre>';
//			var_dump($d);
			print_r($d);
			echo '</pre>';
			echo '</div>';
		}
	}

	/**
	 * 
	 **/
	public function getInitForm() {
		return array(
			'select' => array(
				'customer' => $this->getPartsOrderName(), 
				'car_model' => $this->getPartsCarModel(), 
				'cars_tank' => $this->getPartsCarsTank(), 
				'goods_name' => $this->getPartsGoodsName(), 
				'ship_addr' => $this->getPartsShipAddr(), 
				'qty' => $this->getPartsQty(), 
				'outgoing_warehouse' => $this->getPartsOutgoingWarehouse(), 
				'status' => $this->getPartsStatus(), 
				'period' => $this->getPartsPeriod(), 
			)
		);
	}

	/**
	 * 「氏名」
	 **/
	private function getPartsOrderName() {
		return array(
			0 => '', 
			43 => '顧客①', 
			45 => '顧客②',
		);
	}

	/**
	 * 「車種」
	 **/
	private function getPartsCarModel() {
		return array(
			0 => '', 
			1 => '6t-1', 
			2 => '6t-2',
			3 => '6t-3',
			4 => '6t-4',
			5 => '6t-5',
		);
	}

	/**
	 * 「槽」
	 **/
	private function getPartsCarsTank() {
		return array(
			0 => '', 
			1 => '1', 
			2 => '2',
			3 => '3',
		);
	}

	/**
	 * 「品名」
	 **/
	private function getPartsGoodsName() {
		return array(
			0 => '', 
			10 => '商品①', 
			20 => '商品②',
			30 => '商品③',
			40 => '商品④',
			50 => '商品⑤',
		);
	}

	/**
	 * 「配送先」
	 **/
	private function getPartsShipAddr() {
		return array(
			0 => '', 
			10 => '配送先①', 
			20 => '配送先②',
			30 => '配送先③',
			40 => '配送先④',
			50 => '配送先⑤',
		);
	}

	/**
	 * 「量(t)」
	 **/
	private function getPartsQty() {
		return array(
			0 => '', 
			1 => '1', 
			2 => '2',
			3 => '3',
			4 => '4',
			5 => '5',
			6 => '6',
		);
	}

	/**
	 * 「出庫倉庫」
	 **/
	private function getPartsOutgoingWarehouse() {
		return array(
			0 => '', 
			1 => '出庫倉庫①', 
			2 => '出庫倉庫②',
		);
	}

	/**
	 * 「状態」：注文処理の状態
	 **/
	private function getPartsStatus() {
		return array(
			'' => '', 
			0 => '未確定', 
			1 => '確定',
			2 => '削除',
		);
	}

	/**
	 * 「確認」:ロット登録状況、領域生成状況
	 **/
	private function getPartsLotFg() {
		return array(
			'' => '', 
			0 => '未作成', 
			1 => '未登録',
			2 => '登録済',
		);
	}

	/**
	 * 繰り返し期間
	 * 
	 * yc_schedule_repeat.period
	 **/
	private function getPartsPeriod() {
		return array(
			'' => '', 
			0 => '毎日', 
			1 => '毎週',
			2 => '毎月',
			3 => '毎年',
		);
	}
}
?>

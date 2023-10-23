<?php
class ScheduleRepeat {
	protected $_name = 'yc_schedule_repeat';

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
//				'customer'					=> 'required|min:2',
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
	 * 繰り返し情報一覧取得
	 **/
	public function getList($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		$sql  = "SELECT scr.*, scr.sales AS sales ";
		$sql .= ", s.class, s.cars_tank, s.outgoing_warehouse, s.goods, s.ship_addr, s.qty, s.use_stock, s.customer, s.name, s.repeat_fg, s.delivery_dt "; // yc_sales
		$sql .= ", c.name AS customer_name "; // yc_customer
		$sql .= ", g.name AS goods_name "; // yc_goods
		$sql .= "FROM yc_schedule_repeat AS scr ";
		$sql .= "LEFT JOIN yc_sales AS s ON s.sales = scr.sales ";
		$sql .= "LEFT JOIN yc_customer AS c ON s.customer = c.customer ";
		$sql .= "LEFT JOIN yc_goods AS g ON s.goods = g.goods ";
		$sql .= "WHERE scr.repeat is not null ";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
//				if (!empty($get->s['no'])) { $sql .= sprintf("AND s.sales = '%s' ", $get->s['no']); }
//				if (!empty($get->s['goods_name'])) { $sql .= sprintf("AND g.name LIKE '%s%s' ", $get->s['goods_name'], '%'); }

//				if (!empty($get->s['sdt'])) { $sql .= sprintf("AND s.delivery_dt >= '%s 00:00:00' ", $get->s['sdt']); }
//				if (!empty($get->s['edt'])) { $sql .= sprintf("AND s.delivery_dt <= '%s 23:59:59' ", $get->s['edt']); }

//				$sql .= "ORDER BY s.rgdt desc";
				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}

		$rows = $wpdb->get_results($sql);

		// repeat情報から、繰り返し注文を生成
		$ret = $this->makeRepeatItems($rows, $get);
		return $ret;
	}

	const OUTPUT_LIMIT = 10;

	/**
	 * Make Repeat Items.
	 *
	 *
	 */
	public function makeRepeatItems($repeat_items = null, $get = null) {
		// sdtから表示用にOUTPUT_LIMIT数分、日付を生成
		$sdt = new DateTime($get->s['sdt']);
		for ($i = 0; $i<self::OUTPUT_LIMIT; $i++) {
			$sdt->modify('+1 day');
			$sdts[] = $sdt->format('Y-m-d');
		}
$this->vd($sdts);

		foreach ($repeat_items as $i => $r) {
			if (!isset($r->sales)) { continue; }
			if (!isset($r->repeat_s_dt) || $r->repeat_s_dt == '0000-00-00') { continue; }
			if (!isset($r->repeat_e_dt) || $r->repeat_e_dt == '0000-00-00') { continue; }
			if (!isset($r->period)) { continue; }

			// copy不要部分を初期化
			$r->base_sales = $r->sales;
//			$r->sales = null;
			$r->class = $r->lot_fg = $r->status = 0;
			$r->rgdt = $r->updt = $r->upuser = null;

			switch ($r->period) { 
				default: 
				case 0: // 毎日
					$period = '+1 day';
					break;

				case 1: // 毎週
					$period = '+1 week';
					break;

				case 2: // 毎月
					$period = '+1 month';
					break;

				case 3: // 毎年
					$period = '+1 year';
					break;
			}

// 繰り返し注文の生成
			$r_sdt = new DateTime($r->repeat_s_dt);
//			for ($i = 0; $i<100; $i++) {
			$delivery_dt = $r->repeat_s_dt;
			while ($delivery_dt < $r->repeat_e_dt) {
				$r_sdt->modify($period);
				$delivery_dt = $r_sdt->format('Y-m-d');
				if (!in_array($delivery_dt, $sdts)) { continue; }
				$ret_repeat_items[$delivery_dt][$r->sales][] = $r;
			}
/*
			$delivery_dt = new DateTime($r->delivery_dt);
			for ($i = 0; $i<self::OUTPUT_LIMIT; $i++) {
				$delivery_dt->modify($period);
				$r->delivery_dt = $delivery_dt->format('Y-m-d');
				if (!in_array($r->delivery_dt, $sdts)) { continue; }
				$ret_repeat_items[$r->delivery_dt][$r->sales][] = $r;
			}
*/
/*
			$arrival_dt = new DateTime($r->arrival_dt);
			$arrival_dt->modify($period);
			$r->arrival_dt = $arrival_dt->format('Y-m-d');
*/
//			$ret_repeat_items[] = $r;
		}
		return $ret_repeat_items;
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

		$sql  = "SELECT s.*, sr.*, s.sales AS sales FROM ". $this->getTableName(). " as s "; 
		// →リピート登録がない場合、JOIN後に受注番号(yc_sales.sales)が消えるため、"s.sales AS sales"カラム表示を追加
		$sql .= "LEFT JOIN yc_schedule_repeat AS sr ON s.sales = sr.sales ";
		$sql .= sprintf("WHERE s.sales = '%s' ", $sales);
		$sql .= "LIMIT 1;";
		$rows = $wpdb->get_results($sql);

		$ret = current($rows);
		$ret->week = explode(',', $ret->week);

		return $ret;
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
				if ($col !== 'qty') {
					$data[$col] = $post->$col;
				} else {
					$select->qty = $this->getPartsQty();
					$data[$col] = $select->qty[$post->$col];
				}
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

		// schedule_repeat関連値登録
		// upsert
		$targetId = $wpdb->get_var($wpdb->prepare("SELECT sales FROM yc_schedule_repeat WHERE sales = %s", $sales));
		if (is_null($targetId)) {
			$ret[] = $wpdb->insert(
				'yc_schedule_repeat', 
				array(
					'repeat' => null, 
					'sales' => $sales, 
					'period' => $post->period, 
					'span' => $post->span, 
					'week' => implode(',', array_keys($post->week)), 
					'repeat_s_dt' => $post->repeat_s_dt, 
					'repeat_e_dt' => $post->repeat_e_dt, 
					'rgdt' => date('Y-m-d H:i:s')
				)
				//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
			);
		} else {
			$ret[] = $wpdb->update(
				'yc_schedule_repeat', 
				array(
					'period' => $post->period, 
					'span' => $post->span, 
					'week' => implode(',', array_keys($post->week)), 
					'repeat_s_dt' => $post->repeat_s_dt, 
					'repeat_e_dt' => $post->repeat_e_dt, 
					'updt' => date('Y-m-d H:i:s')
				),
				array(
					'sales' => $sales
				)
			);
		}

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
				if ($col !== 'qty') {
					$data[$col] = $post->$col;
				} else {
					$select->qty = $this->getPartsQty();
					$data[$col] = $select->qty[$post->$col];
				}
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
		$targetId = $wpdb->get_var($wpdb->prepare("SELECT sales FROM yc_schedule_repeat WHERE sales = %s", $post->sales));
		if (is_null($targetId)) {
			$ret[] = $wpdb->insert(
				'yc_schedule_repeat', 
				array(
					'repeat' => null, 
					'sales' => $post->sales, 
					'period' => $post->period, 
					'span' => $post->span, 
					'week' => implode(',', array_keys($post->week)), 
					'repeat_s_dt' => $post->repeat_s_dt, 
					'repeat_e_dt' => $post->repeat_e_dt, 
					'rgdt' => date('Y-m-d H:i:s')
				)
				//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
			);
		} else {
			$ret[] = $wpdb->update(
				'yc_schedule_repeat', 
				array(
					'period' => $post->period, 
					'span' => $post->span, 
					'week' => implode(',', array_keys($post->week)), 
					'repeat_s_dt' => $post->repeat_s_dt, 
					'repeat_e_dt' => $post->repeat_e_dt, 
					'updt' => date('Y-m-d H:i:s')
				),
				array(
					'sales' => $post->sales
				)
			);
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

//$this->vd($post);
//$this->vd($sqls);
//$this->vd($rets);

		//ロット登録領域の作成処理
		foreach ($rets as $sales => $d) {
			if ($d->count == 0) {
				// 数量(t)/0.5(t)=レコード数
				$loop = (float) $post->arr_qty[$sales] / 0.5;
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

		// ロット登録領域を生成したら、yc_sales.lot_fgを変更する。(0:未作成 → 1:未登録)
		if (!empty($results)) {
			foreach ($results as $sales => $ret) {
				$upd_ret[$sales] = $wpdb->update(
					$this->getTableName(), 
					array(
						'sales' => $sales,
						'lot_fg' => array_search('未登録', $this->getPartsLotFg()),
					), 
					array('sales' => $sales)
				);
			}
		}
//$this->vd($upd_ret);
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
			$sales = $d->sales;
			$check_arr[] = $d->lot;
		}

//		$this->vd($check_arr);
		if (in_array(0, $check_arr) || in_array(NULL, $check_arr)) {
			$lot_fg = 1;
		} else {
			$lot_fg = 2;
		}

//		$this->vd($lot_fg);
		$ret = $wpdb->update(
			$this->getTableName(), 
			array(
				'sales' => $sales,
				'lot_fg' => $lot_fg,
			), 
			array('sales' => $sales)
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
				'sales' => $sales, 
				'status' => $change_status
			);
		}

//$this->vd($data);exit;

		foreach ($data as $sales => $d) {
			$ret[] = $wpdb->update(
				$this->getTableName(), 
				$d, 
				array('sales' => $sales)
			);
		}
		return true;
	}

	/**
	 * Tankを基準にTBを表示用に集計
	 * 
	 * $rows : getList()で取得した表示分の注文
	 **/
	public function sumTanks($rows = null) {
		global $wpdb;
//$this->vd($rows);exit;

//TODO:20231013
$sql = 'select sales,goods,tank,count(tank) * 0.5 as tb_qty from yc_goods_detail group by sales,goods,tank;'; // 0.5t/TB
/*
		$sql = 'SELECT * FROM yc_goods_detail AS gd WHERE gd.id is not null ';
		foreach ($rows as $day => $list) {
			foreach ($list as $sales => $d) {
				$sql .= sprintf('(gd.sales = "%s" AND gd.goods = "%s") OR ', $sales, current($d)->goods);

				$data[$sales] = array(
					'sales' => $sales, 
					'goods' => current($d)->goods
				);

			}
		}
*/
//$this->vd($sql);exit;
		$ret = $wpdb->get_results($sql);
//$this->vd($ret);exit;
		foreach ($ret as $i => $d) {
			$tanks[$d->sales][$d->goods][] = array($d->tank, $d->tb_qty);
		}
//$this->vd($tanks);exit;
		return $tanks;
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
				'span' => $this->getPartsSpan(), 
				'week' => $this->getPartsWeek(), 
			)
		);
	}

	/**
	 * 「氏名」
	 **/
	private function getPartsOrderName() {
		global $wpdb;

		$sql  = "SELECT c.customer, c.name FROM yc_customer as c ";
		$sql .= ";";
		$rows = $wpdb->get_results($sql);

		// 配列整形
		$ret[0] = '';
		foreach ($rows as $i => $d) {
			$ret[$d->customer] = $d->name;
		}
		return $ret;

/*
		return array(
			0 => '', 
			43 => '顧客①', 
			45 => '顧客②',
		);
*/
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
			6 => '6t-6',
			7 => '6t-7',
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
		global $wpdb;
		$sql  = "SELECT c.customer, g.goods, g.name FROM yc_customer as c ";
		$sql .= "LEFT JOIN yc_customer_goods as cg ON c.customer = cg.customer ";
		$sql .= "LEFT JOIN yc_goods as g ON cg.goods = g.goods ";
		$sql .= ";";
		$rows = $wpdb->get_results($sql);

		// 配列整形
		$ret[0] = '';
		foreach ($rows as $i => $d) {
			$ret[$d->customer][0] = '';
			$ret[$d->customer][$d->goods] = sprintf("%s", $d->name);
		}

		return $ret;
	}

	/**
	 * 「配送先」
	 **/
	private function getPartsShipAddr() {
		global $wpdb;
		$sql  = "SELECT c.*, cd.* FROM yc_customer as c ";
		$sql .= "LEFT JOIN yc_customer_detail as cd ON c.customer = cd.customer ";
		$sql .= ";";
		$rows = $wpdb->get_results($sql);

		// 配列整形
		foreach ($rows as $i => $d) {
			$ret[$d->customer][0] = '';
			$ret[$d->customer][$d->detail] = sprintf("%s %s %s %s", $d->pref, $d->addr1, $d->addr2, $d->addr3);
		}

		return $ret;
	}

	/**
	 * 「量(t)」
	 **/
	private function getPartsQty() {
		return array(
			0 => '', 
			'0.5' => '0.5', 
			'1.0' => '1.0', 
			'1.5' => '1.5', 
			'2.0' => '2.0',
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

	/**
	 * 繰り返し間隔
	 * 
	 * yc_schedule_repeat.span
	 **/
	private function getPartsSpan() {
		$ret = range(0, 31);
		$ret[0] = '';
		return $ret;
	}

	/**
	 * 繰り返し曜日
	 * 
	 * yc_schedule_repeat.week
	 **/
	private function getPartsWeek() {
		return array(
			1 => '月', 
			2 => '火',
			3 => '水',
			4 => '木',
			5 => '金',
			6 => '土',
			7 => '日',
		);
	}
}
?>

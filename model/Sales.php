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
		$sql .= "LEFT JOIN yc_schedule_repeat as sc ON s.id = sc.sales ";
		$sql .= "LEFT JOIN yc_goods as g ON s.goods = g.goods ";
		$sql .= "WHERE s.id is not null ";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
//			$sql .= ";";

		} else {
			if ($get->action == 'search') {
				if (!empty($get->s['no'])) { $sql .= sprintf("AND s.id = '%s' ", $get->s['no']); }
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


		foreach ($rows as $i => $row) {
			if ($row->repeat_fg == true) {
				// repeat分
				if ($row->period == 1) { // 毎週 (元注文の配送予定日を起点)
					for ($i=0; $i<10; $i++) {
						$r = clone $row;
						$r->id = NULL;
						$r->goods_name = 'rep:'. $r->goods_name;
						$r->status = 0;
						$date = new DateTime($r->delivery_dt);
						$date->modify(sprintf('+%d month', $i));
						$r->delivery_dt = $date->format('Y-m-d');
						$rows[] = $r;
					}
				}
			}
		}

		// sales-listの処理
		if ($un_convert == true) {

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
				$tmp[$row->delivery_dt][$row->id] = $row;
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

		$sql  = "SELECT s.id, s.ship_addr, s.arrival_dt, s.name, g.goods, g.name as goods_name, g.qty as goods_qty, gd.id as lot_tmp_id, gd.lot, gd.tank ";
		$sql .= "FROM yc_sales as s ";
		$sql .= "LEFT JOIN yc_goods as g ON s.goods = g.goods ";
		$sql .= "LEFT JOIN yc_goods_detail as gd on s.id = gd.sales ";
		$sql .= "WHERE s.id is not null ";
		$sql .= "AND gd.id is not null ";

		if (in_array($get->action, array('save', 'confirm', 'edit', 'complete'))) {
			$sql .= sprintf("AND s.id = %d and g.goods = %d ", $get->sales, $get->goods);
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
	public function regDetail($prm = null, $p = null) {
		global $wpdb;
		$cur_user = $this->getCurUser();

		if (current($cur_user->roles) != 'administrator') {
			$app = $this->getApplicantByEmail($cur_user->user_email);
		} else {
			$app = null;
		}

//var_dump($app);

/*
$wpdb->query($wpdb->prepare(
"
 INSERT INTO $wpdb->table_name
 (column1, column2,column3)
 VALUES(%d,%s,%s)
 ON DUPLICATE KEY UPDATE
 column2 = %s,
 column3 = %s
 ",
$value1,
$value2,
$value3,
$value4,
$value5
));
*/

		if (!empty($app)) {
/*
			$sql = "UPDATE wp_applicant ";
			$sql .= "SET company_name = 'test company', ";
			$sql .= sprintf("updt = '%s' ", date('Y-m-d H:i:s')); // updt
			$sql .= "WHERE applicant = '". $app. "' ";
			$sql .= "AND mail = '". $cur_user->user_email. "' ";
			$sql .= ";";
*/

			$where = array(
				'applicant' => $app, 
				'mail' => $cur_user->user_email
			);

			$p->updt = date('Y-m-d H:i:s'); // updt

			$exist_columns = $wpdb->get_col("DESC wp_applicant;", 0);
			foreach ($exist_columns as $i => $col) {
					// 更新除外カラムをスキップ
					if (in_array($col, array('applicant', 'mail'))) { continue; }

					if (isset($p->$col)) {
						$data[$col] = $p->$col;
					}
			}

//unset($data['invoice_addr_fg']);

/*
unset($data['location_fg']);
unset($data['supervisor_fg']);
unset($data['defective']);
unset($data['sales_qty']);
unset($data['about_returns']);
*/
			// JSON形式で登録している項目のコード
			if (!empty($data['expenses'])) { $data['expenses'] = json_encode($data['expenses']); }
			if (!empty($data['payment'])) { $data['payment'] = json_encode($data['payment']); }
//			if (!empty($data['return_shipping'])) { $data['return_shipping'] = json_encode($data['return_shipping']); }

			// 入力欄「その他」がある項目の制御
			$data['expenses_other'] = (!empty($data['expenses']) && in_array('9', json_decode($data['expenses']))) ? $p->expenses_other : '';
			$data['payment_other'] = (!empty($data['payment']) && in_array('9', json_decode($data['payment']))) ? $p->payment_other : '';
			$data['defective_other'] = (!empty($data['defective']) && $data['defective'] == 9) ? $p->defective_other : '';
			$data['sales_qty_other'] = (!empty($data['sales_qty']) && $data['sales_qty'] == 9) ? $p->sales_qty_other : '';
			$data['about_returns_other'] = (!empty($data['about_returns']) && $data['about_returns'] == 9) ? $p->about_returns_other : '';
			$data['return_shipping_other'] = (!empty($data['return_shipping_other']) && $data['return_shipping'] == 9) ? $p->return_shipping_other : '';

			// ファイル(商品画像等)アップ時の制御
//			$r_goods_image = (!empty($_FILES['goods_image']['name'][0])) ? $_FILES['goods_image']['name'][0] : null;
			$data['goods_image1'] = (!empty($p->goods_image1)) ? $p->goods_image1 : '';

			$ret = $wpdb->update(
				'wp_applicant', 
				$data, 
				$where
			);
//$this->vd(array($ret, $data, $where));exit;
			return $ret;

		} else {
			$sql = "INSERT INTO wp_applicant VALUES (";
			$date = date('md-His');
			$sql .= sprintf("'test-%s', ", $date); // applicant
			$sql .="'1','biz_number','company_name','company_name_kana','zip','pref','addr','addr2','addr3','addr_kana','tel',";
			$sql .="'ceo_name_kana_mei','ceo_birth','ceo_addr_fg','ceo_zip','ceo_pref','ceo_addr1','ceo_addr2','ceo_addr3','ceo_addr_kana','ceo_tel',";
			$sql .="'1','staff_company_name','staff_company_name_kana','staff_name_sei','staff_name_mei','staff_name_kana_sei','staff_name_kana_mei',";
			$sql .="'staff_mail','staff_section','staff_post','staff_tel','staff_fax','1','staff_zip','staff_pref','staff_addr1','staff_addr2',";
			$sql .="'staff_addr3','staff_addr_kana','1','1','invoice_company_name','invoice_company_name_kana','invoice_name_sei','invoice_name_mei',";
			$sql .="'invoice_name_kana_sei','invoice_name_kana_mei','invoice_section','invoice_post','invoice_tel','invoice_fax','1','invoice_zip',";
			$sql .="'invoice_pref','invoice_addr1','invoice_addr2','invoice_addr3','invoice_addr_kana','fin_name','fin_branch_name','fin_account_type','fin_account_number',";
			$sql .="'fin_account_name','fin_account_name_kana','goods_name1','goods_price1','goods_image1','goods_name2','goods_price2','goods_image2','goods_name3',";
			$sql .="'goods_price3','goods_image3','price_range_min','price_range_max','other_site_url','distributor','corp_name','corp_name_kana','corp_name_en','1',";
			$sql .="'supervisor_zip','supervisor_pref','supervisor_addr','supervisor_addr2','supervisor_addr3','supervisor_addr_kana','1',";
			$sql .="'supervisor_name_sei','supervisor_name_mei','supervisor_mail','supervisor_tel','supervisor_fax','contact_s_time','contact_e_time','expenses',";
			$sql .="'1','1','delivery_time','delivery_time_none','1','due_payment','1','due_returns','1',";
			$sql .="'2','2','2','2','2','2','1','2','2','2','2','status','shop_category','open_dt','close_dt','remark',";
			$sql .="'field1','field2','field3','message',";
			$sql .= sprintf("'%s',", date('Y-m-d H:i:s')); // rgdt
			$sql .= "'updt','test'";
			$sql .= ");";
		}

		$ret = $wpdb->query($sql);
		return $ret;
	}

	/**
	 * 受注情報更新
	 **/
	public function updDetail($get = null, $post = null) {
		$post = (object) $post;
		global $wpdb;

		$exist_columns = $wpdb->get_col("DESC ". $this->getTableName(). ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!empty($post->$col)) {
				$data[$col] = $post->$col;
			}
		}

$this->vd($data);
/*
		$ret_sql = $wpdb->prepare(
		"UPDATE wp_applicant 
		 set 
			company_name = %s, 
			company_name_kana = %s
		 WHERE applicant = %s
		 ",
		$p->company_name, 
		$p->company_name_kana, 
		$p->applicant
		);
*/
		$ret = $wpdb->update(
			$this->getTableName(), 
/*
			array(
				'company_name' => $p->company_name,
				'company_name_kana' => $p->company_name_kana,
			), 
*/
			$data, 
			array('id' => $post->sales)
		);
//var_dump($ret);
		return true;
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
				'order_name' => $this->getPartsOrderName(), 
				'car_model' => $this->getPartsCarModel(), 
				'goods_name' => $this->getPartsGoodsName(), 
				'ship_addr' => $this->getPartsShipAddr(), 
				'qty' => $this->getPartsQty(), 
				'outgoing_warehouse' => $this->getPartsOutgoingWarehouse(), 
				'status' => $this->getPartsStatus(), 
			)
		);
	}

	/**
	 * 「氏名」
	 **/
	private function getPartsOrderName() {
		return array(
			0 => '', 
			1 => '顧客①', 
			2 => '顧客②',
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

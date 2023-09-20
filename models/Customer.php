<?php
class Customer {
	protected $_name = 'yc_customer';

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

		$step1 = array(
			'rules' => array(
				'customer_name'				=> 'required|max:100',
//				'pref'						=> 'required|max:100',

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
	 * 顧客情報一覧取得
	 **/
	public function getList($get = null, $un_convert = null) {
		$get = (object) $get;

		global $wpdb;
		$cur_user = wp_get_current_user();

		// your_name, your_emailで検索してIDを取得するSQL
		//$rows = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_field_your-email'");
		$sql  = "SELECT c.*, c.name as customer_name ";
//		$sql  = "SELECT s.*, sc.id as repeat_id, sc.period, sc.span, sc.day_of_week, sc.st_dt, sc.ed_dt, g.name as goods_name ";
		$sql .= "FROM yc_customer as c ";
//		$sql .= "LEFT JOIN yc_schedule_repeat as sc ON s.id = sc.order_id ";
//		$sql .= "LEFT JOIN yc_goods as g ON s.goods = g.goods ";
		$sql .= "WHERE c.customer is not null ";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
				if (!empty($get->s['no'])) { $sql .= sprintf("AND c.customer = '%s' ", $get->s['no']); }
				if (!empty($get->s['customer_name'])) { $sql .= sprintf("AND c.name LIKE '%s%s' ", $get->s['customer_name'], '%'); }
/*
				if (!empty($get->s['order_s_dt'])) { $sql .= sprintf("AND s.rgdt >= '%s 00:00:00' ", $get->s['order_s_dt']); }
				if (!empty($get->s['order_e_dt'])) { $sql .= sprintf("AND s.rgdt <= '%s 23:59:59' ", $get->s['order_e_dt']); }

				if (!empty($get->s['arrival_s_dt'])) { $sql .= sprintf("AND s.arrival_dt >= '%s 00:00:00' ", $get->s['arrival_s_dt']); }
				if (!empty($get->s['arrival_e_dt'])) { $sql .= sprintf("AND s.arrival_dt <= '%s 23:59:59' ", $get->s['arrival_e_dt']); }
*/
//				$sql .= "ORDER BY g.goods desc";
				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}
//$this->vd($sql);
		$rows = $wpdb->get_results($sql);
		if ($un_convert == true) { return $rows; }

		// convert
		foreach ($rows as $i => $row) {
			$tmp[$row->delivery_dt][$row->id] = $row;
		}

		// repeat copy
		$test = (object) array (
			'id' => 1,
			'class' => 2,
			'delivery_dt' => '2022-12-21',
			'goods' => '',
			'goods_name' => 'ミルククイーン',
			'ship_addr' => 'A棟',
			'qty' => 6,
			'arrival_dt' => '2022-12-20',
			'name' => '梅田畜産',
			'repeat_fg' => 1,
			'remark' => '',
			'field1' => '',
			'field2' => '',
			'field3' => '',
			'rgdt' => '2023-01-25 06:19:00', 
			'updt' => '',
			'upuser' => '',
			'repeat_id' => 1,
			'period' => 1,
			'span' => 5,
			'day_of_week' => '',
			'st_dt' => '2022-12-20 00:00:00', 
			'ed_dt' => '2023-12-20 00:00:00',
		);

		$days10 = array('2022-12-20', '2022-12-21', '2022-12-22', '2022-12-23', '2022-12-24', '2022-12-25');
		//$days10 = array('2022-12-20', '2022-12-21', '2022-12-22', '2022-12-23', '2022-12-24', '2022-12-25', '2022-12-26', '2022-12-27', '2022-12-28', '2022-12-29', '2022-12-30', '2022-12-31', '2023-01-01');
			foreach ($days10 as $i => $day) {
				$t = $test;
				$t->delivery_dt = $day;
				// repeat分
				$t->goods_name = 'rep:ミルククイーン';
				$result[$day][] = $t;
				// 個別分
				if (!empty($tmp[$day])) {
					foreach ($tmp[$day] as $i => $list) {
						$result[$day][] = $list;
					}
				}
			}

//$this->vd($result);
		return $result;
	}

	/**
	 * 顧客情報詳細取得
	 **/
	public function getDetail($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		// 顧客IDで検索して顧客情報を取得するSQL
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
	 * 顧客情報詳細取得
	 * - 顧客コード(customer)から抽出
	 **/
	public function getDetailByCustomerCode($customer = null) {
		global $wpdb;

		$sql  = "SELECT c.*, cd.* FROM ". $this->getTableName(). " as c LEFT JOIN yc_customer_detail AS cd ON c.customer = cd.customer ";
		$sql .= sprintf("WHERE c.customer = '%s' ", $customer);
		$sql .= ";";

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
	 * 顧客情報登録
	 **/
	public function regDetail($get = null, $post = null) {
		global $wpdb;

		$exist_columns = $wpdb->get_col("DESC ". $this->getTableName(). ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!empty($post->$col)) {
				$data[$col] = $post->$col;
			}
		}

		$data['name'] = $post->customer_name;
		$data['rgdt'] = date('Y-m-d H:i:s');

		$ret = $wpdb->insert(
			$this->getTableName(), 
			$data
			//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
		);

		// 登録したIDを取得
		$customer = $wpdb->insert_id;

		foreach ($post->pref as $i => $d) {
			$detail = $i+1;
			$ret_detail[] = $wpdb->insert(
				'yc_customer_detail', 
				array(
					'customer' => $customer, 
					'detail' => $detail, 
					'pref' => $post->pref[$i], 
					'addr1' => $post->addr1[$i], 
					'addr2' => $post->addr2[$i], 
					'addr3' => $post->addr3[$i], 
					'rgdt' => date('Y-m-d H:i:s')
				)
				//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
			);
		}

		// 登録情報を再取得
		$rows = $this->getDetailByCustomerCode($customer);
		return $rows;
	}

	/**
	 * 顧客情報更新
	 **/
	public function updDetail($get = null, $post = null) {
		$post = (object) $post;
		global $wpdb;
$post->name = $post->customer_name;

		$exist_columns = $wpdb->get_col("DESC ". $this->getTableName(). ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!empty($post->$col)) {
				$data[$col] = $post->$col;
			}
		}
		$ret = $wpdb->update(
			$this->getTableName(), 
			$data, 
			array('customer' => $post->customer)
		);

		// upsert
		if ($post->list) {
			foreach ($post->list as $i => $d) {
				$detail = $i+1;
				$targetId = $wpdb->get_var($wpdb->prepare("SELECT customer FROM yc_customer_detail WHERE customer = %s AND detail = %s", $post->customer, $detail));
				if (is_null($targetId)) {
					$ret[] = $wpdb->insert(
						'yc_customer_detail', 
						array(
							'customer' => $post->customer, 
							'detail' => $detail, 
							'pref' => $d->pref, 
							'addr1' => $d->addr1, 
							'addr2' => $d->addr2, 
							'addr3' => $d->addr3, 
							'rgdt' => date('Y-m-d H:i:s')
						)
						//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
					);
				} else {
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
				}
			}
		}

		// 更新情報を再取得
		$rows = $this->getDetailByCustomerCode($post->customer);
		return $rows;
	}

	public function vd($d) {
		$cur_user = wp_get_current_user();
		//var_dump($cur_user->user_login);
		//var_dump($cur_user->user_email);

		if (current($cur_user->roles) == 'administrator') {
			echo '<pre>';
			var_dump($d);
			echo '</pre>';
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
}
?>

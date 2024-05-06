<?php
/**
 * Sales.php short discription
 *
 * long discription
 *
 */
require_once(dirname(__DIR__). '/library/Ext/Model/Base.php');
/**
 * SalesClass short discription
 *
 * long discription
 *
 */
class Sales extends Ext_Model_Base {
	protected $_name = 'yc_sales';

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
				'customer'					=> 'required|min:1', 
				'class'						=> 'required', 
				'goods'						=> 'required|max:3', 
				'qty'						=> 'required', 
				'delivery_dt'				=> 'required', 
				'outgoing_warehouse'		=> 'required', 

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
		global $wpdb;
		$cur_user = wp_get_current_user();

		$sql  = "SELECT s.*, sc.repeat, sc.period, sc.span, sc.week, sc.repeat_s_dt, sc.repeat_e_dt, g.name as goods_name, g.separately_fg, c.*, c.name AS customer_name, s.rgdt AS rgdt, s.updt AS updt, s.upuser AS upuser ";
//		$sql  = "SELECT s.*, sc.repeat, sc.period, sc.span, sc.week, sc.repeat_s_dt, sc.repeat_e_dt, g.name as goods_name, gd.* ";
		$sql .= "FROM yc_sales AS s ";
		$sql .= "LEFT JOIN yc_customer AS c ON s.customer = c.customer ";
		$sql .= "LEFT JOIN yc_schedule_repeat AS sc ON s.sales = sc.sales ";
		$sql .= "LEFT JOIN yc_goods AS g ON s.goods = g.goods ";
//		$sql .= "LEFT JOIN yc_goods_detail AS gd ON g.goods = gd.goods ";
		$sql .= "WHERE s.sales is not null AND s.status <> 9 ";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
				if (!empty($get->s['no'])) { $sql .= sprintf("AND s.sales = '%s' ", $get->s['no']); }
				if (!empty($get->s['goods_name'])) { $sql .= sprintf("AND g.name LIKE '%s%s' ", $get->s['goods_name'], '%'); }

				if (!empty($get->s['order_s_dt'])) { $sql .= sprintf("AND s.rgdt >= '%s 00:00:00' ", $get->s['order_s_dt']); }
				if (!empty($get->s['order_e_dt'])) { $sql .= sprintf("AND s.rgdt <= '%s 23:59:59' ", $get->s['order_e_dt']); }

				if (!empty($get->s['arrival_s_dt'])) { $sql .= sprintf("AND s.arrival_dt >= '%s 00:00:00' ", $get->s['arrival_s_dt']); }
				if (!empty($get->s['arrival_e_dt'])) { $sql .= sprintf("AND s.arrival_dt <= '%s 23:59:59' ", $get->s['arrival_e_dt']); }

				if ($get->page != 'delivery-graph') { // 配送表画面以外の時は降順
					$sql .= "ORDER BY s.rgdt desc";
				}
				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}

		$rows = $wpdb->get_results($sql);
/*
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
*/

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

$sdt = $get->s['sdt'];
$dt = new DateTime($sdt. ' +1 days');
//$this->vd($dt->format('Y-m-d'));

			for ($i = 0; $i<11; $i++) { // 表示は11日分
				$dt = new DateTime($sdt. ' +'. $i. ' days');
				$days10[] = $dt->format('Y-m-d');
			}
//$this->vd($days10);
/*
			$days10 = array(
				'2023-07-17', '2023-07-18', '2023-07-19', '2023-07-20', '2023-07-21', '2023-07-22',
				'2022-12-20', '2022-12-21', '2022-12-22', '2022-12-23', '2022-12-24', '2022-12-25'
			);
*/

//$this->vd($rows);exit;
			// convert: 配送番号順にソート
			foreach ($rows as $i => $row) {
				$tmp[$row->delivery_dt][$row->sales][$row->id] = $row;
			}
//$this->vd($tmp);exit;
			// 指定日付の注文を抽出
			foreach ($days10 as $i => $day) {
/*
				$t = $tmp;
				$t->delivery_dt = $day;
				// repeat分
				$result[$day][] = $t;
*/
				// 個別分
				if (!empty($tmp[$day])) {
					foreach ($tmp[$day] as $sales => $list) {
						$result[$day][$sales] = $list;
					}
				} else {
					$result[$day] = '';
				}
			}
//$this->vd($result);exit;
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

		$sql  = "SELECT s.*, sr.*, s.sales AS sales FROM ". $this->getTableName(). " as s "; 
		// →リピート登録がない場合、JOIN後に受注番号(yc_sales.sales)が消えるため、"s.sales AS sales"カラム表示を追加
		$sql .= "LEFT JOIN yc_schedule_repeat AS sr ON s.sales = sr.sales ";
		$sql .= sprintf("WHERE s.sales = '%s' ", $sales);
		$sql .= "AND s.status <> 9 "; // 削除フラグが立ってないもの
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

		$sql  = "SELECT s.sales, s.ship_addr, s.arrival_dt, s.name, g.goods, g.name as goods_name, g.qty as goods_qty, gd.id as lot_tmp_id, gd.lot, gd.barcode, gd.tank, c.customer, c.name AS customer_name ";
		$sql .= "FROM yc_sales as s ";
		$sql .= "LEFT JOIN yc_goods as g ON s.goods = g.goods ";
		$sql .= "LEFT JOIN yc_goods_detail as gd on s.sales = gd.sales ";
		$sql .= "LEFT JOIN yc_customer as c ON s.customer = c.customer ";
		$sql .= "WHERE s.sales is not null AND s.status <> 9 ";
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
//		$post->repeat_fg = ($post->repeat_fg == 'on') ? 1 : 0;

//		$p->updt = date('Y-m-d H:i:s'); // updt

		$exist_columns = $wpdb->get_col("DESC ". $this->getTableName(). ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!is_null($post->$col)) {
				if ($col != 'qty') {
					$data[$col] = $post->$col;
				} else {
					if (!in_array($post->class, array(8,9,10))) { // 太田畜産、村上畜産用、「直取」用
						$select_qty = $this->getPartsQty();
						$data[$col] = $select_qty[$post->$col];
					} else {
						$data[$col] = $post->$col;
					}
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

/*
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
*/
		// 登録情報を再取得
		$rows = $this->getDetailBySalesCode($sales);
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
//		$post->repeat_fg = ($post->repeat_fg == 'on') ? 1 : 0;
		$post->repeat_fg = ($post->repeat_fg) ? $post->repeat_fg : 0; // checkboxのvalueがfalseだとパラメータが取れないため初期化

		// 既登録情報を取得
		$registed = $this->getDetailBySalesCode($post->sales);

		$exist_columns = $wpdb->get_col("DESC ". $this->getTableName(). ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!is_null($post->$col)) {
				if ($col != 'qty') {
					$data[$col] = $post->$col;
				} else {
					if (!in_array($post->class, array(8,9,10))) {
						// 結果入力の更新以外は、プルダウンから選択
						$select_qty = $this->getPartsQty();
						$data[$col] = $select_qty[$post->$col];
					} else {
						// 結果入力からの更新は、テキスト入力のため、floatに変換
						$data[$col] = (float) $post->$col;
					}
				}
			}
		}

		$data['updt'] = date('Y-m-d H:i:s');
		$cur_user = wp_get_current_user();
		$data['upuser'] = $cur_user->user_login;

		$ret = $wpdb->update(
			$this->getTableName(), 
			$data, 
			array('sales' => $post->sales)
		);

		// 既登録の情報と、数量が変更する場合
		$confirm_status = (int) array_search('確定', $this->getPartsStatus());
		if ($registed->qty != $post->qty && $registed->status == $confirm_status && $get->page == 'sales-detail') {
//$this->vd("into lot delete process.");

			/**
			 * 詳細情報の更新(数量変更によるロット登録欄数の変更等)
			 **/
			// 既に作成されいてるロット登録欄を削除
			$ret_delete[] = $wpdb->delete(
				'yc_goods_detail', 
				array(
					'sales' => $post->sales, 
				)
				//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
			);

			// 変更後のロット数で、ロット登録欄を再作成
			$count = $post->qty * 2; // 個数 (500kg/個) = ロット番号入力レコード生成数
			for ($j = 0; $j < $count; $j++) {
				$ret_remake_lot[] = $wpdb->insert(
					'yc_goods_detail', 
					array(
						'id' => null, 
						'sales' => $post->sales, 
						'goods' => $post->goods, 
						'lot' => null, 
						'barcode' => null, 
						'tank' => null, 
						'rgdt' => date('Y-m-d H:i:s')
					)
					//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
				);
			}
		}

/*
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
*/
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
				'lot' => $post->lot[$id], 
				'barcode' => (!empty($post->barcode[$id])) ? $post->barcode[$id] : NULL
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
	 * ロット番号の登録状況の確認
	 * 
	 * 「現在日付から、2日前の注文を対象として、ロット番号の入力が未済のもの(=未登録)があれば、アラート表示する。」
	 * 「アラートは、対象の注文のロット番頭入力を完了(=登録済み)にするまで、表示し続ける。」
	 * 
	 **/
	public function checkLotNumberStatus() {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		// 現在日付から、2日前の日付取得
		$now = date('Y-m-d');
		$dt = new DateTime($now. ' -2 days');
		$alert_dt = $dt->format('Y-m-d');

		$sql  = "SELECT s.sales, s.delivery_dt, s.lot_fg ";
		$sql .= "FROM yc_sales as s ";
		$sql .= "WHERE s.sales is not null AND s.status <> 9 ";
		$sql .= sprintf("AND s.delivery_dt <= '%s' ", $alert_dt);
		$sql .= "AND s.class <> 7 "; // 6t-⑦(「直取」専用)を対象外とする
		$sql .= "AND s.lot_fg < 2 ";

		$rows = $wpdb->get_results($sql);

		// convert
		foreach ($rows as $i => $row) {
			$conv[$row->delivery_dt][] = $row;
		}

		// sum
		foreach ($conv as $delivery_dt => $objs) {
			$sum[$delivery_dt] = count($objs);
		}

		// make alert message
		foreach ($sum as $delivery_dt => $cnt) {
			$alert_message = sprintf('%s ロット番号が未処理の注文が %s 件 あります。', $delivery_dt, $cnt);
//			$ret[] = mb_convert_encoding($alert_message, 'UTF-8', 'SJIS');
			$ret[] = $alert_message;
		}

		return (!empty($ret)) ? $ret : array();
	}

	/**
	 * 受領書の受取状況の確認
	 * 
	 * 「現在日付から、2日前の注文を対象として、受領書の受取が未済のもの(=未登録)があれば、アラート表示する。」
	 * 「アラートは、対象の注文のロット番頭入力を完了(=登録済み)にするまで、表示し続ける。」
	 * 
	 **/
	public function checkReceiptStatus() {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		// 現在日付から、2日前の日付取得
		$now = date('Y-m-d');
		$dt = new DateTime($now. ' -2 days');
		$alert_dt = $dt->format('Y-m-d');

		$sql  = "SELECT s.sales, s.delivery_dt, s.receipt_fg ";
		$sql .= "FROM yc_sales as s ";
		$sql .= "WHERE s.sales is not null AND s.status <> 9 ";
		$sql .= sprintf("AND s.delivery_dt <= '%s' ", $alert_dt);
		$sql .= "AND s.class <> 7 "; // 6t-⑦(「直取」専用)を対象外とする
		$sql .= "AND s.receipt_fg = 0 ";

		$rows = $wpdb->get_results($sql);

		// convert
		foreach ($rows as $i => $row) {
			$conv[$row->delivery_dt][] = $row;
		}

		// sum
		foreach ($conv as $delivery_dt => $objs) {
			$sum[$delivery_dt] = count($objs);
		}

		// make alert message
		foreach ($sum as $delivery_dt => $cnt) {
			$alert_message = sprintf('%s 受領書の受取が未処理の注文が %s 件 あります。', $delivery_dt, $cnt);
//			$ret[] = mb_convert_encoding($alert_message, 'UTF-8', 'SJIS');
			$ret[] = $alert_message;
		}

		return (!empty($ret)) ? $ret : array();
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
	 * 注文情報のコピー登録
	 * 
	 **/
	public function copyDetail($get = null, $post = null) {
		$rows = $this->getDetailForRepeatByBaseSalesCode($post->base_sales);
//$this->vd($rows);exit;
		$post->sales = null;
		$post->goods = $rows->goods;
		$post->ship_addr = $rows->ship_addr;

		$select_qty = $this->getPartsQty();
		$post->qty = $select_qty[sprintf('%.1f', $rows->qty)];

		$post->customer = $rows->customer;
//		$post->repeat_fg = 0;
		$post->lot_fg = 0;
		$post->status = 0;
		$post->rgdt = null;
		$post->updt = null;
		$post->upuser = null;
		$post->period= null;
		$post->span= null;
		$post->week= null;
		$post->repeat_s_dt= null;
		$post->repeat_e_dt= null;
//$this->vd($post);exit;
		$ret = $this->regDetail($get, $post);
		return $ret;
	}

	/**
	 * 受注情報詳細取得（繰返注文のための）
	 * - 元注文コード(base_sales)から抽出
	 **/
	public function getDetailForRepeatByBaseSalesCode($sales = null) {
		global $wpdb;

		$sql  = "SELECT s.*, sr.*, s.sales AS sales FROM ". $this->getTableName(). " as s "; 
		// →リピート登録がない場合、JOIN後に受注番号(yc_sales.sales)が消えるため、"s.sales AS sales"カラム表示を追加
		$sql .= "LEFT JOIN yc_schedule_repeat AS sr ON s.sales = sr.sales ";
		$sql .= sprintf("WHERE s.sales = '%s' ", $sales);
		$sql .= "AND s.status <> 9 ";
		$sql .= "AND s.repeat_fg = 1 ";
		$sql .= "LIMIT 1;";
		$rows = $wpdb->get_results($sql);

		$ret = current($rows);
		$ret->week = explode(',', $ret->week);

		return $ret;
	}

	/**
	 * 入庫一覧取得
	 * 
	 **/
	public function getListByArrivalDt($get = null, $post = null, $bulk = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		$sql  = "SELECT s.sales, s.goods, g.name AS goods_name, s.arrival_dt, s.customer AS customer, s.qty, s.outgoing_warehouse, s.repeat_fg, s.remark, c.name AS customer_name, s.ship_addr, cd.tank ";
		$sql .= "FROM yc_sales AS s ";
		$sql .= "LEFT JOIN yc_goods AS g ON s.goods = g.goods ";
		$sql .= "LEFT JOIN yc_customer AS c ON s.customer = c.customer ";
		$sql .= "LEFT JOIN yc_customer_detail AS cd ON c.customer = cd.customer AND s.ship_addr = cd.detail ";
		$sql .= "WHERE s.sales is not null AND s.status <> 9 ";
		$sql .= "AND s.class NOT IN (8,9,10) "; // 「結果入力」欄は表示せず

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
				if ($bulk != true) {
					if (!empty($get->s['arrival_s_dt'])) { $sql .= sprintf("AND s.arrival_dt = '%s' ", $get->s['arrival_s_dt']); }
				} else {
					if (!empty($get->s['arrival_s_dt'])) { $sql .= sprintf("AND s.arrival_dt >= '%s' ", $get->s['arrival_s_dt']); }
					if (!empty($get->s['arrival_e_dt'])) { $sql .= sprintf("AND s.arrival_dt <= '%s' ", $get->s['arrival_e_dt']); }
				}

				if (!empty($get->s['customer_name'])) { $sql .= sprintf("AND c.name LIKE '%s%s' ", $get->s['customer_name'], '%'); }
				if (!empty($get->s['goods_name'])) { $sql .= sprintf("AND g.name LIKE '%s%s' ", $get->s['goods_name'], '%'); }

				if (!empty($get->s['outgoing_warehouse'])) { $sql .= sprintf("AND s.outgoing_warehouse = '%s' ", $get->s['outgoing_warehouse']); }

				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}

		$rows = $wpdb->get_results($sql);
		return $rows;
	}

	/**
	 * 入庫一覧情報の商品単位の集計
	 * 
	 **/
	public function sumReceiveListByGoods($rows = null) {
		foreach ($rows as $i => $d) {
			$ret[$d->goods][$d->arrival_dt][$d->customer][] = $d;

			$sum_tmp[$d->goods][$d->arrival_dt]['goods'] = $d->goods;
			$sum_tmp[$d->goods][$d->arrival_dt]['goods_name'] = $d->goods_name;
			$sum_tmp[$d->goods][$d->arrival_dt]['qty'][] = $d->qty;
			$sum_tmp[$d->goods][$d->arrival_dt]['outgoing_warehouse'] = $d->outgoing_warehouse;
			$sum_tmp[$d->goods][$d->arrival_dt]['repeat'] = $d->repeat;
			$sum_tmp[$d->goods][$d->arrival_dt]['repeat_fg'] = $d->repeat_fg;
		}

		foreach ($sum_tmp as $goods => $list) {
			foreach ($list as $arrival_dt => $d) {
				$sum_list[$goods][$arrival_dt] = (object) $d;
			}
		}

		return array($ret, $sum_list);
	}

	/**
	 * 入庫一覧情報の集計
	 * 
	 **/
	public function sumReceiveList($rows = null) {
		foreach ($rows as $i => $d) {
			$sum_qty[] = $d->qty;
		}

		return array_sum($sum_qty);
	}

	/**
	 * 注文集計情報の集計
	 * 
	 **/
	public function sumSalesSummaryList($rows = null) {
		foreach ($rows as $i => $d) {
			$sum_qty[] = $d->sum_qty;
		}

		return array_sum($sum_qty);
	}

	/**
	 * 配送先(タンク)名の取得
	 * 
	 **/	
	public function setTankName($rows) {
		$initForm = $this->getInitForm();
		$ship_addr = $initForm['select']['ship_addr'];

		foreach ($rows as $delivery_dt => $d) {
			foreach ($d as $sales => $v) {
				$t = current($v);
				$t->tank_name = $ship_addr[$t->customer][$t->ship_addr];
			}
		}

		return $rows;
	}

	/**
	 * 繰り返し設定の初期化
	 * 
	 **/
	public function initRepeatFg($post = null) {
		$post = (object) $post;
		global $wpdb;

		$data = array(
			'sales' => $post->base_sales, 
			'repeat_fg' => 0
		);

//$this->vd($data);exit;

		$ret = $wpdb->update(
			$this->getTableName(), 
			$data, 
			array('sales' => $post->base_sales)
		);
		return $ret;
	}

	/**
	 * 受注の集計(注文集計)
	 * 
	 **/
	public function getSummary($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		$sql  = "SELECT s.goods, g.name AS goods_name, s.arrival_dt, s.customer AS customer, s.qty, s.outgoing_warehouse, c.name AS customer_name, s.ship_addr, cd.tank, SUM(s.qty) AS sum_qty, s.field1 AS result_ship_addr ";
		$sql .= "FROM yc_sales AS s ";
		$sql .= "LEFT JOIN yc_goods AS g ON s.goods = g.goods ";
		$sql .= "LEFT JOIN yc_customer AS c ON s.customer = c.customer ";
		$sql .= "LEFT JOIN yc_customer_detail AS cd ON c.customer = cd.customer AND s.ship_addr = cd.detail ";
		$sql .= "WHERE s.sales is not null AND s.status <> 9 ";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
				if (!empty($get->s['customer_name'])) { $sql .= sprintf("AND c.name LIKE '%s%s%s' ", '%', $get->s['customer_name'], '%'); }
				if (!empty($get->s['tank'])) { $sql .= sprintf("AND cd.tank LIKE '%s%s%s' ", '%', $get->s['tank'], '%'); }
				if (!empty($get->s['goods_name'])) { $sql .= sprintf("AND g.name LIKE '%s%s%s' ", '%', $get->s['goods_name'], '%'); }

				if (!empty($get->s['delivery_s_dt'])) { $sql .= sprintf("AND s.delivery_dt >= '%s' ", $get->s['delivery_s_dt']); }
				if (!empty($get->s['delivery_e_dt'])) { $sql .= sprintf("AND s.delivery_dt <= '%s' ", $get->s['delivery_e_dt']); }

				if (!empty($get->s['outgoing_warehouse'])) { $sql .= sprintf("AND s.outgoing_warehouse = '%s' ", $get->s['outgoing_warehouse']); }

//				$sql .= ";";

			} else {
//				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}

		$sql .= "GROUP BY c.customer, g.goods, s.outgoing_warehouse";
		$sql .= ";";

//$this->vd($sql);
		$rows = $wpdb->get_results($sql);
		return $rows;
	}

	/**
	 * 
	 **/
	public function getInitForm() {
		return array(
			'select' => array(
				'customer' => $this->getPartsOrderName(), 
				'car_model' => $this->getPartsCarModel(), 
				'car_model_add' => $this->getPartsCarModelAdd(), 
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
	 * 「車種」結果入力分 追加用
	 **/
	private function getPartsCarModelAdd() {
		return array(
			8 => '6t-8',
			9 => '6t-9',
			10 => '6t-10',
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
		$sql  = "SELECT c.customer, g.goods, g.name, g.separately_fg, g.remark FROM yc_customer as c ";
		$sql .= "LEFT JOIN yc_customer_goods as cg ON c.customer = cg.customer ";
		$sql .= "LEFT JOIN yc_goods as g ON cg.goods = g.goods ";
		$sql .= ";";
		$rows = $wpdb->get_results($sql);

		// 配列整形
		$ret[0] = '';
		foreach ($rows as $i => $d) {
			$ret[$d->customer][0] = '';
			$separately = ($d->separately_fg == true) ? " （バラ）" : null;
			$ret[$d->customer][$d->goods] = sprintf("%s%s", $d->name, $separately);
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
			$ret[$d->customer][$d->detail] = sprintf("%s", $d->tank);
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
			9 => '削除',
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
			9 => 'カスタム',
		);
	}

	/**
	 * 繰り返し間隔
	 * 
	 * yc_schedule_repeat.span
	 **/
	private function getPartsSpan() {
		$ret = range(0, 99);
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

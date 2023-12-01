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
				'goods_name'				=> 'required|max:100',
				'qty'						=> 'required|max:100',
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
	 * 商品情報詳細取得
	 * - 商品コード(goods)から抽出
	 **/
	public function getDetailByGoodsCode($goods = null) {
		global $wpdb;

		$sql  = "SELECT g.* FROM ". $this->getTableName(). " as g ";
		$sql .= sprintf("WHERE g.goods = '%s' ", $goods);
		$sql .= "LIMIT 1;";

		$rows = $wpdb->get_results($sql);
		return $rows[0];
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
	 * 商品情報登録
	 **/
	public function regDetail($get = null, $post = null) {
		global $wpdb;

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

		$exist_columns = $wpdb->get_col("DESC ". $this->getTableName(). ";", 0);
		foreach ($exist_columns as $i => $col) {
			if(!empty($post->$col)) {
				$data[$col] = $post->$col;
			}
		}

		$data['name'] = $post->goods_name;

		$ret = $wpdb->insert(
			$this->getTableName(), 
			$data
			//array('%s', '%s', '%d', '%s') // 第3引数: フォーマット
		);

		// 登録したIDを取得
		$goods = $wpdb->insert_id;

		// 登録情報を再取得
		$rows = $this->getDetailByGoodsCode($goods);
		return $rows;
	}

	/**
	 * 商品情報更新
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
		$ret = $wpdb->update(
			$this->getTableName(), 
			$data, 
			array('goods' => $post->goods)
		);

		// 更新情報を再取得
		$rows = $this->getDetailByGoodsCode($post->goods);
		return $rows;
	}

	/**
	 * 在庫証明書 情報一覧取得
	 **/
	public function getStockExportList($get = null) {
		$get = (object) $get;
		global $wpdb;
		$cur_user = wp_get_current_user();

		$sql  = "SELECT st.goods, g.name AS goods_name, (g.qty * 1000) AS qty, count(*) AS cnt, (count(*) * 500) AS stock_total ";
		$sql .= "FROM yc_stock AS st ";
		$sql .= "LEFT JOIN yc_stock_detail AS std ON st.stock = std.stock ";
		$sql .= "LEFT JOIN yc_goods AS g ON g.goods = st.goods ";
		$sql .= "WHERE st.stock is not null ";

		if (current($cur_user->roles) != 'administrator') {
//			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		if (empty($get->action)) {
			$sql .= "GROUP BY st.goods ";
//			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";

		} else {
			if ($get->action == 'search') {
//				if (!empty($get->s['no'])) { $sql .= sprintf("AND g.goods = '%s' ", $get->s['no']); }
//				if (!empty($get->s['goods_name'])) { $sql .= sprintf("AND g.name LIKE '%s%s' ", $get->s['goods_name'], '%'); }
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
	 * 
	 **/
	public function getInitForm() {
		return array(
			'select' => array(
				'goods_name' => $this->getPartsGoodsName(), 
			)
		);
	}

	/**
	 * 「品名」一覧用
	 **/
	private function getPartsGoodsName() {
		global $wpdb;
		$rows = $this->getList();

		// 配列整形
		foreach ($rows as $i => $d) {
			if (!isset($d->name)) { continue; }
			$ret[$d->goods] = sprintf("%s", $d->name);
		}
		return $ret;
	}
}
?>

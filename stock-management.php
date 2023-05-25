<?php
//require(__DIR__. '/library/rakit/rakid/vendor/autoload.php');
use Rakit\Validation\Validator;
//require(__DIR__. '/library/vendor/autoload.php');
use eftec\bladeone\BladeOne;

require_once(dirname(__DIR__). '/stock-management/model/model.php');
require_once(dirname(__DIR__). '/stock-management/model/Shop.php');
require_once(dirname(__DIR__). '/stock-management/model/Applicant.php');
require_once(dirname(__DIR__). '/stock-management/model/Order.php');

//require(__DIR__. '/library/vendor/vendor_phpspreadsheet/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set('Asia/Tokyo');

/*
Plugin Name:Stock Management
Plugin URI: http://www.example.com/plugin
Description: 商品の在庫数を集計し、発注スケジュールの管理をする。
Author: myu
Version: 0.1
Author URI: http://www.example.com
*/

class StockManagement {

	/**
	 * 
	 **/
	function __construct() {
		add_action('admin_menu', array($this, 'add_pages'));
		add_action('admin_menu', array($this, 'add_sub_menu'));
//		add_action('init', array($this, 'export_csv'));
//		add_action('init', array($this, 'export_pdf'));
	}

	/**
	 * 
	 **/
	function add_pages() {
		add_menu_page('在庫/発注予定管理','在庫/発注予定管理',  'level_8', 'stock-management', array($this,'menu_top'), '', 26);
	}

	/**
	 * 
	 **/
	function add_sub_menu() {
		$cur_user = wp_get_current_user();

		switch ($cur_user->roles[0]) {
			case 'administrator':
				if (in_array($cur_user->user_login, array('admin'))) {
					add_submenu_page('stock-management', '商品登録','商品登録', 'read', 'goods-regist', array(&$this, 'goods_regist'));
					add_submenu_page('stock-management', '配送方法登録','配送方法登録', 'read', 'method-regist', array(&$this, 'method_regist'));
					add_submenu_page('stock-management', '顧客登録','顧客登録', 'read', 'customer-regist', array(&$this, 'customer_regist'));
					add_submenu_page('stock-management', '注文登録','注文登録', 'read', 'order-regist', array(&$this, 'order_regist'));
					add_submenu_page('stock-management', 'ロット番号登録','ロット番号登録', 'read', 'lot-regist', array(&$this, 'lot_regist'));
					add_submenu_page('stock-management', '在庫・配送予定表①','在庫・配送予定表①', 'read', 'stock-1-list', array(&$this, 'stock_1_list'));
					add_submenu_page('stock-management', '在庫管理表②','在庫管理表②', 'read', 'stock-2-list', array(&$this, 'stock_2_list'));
					add_submenu_page('stock-management', '配送予定表③','配送予定表③', 'read', 'order-list', array(&$this, 'order_list'));
					add_submenu_page('stock-management', '日別商品集計','日別商品集計', 'read', 'sum-day-goods', array(&$this, 'sum_day_goods'));
				} else {
					$this->remove_menus();
				}
				break;
			default:
				$this->remove_menus();
				add_action( 'admin_bar_menu', 'remove_admin_bar_menus', 999 );
				break;
		}
	}

	/**
	 * 
	 **/
	function reload() {
		unset($_POST); 
		unset($p); 
//		echo '<script type="text/javascript">if (window.name != "any") {window.location.reload();window.name = "any";} else {window.name = "";}</script>';
	}

	/**
	 * 
	 **/
	function confirm() {
		$blade = $this->set_view();
		list($prm, $p, $rows) = $this->preStepProcess('confirm');
		echo $blade->run("shop-detail-confirm", compact('rows', 'prm'));
	}

	/**
	 * 
	 **/
	function status() {
		$blade = $this->set_view();
		list($prm, $p, $rows, $step_num) = $this->preStepProcess('confirm');

		// 状態取得
		$tb = new Applicant;
		$status = $tb->getStatusForMenu();
		echo $blade->run("shop-detail-status", compact('status', 'step_num'));
	}

	/**
	 * 
	 **/
	function set_view() {
		$views = __DIR__. '/views';
		$cache = __DIR__. '/cache';
		$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
		return $blade;
	}

	/**
	 * menu
	 **/
	function menu_top() {
		$blade = $this->set_view();

		$applicant = new Applicant();
		$list = $applicant->getList();

		$msg = $this->getValidMsg();
		$title = '<p>menu_top</p>';
		echo $blade->run("sample", compact('title','fugafuga', 'msg'));
	}

	/**
	 * バリデーション実行
	 * 
	 **/
	function getValidMsg($step_num = null) {
		$app = new Applicant();
		$ve = $app->getValidElement($step_num);

		// rakid
		$validator = new Validator;
		$validator->setMessages([
//			'required' => ':attribute を入力してください',
			'required' => 'を入力してください',
			'email' => ':email tidak valid',
			'min' => 'の文字数が不足しています。',
			'max' => 'が文字数をオーバーしています。',
			'regex' => 'をカタカナで入力してください。',
			'biz_number' => 'は、国税庁が指定する13桁の番号で入力してください。',
			'goods_image1' => 'が選択されていません。',
			// etc
		]);

		// 項目コピーのradioにチェックが入ってる場合、rulesを削除してValidation不要にする
		$ve = $app->initValidationRules($_POST, $ve);

		// 入力欄「その他」のradioにチェックが入ってる場合、rulesを変更してValidationする
		$ve = $app->changeValidationRules($_POST, $ve);

		// 必須：商品画像①のvalidation追加
		if ((!empty($_FILES)) && ($step_num == 3)) {
			$ve = $app->changeFileValidationRules($_POST + $_FILES, $ve);
		}

		// make it
		$validation = $validator->make($_POST + $_FILES, $ve['rules'], $ve['messages']);
		
		// then validate
		$validation->validate();
		
		if ($validation->fails()) {
			// handling errors
			$errors = $validation->errors();
			$msg = $errors->firstOfAll();
		} else {
			// validation passes
			$msg = array('msg' => 'success');
		}
		return $msg;
	}

	/**
	 *
	 **/
	function goods_regist() {
		$blade = $this->set_view();
		echo $blade->run("goods-regist");
	}

	/**
	 *
	 **/
	function method_regist() {
		$blade = $this->set_view();
		echo $blade->run("method-regist");
	}

	/**
	 *
	 **/
	function customer_regist() {
		$blade = $this->set_view();
		echo $blade->run("customer-regist");
	}

	/**
	 *
	 **/
	function order_regist() {
		$blade = $this->set_view();
		echo $blade->run("order-regist");
	}

	/**
	 *
	 **/
	function lot_regist() {
		$blade = $this->set_view();
		$this->remove_menus();

		$prm = (object) $_GET;

		$tb = new Order;
		$rows = $tb->getLotNumberListByOrder($prm);
//$this->vd($rows);
		echo $blade->run("lot-regist", compact('rows'));
	}

	/**
	 *
	 **/
	function sum_day_goods() {
		$this->remove_menus();
		$blade = $this->set_view();
		echo $blade->run("sum-day-goods");
	}

	/**
	 *
	 **/
	function stock_1_list() {
		$blade = $this->set_view();
		//echo $blade->run("shop-1-list", compact('rows', 'prm', 'step_num', 'msg', 'aliases', 'initForm'));
		echo $blade->run("stock-1-list");
	}

	/**
	 *
	 **/
	function stock_2_list() {
		$blade = $this->set_view();
		echo $blade->run("stock-2-list", compact());
	}

	/**
	 * 申込データ一覧画面
	 *
	 **/
	function order_list() {
		$blade = $this->set_view();
		$prm = (object) $_GET;
		$p = (object) $_POST;

		switch($prm->action) {
			case 'regist':
				$tb = new Applicant;
				break;

			case '-1':
				$_GET['post'] = '-1';
				$_GET['action'] = 'search';
				if ($prm->export_all && $prm->service_type) {
					$_GET['action'] = 'export_all';
					echo 'case export_all';
					$this->export_csv($prm);
				}

			case 'export_pdf':
//				$_GET['action'] = 'export_pdf';
				echo 'case export_pdf';
				$this->export_pdf($prm);

			default:
				$tb = new Order;
				$initForm = $tb->getInitForm();
				$rows = $tb->getList();
				$formPage = 'order-list';
//$this->vd($rows);
				echo $blade->run("order-list", compact('rows', 'formPage', 'initForm'));
				break;

			case 'search' :
				$tb = new Applicant;
				$initForm = $tb->getInitForm();
//				$prm = (!empty($prm->post)) ? (object) $prm : $tb->getPrm();
				$rows = $tb->getList($prm);
				$formPage = 'order-list';
				echo $blade->run("order-list", compact('rows', 'formPage', 'initForm'));
				break;
				
			case 'save':
				if (!empty($_POST)) {
					$prm = (object) $_POST;
//					$tb = new Postmeta;
//					$result = $tb->updShopDetail($prm, $p);
					if ($prm->cmd == 'save') {
						$prm->messages = array('error' => array('error is _field_company-name.')); // TEST DATA 
						$tb = new Applicant;
						$rows = $tb->updDetail($prm);

					}
					if (empty($prm->messages)) {
	//					$result = $tb->updShopDetail($prm, $p);
					} else {
						echo '<script>var msg = document.getElementById("msg"); msg.innerHTML = "'. $p->messages['error'][0]. '";</script>';
					}
				}
				$formPage = 'order-list';
				echo $blade->run("shop-detail", compact('rows', 'formPage', 'prm'));
				break;

			case 'edit':
				$tb = new Applicant;
				$initForm = $tb->getInitForm();
				$rows = $tb->getDetail($prm);
				$p = $rows;
				$formPage = 'order-list';
				echo $blade->run("shop-detail", compact('rows', 'formPage', 'prm', 'p', 'initForm'));
				break;

			case 'edit-exe':
				$prm = (object) $_GET;
				$p = (object) $_POST;
/*
				$tb = new Postmeta;
				$rows = $tb->getShopDetail($prm);
*/
					//$this->_rows = $tb->updShopDetail($prm, $p);
				// TODO: transaction, validation
				$tb = new Applicant;
				if (!empty($_POST)) {
					if ($p->cmd == 'save') {
						$p->messages = array('error' => array('error is _field_company-name.')); // TEST DATA 
$msg = $this->getValidMsg();		
$this->vd($msg);
						if ($msg['msg'] != 'success') {
						} else {
							$rows = $tb->updDetail($prm, $p);
						}

					}
					if (empty($p->messages)) {
	//					$result = $tb->updShopDetail($prm, $p);
					} else {
						echo '<script>var msg = document.getElementById("msg"); msg.innerHTML = "'. $p->messages['error'][0]. '";</script>';
					}
				}
				
				$rows = $tb->getDetail($prm);

				$formPage = 'order-list';
				echo $blade->run("shop-detail", compact('rows', 'formPage', 'prm', 'p', 'msg'));

				break;

			case 'cancel':
				$prm = (object) $_GET;
				unset($_POST);
				$tb = new Applicant;
				$rows = $tb->getDetail($prm);
				$p = $rows;
				$formPage = 'order-list';
				echo $blade->run("shop-detail", compact('rows', 'formPage', 'prm', 'p'));
				break;

			case 'preview':
				// 申込データプレビュー画面
				// (PDF保存形式でプレビューする)
				echo 'test preview';
				$app = new Applicant;
				$curUser = $app->getCurUser();
				if ($curUser->roles != 'administrator') {
					$applicant = htmlspecialchars($_GET['post']);
					$row = $app->getDetailByApplicantCode($applicant);

				} else {
					$row = null;
				}
				echo $blade->run("preview", compact('row', 'formPage', 'prm', 'p'));
				break;

			case 'init-status':
				$prm = (object) $_GET;
				unset($_POST);
				$applicant = $prm->post;
				$tb = new Applicant;
				$ret = $tb->initStatus($applicant);
				$result = ($ret == true) ? 'true' : 'false';
				echo '<script>window.location.href = "'. home_url(). '/wp-admin/admin.php?page=order-list&init-status='. $result. '";</script>';
				break;
		}
	}

	/**
	 *
	 **/
	function remove_menus() {
		remove_menu_page('index.php'); //ダッシュボード
		remove_menu_page('profile.php'); // プロフィール
		remove_menu_page('edit.php'); //投稿メニュー
//		remove_menu_page('edit.php?post_type=memo'); //カスタム投稿タイプmemo
		remove_menu_page('upload.php'); // メディア
		remove_menu_page('edit.php?post_type=page'); //固定ページ
		remove_menu_page('edit-comments.php'); //コメント
		remove_menu_page('themes.php'); //外観
		remove_menu_page('plugins.php'); //プラグイン
//		remove_menu_page('users.php'); //ユーザー
		remove_menu_page('tools.php'); //ツールメニュー 
		remove_menu_page('options-general.php'); //設定 
	}

	/**
	 *
	 **/
	function vd($d) {
		echo '<pre>';
//		var_dump($d);
		print_r($d);
		echo '</pre>';
	}
}

$StockManagement = new StockManagement;

<?php
//require(__DIR__. '/library/rakit/rakid/vendor/autoload.php');
use Rakit\Validation\Validator;
//require(__DIR__. '/library/vendor/autoload.php');
use eftec\bladeone\BladeOne;

require_once(dirname(__DIR__). '/stock-management/models/model.php');
require_once(dirname(__DIR__). '/stock-management/models/Shop.php');
require_once(dirname(__DIR__). '/stock-management/models/Applicant.php');
require_once(dirname(__DIR__). '/stock-management/models/Sales.php');
require_once(dirname(__DIR__). '/stock-management/models/Goods.php');
require_once(dirname(__DIR__). '/stock-management/models/Customer.php');

require_once(dirname(__DIR__). '/stock-management/controllers/CustomerController.php');
require_once(dirname(__DIR__). '/stock-management/controllers/GoodsController.php');
require_once(dirname(__DIR__). '/stock-management/controllers/SalesController.php');
require_once(dirname(__DIR__). '/stock-management/controllers/MenuController.php');

//require(__DIR__. '/library/vendor/vendor_phpspreadsheet/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set('Asia/Tokyo');

/*
Plugin Name:Stock Management
Plugin URI: http://www.example.com/plugin
Description: 商品の在庫数を集計し、発注スケジュールの管理をする。
Author: gk12
Version: 0.1
Author URI: http://hack-note.com
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
				if (in_array($cur_user->user_login, array('admin', 'yamachu'))) {
					// 登録画面
					add_submenu_page('stock-management', '商品登録','🔷商品登録', 'read', 'goods-detail', array(&$this, 'goods_detail'));
					add_submenu_page('stock-management', '顧客登録','🔷顧客登録', 'read', 'customer-detail', array(&$this, 'customer_detail'));
					add_submenu_page('stock-management', '注文登録','🔷注文登録', 'read', 'sales-detail', array(&$this, 'sales_detail'));

					// 検索画面
					add_submenu_page('stock-management', '商品検索','🔶商品検索', 'read', 'goods-list', array(&$this, 'goods_list'));
					add_submenu_page('stock-management', '顧客検索','🔶顧客検索', 'read', 'customer-list', array(&$this, 'customer_list'));
					add_submenu_page('stock-management', '注文検索','🔶注文検索', 'read', 'sales-list', array(&$this, 'sales_list'));

					// その他
					add_submenu_page('stock-management', 'ロット番号登録','ロット番号登録', 'read', 'lot-regist', array(&$this, 'lot_regist'));
					add_submenu_page('stock-management', '配送予定表③','配送予定表③', 'read', 'delivery-graph', array(&$this, 'delivery_graph'));
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
	 * メニュー
	 **/
	function menu_top() {
		$m = new MenuController();
		$m->listAction();
	}

	/**
	 * 商品詳細
	 **/
	function goods_detail() {
		$g = new GoodsController();
		$g->detailAction();
	}

	/**
	 * 顧客詳細
	 **/
	function customer_detail() {
		$c = new CustomerController();
		$c->detailAction();
	}

	/**
	 * 注文詳細
	 **/
	function sales_detail() {
		$s = new SalesController();
		$s->detailAction();
	}

	/**
	 * ロット管理
	 **/
	function lot_regist() {
		$s = new SalesController();
		$s->lotRegistAction();
	}

	/**
	 * 日別集計
	 **/
	function sum_day_goods() {
		$s = new SalesController();
		$s->sumDayGoodsAction();
	}

	/**
	 * 商品検索
	 **/
	function goods_list() {
		$g = new GoodsController();
		$g->listAction();
	}

	/**
	 * 顧客検索
	 **/
	function customer_list() {
		$c = new CustomerController();
		$c->listAction();
	}

	/**
	 * 注文検索
	 **/
	function sales_list() {
		$s = new SalesController();
		$s->listAction();
	}

	/**
	 * 配送表
	 **/
	function delivery_graph() {
		$s = new SalesController();
		$s->deliveryGraph();
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
}

$StockManagement = new StockManagement;

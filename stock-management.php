<?php
require(__DIR__. '/library/rakit/rakid/vendor/autoload.php');
use Rakit\Validation\Validator;
require(__DIR__. '/library/vendor/autoload.php');
use eftec\bladeone\BladeOne;

require_once(dirname(__DIR__). '/stock-management/models/model.php');
require_once(dirname(__DIR__). '/stock-management/models/Sales.php');
require_once(dirname(__DIR__). '/stock-management/models/Goods.php');
require_once(dirname(__DIR__). '/stock-management/models/Customer.php');
require_once(dirname(__DIR__). '/stock-management/models/ScheduleRepeat.php');
require_once(dirname(__DIR__). '/stock-management/models/RepeatExclude.php');
require_once(dirname(__DIR__). '/stock-management/models/Stock.php');
require_once(dirname(__DIR__). '/stock-management/models/StockTransfer.php');

require_once(dirname(__DIR__). '/stock-management/controllers/CustomerController.php');
require_once(dirname(__DIR__). '/stock-management/controllers/GoodsController.php');
require_once(dirname(__DIR__). '/stock-management/controllers/SalesController.php');
require_once(dirname(__DIR__). '/stock-management/controllers/MenuController.php');
require_once(dirname(__DIR__). '/stock-management/controllers/StockController.php');

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
				$this->pack_add_submenu_page();
				break;

			case 'editor':
//				if (in_array($cur_user->user_login, array('admin', 'ceo', 'user'))) {
				$this->pack_add_submenu_page_for_editor();
				$this->remove_menus();

//				} else {
//					$this->remove_menus();
//				}
				break;

			case 'subscriber' :
//				if (in_array($cur_user->user_login, array('naitou'))) {
					add_submenu_page('stock-management', '配送予定表','🍎配送予定表', 'read', 'delivery-graph', array(&$this, 'delivery_graph'));
					add_submenu_page('stock-management', '配送予定表STG','🌳配送予定表(STG)', 'read', 'delivery-graph-stg', array(&$this, 'delivery_graph_stg'));
//				} else {
//					$this->remove_menus();
//				}

			default:
				$this->remove_menus();
				//add_action( 'admin_bar_menu', 'remove_admin_bar_menus', 999 );
				break;
		}
	}

	/**
	 * 
	 **/
	function pack_add_submenu_page() {
		// 登録画面
		add_submenu_page('stock-management', '商品登録','🔷商品登録', 'read', 'goods-detail', array(&$this, 'goods_detail'));
		add_submenu_page('stock-management', '顧客登録','🔷顧客登録', 'read', 'customer-detail', array(&$this, 'customer_detail'));
		add_submenu_page('stock-management', '注文登録','🔷注文登録', 'read', 'sales-detail', array(&$this, 'sales_detail'));
		add_submenu_page('', '在庫登録','🌟在庫登録', 'read', 'stock-detail', array(&$this, 'stock_detail'));
		add_submenu_page('stock-management', '在庫登録(一括)','🌟在庫登録(一括)', 'read', 'stock-bulk', array(&$this, 'stock_bulk'));
		add_submenu_page('stock-management', '転送処理','🔁転送', 'read', 'stock-transfer', array(&$this, 'stock_transfer'));

		// 検索画面
		add_submenu_page('stock-management', '商品検索','🔶商品検索', 'read', 'goods-list', array(&$this, 'goods_list'));
		add_submenu_page('stock-management', '顧客検索','🔶顧客検索', 'read', 'customer-list', array(&$this, 'customer_list'));
		add_submenu_page('stock-management', '注文検索','🔶注文検索', 'read', 'sales-list', array(&$this, 'sales_list'));
		add_submenu_page('stock-management', '在庫検索','🌟在庫検索', 'read', 'stock-list', array(&$this, 'stock_list'));
		add_submenu_page('stock-management', '入庫予定日検索','🌟入庫予定日検索', 'read', 'stock-receive', array(&$this, 'stock_receive'));

		// その他
		add_submenu_page('', 'ロット番号登録','ロット番号登録', 'read', 'lot-regist', array(&$this, 'lot_regist'));
		add_submenu_page('', '在庫ロット番号登録','🌟在庫ロット番号登録', 'read', 'stock-lot-regist', array(&$this, 'stock_lot_regist'));
		add_submenu_page('stock-management', '配送予定表','🍎配送予定表', 'read', 'delivery-graph', array(&$this, 'delivery_graph'));
		add_submenu_page('stock-management', '配送予定表STG','🌳配送予定表(STG)', 'read', 'delivery-graph-stg', array(&$this, 'delivery_graph_stg'));
//		add_submenu_page('stock-management', '日別商品集計','日別商品集計', 'read', 'sum-day-goods', array(&$this, 'sum_day_goods'));
		add_submenu_page('stock-management', '在庫証明書','🍃在庫証明書', 'read', 'stock-export', array(&$this, 'stock_export'));
		add_submenu_page('stock-management', '倉出伝票','🍃倉出伝票', 'read', 'stock-export-day', array(&$this, 'stock_export_day'));
		add_submenu_page('stock-management', '注文集計','✡注文集計', 'read', 'sales-summary', array(&$this, 'sales_summary'));
	}

	/**
	 * 
	 **/
	function pack_add_submenu_page_for_editor() {
		// 登録画面
//		add_submenu_page('stock-management', '商品登録','🔷商品登録', 'read', 'goods-detail', array(&$this, 'goods_detail'));
//		add_submenu_page('stock-management', '顧客登録','🔷顧客登録', 'read', 'customer-detail', array(&$this, 'customer_detail'));
		add_submenu_page('', '注文登録','🔷注文登録', 'read', 'sales-detail', array(&$this, 'sales_detail'));
//		add_submenu_page('', '在庫登録','🌟在庫登録', 'read', 'stock-detail', array(&$this, 'stock_detail'));
//		add_submenu_page('stock-management', '在庫登録(一括)','🌟在庫登録(一括)', 'read', 'stock-bulk', array(&$this, 'stock_bulk'));
//		add_submenu_page('stock-management', '転送処理','🔁転送', 'read', 'stock-transfer', array(&$this, 'stock_transfer'));

		// 検索画面
//		add_submenu_page('stock-management', '商品検索','🔶商品検索', 'read', 'goods-list', array(&$this, 'goods_list'));
//		add_submenu_page('stock-management', '顧客検索','🔶顧客検索', 'read', 'customer-list', array(&$this, 'customer_list'));
		add_submenu_page('stock-management', '注文検索','🔶注文検索', 'read', 'sales-list', array(&$this, 'sales_list'));
//		add_submenu_page('stock-management', '在庫検索','🌟在庫検索', 'read', 'stock-list', array(&$this, 'stock_list'));
//		add_submenu_page('stock-management', '入庫予定日検索','🌟入庫予定日検索', 'read', 'stock-receive', array(&$this, 'stock_receive'));

		// その他
//		add_submenu_page('', 'ロット番号登録','ロット番号登録', 'read', 'lot-regist', array(&$this, 'lot_regist'));
//		add_submenu_page('', '在庫ロット番号登録','🌟在庫ロット番号登録', 'read', 'stock-lot-regist', array(&$this, 'stock_lot_regist'));
		add_submenu_page('stock-management', '配送予定表','🍎配送予定表', 'read', 'delivery-graph', array(&$this, 'delivery_graph'));
		add_submenu_page('stock-management', '配送予定表STG','🌳配送予定表(STG)', 'read', 'delivery-graph-stg', array(&$this, 'delivery_graph_stg'));
//		add_submenu_page('stock-management', '日別商品集計','日別商品集計', 'read', 'sum-day-goods', array(&$this, 'sum_day_goods'));
//		add_submenu_page('stock-management', '在庫証明書','🍃在庫証明書', 'read', 'stock-export', array(&$this, 'stock_export'));
//		add_submenu_page('stock-management', '倉出伝票','🍃倉出伝票', 'read', 'stock-export-day', array(&$this, 'stock_export_day'));
		add_submenu_page('stock-management', '注文集計','✡注文集計', 'read', 'sales-summary', array(&$this, 'sales_summary'));
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
	 * 在庫詳細
	 **/
	function stock_detail() {
		$s = new StockController();
		$s->detailAction();
	}

	/**
	 * 在庫一括
	 **/
	function stock_bulk() {
		$s = new StockController();
		$s->bulkAction();
	}

	/**
	 * 転送処理
	 **/
	function stock_transfer() {
		$s = new StockController();
		$s->transferAction();
	}

	/**
	 * 在庫ロット番号登録
	 **/
	function stock_lot_regist() {
		$s = new StockController();
		$s->lotRegistAction();
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
	 * 在庫検索
	 **/
	function stock_list() {
		$s = new StockController();
		$s->listAction();
	}

	/**
	 * 入庫予定日検索
	 **/
	function stock_receive() {
		$s = new StockController();
		$s->receiveAction();
	}

	/**
	 * 在庫証明書
	 **/
	function stock_export() {
		$s = new StockController();
		$s->exportAction();
	}

	/**
	 * 倉出伝票
	 **/
	function stock_export_day() {
		$s = new StockController();
		$s->exportDayAction();
	}

	/**
	 * 配送表
	 **/
	function delivery_graph() {
		$s = new SalesController();
		$s->deliveryGraph();
	}

	/**
	 * 配送表(STG)
	 **/
	function delivery_graph_stg() {
		$s = new SalesController();
		$s->deliveryGraphSTG();
	}

	/**
	 * 注文集計
	 **/
	function sales_summary() {
		$s = new SalesController();
		$s->summaryAction();
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
		remove_menu_page('link-manager.php'); //リンク 
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

/**
 * バージョンアップ通知の非表示
 **/
function update_nag_hide() {
	remove_action('admin_notices', 'update_nag', 3);
	remove_action('admin_notices', 'maintenance_nag', 10);
}
add_action('admin_init', 'update_nag_hide');

/**
 * 「WordPress のご利用ありがとうございます。」の非表示、文言の追加
 **/
function custom_admin_footer() {
	// echo '<a href="mailto:test@test.com">システム管理者へ問合せ</a>';
}
add_filter('admin_footer_text', 'custom_admin_footer');

/**
 * セッションの開始
 **/
function init_session_start() {
	// セッションが開始されていなければここで開始
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
}
add_action('after_setup_theme', 'init_session_start');

/**
 * フッターの「最新バージョン」の非表示
 **/
function remove_version_admin() {
	echo '<style type="text/css">#footer-upgrade { display: none; }</style>';
}
add_action('admin_head', 'remove_version_admin');

/**
 * セキュリティヘッダーの付与
 *
 * Content-Security-Policy は、管理画面のスクリプトやスタイルを壊さないよう、
 * 他サイトへの埋め込み禁止（frame-ancestors）だけを指定する。
 * Strict-Transport-Security は、HTTPS で開かれたときだけ送る。
 **/
function sm_security_headers() {
	return array(
		'X-Content-Type-Options' => 'nosniff',
		'X-Frame-Options' => 'SAMEORIGIN',
		'Content-Security-Policy' => "frame-ancestors 'self'",
	);
}

function sm_send_security_headers() {
	if (headers_sent()) {
		return;
	}
	foreach (sm_security_headers() as $name => $value) {
		header($name . ': ' . $value);
	}
	if (is_ssl()) {
		header('Strict-Transport-Security: max-age=31536000');
	}
}
add_action('send_headers', 'sm_send_security_headers');
add_action('admin_init', 'sm_send_security_headers');

$StockManagement = new StockManagement;

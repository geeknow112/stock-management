<?php
/**
 * SalesCli.php short discription
 *
 * long discription
 *
 */
use eftec\bladeone\BladeOne;
require_once(dirname(__DIR__). '/library/Ext/Controller/Action.php');
require_once(dirname(__DIR__). '/library/Ext/Model/Base.php');
require_once(dirname(__DIR__). '/models/Sales.php');
/**
 * SalesCliClass short discription
 *
 * long discription
 *
 */
class SalesCli extends Ext_Controller_Action
{
	protected $_test = 'test';

	public function __construct() {
		echo $this->_test;
		$this->setTb('Sales');
	}


	public $_cron_data = 'test data';


	/**
	 * salesテーブルへ登録のための成形
	 * 
	 **/
	private function convertSalesData($post = null) {
		$r_order = array();
		foreach ($post->r_order as $i => $oid) {
			if (!empty($oid)) {
				$r_order = explode('_', $oid);
			} else {
				unset($post->r_order[$i]);
			}
		}
		$ddt = $r_order[5];
		$delivery_dt = substr($ddt, 0, 4). '-'. substr($ddt, 4, 2). '-'. substr($ddt, 6, 2);
		$post->base_delivery_dt = $delivery_dt; // スケジュール変更前のdelivery_dt
		$post->delivery_dt = (empty($post->change_delivery_dt)) ? $delivery_dt : $post->change_delivery_dt;
		$post->goods = $r_order[3];
		$post->class = $post->class;
		$post->cars_tank = $post->cars_tank;
		$post->base_sales = $r_order[2];
		$post->repeat = $r_order[4];
		$post->outgoing_warehouse = ($post->r_warehouse) ? $post->r_warehouse : $this->getTb()->getOutgoingWarehouseBySales($r_order[2]);
		$post->arrival_dt = ($post->r_arrival_dt) ? $post->r_arrival_dt : $this->setArrivalDt($delivery_dt);
	}

	/**
	 * 繰返を未確定に変更する処理 (「繰返→未確定」)
	 * 
	 **/
	public function registOrderProcessForRepeat($get = null, $post = null) {
		
		// salesテーブルへ登録のための成形
		$this->convertSalesData($post);
//		var_dump($post);exit;

		// salesテーブルへ登録
		$post->repeat_fg = true;
		$rows = $this->getTb()->copyDetail($get, $post);
//		var_dump($post);exit;
		// repeat_excludeテーブルに必要な情報を追加
		$post->sales = $post->base_sales;
		if (!empty($post->base_delivery_dt)) { $post->delivery_dt = $post->base_delivery_dt; }
//		var_dump($post);exit;

		// repeat_excludeテーブルへ登録
		$RepeatExclude = new RepeatExclude;
		$RepeatExclude->updDetail($get, $post);

		// 元注文の繰り返しOFF
		$init_bool = $this->getTb()->initRepeatFg($post);
//		$this->vd($init_bool);exit;

		// 元注文の繰り返しを新注文へコピーする
		$post->sales = $rows->sales;
		$post->delivery_dt = $rows->delivery_dt; // repeat_s_dtに新しいdelivery_dtを設定
		$ScheduleRepeat = new ScheduleRepeat;
		$ScheduleRepeat->copyDetail($get, $post);

		return $rows->sales;
	}

	/**
	 * 入庫予定日の初期設定
	 *   - 配送予定日の3日前に設定する
	 * 
	 * $delivery_dt: 配送予定日
	 **/
	private function setArrivalDt($delivery_dt = null) {
		$arrival_dt = new DateTime($delivery_dt. ' -3 days');
		return $arrival_dt->format('Y-m-d');;
	}

}


$_SERVER['HTTP_HOST'] = 'stg.lober-env-imp.work';
//$wp_dir = dirname(__DIR__). '/../../..';
//$wp_config = $wp_dir. '/wp-load.php';
require_once('/home/bitnami/stack/wordpress/wp-load.php');

$SalesCli = new SalesCli();
$cache_file = dirname(__DIR__). '/cache/tmp_file.json';
$bulk_data = json_decode(file_get_contents($cache_file));
//var_dump($bulk_data[0]->post->r_orders);exit;
foreach ($bulk_data as $i => $d) {
	foreach ($d->post as $k => $v) {
		if (preg_match('/customer*/', $k, $m)) {
			unset($d->post->$k);
		}

		if (preg_match('/^r_order$/', $k, $m)) {
			foreach ($v as $i => $data) {
				if (empty($data)) {
					unset($d->post->$k[$i]);
				}
			}
		}
	}
}
//var_dump($d->post);exit;

$d = current($bulk_data);
//var_dump($d);exit;
$get = $d->get;
$post = $d->post;
$orders = $post->r_order;
//var_dump($orders);exit;

$Sales = new Sales();

if (is_array($orders) && count($orders) > 0) {
	$j = 0;
	foreach ($orders as $i => $order) {
		$post->r_order[] = $order;
		$post->class = 1;
		$post->cars_tank = 1;
//		var_dump($post);exit;

		$SalesCli->registOrderProcessForRepeat($get, $post);

//		if ($j > 2) { var_dump($post);exit; }
		$j++;
	}
}

?>

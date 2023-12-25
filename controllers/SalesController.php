<?php
/**
 * SalesController.php short discription
 *
 * long discription
 *
 */
use eftec\bladeone\BladeOne;
require_once(dirname(__DIR__). '/library/Ext/Controller/Action.php');
/**
 * SalesControllerClass short discription
 *
 * long discription
 *
 */
class SalesController extends Ext_Controller_Action
{
	protected $_test = 'test';

	/**
	 *
	 **/
	public function listAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		try {
			// pagination
			$this->setPager('Sales');
			$wp_list_table = $this->getPager();

		} catch (Exception $e) {
			echo '<b>'. $e->getMessage(). '</b>';
		}

		global $wpdb;

		$this->setTb('Sales');

		switch($post->cmd) {
			case 'search':
			case 'edit':
				$ret = $this->getTb()->changeStatus($post->change_status, $post->no);
				$this->getTb()->makeLotSpace($get, $post);

			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList($get, $un_convert = true);
				$formPage = 'sales-list';
				echo $this->get_blade()->run("sales-list", compact('rows', 'get', 'post', 'formPage', 'initForm', 'wp_list_table'));
				break;
		}
	}

	/**
	 *
	 **/
	public function detailAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		$cur_user = wp_get_current_user();

		$this->setTb('Sales');
		$page = 'sales-detail';
		$initForm = $this->getTb()->getInitForm($post);

		$rows = null;
		switch($get->action) {
			case 'regist':
				break;

			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList();
$gnames = json_encode($initForm['select']['goods_name']);
$test_ship_addr = json_encode($initForm['select']['ship_addr']);
				echo $this->get_blade()->run("sales-detail", compact('cur_user', 'rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr'));
				break;

			case 'confirm':
				if (!empty($post)) {
					switch ($post->cmd) {
						default:
						case 'cmd_confirm':
							$msg = $this->getValidMsg();
							$rows = $post;
							if (!empty($rows->delivery_dt) && empty($rows->arrival_dt)) { $rows->arrival_dt = $this->setArrivalDt($rows->delivery_dt); } // post値[arrival_dt]が空の場合、[delivery_dt]の3日前に自動設定
							if (!empty($rows->week)) { $rows->week = array_keys($rows->week); } // post値[week]をcheckbox形式に変換
							if ($rows->sales) { $rows->btn = 'update'; }

							if ($msg['msg'] !== 'success') {
								$rows->messages = $msg;
							}
						break;
					}
				}
				if($rows->messages) {
						$msg = $rows->messages;
						$get->action = 'save';
				} else {
				}
$gnames = json_encode($initForm['select']['goods_name']);
$test_ship_addr = json_encode($initForm['select']['ship_addr']);
$set_ship_addr = ($post->customer && $post->ship_addr) ? $initForm['select']['ship_addr'][$post->customer][$post->ship_addr] : null;
				echo $this->get_blade()->run("sales-detail", compact('cur_user', 'rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr', 'set_ship_addr'));
				break;

			case 'save':
				if (!empty($post)) {
					if ($post->cmd == 'save') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->regDetail($get, $post);
//							$rows->order_name = $rows->name;

							// 繰り返し情報登録
							if ($rows->repeat_fg == 1) {
								$post->sales = $rows->sales;
								$ScheduleRepeat = new ScheduleRepeat();
								$repeat = $ScheduleRepeat->updDetail($get, $post);
							}

							// 登録情報を再取得
							$rows = $this->getTb()->getDetailBySalesCode($rows->sales);

							$get->action = 'complete';

						} else {
							$rows = $post;
//							$rows->name = $post->order_name;
							$rows->messages = $msg;
						}
					}
				}

$gnames = json_encode($initForm['select']['goods_name']);
$test_ship_addr = json_encode($initForm['select']['ship_addr']);
$set_ship_addr = ($post->customer && $post->ship_addr) ? $initForm['select']['ship_addr'][$post->customer][$post->ship_addr] : null;
				echo $this->get_blade()->run("sales-detail", compact('cur_user', 'rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr', 'set_ship_addr'));
				break;

			case 'edit-exe':
				if (!empty($post)) {
					if ($post->cmd == 'update') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->updDetail($get, $post);
//							$rows->order_name = $rows->name;

							// 繰り返し情報登録
							if ($rows->repeat_fg == 1) {
								$ScheduleRepeat = new ScheduleRepeat();
								$repeat = $ScheduleRepeat->updDetail($get, $post);
							}

							// 更新情報を再取得
							$rows = $this->getTb()->getDetailBySalesCode($rows->sales);

							$get->action = 'complete';

						} else {
							$rows = $post;
//							$rows->name = $post->order_name;
							$rows->messages = $msg;
						}
					}
				}
//$this->vd($rows);
$gnames = json_encode($initForm['select']['goods_name']);
$test_ship_addr = json_encode($initForm['select']['ship_addr']);
$set_ship_addr = ($post->customer && $post->ship_addr) ? $initForm['select']['ship_addr'][$post->customer][$post->ship_addr] : null;
				echo $this->get_blade()->run("sales-detail", compact('cur_user', 'rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr', 'set_ship_addr'));
				break;

			case 'edit':
				if (!empty($get->sales)) {
					$rows = $this->getTb()->getDetailBySalesCode($get->sales);
//					$rows->goods_name = $rows->name;
					$rows->cmd = $post->cmd = 'cmd_update';

				} else {
					$msg = $this->getValidMsg();

					$rows = $post;
//					$rows->name = $post->goods_name;

					if ($msg['msg'] !== 'success') {
						$rows->messages = $msg;
					}
				}
$gnames = json_encode($initForm['select']['goods_name']);
$test_ship_addr = json_encode($initForm['select']['ship_addr']);
$set_ship_addr = ($post->customer && $post->ship_addr) ? $initForm['select']['ship_addr'][$post->customer][$post->ship_addr] : null;
				echo $this->get_blade()->run("sales-detail", compact('cur_user', 'rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr', 'set_ship_addr'));
				break;
		}
	}

	/**
	 *
	 **/
	public function lotRegistAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;
//		$this->remove_menus();

		$this->setTb('Sales');

		switch($get->action) {
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getLotNumberListBySales($get);
				echo $this->get_blade()->run("lot-regist", compact('rows', 'formPage', 'get', 'post', 'msg'));
				break;

			case 'save':
				if (!empty($post)) {
					if ($post->cmd == 'save') {
						$msg = $this->getValidMsg(2);
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->updLotDetail($get, $post);
							$get->sales = $post->sales;
							$get->goods = $post->goods;
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->messages = $msg;
						}
					}
				}
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getLotNumberListBySales($get);

				// lot_fgの変更
				$this->getTb()->updLotFg($rows);

				echo $this->get_blade()->run("lot-regist", compact('rows', 'formPage', 'get', 'post', 'msg'));
				break;

			case 'confirm':
				if (!empty($post)) {
					switch ($post->cmd) {
						default:
						case 'cmd_confirm':
							$msg = $this->getValidMsg(2);
							$rows = $this->getTb()->getLotNumberListBySales($get);

							// DBの更新対象データを、post値に変更
							$plt_id = $post->lot_tmp_id;
							foreach ($rows as $lot_tmp_id => $d) {
								$d->tank = $post->tank[$lot_tmp_id];
								$d->lot = $post->lot[$lot_tmp_id];
							}

							if ($msg['msg'] !== 'success') {
								$rows->messages = $msg;
							}
						break;
					}
				}
				if($rows->messages) {
						$msg = $rows->messages;
						$get->action = 'save';
				} else {
				}
//$this->vd(array($get, $post, $msg, $rows, $page));
				echo $this->get_blade()->run("lot-regist", compact('rows', 'get', 'post', 'msg'));
				break;

			case 'edit':
				if (!empty($post->sales) && !empty($post->goods)) {
					$post->action = $get->action;
					$rows = $this->getTb()->getLotNumberListBySales($post);
					$rows->cmd = $post->cmd = 'cmd_update';

				} else {
					$msg = $this->getValidMsg();

					$rows = $post;
					$rows->name = $post->goods_name;

					if ($msg['msg'] !== 'success') {
						$rows->messages = $msg;
					}
				}
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getLotNumberListBySales($get);

				// lot_fgの変更
				$this->getTb()->updLotFg($rows);

				echo $this->get_blade()->run("lot-regist", compact('rows', 'get', 'post', 'msg'));
				break;

		}
	}

	/**
	 *
	 **/
	public function deliveryGraph() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		$cur_user = wp_get_current_user();

		$this->setTb('Sales');

		if (!isset($get->action) || $post->action == 'regist') { $get->action = $post->action; }

		switch($get->action) {
			case 'regist':
				// salesテーブルへ登録のための成形
				$this->convertSalesData($post);
				//$this->vd($post);

				// salesテーブルへ登録
				$rows = $this->getTb()->copyDetail($get, $post);

				// repeat_excludeテーブルに必要な情報を追加
				$post->sales = $post->base_sales;
				if (!empty($post->base_delivery_dt)) { $post->delivery_dt = $post->base_delivery_dt; }
				//$this->vd($post);exit;

				// repeat_excludeテーブルへ登録
				$RepeatExclude = new RepeatExclude;
				$RepeatExclude->updDetail($get, $post);

			case 'search':
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList($get);
				$rows = $this->getTb()->setTankName($rows); // 配送先(タンク)名の取得
				$sumTanks = $this->getTb()->sumTanks($rows);
				$formPage = 'delivery-graph';

// 日付から範囲内にrepeatがあるか確認し、あったら注文を参照し、repeat注文を生成して6t-0欄に表示する。
//$this->vd($get->s['sdt']);
$ScheduleRepeat = new ScheduleRepeat;
$repeat_list = $ScheduleRepeat->getList($get);

//$this->vd($rows);
//$this->vd(array_keys($repeat_list));
//$this->vd($repeat_list);


//$this->vd($repeat_list['2023-11-09']);

//$t = (array) current(current(current($rows['2023-07-17'])));
//$this->vd($t);
$r = array(
	array(
		'id' => '1',
		'goods_name' => 'g-1',
		'categoryNo' => '1'
	),
	array(
		'id' => '2',
		'goods_name' => 'g-2',
		'categoryNo' => '2'
	),

);
				// ロット番号アラートの作成
				$msg = $this->getTb()->checkLotNumberStatus();

				echo $this->get_blade()->run("delivery-graph", compact('cur_user', 'rows', 'get', 'post', 'formPage', 'initForm', 'r', 'sumTanks', 'msg', 'repeat_list'));
				break;
		}
	}

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

	/**
	 *
	 **/
	public function sumDayGoodsAction() {
		echo $this->get_blade()->run("sum-day-goods");
	}
}
?>

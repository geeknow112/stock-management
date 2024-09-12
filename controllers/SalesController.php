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

		// [検索画面へ戻る]ボタン用の処理
		switch($get->cmd) {
			case 'search':
			default: 
				$session_key = 'sales-search';

				// session 登録
				$uri = $_SERVER['REQUEST_URI'];
				$_SESSION[$session_key] = $uri;
				break;
		}

		switch($post->cmd) {
			case 'search':
			case 'edit':
			default:
				// 更新処理後、検索条件の維持のため、GET値をPOST値から補填
				if (!isset($get->s)) {  $get->s = $post->s; }
				$get->s['change_status'] = $post->change_status;

				$initForm = $this->getTb()->getInitForm();
				$initForm['select']['car_model'] = array_merge($initForm['select']['car_model'], $initForm['select']['car_model_add']); // 検索用に「車種」プルダウンに要素追加

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

		// [検索画面へ戻る]ボタン用の処理
		if (empty($get->action)) {
				$session_key = 'sales-search';

				// session 初期化
				$_SESSION[$session_key] = '';
		}

		// 車種単位の限界値(6t)を監視する処理
		$sales = ($get->sales) ? $get->sales : $post->sales;
		$d_dt = $this->getTb()->getDeliveryDtBySales($sales);
		$sum_qty = $this->getTb()->getSumQtyByDeliveryDt($d_dt);
//		$this->vd($sum_qty);
		$initForm['select']['car_model_limit'] = (!empty($sum_qty)) ? $sum_qty : array();
//		$this->vd($initForm['select']['car_model']);
//		$this->vd($initForm['select']['car_model_limit']);

		// 車種の明細表示用の情報取得処理
		$class_detail = json_encode($this->getTb()->getClassDetailByDeliveryDt($d_dt));

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
				echo $this->get_blade()->run("sales-detail", compact('cur_user', 'rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr', 'set_ship_addr', 'class_detail'));
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
				if (!empty($rows)) { $this->getTb()->updLotFg($rows); }

				echo $this->get_blade()->run("lot-regist", compact('rows', 'formPage', 'initForm', 'get', 'post', 'msg'));
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
								$d->barcode = $post->barcode[$lot_tmp_id];
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
				echo $this->get_blade()->run("lot-regist", compact('rows', 'formPage', 'initForm', 'get', 'post', 'msg'));
				break;

			case 'edit':
				if (!empty($post->sales) && !empty($post->goods)) {
					$post->action = $get->action;
					$rows = $this->getTb()->getLotNumberListBySales($post);
					$post->cmd = 'cmd_update';

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

				echo $this->get_blade()->run("lot-regist", compact('rows', 'formPage', 'initForm', 'get', 'post', 'msg'));
				break;

		}
	}

	/**
	 *
	 **/
	public function deliveryGraph($viewUrl = null) {
		$get = (object) $_GET;
		$post = (object) $_POST;

		$cur_user = wp_get_current_user();

		$this->setTb('Sales');

		// [検索画面へ戻る]ボタン用の処理
		switch($get->cmd) {
			case 'search':
			default: 
				$session_key = 'sales-search';

				// session 登録
				$uri = $_SERVER['REQUEST_URI'];
				$_SESSION[$session_key] = $uri;
				break;
		}

		if (!isset($get->action) || $post->action == 'regist') { $get->action = $post->action; }

		if (!isset($get->action) || $post->action == 'set_result') { $get->action = $post->action; }

		if (!isset($get->action) || $post->action == 'set_receipt') { $get->action = $post->action; }

		if (!isset($get->action) || $post->action == 'set_direct_delivery') { $get->action = $post->action; }

		if (!isset($get->action) || $post->action == 'make_lot_space') { $get->action = $post->action; }

		if (!isset($get->action) || $post->action == 'order_update') { $get->action = $post->action; }

		switch($get->action) {
			case 'order_update': // 「量」、「配送先」の更新
				$data['sales'] = $post->sales;
				$data['repeat_fg'] = $post->repeat_fg; // repeat_fgをupdDetailで初期化させないため
				$data['qty'] = number_format($post->change_qty, 1);
				$data['ship_addr'] = $post->change_ship_addr;
				$data['use_stock'] = $post->use_stock;
				$data['field1'] = $post->ship_addr_text;
				$data['field2'] = true; // 「量」を変更した場合、予定表の入力欄をテキスト表示にするため、初回変更時にフラグをtrueにする。
				(object) $data;
				$result = $this->getTb()->updDetail($get, $data);
				break;

			case 'make_lot_space': // 配送予定表からロット登録欄の作成
				$data['sales'] = $post->sales;
				$data['repeat_fg'] = $post->repeat_fg; // repeat_fgをupdDetailで初期化させないため
				$data['lot_fg'] = 1;
				$data['status'] = $post->change_status = 1;
				$data['use_stock'] = $post->use_stock;
				(object) $data;
				$result = $this->getTb()->updDetail($get, $data);
				$this->getTb()->makeLotSpaceSingle($get, $post);
				break;

			case 'set_receipt': // 「受領書」フラグの更新
				$data['sales'] = $post->sales;
				$data['repeat_fg'] = $post->repeat_fg; // repeat_fgをupdDetailで初期化させないため
				$data['receipt_fg'] = true;
				$data['use_stock'] = $post->use_stock;
				(object) $data;
				$result = $this->getTb()->updDetail($get, $data);
				break;

			case 'set_result': // 「結果入力」欄の登録

				$oid = $post->oid;
				$ret['delivery_dt'] = sprintf('%s-%s-%s', substr($oid, 0, 4), substr($oid, 4, 2), substr($oid, 6, 2));
				$ret['arrival_dt'] = $this->setArrivalDt($ret['delivery_dt']); // [delivery_dt]の3日前に自動設定
				$ret['class'] = (int) substr($oid, 8, 2);
				$ret['cars_tank'] = (int) substr($oid, 10, 2);

				// 整形
				$data = str_replace('\"', '', $post->odata);
				$data = str_replace('{', '', $data);
				$data = str_replace('}', '', $data);
				$data = explode(',', $data);
				foreach ($data as $i => $d) {
					$v = explode(':', $d);
					$ret[$v[0]] = $v[1];
				}

				$pdata = (object) $ret;

				// class = (8,9)の場合、ship_addrの入力値を、field1に登録する
				if (in_array($pdata->class, array(8,9))) {
					$pdata->field1 = $pdata->ship_addr;
					unset($pdata->ship_addr);
				}
//				$this->vd($pdata);

				// yc_salesへ登録
				$result = $this->getTb()->regDetail($get, $pdata);
//$this->vd($result);
				break;
		}

		switch($get->action) {
			case 'regist':
			case 'set_direct_delivery': // 「直取分」の処理
				// salesテーブルへ登録のための成形
				$this->convertSalesData($post);
//				$this->vd($post);exit;

				// salesテーブルへ登録
				$post->repeat_fg = true;
				$rows = $this->getTb()->copyDetail($get, $post);

				// repeat_excludeテーブルに必要な情報を追加
				$post->sales = $post->base_sales;
				if (!empty($post->base_delivery_dt)) { $post->delivery_dt = $post->base_delivery_dt; }
				//$this->vd($post);exit;

				// repeat_excludeテーブルへ登録
				$RepeatExclude = new RepeatExclude;
				$RepeatExclude->updDetail($get, $post);

				// 元注文の繰り返しOFF
				$init_bool = $this->getTb()->initRepeatFg($post);
//				$this->vd($init_bool);exit;

				// 元注文の繰り返しを新注文へコピーする
				$post->sales = $rows->sales;
				$post->delivery_dt = $rows->delivery_dt; // repeat_s_dtに新しいdelivery_dtを設定
				$ScheduleRepeat = new ScheduleRepeat;
				$ScheduleRepeat->copyDetail($get, $post);

			case 'search':
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList($get);
				$formPage = 'delivery-graph';

				// 日付から範囲内にrepeatがあるか確認し、あったら注文を参照し、repeat注文を生成して6t-0欄に表示する。
				//$this->vd($get->s['sdt']);
				$ScheduleRepeat = new ScheduleRepeat;
				$repeat_list = $ScheduleRepeat->getList($get);

				// 配送先(タンク)名の取得
				$rows = $this->getTb()->setTankName($rows);
				$repeat_list = $this->getTb()->setTankName($repeat_list); // 6t-0からの移動時に、配送先コピー不要となったため削除(2024/06/02) →6t-0に表示が必要となったため再表示(2024/06/24)

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
				$msg1 = $this->getTb()->checkLotNumberStatus();

				// 受領書受取アラートの作成
				$msg2 = $this->getTb()->checkReceiptStatus();

				if (!empty($msg1) || !empty($msg2)) {
					$msg = array_merge($msg1, $msg2);
				}

				$initForm['fix_customer'] = array(
					// 太田畜産用
					'17' => array(
						'customer' => array(
							'17' => $initForm['select']['customer'][17]
						), 
						'goods' => array(
							'17' => $initForm['select']['goods_name'][17]
						), 
					), 
					// 村上養鶏場用
					'31' => array(
						'customer' => array(
							'31' => $initForm['select']['customer'][31]
						), 
						'goods' => array(
							'31' => $initForm['select']['goods_name'][31]
						), 
					), 
				);

//$this->vd($initForm['fix_customer']);

				$gnames = json_encode($initForm['select']['goods_name']);
				$test_ship_addr = json_encode($initForm['select']['ship_addr']);

				$formPage = (!is_null($viewUrl)) ? $viewUrl : 'delivery-graph';
				echo $this->get_blade()->run($formPage, compact('cur_user', 'rows', 'get', 'post', 'formPage', 'initForm', 'r', 'msg', 'repeat_list', 'gnames', 'test_ship_addr'));
				break;
		}
	}

	/**
	 *
	 **/
	public function deliveryGraphSTG() {
		$this->deliveryGraph('delivery-graph-stg');
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
		$post->outgoing_warehouse = $post->r_warehouse;
		$post->arrival_dt = $post->r_arrival_dt;
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
	 * 注文集計
	 * 
	 **/
	public function summaryAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		$this->setTb('Sales');

//$this->vd($get);
//$this->vd($post);

		switch($post->cmd) {
			case 'search':
			default:
				$initForm = $this->getTb()->getInitForm();

//				$rows = $this->getTb()->getList($get, $un_convert = true);
				$rows = $this->getTb()->getSummary($get);

				// 合計値の作成
				if (!empty(current($rows))) {
					$total = $this->getTb()->sumSalesSummaryList($rows);
				} else {
					$total = null;
				}

				$formPage = 'sales-summary';
				echo $this->get_blade()->run("sales-summary", compact('rows', 'get', 'post', 'formPage', 'initForm', 'total'));
				break;
		}
	}
}
?>

<?php
/**
 * StockController.php short discription
 *
 * long discription
 *
 */
use eftec\bladeone\BladeOne;
require_once(dirname(__DIR__). '/library/Ext/Controller/Action.php');
/**
 * StockControllerClass short discription
 *
 * long discription
 *
 */
class StockController extends Ext_Controller_Action
{
	protected $_test = 'test';

	/**
	 * 在庫検索
	 * 
	 **/
	public function listAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		try {
			// pagination
			$this->setPager('Stock');
			$wp_list_table = $this->getPager();

		} catch (Exception $e) {
			echo '<b>'. $e->getMessage(). '</b>';
		}

		global $wpdb;

		$this->setTb('Stock');

		// [検索画面へ戻る]ボタン用の処理
		switch($get->cmd) {
			case 'search':
			default: 
				$session_key = $get->page; // 'stock-list'

				// session 登録
				$uri = $_SERVER['REQUEST_URI'];
				$_SESSION[$session_key] = $uri;
				break;
		}

		switch($post->cmd) {
			case 'search':
			default:
				// 更新処理後、検索条件の維持のため、GET値をPOST値から補填
				if (!isset($get->s)) {  $get->s = $post->s; }
				$get->s['change_status'] = $post->change_status;

				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList($get, $un_convert = true);
				$formPage = 'stock-list';
				echo $this->get_blade()->run("stock-list", compact('rows', 'get', 'post', 'formPage', 'initForm', 'wp_list_table'));
				break;

			case 'cmd_cancel_transfer':
//				$this->vd($get);
//				$this->vd($post);

				$StockTransfer = new StockTransfer;
				$result = $StockTransfer->cancelTransfer($get->stock);
//$this->vd($result);

				$get = $post;
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList($get, $un_convert = true);
				$formPage = 'stock-list';
				echo $this->get_blade()->run("stock-list", compact('rows', 'get', 'post', 'formPage', 'initForm', 'wp_list_table'));
				break;
		}
	}

	/**
	 * 在庫登録
	 *
	 **/
	public function detailAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

if ($post->cmd == 'cmd_transfer') {
	$this->vd('cmd_transfer');
//	$this->vd($get);
//	$this->vd($post);
}
		global $wpdb;

		$this->setTb('Stock');
		$initForm = $this->getTb()->getInitForm();

		switch($get->action) {
			case 'search':
			default:
				$formPage = 'stock-detail';
				echo $this->get_blade()->run("stock-detail", compact('get', 'post', 'formPage', 'initForm', 'wp_list_table'));
				break;

			case 'confirm':
				if (!empty($post)) {
					switch ($post->cmd) {
						default:
						case 'cmd_confirm':
							$post->subtotal = str_replace(',', '', $post->subtotal); // カンマ除去
							$msg = $this->getValidMsg();
							$rows = $post;
							if ($rows->stock) { $rows->btn = 'update'; }
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

				echo $this->get_blade()->run("stock-detail", compact('rows', 'get', 'initForm', 'post', 'msg'));
				break;

			case 'save':
				if (!empty($post)) {
					if ($post->cmd == 'save') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->regDetail($get, $post);
//							$rows->customer_name = $rows->name;
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->messages = $msg;
						}
					}
				}
				echo $this->get_blade()->run("stock-detail", compact('rows', 'get', 'initForm', 'post', 'msg'));
				break;

			case 'edit-exe':
				if (!empty($post)) {
					if ($post->cmd == 'update') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->updDetail($get, $post);
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->messages = $msg;
						}
					}
				}

//				$rows_goods = $this->getTb()->getGoodsByCustomerCode($get->customer);
//				$cust_goods = $this->objectColumn($rows_goods, 'goods');

				echo $this->get_blade()->run("stock-detail", compact('rows', 'get', 'post', 'initForm', 'msg', 'goods_list'));
				break;

			case 'edit':
				if (!empty($get->stock)) {
					$rows = $this->getTb()->getDetail($get);
					$rows->cmd = $post->cmd = 'cmd_update';

				} else {
					$msg = $this->getValidMsg();

					$rows = $post;
					$rows->name = $post->customer_name;

					if ($msg['msg'] !== 'success') {
						$rows->messages = $msg;
					}
				}

				if ($post->cmd == 'cmd_update' ) {
//					$rows_tanks = $this->convertData($rows);
//					$rows_tanks_count = $this->countObject($rows_tanks);
				}

				echo $this->get_blade()->run("stock-detail", compact('rows', 'get', 'post', 'msg', 'initForm', 'rows_tanks', 'rows_tanks_count', 'rows_addrs', 'rows_addrs_count', 'rows_goods', 'goods_list', 'cust_goods'));
				break;
		}
	}

	/**
	 * 在庫登録
	 *
	 **/
	public function bulkAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

if ($post->cmd == 'cmd_transfer') {
	$this->vd('cmd_transfer');
//	$this->vd($get);
//	$this->vd($post);
}
		global $wpdb;

		$this->setTb('Stock');
		$initForm = $this->getTb()->getInitForm();

		switch($get->action) {
			case 'search':
			default:
				$formPage = 'stock-bulk';
				echo $this->get_blade()->run("stock-bulk", compact('get', 'post', 'formPage', 'initForm', 'wp_list_table'));
				break;

			case 'confirm':
				if (!empty($post)) {
					switch ($post->cmd) {
						default:
						case 'cmd_confirm':
							$msg = $this->getValidMsg();
							$rows = $post;
							$rows->outgoing_warehouse = $rows->warehouse = $post->warehouse = ($get->warehouse) ? $get->warehouse : $post->outgoing_warehouse;
							if ($rows->pre_cmd == 'cmd_update') { $post->btn = 'update'; }
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

				echo $this->get_blade()->run("stock-bulk", compact('rows', 'get', 'initForm', 'post', 'msg'));
				break;

			case 'save':
				if (!empty($post)) {
					if ($post->cmd == 'save') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->regDetail($get, $post);
//							$rows->customer_name = $rows->name;
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->messages = $msg;
						}
					}
				}
				echo $this->get_blade()->run("stock-bulk", compact('rows', 'get', 'initForm', 'post', 'msg'));
				break;

			case 'edit-exe':
				if (!empty($post)) {
					if ($post->cmd == 'update') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->updDetailBulk($get, $post);
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->messages = $msg;
						}
					}
				}

//				$rows_goods = $this->getTb()->getGoodsByCustomerCode($get->customer);
//				$cust_goods = $this->objectColumn($rows_goods, 'goods');

				echo $this->get_blade()->run("stock-bulk", compact('rows', 'get', 'post', 'initForm', 'msg', 'goods_list'));
				break;

			case 'edit':
				if (!empty($get->arrival_dt)) {
					$rows = $this->getTb()->getDetailByArrivalDt($get->arrival_dt, $get->warehouse);
					$rows->arrival_dt = $get->arrival_dt;
					$rows->outgoing_warehouse = $rows->warehouse = $post->warehouse = ($get->warehouse) ? $get->warehouse : $post->outgoing_warehouse;
//$this->vd($post);$this->vd($rows);exit;
					$rows->cmd = $post->cmd = 'cmd_update';

				} else {
					$msg = $this->getValidMsg();

					$rows = $post;
					$rows->name = $post->customer_name;

					if ($msg['msg'] !== 'success') {
						$rows->messages = $msg;
					}
				}

				if ($post->cmd == 'cmd_update' ) {
//					$rows_tanks = $this->convertData($rows);
//					$rows_tanks_count = $this->countObject($rows_tanks);
				}

//$this->vd($rows);
				echo $this->get_blade()->run("stock-bulk", compact('rows', 'get', 'post', 'msg', 'initForm', 'rows_tanks', 'rows_tanks_count', 'rows_addrs', 'rows_addrs_count', 'rows_goods', 'goods_list', 'cust_goods'));
				break;
		}
	}

	/**
	 * 在庫登録: ロット登録
	 *
	 **/
	public function lotRegistAction() {
		if(empty($_POST['arrival_dt'])) { $_POST['arrival_dt'] = $_GET['arrival_dt']; }
		if(empty($_POST['outgoing_warehouse'])) { $_POST['outgoing_warehouse'] = $_GET['warehouse']; }
		if(empty($_POST['outgoing_warehouse'])) { $_POST['outgoing_warehouse'] = $_POST['warehouse']; }

		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Stock');
		$initForm = $this->getTb()->getInitForm();

		switch($get->action) {
			case 'search':
			default:
				$rows = $this->getTb()->getDetailLotByStockCode($get->stock);
//$this->vd($rows);
				$formPage = 'stock-lot-regist';
				echo $this->get_blade()->run("stock-lot-regist", compact('rows', 'get', 'post', 'formPage', 'initForm'));
				break;

			case 'confirm':
				if (!empty($post)) {
					switch ($post->cmd) {
						default:
						case 'cmd_confirm':
							$msg = $this->getValidMsg();
							$rows = $post;
							if ($rows->customer) { $post->btn = 'update'; }
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

				// 表示用に成形
				$rows = $this->convertLotList($rows, $post);

				echo $this->get_blade()->run("stock-lot-regist", compact('rows', 'get', 'initForm', 'post', 'msg'));
				break;

			case 'save':
				if (!empty($post)) {
					if ($post->cmd == 'save') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->updLotNumber($get, $post); // ロット番号登録
//							$rows->customer_name = $rows->name;
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->messages = $msg;
						}
					}
				}
				echo $this->get_blade()->run("stock-lot-regist", compact('rows', 'get', 'initForm', 'post', 'msg'));
				break;

			case 'edit-exe':
				if (!empty($post)) {
					if ($post->cmd == 'update') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
if ($post->tank) { $post->list = $this->sortDataTanks($post); }
if ($post->pref) { $post->list = $this->sortData($post); }
							$rows = $this->getTb()->updDetail($get, $post);
//							$rows->customer_name = $rows->name;
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->messages = $msg;
						}
					}
				}
//$this->vd($post);
				if ($post->cmd == 'update' ) { $rows_tanks = $this->convertData($rows); }
				if ($post->cmd == 'update' ) { $rows_addrs = $this->convertData($rows); }
//$this->vd($rows_addrs);

				$rows_goods = $this->getTb()->getGoodsByCustomerCode($get->customer);
				$cust_goods = $this->objectColumn($rows_goods, 'goods');

				echo $this->get_blade()->run("stock-lot-regist", compact('rows', 'get', 'post', 'msg', 'rows_tanks', 'rows_addrs', 'rows_goods', 'goods_list', 'cust_goods'));
				break;

			case 'edit':
				if (!empty($get->arrival_dt)) {
					$rows = $this->getTb()->getDetailByArrivalDt($get->arrival_dt, $get->warehouse);
					$rows->arrival_dt = $get->arrival_dt;
					$rows->outgoing_warehouse = $get->warehouse;
//$this->vd($rows);
					$rows->cmd = $post->cmd = 'cmd_update';

				} else {
					$msg = $this->getValidMsg();

					$rows = $post;
					$rows->name = $post->customer_name;

					if ($msg['msg'] !== 'success') {
						$rows->messages = $msg;
					}
				}

				if ($post->cmd == 'cmd_update' ) {
//					$rows_tanks = $this->convertData($rows);
//					$rows_tanks_count = $this->countObject($rows_tanks);
				}

//$this->vd($rows);
				echo $this->get_blade()->run("stock-lot-regist", compact('rows', 'get', 'post', 'msg', 'initForm', 'rows_tanks', 'rows_tanks_count', 'rows_addrs', 'rows_addrs_count', 'rows_goods', 'goods_list', 'cust_goods'));
				break;
		}
	}

	/**
	 * 表示用に成形(ロット配列用)
	 * 
	 **/
	private function convertLotList($rows = null, $post = null) {
//		$this->vd($rows->lot);
		foreach ($rows->lot as $i => $lot) {
			$ret[$i] = (object) array(
				'goods_name' => $post->goods_name, 
				'lot' => $lot
			);
		}
		return (object) $ret;
	}

	/**
	 * 入庫予定日検索
	 *
	 **/
	public function receiveAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Sales');
		$initForm = $this->getTb()->getInitForm();

		if (!empty($get->s['arrival_s_dt']) && !empty($get->s['arrival_e_dt'])) {
			switch($post->cmd) {
				case 'regist':
					// salesテーブルへ登録のための成形
					$this->convertSalesData($post);

					// salesテーブルへ更新
					$rows = $this->getTb()->updDetail($get, $post);
// $this->vd($post);
// $this->vd($rows);exit;

				case 'search':
				default:
					// 日付から範囲内にrepeatがあるか確認し、あったら注文を参照し、repeat注文を生成して6t-0欄に表示する。
					$sdt = new DateTime($get->s['arrival_s_dt']);
					$sdt->modify('+3 day');
					$get->s['sdt'] = $sdt->format('Y-m-d'); // delivery_dtに変換 = +3日

					$edt = new DateTime($get->s['arrival_e_dt']);
					$edt->modify('+3 day');
					$get->s['edt'] = $edt->format('Y-m-d'); // delivery_dtに変換 = +3日

					$interval_days = $sdt->diff($edt);
					$use_days = $interval_days->format('%a');

					if ((($sdt <= $edt) == true) && ($use_days < 100)) { // 開始日より前の日付を終了日に入力した場合、かつ、範囲が100日を超える場合は、除外

						$ScheduleRepeat = new ScheduleRepeat;
						$repeats = $ScheduleRepeat->getList($get);

						switch ($get->sum_span) {
							default:
								// 未確定注文の10日分を生成
								$repeat_list = $repeats;

								// arrival_dtを初期化
	/*
								foreach ($repeat_list as $arrival_dt => $list) {
									foreach ($list as $sales => $d) {
										current($d)->arrival_dt = $arrival_dt;
									}
								}
	*/
								// $repeat_list を $rows の形式に変換
								foreach ($repeat_list as $arrival_dt => $list) {
									foreach ($list as $sales => $d) {
										$rep = current($d);

										// 繰り返し用に「配送先」を取得 ($ScheduleRepeat->getListで取得すると、JOINが増えSQLが複雑化するためここで取得する。)
										$rep->tank = $this->getTb()->getTankByCustomerAndShipAddr($rep->customer, $rep->ship_addr);

										// 検索条件：「品名」の対応
										if (!empty($get->s['goods_name'])) { 
											if (preg_match('/.*'. $get->s['goods_name']. '.*/', $rep->goods_name)) {
												$r_rows[] = $this->setRepeatRow($rep); // 表示形式に変換
											}
										} else {
											$r_rows[] = $this->setRepeatRow($rep); // 表示形式に変換
										}
									}
								}

								// 検索条件の対応
								$r_rows = $this->setSearchRuleForRepeat($get, $r_rows);

								// 確定した注文の取得
								$ret = $this->getTb()->getListByArrivalDt($get, $post, true);

								// 確定注文と、未確定注文のマージ
								$rows = (object) array_merge((array) $ret, (array) $r_rows); // object merge

								// arrival_dtでソート
								$sort_data = $rows;
								foreach ($sort_data as $i => $d) {
									$r_sort[$d->arrival_dt][] = $d;
								}

								if (!empty($r_sort)) {
									ksort($r_sort);
								}

								foreach ($r_sort as $arrival_dt => $list) {
									foreach ($list as $j => $d) {
										$rr_sort[] = $d;
									}
								}
								//$this->vd($rr_sort);

								$rows = (object) $rr_sort;
								break;
						}
//$this->vd($rows);
//$this->vd($repeat_list);
//$this->vd($r_rows);

						// 合計値の作成
						if (!empty(current($rows))) {
							list($detail, $sum_list) = $this->getTb()->sumReceiveListByGoods($rows);
//$this->vd($detail);
//$this->vd($sum_list);

							$total = $this->getTb()->sumReceiveList($rows);
						} else {
							$detail = $sum_list = $total = null;
						}
					}

					$formPage = 'stock-list';
					echo $this->get_blade()->run("stock-receive", compact('rows', 'get', 'post', 'formPage', 'initForm', 'detail', 'sum_list', 'total'));
					break;
			}

		} else {
				$formPage = 'stock-list';
				echo $this->get_blade()->run("stock-receive", compact('rows', 'get', 'post', 'formPage', 'initForm', 'detail', 'sum_list', 'total'));
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
		$adt = $r_order[5];
		$arrival_dt = substr($ddt, 0, 4). '-'. substr($ddt, 4, 2). '-'. substr($ddt, 6, 2);
		$post->arrival_dt = (empty($post->change_arrival_dt)) ? $arrival_dt : $post->change_arrival_dt;
		$post->goods = $r_order[3];
		$post->repeat_fg = $r_order[4];
		$post->sales = $r_order[2];
		$post->remark = 'reserve_fg';
	}

	/**
	 * 繰り返し情報を表示形式に設定
	 *
	 **/
	private function setRepeatRow($rep = null) {
		return (object) array(
			'goods' => $rep->goods, 
			'goods_name' => $rep->goods_name, 
			'arrival_dt' => $rep->arrival_dt, 
			'customer' => $rep->customer, 
			'qty' => $rep->qty, 
			'outgoing_warehouse' => $rep->outgoing_warehouse, 
			'customer_name' => $rep->customer_name, 
			'repeat' => $rep->repeat, 
			'repeat_fg' => $rep->repeat_fg, 
			'tank' => $rep->tank, 
		);
	}

	/**
	 * 未確定部分(繰り返し設定)の検索条件の適用処理
	 *
	 **/
	private function setSearchRuleForRepeat($get = null, $r_rows = null) {
		// 検索条件の対応
		foreach ($r_rows as $i => $d) {
			// 「顧客名」
			if (!empty($get->s['customer_name'])) { 
				if (!preg_match('/^.*'. $get->s['customer_name']. '.*/', $d->customer_name)) {
					unset($r_rows[$i]);
				}
			}

			// 「配送先」
			if (!empty($get->s['tank'])) { 
				if (!preg_match('/^.*'. $get->s['tank']. '.*/', $d->tank)) {
					unset($r_rows[$i]);
				}
			}

			// 「品名」
			if (!empty($get->s['goods_name'])) { 
				if (!preg_match('/.*'. $get->s['goods_name']. '.*/', $d->goods_name)) {
					unset($r_rows[$i]);
				}
			}
		}
		return $r_rows;
	}

	/**
	 * 在庫証明書 出力
	 *
	 **/
	public function exportAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Stock');

		$initForm = $this->getTb()->getInitForm();

		switch($get->cmd) {
			case 'search':
			default:
				$rows = $this->getTb()->getStockExportList($get);

				// 「注文」による在庫の減少 のための注文取得
				$dlist = $this->getTb()->getSalesDeliveredList($get);

//				$this->vd(count($rows));
//$this->vd($rows);
//$this->vd($dlist);
				// 「注文」(配送済み) 除外
				foreach ($rows as $i => $stock) {
					foreach ($dlist as $j => $del) {

						// 商品別で数量による除外
						if ($stock->goods == $del->goods) {
							if ($get->match_lot != true) {
								unset($rows[$i]);
								unset($dlist[$j]);
								break;

							} else {
								// ロット番号による除外
								if ($stock->lot == $del->lot) {
									unset($rows[$i]);
									unset($dlist[$j]);
									break;
								}
							}
						}

					}
				}

//				$this->vd(count($rows));

				// 再集計
				foreach ($rows as $i => $stock) {
					$data[$stock->goods][] = $stock;
				}
//				$this->vd($data);

				unset($rows);
				foreach ($data as $goods => $stocks) {

					// ロット番号(カウント)表示のための整形
					$lots = array();
					foreach ($stocks as $i => $std) {
						if (empty($std->lot)) { continue; }
						$tmp_lots[$std->lot][] = $std->lot;
					}

					foreach ($tmp_lots as $lot => $list) {
						$lots[] = sprintf('%s (%d)', $lot, count($list));
					}

					// ロット番号のソート(先頭の年度「23,24..」の順でソート)
					asort($lots);

					unset($tmp_lots);

					$goods = $stocks[0]->goods;
					$s['goods'] = $goods;
					$s['goods_name'] = $stocks[0]->goods_name;
					$s['qty'] = $stocks[0]->qty;
					$s['cnt'] = count($stocks);
					$s['stock_total'] = count($stocks) * 500;
//$this->vd($lots);exit;
					$s['lots'] = implode(', ', $lots);
					$rows[$goods] = (object) $s;
				}
//				$this->vd($rows);
				break;
		}

		// 在庫TB個数の総合計
		$stock_cnt = array_sum(array_column((array) $rows, 'cnt'));

		// 在庫数量の総合計
		$stock_sum = array_sum(array_column((array) $rows, 'stock_total'));

		// ソートのための順序作成
		$Goods = new Goods;
		$glist = $Goods->getList();
		//$this->vd($glist);
		foreach ($glist as $i => $gd) {
			$sort[$gd->goods] = (object) array(
				'goods_name' => $gd->name, 
				'separately_fg' => $gd->separately_fg, 
			);
		}

		echo $this->get_blade()->run("stock-export", compact('rows', 'get', 'post', 'formPage', 'initForm', 'stock_cnt', 'stock_sum', 'sort'));
	}

	/**
	 * 倉出伝票 出力
	 *
	 **/
	public function exportDayAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Stock');

		$initForm = $this->getTb()->getInitForm();

		// 「注文」分 ※配送予定表の①～⑥、⑧、⑨
		$rows = $this->getTb()->getStockExportListDay($get);

		// 「直取」分
		$jks = $this->getTb()->getStockExportListDay($get, true);

		// 「転送」　丹波SP ＞ 内藤SP
		$trans_t_n = $this->getTb()->getStockTransferList($get, 1);

		// 「転送」　内藤SP ＞ 丹波SP
		$trans_n_t = $this->getTb()->getStockTransferList($get, 2);

		echo $this->get_blade()->run("stock-export-day", compact('rows', 'jks', 'get', 'post', 'formPage', 'initForm', 'stock_cnt', 'stock_sum', 'trans_t_n', 'trans_n_t'));
	}

	/**
	 * 転送処理
	 *
	 **/
	public function transferAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

if ($post->cmd == 'cmd_transfer') {
	$this->vd('cmd_transfer');
//	$this->vd($get);
//	$this->vd($post);
}
		global $wpdb;

		$this->setTb('StockTransfer');
		$initForm = $this->getTb()->getInitForm();

		switch($get->action) {
			case 'search':
			default:
				$formPage = 'stock-transfer';
				echo $this->get_blade()->run("stock-transfer", compact('get', 'post', 'formPage', 'initForm', 'wp_list_table'));
				break;

			case 'confirm':
				if (!empty($post)) {
					switch ($post->cmd) {
						default:
						case 'cmd_confirm':
							$msg = $this->getValidMsg();
							$rows = $post;
							if ($rows->customer) { $post->btn = 'update'; }
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

				echo $this->get_blade()->run("stock-transfer", compact('rows', 'get', 'initForm', 'post', 'msg'));
				break;

			case 'save':
				if (!empty($post)) {
					if ($post->cmd == 'save') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->regDetail($get, $post);
//							$rows->customer_name = $rows->name;
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->messages = $msg;
						}
					}
				}
				echo $this->get_blade()->run("stock-transfer", compact('rows', 'get', 'initForm', 'post', 'msg'));
				break;

			case 'edit-exe':
				if (!empty($post)) {
					if ($post->cmd == 'update') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
if ($post->tank) { $post->list = $this->sortDataTanks($post); }
if ($post->pref) { $post->list = $this->sortData($post); }
							$rows = $this->getTb()->updDetail($get, $post);
//							$rows->customer_name = $rows->name;
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->messages = $msg;
						}
					}
				}
//$this->vd($post);
				if ($post->cmd == 'update' ) { $rows_tanks = $this->convertData($rows); }
				if ($post->cmd == 'update' ) { $rows_addrs = $this->convertData($rows); }
//$this->vd($rows_addrs);

				$rows_goods = $this->getTb()->getGoodsByCustomerCode($get->customer);
				$cust_goods = $this->objectColumn($rows_goods, 'goods');

				echo $this->get_blade()->run("stock-transfer", compact('rows', 'get', 'post', 'msg', 'rows_tanks', 'rows_addrs', 'rows_goods', 'goods_list', 'cust_goods'));
				break;

			case 'edit':
				if (!empty($get->stock)) {
					$rows = $this->getTb()->getDetailByStockCode($get->stock);
					$rows->cmd = $post->cmd = 'cmd_update';

				} else {
					$msg = $this->getValidMsg();

					$rows = $post;
					$rows->name = $post->customer_name;

					if ($msg['msg'] !== 'success') {
						$rows->messages = $msg;
					}
				}

				if ($post->cmd == 'cmd_update' ) {
//					$rows_tanks = $this->convertData($rows);
//					$rows_tanks_count = $this->countObject($rows_tanks);
				}

//$this->vd($rows);
				echo $this->get_blade()->run("stock-transfer", compact('rows', 'get', 'post', 'msg', 'initForm', 'rows_tanks', 'rows_tanks_count', 'rows_addrs', 'rows_addrs_count', 'rows_goods', 'goods_list', 'cust_goods'));
				break;
		}
	}
}
?>

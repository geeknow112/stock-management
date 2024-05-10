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
	 * �݌Ɍ���
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

		// [������ʂ֖߂�]�{�^���p�̏���
		switch($get->cmd) {
			case 'search':
			default: 
				$session_key = $get->page; // 'stock-list'

				// session �o�^
				$uri = $_SERVER['REQUEST_URI'];
				$_SESSION[$session_key] = $uri;
				break;
		}

		switch($post->cmd) {
			case 'search':
			default:
				// �X�V������A���������̈ێ��̂��߁AGET�l��POST�l�����U
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
	 * �݌ɓo�^
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
							$post->subtotal = str_replace(',', '', $post->subtotal); // �J���}����
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
	 * �݌ɓo�^
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
	 * �݌ɓo�^: ���b�g�o�^
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

				// �\���p�ɐ��`
				$rows = $this->convertLotList($rows, $post);

				echo $this->get_blade()->run("stock-lot-regist", compact('rows', 'get', 'initForm', 'post', 'msg'));
				break;

			case 'save':
				if (!empty($post)) {
					if ($post->cmd == 'save') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->updLotNumber($get, $post); // ���b�g�ԍ��o�^
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
	 * �\���p�ɐ��`(���b�g�z��p)
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
	 * ���ɗ\�������
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
					// sales�e�[�u���֓o�^�̂��߂̐��`
					$this->convertSalesData($post);

					// sales�e�[�u���֍X�V
					$rows = $this->getTb()->updDetail($get, $post);
// $this->vd($post);
// $this->vd($rows);exit;

				case 'search':
				default:
					// ���t����͈͓���repeat�����邩�m�F���A�������璍�����Q�Ƃ��Arepeat�����𐶐�����6t-0���ɕ\������B
					$sdt = new DateTime($get->s['arrival_s_dt']);
					$sdt->modify('+3 day');
					$get->s['sdt'] = $sdt->format('Y-m-d'); // delivery_dt�ɕϊ� = +3��

					$edt = new DateTime($get->s['arrival_e_dt']);
					$edt->modify('+3 day');
					$get->s['edt'] = $edt->format('Y-m-d'); // delivery_dt�ɕϊ� = +3��

					$interval_days = $sdt->diff($edt);
					$use_days = $interval_days->format('%a');

					if ((($sdt <= $edt) == true) && ($use_days < 100)) { // �J�n�����O�̓��t���I�����ɓ��͂����ꍇ�A���A�͈͂�100���𒴂���ꍇ�́A���O

						$ScheduleRepeat = new ScheduleRepeat;
						$repeats = $ScheduleRepeat->getList($get);

						switch ($get->sum_span) {
							default:
								// ���m�蒍����10�����𐶐�
								$repeat_list = $repeats;

								// arrival_dt��������
	/*
								foreach ($repeat_list as $arrival_dt => $list) {
									foreach ($list as $sales => $d) {
										current($d)->arrival_dt = $arrival_dt;
									}
								}
	*/
								// $repeat_list �� $rows �̌`���ɕϊ�
								foreach ($repeat_list as $arrival_dt => $list) {
									foreach ($list as $sales => $d) {
										$rep = current($d);

										// �J��Ԃ��p�Ɂu�z����v���擾 ($ScheduleRepeat->getList�Ŏ擾����ƁAJOIN������SQL�����G�����邽�߂����Ŏ擾����B)
										$rep->tank = $this->getTb()->getTankByCustomerAndShipAddr($rep->customer, $rep->ship_addr);

										// ���������F�u�i���v�̑Ή�
										if (!empty($get->s['goods_name'])) { 
											if (preg_match('/.*'. $get->s['goods_name']. '.*/', $rep->goods_name)) {
												$r_rows[] = $this->setRepeatRow($rep); // �\���`���ɕϊ�
											}
										} else {
											$r_rows[] = $this->setRepeatRow($rep); // �\���`���ɕϊ�
										}
									}
								}

								// ���������̑Ή�
								$r_rows = $this->setSearchRuleForRepeat($get, $r_rows);

								// �m�肵�������̎擾
								$ret = $this->getTb()->getListByArrivalDt($get, $post, true);

								// �m�蒍���ƁA���m�蒍���̃}�[�W
								$rows = (object) array_merge((array) $ret, (array) $r_rows); // object merge

								// arrival_dt�Ń\�[�g
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

						// ���v�l�̍쐬
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
	 * sales�e�[�u���֓o�^�̂��߂̐��`
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
	 * �J��Ԃ�����\���`���ɐݒ�
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
	 * ���m�蕔��(�J��Ԃ��ݒ�)�̌��������̓K�p����
	 *
	 **/
	private function setSearchRuleForRepeat($get = null, $r_rows = null) {
		// ���������̑Ή�
		foreach ($r_rows as $i => $d) {
			// �u�ڋq���v
			if (!empty($get->s['customer_name'])) { 
				if (!preg_match('/^.*'. $get->s['customer_name']. '.*/', $d->customer_name)) {
					unset($r_rows[$i]);
				}
			}

			// �u�z����v
			if (!empty($get->s['tank'])) { 
				if (!preg_match('/^.*'. $get->s['tank']. '.*/', $d->tank)) {
					unset($r_rows[$i]);
				}
			}

			// �u�i���v
			if (!empty($get->s['goods_name'])) { 
				if (!preg_match('/.*'. $get->s['goods_name']. '.*/', $d->goods_name)) {
					unset($r_rows[$i]);
				}
			}
		}
		return $r_rows;
	}

	/**
	 * �݌ɏؖ��� �o��
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

				// �u�����v�ɂ��݌ɂ̌��� �̂��߂̒����擾
				$dlist = $this->getTb()->getSalesDeliveredList($get);

//				$this->vd(count($rows));
//$this->vd($rows);
//$this->vd($dlist);
				// �u�����v(�z���ς�) ���O
				foreach ($rows as $i => $stock) {
					foreach ($dlist as $j => $del) {

						// ���i�ʂŐ��ʂɂ�鏜�O
						if ($stock->goods == $del->goods) {
							if ($get->match_lot != true) {
								unset($rows[$i]);
								unset($dlist[$j]);
								break;

							} else {
								// ���b�g�ԍ��ɂ�鏜�O
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

				// �ďW�v
				foreach ($rows as $i => $stock) {
					$data[$stock->goods][] = $stock;
				}
//				$this->vd($data);

				unset($rows);
				foreach ($data as $goods => $stocks) {

					// ���b�g�ԍ�(�J�E���g)�\���̂��߂̐��`
					$lots = array();
					foreach ($stocks as $i => $std) {
						if (empty($std->lot)) { continue; }
						$tmp_lots[$std->lot][] = $std->lot;
					}

					foreach ($tmp_lots as $lot => $list) {
						$lots[] = sprintf('%s (%d)', $lot, count($list));
					}

					// ���b�g�ԍ��̃\�[�g(�擪�̔N�x�u23,24..�v�̏��Ń\�[�g)
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

		// �݌�TB���̑����v
		$stock_cnt = array_sum(array_column((array) $rows, 'cnt'));

		// �݌ɐ��ʂ̑����v
		$stock_sum = array_sum(array_column((array) $rows, 'stock_total'));

		// �\�[�g�̂��߂̏����쐬
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
	 * �q�o�`�[ �o��
	 *
	 **/
	public function exportDayAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Stock');

		$initForm = $this->getTb()->getInitForm();

		// �u�����v�� ���z���\��\�̇@�`�E�A�G�A�H
		$rows = $this->getTb()->getStockExportListDay($get);

		// �u����v��
		$jks = $this->getTb()->getStockExportListDay($get, true);

		// �u�]���v�@�O�gSP �� ����SP
		$trans_t_n = $this->getTb()->getStockTransferList($get, 1);

		// �u�]���v�@����SP �� �O�gSP
		$trans_n_t = $this->getTb()->getStockTransferList($get, 2);

		echo $this->get_blade()->run("stock-export-day", compact('rows', 'jks', 'get', 'post', 'formPage', 'initForm', 'stock_cnt', 'stock_sum', 'trans_t_n', 'trans_n_t'));
	}

	/**
	 * �]������
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

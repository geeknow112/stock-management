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

		switch($post->cmd) {
			case 'search':
			default:
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
							$msg = $this->getValidMsg();
							$rows = $post;
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
				echo $this->get_blade()->run("stock-detail", compact('rows', 'get', 'post', 'msg', 'initForm', 'rows_tanks', 'rows_tanks_count', 'rows_addrs', 'rows_addrs_count', 'rows_goods', 'goods_list', 'cust_goods'));
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

		switch($post->cmd) {
			case 'search':
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getListByArrivalDt($get, $post);

// ���t����͈͓���repeat�����邩�m�F���A�������璍�����Q�Ƃ��Arepeat�����𐶐�����6t-0���ɕ\������B
$sdt = new DateTime($get->s['arrival_s_dt']);
$sdt->modify('+3 day');
$get->s['sdt'] = $sdt->format('Y-m-d'); // delivery_dt�ɕϊ� = +3��
//$this->vd($get);
$ScheduleRepeat = new ScheduleRepeat;
$repeats = $ScheduleRepeat->getList($get);
$repeat_list = $repeats[$get->s['sdt']];

// arrival_dt��������
foreach ($repeat_list as $sales => $d) {
	current($d)->arrival_dt = $get->s['arrival_s_dt'];
}

// $repeat_list �� $rows �̌`���ɕϊ�
foreach ($repeat_list as $sales => $d) {
	$rep = current($d);
	$r_rows[] = (object) array(
		'goods' => $rep->goods, 
		'goods_name' => $rep->goods_name, 
		'arrival_dt' => $rep->arrival_dt, 
		'customer' => $rep->customer, 
		'qty' => $rep->qty, 
		'outgoing_warehouse' => $rep->outgoing_warehouse, 
		'customer_name' => $rep->customer_name, 
		'repeat' => $rep->repeat, 
		'repeat_fg' => $rep->repeat_fg, 
	);
}
//$this->vd($rows);
//$this->vd($repeat_list);
//$this->vd($r_rows);

$rows = (object) array_merge((array) $rows, (array) $r_rows); // object merge
//$this->vd($rows);

				if (!empty(current($rows))) {
					list($detail, $sum_list) = $this->getTb()->sumReceiveListByGoods($rows);
//$this->vd($detail);
//$this->vd($sum_list);

					$total = $this->getTb()->sumReceiveList($rows);
				} else {
					$detail = $sum_list = $total = null;
				}

				$formPage = 'stock-list';
				echo $this->get_blade()->run("stock-receive", compact('rows', 'get', 'post', 'formPage', 'initForm', 'detail', 'sum_list', 'total'));
				break;
		}
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
							// ���b�g�ԍ��ɂ�鏜�O
							if ($stock->lot == $del->lot) {
								unset($rows[$i]);
								unset($dlist[$j]);
								break;
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
					$s['goods'] = $stocks[0]->goods;
					$s['goods_name'] = $stocks[0]->goods_name;
					$s['qty'] = $stocks[0]->qty;
					$s['cnt'] = count($stocks);
					$s['stock_total'] = count($stocks) * 500;
					$rows[] = (object) $s;
				}
//				$this->vd($rows);
				break;
		}

		// �݌�TB���̑����v
		$stock_cnt = array_sum(array_column((array) $rows, 'cnt'));

		// �݌ɐ��ʂ̑����v
		$stock_sum = array_sum(array_column((array) $rows, 'stock_total'));

		echo $this->get_blade()->run("stock-export", compact('rows', 'get', 'post', 'formPage', 'initForm', 'stock_cnt', 'stock_sum'));
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

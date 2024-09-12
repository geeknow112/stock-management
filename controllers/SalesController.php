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

		// [������ʂ֖߂�]�{�^���p�̏���
		switch($get->cmd) {
			case 'search':
			default: 
				$session_key = 'sales-search';

				// session �o�^
				$uri = $_SERVER['REQUEST_URI'];
				$_SESSION[$session_key] = $uri;
				break;
		}

		switch($post->cmd) {
			case 'search':
			case 'edit':
			default:
				// �X�V������A���������̈ێ��̂��߁AGET�l��POST�l�����U
				if (!isset($get->s)) {  $get->s = $post->s; }
				$get->s['change_status'] = $post->change_status;

				$initForm = $this->getTb()->getInitForm();
				$initForm['select']['car_model'] = array_merge($initForm['select']['car_model'], $initForm['select']['car_model_add']); // �����p�Ɂu�Ԏ�v�v���_�E���ɗv�f�ǉ�

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

		// [������ʂ֖߂�]�{�^���p�̏���
		if (empty($get->action)) {
				$session_key = 'sales-search';

				// session ������
				$_SESSION[$session_key] = '';
		}

		// �Ԏ�P�ʂ̌��E�l(6t)���Ď����鏈��
		$sales = ($get->sales) ? $get->sales : $post->sales;
		$d_dt = $this->getTb()->getDeliveryDtBySales($sales);
		$sum_qty = $this->getTb()->getSumQtyByDeliveryDt($d_dt);
//		$this->vd($sum_qty);
		$initForm['select']['car_model_limit'] = (!empty($sum_qty)) ? $sum_qty : array();
//		$this->vd($initForm['select']['car_model']);
//		$this->vd($initForm['select']['car_model_limit']);

		// �Ԏ�̖��ו\���p�̏��擾����
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
							if (!empty($rows->delivery_dt) && empty($rows->arrival_dt)) { $rows->arrival_dt = $this->setArrivalDt($rows->delivery_dt); } // post�l[arrival_dt]����̏ꍇ�A[delivery_dt]��3���O�Ɏ����ݒ�
							if (!empty($rows->week)) { $rows->week = array_keys($rows->week); } // post�l[week]��checkbox�`���ɕϊ�
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

							// �J��Ԃ����o�^
							if ($rows->repeat_fg == 1) {
								$post->sales = $rows->sales;
								$ScheduleRepeat = new ScheduleRepeat();
								$repeat = $ScheduleRepeat->updDetail($get, $post);
							}

							// �o�^�����Ď擾
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

							// �J��Ԃ����o�^
							if ($rows->repeat_fg == 1) {
								$ScheduleRepeat = new ScheduleRepeat();
								$repeat = $ScheduleRepeat->updDetail($get, $post);
							}

							// �X�V�����Ď擾
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

				// lot_fg�̕ύX
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

							// DB�̍X�V�Ώۃf�[�^���Apost�l�ɕύX
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

				// lot_fg�̕ύX
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

		// [������ʂ֖߂�]�{�^���p�̏���
		switch($get->cmd) {
			case 'search':
			default: 
				$session_key = 'sales-search';

				// session �o�^
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
			case 'order_update': // �u�ʁv�A�u�z����v�̍X�V
				$data['sales'] = $post->sales;
				$data['repeat_fg'] = $post->repeat_fg; // repeat_fg��updDetail�ŏ����������Ȃ�����
				$data['qty'] = number_format($post->change_qty, 1);
				$data['ship_addr'] = $post->change_ship_addr;
				$data['use_stock'] = $post->use_stock;
				$data['field1'] = $post->ship_addr_text;
				$data['field2'] = true; // �u�ʁv��ύX�����ꍇ�A�\��\�̓��͗����e�L�X�g�\���ɂ��邽�߁A����ύX���Ƀt���O��true�ɂ���B
				(object) $data;
				$result = $this->getTb()->updDetail($get, $data);
				break;

			case 'make_lot_space': // �z���\��\���烍�b�g�o�^���̍쐬
				$data['sales'] = $post->sales;
				$data['repeat_fg'] = $post->repeat_fg; // repeat_fg��updDetail�ŏ����������Ȃ�����
				$data['lot_fg'] = 1;
				$data['status'] = $post->change_status = 1;
				$data['use_stock'] = $post->use_stock;
				(object) $data;
				$result = $this->getTb()->updDetail($get, $data);
				$this->getTb()->makeLotSpaceSingle($get, $post);
				break;

			case 'set_receipt': // �u��̏��v�t���O�̍X�V
				$data['sales'] = $post->sales;
				$data['repeat_fg'] = $post->repeat_fg; // repeat_fg��updDetail�ŏ����������Ȃ�����
				$data['receipt_fg'] = true;
				$data['use_stock'] = $post->use_stock;
				(object) $data;
				$result = $this->getTb()->updDetail($get, $data);
				break;

			case 'set_result': // �u���ʓ��́v���̓o�^

				$oid = $post->oid;
				$ret['delivery_dt'] = sprintf('%s-%s-%s', substr($oid, 0, 4), substr($oid, 4, 2), substr($oid, 6, 2));
				$ret['arrival_dt'] = $this->setArrivalDt($ret['delivery_dt']); // [delivery_dt]��3���O�Ɏ����ݒ�
				$ret['class'] = (int) substr($oid, 8, 2);
				$ret['cars_tank'] = (int) substr($oid, 10, 2);

				// ���`
				$data = str_replace('\"', '', $post->odata);
				$data = str_replace('{', '', $data);
				$data = str_replace('}', '', $data);
				$data = explode(',', $data);
				foreach ($data as $i => $d) {
					$v = explode(':', $d);
					$ret[$v[0]] = $v[1];
				}

				$pdata = (object) $ret;

				// class = (8,9)�̏ꍇ�Aship_addr�̓��͒l���Afield1�ɓo�^����
				if (in_array($pdata->class, array(8,9))) {
					$pdata->field1 = $pdata->ship_addr;
					unset($pdata->ship_addr);
				}
//				$this->vd($pdata);

				// yc_sales�֓o�^
				$result = $this->getTb()->regDetail($get, $pdata);
//$this->vd($result);
				break;
		}

		switch($get->action) {
			case 'regist':
			case 'set_direct_delivery': // �u���敪�v�̏���
				// sales�e�[�u���֓o�^�̂��߂̐��`
				$this->convertSalesData($post);
//				$this->vd($post);exit;

				// sales�e�[�u���֓o�^
				$post->repeat_fg = true;
				$rows = $this->getTb()->copyDetail($get, $post);

				// repeat_exclude�e�[�u���ɕK�v�ȏ���ǉ�
				$post->sales = $post->base_sales;
				if (!empty($post->base_delivery_dt)) { $post->delivery_dt = $post->base_delivery_dt; }
				//$this->vd($post);exit;

				// repeat_exclude�e�[�u���֓o�^
				$RepeatExclude = new RepeatExclude;
				$RepeatExclude->updDetail($get, $post);

				// �������̌J��Ԃ�OFF
				$init_bool = $this->getTb()->initRepeatFg($post);
//				$this->vd($init_bool);exit;

				// �������̌J��Ԃ���V�����փR�s�[����
				$post->sales = $rows->sales;
				$post->delivery_dt = $rows->delivery_dt; // repeat_s_dt�ɐV����delivery_dt��ݒ�
				$ScheduleRepeat = new ScheduleRepeat;
				$ScheduleRepeat->copyDetail($get, $post);

			case 'search':
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList($get);
				$formPage = 'delivery-graph';

				// ���t����͈͓���repeat�����邩�m�F���A�������璍�����Q�Ƃ��Arepeat�����𐶐�����6t-0���ɕ\������B
				//$this->vd($get->s['sdt']);
				$ScheduleRepeat = new ScheduleRepeat;
				$repeat_list = $ScheduleRepeat->getList($get);

				// �z����(�^���N)���̎擾
				$rows = $this->getTb()->setTankName($rows);
				$repeat_list = $this->getTb()->setTankName($repeat_list); // 6t-0����̈ړ����ɁA�z����R�s�[�s�v�ƂȂ������ߍ폜(2024/06/02) ��6t-0�ɕ\�����K�v�ƂȂ������ߍĕ\��(2024/06/24)

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

				// ���b�g�ԍ��A���[�g�̍쐬
				$msg1 = $this->getTb()->checkLotNumberStatus();

				// ��̏����A���[�g�̍쐬
				$msg2 = $this->getTb()->checkReceiptStatus();

				if (!empty($msg1) || !empty($msg2)) {
					$msg = array_merge($msg1, $msg2);
				}

				$initForm['fix_customer'] = array(
					// ���c�{�Y�p
					'17' => array(
						'customer' => array(
							'17' => $initForm['select']['customer'][17]
						), 
						'goods' => array(
							'17' => $initForm['select']['goods_name'][17]
						), 
					), 
					// ����{�{��p
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
		$ddt = $r_order[5];
		$delivery_dt = substr($ddt, 0, 4). '-'. substr($ddt, 4, 2). '-'. substr($ddt, 6, 2);
		$post->base_delivery_dt = $delivery_dt; // �X�P�W���[���ύX�O��delivery_dt
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
	 * ���ɗ\����̏����ݒ�
	 *   - �z���\�����3���O�ɐݒ肷��
	 * 
	 * $delivery_dt: �z���\���
	 **/
	private function setArrivalDt($delivery_dt = null) {
		$arrival_dt = new DateTime($delivery_dt. ' -3 days');
		return $arrival_dt->format('Y-m-d');;
	}

	/**
	 * �����W�v
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

				// ���v�l�̍쐬
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

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
				echo $this->get_blade()->run("sales-detail", compact('rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr'));
				break;

			case 'confirm':
				if (!empty($post)) {
					switch ($post->cmd) {
						default:
						case 'cmd_confirm':
							$msg = $this->getValidMsg();
							$rows = $post;
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
				echo $this->get_blade()->run("sales-detail", compact('rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr', 'set_ship_addr'));
				break;

			case 'save':
				if (!empty($post)) {
					if ($post->cmd == 'save') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->regDetail($get, $post);
//							$rows->order_name = $rows->name;
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
				echo $this->get_blade()->run("sales-detail", compact('rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr', 'set_ship_addr'));
				break;

			case 'edit-exe':
				if (!empty($post)) {
					if ($post->cmd == 'update') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->updDetail($get, $post);
//							$rows->order_name = $rows->name;
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
				echo $this->get_blade()->run("sales-detail", compact('rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr', 'set_ship_addr'));
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
				echo $this->get_blade()->run("sales-detail", compact('rows', 'get', 'post', 'msg', 'initForm', 'gnames', 'test_ship_addr', 'set_ship_addr'));
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

							// DB�̍X�V�Ώۃf�[�^���Apost�l�ɕύX
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

				// lot_fg�̕ύX
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

		$this->setTb('Sales');

//		$get->action = 'search';
		switch($get->action) {
			case 'search':
//$this->vd($get);
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList($get);
				$sumTanks = $this->getTb()->sumTanks($rows);
//$this->vd($rows);
				$formPage = 'delivery-graph';

// ���t����͈͓���repeat�����邩�m�F���A�������璍�����Q�Ƃ��Arepeat�����𐶐�����6t-0���ɕ\������B
$this->vd($get->s['sdt']);
$ScheduleRepeat = new ScheduleRepeat;
$repeat_list = $ScheduleRepeat->getList($get);

//$this->vd($rows);
//$this->vd(array_keys($repeat_list));
//$this->vd($repeat_list);


//$this->vd($rows['2023-07-17']);
/*
$t = (array) current(current(current($rows['2023-07-17'])));
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
	(array) current(current(current($rows['2023-07-17'])))
);
*/

$msg[] = 'test';
$msg[] = 'test2';
$msg[] = 'test3';
				echo $this->get_blade()->run("delivery-graph", compact('rows', 'formPage', 'initForm', 'r', 'sumTanks', 'msg', 'repeat_list'));
				break;
		}
	}

	/**
	 *
	 **/
	public function sumDayGoodsAction() {
		echo $this->get_blade()->run("sum-day-goods");
	}
}
?>

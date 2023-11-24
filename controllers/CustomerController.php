<?php
/**
 * CustomerController.php short discription
 *
 * long discription
 *
 */
use eftec\bladeone\BladeOne;
require_once(dirname(__DIR__). '/models/Customer.php');
require_once(dirname(__DIR__). '/library/Ext/Controller/Action.php');
/**
 * CustomerControllerClass short discription
 *
 * long discription
 *
 */
class CustomerController extends Ext_Controller_Action
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
			$this->setPager('Customer');
			$wp_list_table = $this->getPager();

		} catch (Exception $e) {
			echo '<b>'. $e->getMessage(). '</b>';
		}

		global $wpdb;

		$get->action = 'search';
		switch($get->action) {
			case 'search':
			default:
				$tb = new Customer;
//				$initForm = $tb->getInitForm();
//				$rows = $tb->getList($get, $un_convert = true);
				$formPage = 'customer-list';
//$this->vd($rows);
				echo $this->get_blade()->run("customer-list", compact('rows', 'formPage', 'initForm', 'wp_list_table'));
				break;
		}
		return $this->_test;
	}

	/**
	 *
	 **/
	public function detailAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		$this->setTb('Customer');
		$page = 'customer-detail';

		// goods_listの取得
		$Goods = new Goods;
		$initFormGoods = $Goods->getInitForm();
		$goods_list = $initFormGoods['select']['goods_name'];
		//$this->vd($goods_list);
		$cust_goods = array();

		$rows = null;
		switch($get->action) {
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList();
				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg', 'rows_addrs', 'rows_goods', 'goods_list', 'cust_goods'));
				break;

			case 'search' :
				$tb = new Applicant;
				$initForm = $tb->getInitForm();
//				$prm = (!empty($prm->post)) ? (object) $prm : $tb->getPrm();
				$rows = $tb->getList($prm);
				$formPage = 'sales-list';
				echo $this->get_blade()->run("sales-list", compact('rows', 'formPage', 'initForm'));
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

				if ($post->cmd == 'cmd_confirm') { $rows_tanks = $this->sortDataTanks($post); }
				if ($post->cmd == 'cmd_confirm') { $rows_addrs = $this->sortData($post); }

				$goods_list = $this->delUnSelectGoods($post->goods_s, $goods_list);
				$cust_goods = $post->goods_s;
//$this->vd($goods_list);
//$this->vd($cust_goods);
				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg', 'rows_tanks', 'rows_addrs', 'goods_list', 'cust_goods'));
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
				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg'));
				break;

			case 'edit-exe':
				if (!empty($post)) {
					if ($post->cmd == 'update') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
if ($post->tank) { $post->list = $this->sortData($post); }
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

				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg', 'rows_tanks', 'rows_addrs', 'rows_goods', 'goods_list', 'cust_goods'));
				break;

			case 'edit':
				if (!empty($get->customer)) {
					$rows = $this->getTb()->getDetailByCustomerCode($get->customer);
					$rows_goods = $this->getTb()->getGoodsByCustomerCode($get->customer);
					$cust_goods = $this->objectColumn($rows_goods, 'goods');

					$rows->customer = $post->customer = current($rows)->customer;
					$rows->customer_name = $post->customer_name = current($rows)->name;
					$rows->cmd = $post->cmd = 'cmd_update';

				} else {
					$msg = $this->getValidMsg();

					$rows = $post;
					$rows->name = $post->customer_name;

					if ($msg['msg'] !== 'success') {
						$rows->messages = $msg;
					}
				}
//$this->vd($rows);
				if ($post->cmd == 'cmd_update' ) {
					$rows_tanks = $this->convertData($rows);
					$rows_tanks_count = $this->countObject($rows_tanks);
					$rows_addrs = $this->convertData($rows);
					$rows_addrs_count = $this->countObject($rows_addrs);
				}

				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg', 'rows_tanks', 'rows_tanks_count', 'rows_addrs', 'rows_addrs_count', 'rows_goods', 'goods_list', 'cust_goods'));
				break;
		}
	}

	/**
	 * post値[tank]をrowsの形式に変換
	 * 
	 **/
	private function sortDataTanks($post = null) {
		if (!isset($post->tank)) { return null; }
		foreach ($post->tank as $i => $d) {
			if (empty($d)) { continue; }
			$tmp[$i] = (object) array(
				'customer' => $post->customer, 
				'name' => $post->customer_name, 
				'tank' => $post->tank[$i], 
			);
		}
		return (object) $tmp;
	}

	/**
	 * post値をrowsの形式に変換
	 * 
	 **/
	private function sortData($post = null) {
		if (!isset($post->pref)) { return null; }
		foreach ($post->pref as $i => $d) {
			if (empty($d)) { continue; }
			$tmp[$i] = (object) array(
				'customer' => $post->customer, 
				'name' => $post->customer_name, 
				'tank' => $post->tank[$i], 
				'pref' => $post->pref[$i], 
				'addr1' => $post->addr1[$i], 
				'addr2' => $post->addr2[$i], 
				'addr3' => $post->addr3[$i], 
			);
		}
		return (object) $tmp;
	}

	/**
	 * rows値を表示用に変換
	 * 
	 **/
	private function convertData($rows = null) {
		$r = clone $rows;
		unset($r->customer);
		unset($r->customer_name);
		unset($r->cmd);
		return (object) $r;
	}

	/**
	 * objectをカウントする
	 * PHP::contでは正常にカウントできなかったため、これを自作
	 **/
	private function countObject($obj = null) {
		$cnt = 0;
		foreach ($obj as $i) {
			$cnt = $cnt + 1;
		}
		return (int) $cnt;
	}

	/**
	 * objectのkeyのみを一覧にする
	 * PHP::array_columnのオブジェクト版
	 **/
	private function objectColumn($obj = null, $key = null) {
		if (is_null($obj) || is_null($key)) { return null; }
		foreach ($obj as $i => $d) {
			$ret[] = $d->$key;
		}
		return $ret;
	}

	/**
	 * goods_listから未選択分を削除
	 * $cust_goods : 顧客に紐づく商品一覧
	 * $goods_list : 商品一覧
	 **/
	private function delUnSelectGoods($cust_goods = null, $goods_list = null) {
		if (is_null($cust_goods) || is_null($goods_list)) { return null; }
		foreach ($goods_list as $goods => $goods_name) {
			if (!in_array($goods, $cust_goods)) {
				unset($goods_list[$goods]);
			}
		}
		return $goods_list;
	}
}
?>

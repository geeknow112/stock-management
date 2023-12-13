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
	 * ÝŒÉŒŸõ
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
	 * ÝŒÉ“o˜^
	 *
	 **/
	public function detailAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

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
				echo $this->get_blade()->run("stock-detail", compact('rows', 'get', 'post', 'msg'));
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

				echo $this->get_blade()->run("stock-detail", compact('rows', 'get', 'post', 'msg', 'rows_tanks', 'rows_addrs', 'rows_goods', 'goods_list', 'cust_goods'));
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
				echo $this->get_blade()->run("stock-detail", compact('rows', 'get', 'post', 'msg', 'initForm', 'rows_tanks', 'rows_tanks_count', 'rows_addrs', 'rows_addrs_count', 'rows_goods', 'goods_list', 'cust_goods'));
				break;
		}
	}

	/**
	 * ÝŒÉ“o˜^: ƒƒbƒg“o˜^
	 *
	 **/
	public function lotRegistAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Stock');
		$initForm = $this->getTb()->getInitForm();

		switch($get->action) {
			case 'search':
			default:
				$formPage = 'stock-lot-regist';
				echo $this->get_blade()->run("stock-lot-regist", compact('get', 'post', 'formPage', 'initForm'));
				break;
		}
	}

	/**
	 * “üŒÉ—\’è“úŒŸõ
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
//$this->vd($rows);

				$sum = $this->getTb()->sumReceiveListByGoods($rows);
//$this->vd($sum);

				$total = $this->getTb()->sumReceiveList($rows);

				$formPage = 'stock-list';
				echo $this->get_blade()->run("stock-receive", compact('rows', 'get', 'post', 'formPage', 'initForm', 'total'));
				break;
		}
	}

	/**
	 * ÝŒÉØ–¾‘ o—Í
	 *
	 **/
	public function exportAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Stock');

		$rows = $this->getTb()->getStockExportList($get);

		// ÝŒÉTBŒÂ”‚Ì‘‡Œv
		$stock_cnt = array_sum(array_column((array) $rows, 'cnt'));

		// ÝŒÉ”—Ê‚Ì‘‡Œv
		$stock_sum = array_sum(array_column((array) $rows, 'stock_total'));

		echo $this->get_blade()->run("stock-export", compact('rows', 'get', 'post', 'formPage', 'initForm', 'stock_cnt', 'stock_sum'));
	}

	/**
	 * ‘qo“`•[ o—Í
	 *
	 **/
	public function exportDayAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Stock');

		$initForm = $this->getTb()->getInitForm();
		$rows = $this->getTb()->getStockExportListDay($get);

		$jks = $this->getTb()->getStockExportListDay($get, true); // u’¼Žæv•ª

		echo $this->get_blade()->run("stock-export-day", compact('rows', 'jks', 'get', 'post', 'formPage', 'initForm', 'stock_cnt', 'stock_sum'));
	}
}
?>

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

// pagination
require_once(dirname(__DIR__). '/library/Ext/wp-admin/includes/class-yc-sales-list-table.php');
$wp_list_table = new YC_Sales_List_Table;

$pagenum       = $wp_list_table->get_pagenum();
$wp_list_table->prepare_items();
/*
$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );
if ( $pagenum > $total_pages && $total_pages > 0 ) {
        wp_redirect( add_query_arg( 'paged', $total_pages ) );
        exit;
}
*/
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
				echo $this->get_blade()->run("sales-list", compact('rows', 'formPage', 'initForm', 'wp_list_table'));
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

		switch($get->action) {
			default:
				$initForm = $this->getTb()->getInitForm();
				$formPage = 'sales-list';
				echo $this->get_blade()->run("sales-detail", compact('formPage', 'get', 'initForm'));
				break;

			case 'regist':
				break;

			case 'save':
				if (!empty($_POST)) {
					$get = (object) $_POST;
					if ($get->cmd == 'save') {
						$get->messages = array('error' => array('error is _field_company-name.')); // TEST DATA 
						$rows = $this->getTb()->updDetail($prm);

					}
					if (empty($get->messages)) {
	//					$result = $tb->updShopDetail($prm, $p);
					} else {
						echo '<script>var msg = document.getElementById("msg"); msg.innerHTML = "'. $post->messages['error'][0]. '";</script>';
					}
				}
				$formPage = 'sales-list';
				echo $this->get_blade()->run("sales-detail", compact('rows', 'formPage', 'prm'));
				break;

			case 'edit':
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getDetail($get);
				$post = $rows;
				$formPage = 'sales-list';
				echo $this->get_blade()->run("sales-detail", compact('rows', 'formPage', 'get', 'post', 'initForm'));
				break;

			case 'edit-exe':
				$get = (object) $_GET;
				$post = (object) $_POST;

				if (!empty($_POST)) {
					if ($post->cmd == 'save') {
						$post->messages = array('error' => array('error is _field_company-name.')); // TEST DATA 
$msg = $this->getValidMsg();		
//$this->vd($msg);
						if ($msg['msg'] != 'success') {
						} else {
							$rows = $this->getTb()->updDetail($get, $post);
						}

					}
					if (empty($post->messages)) {
					} else {
						echo '<script>var msg = document.getElementById("msg"); msg.innerHTML = "'. $post->messages['error'][0]. '";</script>';
					}
				}
				
				$rows = $this->getTb()->getDetail($get);
				$formPage = 'sales-list';
				echo $this->get_blade()->run("sales-detail", compact('rows', 'formPage', 'get', 'post', 'msg'));

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

		$get->action = 'search';
		switch($get->action) {
			case 'search':
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList($get);
				$formPage = 'delivery-graph';
$t = (array) current(current(current($rows['2023-07-17'])));
$this->vd($t);
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
				echo $this->get_blade()->run("delivery-graph", compact('rows', 'formPage', 'initForm', 'r'));
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

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

// pagination
require_once(dirname(__DIR__). '/library/Ext/wp-admin/includes/class-yc-customer-list-table.php');
$wp_list_table = new YC_Customer_List_Table;

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
//$this->vd(preg_replace('/^'. $wpdb->prefix. '/', '', $wpdb->yc_goods));
//$this->vd($wp_list_table->items);
//$d = $wpdb->get_results( "SELECT * FROM yc_goods limit 20;" );
//$this->vd($d);
//$this->vd($this->screen->render_screen_reader_content( 'heading_list' ));

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

		$rows = null;
		switch($get->action) {
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList();
				echo $this->get_blade()->run("customer-detail");
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
//$this->vd($post);
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

//$this->vd($post);
				if ($post->cmd == 'cmd_confirm') { $rows_addrs = $this->sortData($post); }
//$this->vd($rows_addrs);
				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg', 'rows_addrs', 'rows_goods'));
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
				if ($post->cmd == 'update' ) { $rows_addrs = $this->convertData($rows); }
//$this->vd($rows_addrs);
				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg', 'rows_addrs', 'rows_goods'));
				break;

			case 'edit':
				if (!empty($get->customer)) {
					$rows = $this->getTb()->getDetailByCustomerCode($get->customer);
					$rows_goods = $this->getTb()->getGoodsByCustomerCode($get->customer);
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
					$rows_addrs = $this->convertData($rows);
					$rows_addrs_count = $this->countObject($rows_addrs);
				}
//$this->vd($rows_addrs);
				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg', 'rows_addrs', 'rows_addrs_count', 'rows_goods'));
				break;
		}
	}

	/**
	 * post値をrowsの形式に変換
	 * 
	 **/
	private function sortData($post = null) {
		foreach ($post->pref as $i => $d) {
			if (empty($d)) { continue; }
			$tmp[$i] = (object) array(
				'customer' => $post->customer, 
				'name' => $post->customer_name, 
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
}
?>

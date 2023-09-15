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
			case 'regist':
				$tb = new Applicant;
				break;

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
					switch ($post->cmd) {
						default:
						case 'cmd_confirm':
							$msg = $this->getValidMsg();
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->id = $rows->customer;
$this->vd($rows);
							if ($rows->customer) { $rows->btn = 'update'; }

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

				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg'));
				break;

			case 'complete':
				$prm = $tb->getPrm();
				$rows = $tb->regDetail($prm);
				echo $this->get_blade()->run("shop-detail-complete", compact('rows', 'prm'));
				break;

			case 'save':
				if (!empty($post)) {
					if ($post->cmd == 'save') {
						$msg = $this->getValidMsg();
						if ($msg['msg'] == 'success') {
							$rows = $this->getTb()->regDetail($get, $post);
							$rows->customer_name = $rows->name;
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
							$rows = $this->getTb()->updDetail($get, $post);
							$rows->customer_name = $rows->name;
							$get->action = 'complete';

						} else {
							$rows = $post;
							$rows->name = $post->customer_name;
							$rows->messages = $msg;
						}
					}
				}
$this->vd($rows);
				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg'));
				break;

			case 'edit':
				if (!empty($get->customer)) {
					$rows = $this->getTb()->getDetailByCustomerCode($get->customer);
					$rows->customer_name = $rows->name;
					$rows->cmd = $post->cmd = 'cmd_update';

				} else {
					$msg = $this->getValidMsg();

					$rows = $post;
					$rows->name = $post->customer_name;

					if ($msg['msg'] !== 'success') {
						$rows->messages = $msg;
					}
				}
				echo $this->get_blade()->run("customer-detail", compact('rows', 'get', 'post', 'msg'));
				break;

			case 'cancel':
				$prm = (object) $_GET;
				unset($_POST);
				$tb = new Applicant;
				$rows = $tb->getDetail($prm);
				$p = $rows;
				$formPage = 'sales-list';
				echo $this->get_blade()->run("shop-detail", compact('rows', 'formPage', 'prm', 'p'));
				break;

			case 'preview':
				// 申込データプレビュー画面
				// (PDF保存形式でプレビューする)
				echo 'test preview';
				$app = new Applicant;
				$curUser = $app->getCurUser();
				if ($curUser->roles != 'administrator') {
					$applicant = htmlspecialchars($_GET['post']);
					$row = $app->getDetailByApplicantCode($applicant);

				} else {
					$row = null;
				}
				echo $this->get_blade()->run("preview", compact('row', 'formPage', 'prm', 'p'));
				break;

			case 'init-status':
				$prm = (object) $_GET;
				unset($_POST);
				$applicant = $prm->post;
				$tb = new Applicant;
				$ret = $tb->initStatus($applicant);
				$result = ($ret == true) ? 'true' : 'false';
				echo '<script>window.location.href = "'. home_url(). '/wp-admin/admin.php?page=sales-list&init-status='. $result. '";</script>';
				break;
		}
	}
}
?>

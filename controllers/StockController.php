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
	 * ÝŒÉ“o˜^
	 * 
	 **/
	public function listAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Sales');

		switch($post->cmd) {
			case 'search':
			default:
				$initForm = $this->getTb()->getInitForm();
				$rows = $this->getTb()->getList($get, $un_convert = true);
				$formPage = 'stock-list';
				echo $this->get_blade()->run("stock-list", compact('rows', 'get', 'post', 'formPage', 'initForm', 'wp_list_table'));
				break;
		}
//		return $this->_test;
	}

	/**
	 *
	 **/
	public function detailAction() {
		echo $this->get_blade()->run("customer-detail");
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

				$formPage = 'stock-list';
				echo $this->get_blade()->run("stock-receive", compact('rows', 'get', 'post', 'formPage', 'initForm', 'wp_list_table'));
				break;
		}
	}
}
?>

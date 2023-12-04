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
//		return $this->_test;
	}

	/**
	 *
	 **/
	public function detailAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		global $wpdb;

		$this->setTb('Stock');

		switch($post->cmd) {
			case 'search':
			default:
				$initForm = $this->getTb()->getInitForm();
				$formPage = 'stock-detail';
				echo $this->get_blade()->run("stock-detail", compact('get', 'post', 'formPage', 'initForm', 'wp_list_table'));
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

				$formPage = 'stock-list';
				echo $this->get_blade()->run("stock-receive", compact('rows', 'get', 'post', 'formPage', 'initForm', 'wp_list_table'));
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

		echo $this->get_blade()->run("stock-export-day", compact('rows', 'get', 'post', 'formPage', 'initForm', 'stock_cnt', 'stock_sum'));
	}
}
?>

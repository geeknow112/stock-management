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

		$this->setTb('Sales');

		$get->action = 'search';
		switch($get->action) {
			case 'search':
			default:
//				$initForm = $tb->getInitForm();
//				$rows = $tb->getList($get, $un_convert = true);
				$formPage = 'stock-list';
//$this->vd($rows);
				echo $this->get_blade()->run("stock-list", compact('rows', 'formPage', 'initForm'));
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
		echo $this->get_blade()->run("stock-receive");
	}
}
?>

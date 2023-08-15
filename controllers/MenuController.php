<?php
/**
 * MenuController.php short discription
 *
 * long discription
 *
 */
use eftec\bladeone\BladeOne;
require_once(dirname(__DIR__). '/library/Ext/Controller/Action.php');
/**
 * MenuControllerClass short discription
 *
 * long discription
 *
 */
class MenuController extends Ext_Controller_Action
{
	protected $_test = 'test';

	/**
	 *
	 **/
	public function listAction() {
		$get = (object) $_GET;
		$post = (object) $_POST;

		$get->action = 'search';
		switch($get->action) {
			case 'search':
			default:
				$tb = new Customer;
//				$initForm = $tb->getInitForm();
//				$rows = $tb->getList($get, $un_convert = true);
				$formPage = 'menu-top';
//$this->vd($rows);
				echo $this->get_blade()->run("menu-top", compact('rows', 'formPage', 'initForm'));
				break;
		}
		return $this->_test;
	}

	/**
	 *
	 **/
	public function detailAction() {
		echo $this->get_blade()->run("customer-detail");
	}
}
?>

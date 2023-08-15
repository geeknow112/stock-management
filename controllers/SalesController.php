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
				echo $this->get_blade()->run("sales-list", compact('rows', 'formPage', 'initForm'));
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
				echo $blade->run("sales-detail", compact('rows', 'formPage', 'prm'));
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
}
?>

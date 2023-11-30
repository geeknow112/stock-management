<?php
/**
* Action.php short discription
*
* long discription
*
*/
use Rakit\Validation\Validator;
use eftec\bladeone\BladeOne;
/**
* ExtControllerActionClass short discription
*
* long discription
*
*/
abstract class Ext_Controller_Action
{
	protected $_blade;

	/**
	 * 
	 **/
	public function __construct() {
		$this->set_blade();
	}

	/**
	 * 
	 **/
	function set_blade() {
		$views = dirname(__DIR__). '/../../views';
		$cache = dirname(__DIR__). '/../../cache';
		$this->_blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
	}

	/**
	 * 
	 **/
	function get_blade() {
		return $this->_blade;
	}

	/**
	 * 
	 *
	 **/
	protected $_tb = null;

	/**
	 * 
	 *
	 **/
	public function setTb($modelClassName = null) {
		$this->_tb = new $modelClassName;
	}

	/**
	 * 
	 *
	 **/
	public function getTb() {
		return $this->_tb;
	}

	/**
	 * 
	 *
	 **/
	protected $_pager;

	/**
	 * pagination
	 *
	 **/
	public function setPager($modelName = null) {
		if (is_null($modelName)) { throw new Exception('Empty Model Name in function set Pager...'); }
		switch ($modelName) {
			case 'Goods' :
				require_once(dirname(__DIR__). '/wp-admin/includes/class-yc-goods-list-table.php');
				$wp_list_table = new YC_Goods_List_Table;
				break;

			case 'Customer' :
				require_once(dirname(__DIR__). '/wp-admin/includes/class-yc-customer-list-table.php');
				$wp_list_table = new YC_Customer_List_Table;
				break;

			case 'Sales' :
				require_once(dirname(__DIR__). '/wp-admin/includes/class-yc-sales-list-table.php');
				$wp_list_table = new YC_Sales_List_Table;
				break;

			case 'Stock' :
				require_once(dirname(__DIR__). '/wp-admin/includes/class-yc-stock-list-table.php');
				$wp_list_table = new YC_Stock_List_Table;
				break;
		}
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
//$this->vd(phpinfo());exit;
//$d = $wpdb->get_results( "SELECT * FROM yc_goods limit 20;" );
//$this->vd($d);
//$this->vd($this->screen->render_screen_reader_content( 'heading_list' ));
		$this->_pager = $wp_list_table;
	}

	/**
	 * 
	 *
	 **/
	public function getPager() {
		return $this->_pager;
	}

	/**
	 * �o���f�[�V�������s
	 * 
	 **/
	function getValidMsg($step_num = null) {
		$app = $this->getTb();
		$ve = $app->getValidElement($step_num);

		// rakid
		$validator = new Validator;
		$validator->setMessages([
//			'required' => ':attribute ����͂��Ă�������',
			'required' => '����͂��Ă�������',
			'email' => ':email tidak valid',
			'min' => '�̕��������s�����Ă��܂��B',
			'max' => '�����������I�[�o�[���Ă��܂��B',
			'regex' => '���J�^�J�i�œ��͂��Ă��������B',
			'biz_number' => '�́A���Œ����w�肷��13���̔ԍ��œ��͂��Ă��������B',
			'goods_image1' => '���I������Ă��܂���B',
			// etc
		]);
/*
		// ���ڃR�s�[��radio�Ƀ`�F�b�N�������Ă�ꍇ�Arules���폜����Validation�s�v�ɂ���
		$ve = $app->initValidationRules($_POST, $ve);

		// ���͗��u���̑��v��radio�Ƀ`�F�b�N�������Ă�ꍇ�Arules��ύX����Validation����
		$ve = $app->changeValidationRules($_POST, $ve);

		// �K�{�F���i�摜�@��validation�ǉ�
		if ((!empty($_FILES)) && ($step_num == 3)) {
			$ve = $app->changeFileValidationRules($_POST + $_FILES, $ve);
		}
*/
		// make it
		$validation = $validator->make($_POST + $_FILES, $ve['rules'], $ve['messages']);

		// then validate
		$validation->validate();
		
		if ($validation->fails()) {
			// handling errors
			$errors = $validation->errors();
			$msg = $errors->firstOfAll();
		} else {
			// validation passes
			$msg = array('msg' => 'success');
		}
		return $msg;
	}

	/**
	 *
	 **/
	function vd($d) {
//return false;
		global $wpdb;
		$cur_user = wp_get_current_user();
		if (current($cur_user->roles) == 'administrator') {
			echo '<div class="border border-success mb-3">';
			echo '<pre>';
//			var_dump($d);
			print_r($d);
			echo '</pre>';
			echo '</div>';
		}
	}
}
?>

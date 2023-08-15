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

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
	 * バリデーション実行
	 * 
	 **/
	function getValidMsg($step_num = null) {
		$app = $this->getTb();
		$ve = $app->getValidElement($step_num);

		// rakid
		$validator = new Validator;
		$validator->setMessages([
//			'required' => ':attribute を入力してください',
			'required' => 'を入力してください',
			'email' => ':email tidak valid',
			'min' => 'の文字数が不足しています。',
			'max' => 'が文字数をオーバーしています。',
			'regex' => 'をカタカナで入力してください。',
			'biz_number' => 'は、国税庁が指定する13桁の番号で入力してください。',
			'goods_image1' => 'が選択されていません。',
			// etc
		]);
/*
		// 項目コピーのradioにチェックが入ってる場合、rulesを削除してValidation不要にする
		$ve = $app->initValidationRules($_POST, $ve);

		// 入力欄「その他」のradioにチェックが入ってる場合、rulesを変更してValidationする
		$ve = $app->changeValidationRules($_POST, $ve);

		// 必須：商品画像①のvalidation追加
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

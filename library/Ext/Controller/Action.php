<?php
/**
* Action.php short discription
*
* long discription
*
*/
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

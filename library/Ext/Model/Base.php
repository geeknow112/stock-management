<?php
/**
* Base.php short discription
*
* long discription
*
*/

/**
* ExtModelBaseClass short discription
*
* long discription
*
*/
abstract class Ext_Model_Base
{

	/**
	 * 
	 **/
	public function __construct() {
	}

	/**
	 *
	 **/
	public function vd($d) {
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

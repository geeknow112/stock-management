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
	public function getTableName() {
		return $this->_name;
	}

	/**
	 *
	 **/
	public function getCurUser() {
		$cur_user = wp_get_current_user();
		return $cur_user;
	}

	/**
	 * 
	 **/
	public function getInitForm() {
		return array(
			'select' => array(
				'order_name' => $this->getPartsOrderName(), 
				'car_model' => $this->getPartsCarModel(), 
				'goods_name' => $this->getPartsGoodsName(), 
				'ship_addr' => $this->getPartsShipAddr(), 
				'qty' => $this->getPartsQty(), 
				'outgoing_warehouse' => $this->getPartsOutgoingWarehouse(), 
			)
		);
	}

	/**
	 * 「氏名」
	 **/
	private function getPartsOrderName() {
		return null;
	}

	/**
	 * 「車種」
	 **/
	private function getPartsCarModel() {
		return null;
	}

	/**
	 * 「品名」
	 **/
	private function getPartsGoodsName() {
		return null;
	}

	/**
	 * 「配送先」
	 **/
	private function getPartsShipAddr() {
		return null;
	}

	/**
	 * 「量(t)」
	 **/
	private function getPartsQty() {
		return null;
	}

	/**
	 * 「出庫倉庫」
	 **/
	private function getPartsOutgoingWarehouse() {
		return null;
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

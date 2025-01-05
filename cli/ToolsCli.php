<?php
/**
 * ToolsCli.php short discription
 *
 * long discription
 *
 */
use eftec\bladeone\BladeOne;
require_once(dirname(__DIR__). '/library/Ext/Controller/Action.php');
require_once(dirname(__DIR__). '/library/Ext/Model/Base.php');
require_once(dirname(__DIR__). '/models/Sales.php');
/**
 * ToolsCliClass short discription
 *
 * long discription
 *
 */
class ToolsCli extends Ext_Controller_Action
{
	protected $_test = 'test';

	public function __construct() {
		echo $this->_test;
		$this->setTb('Sales');
	}


	public $_cron_data = 'test data';

}


$_SERVER['HTTP_HOST'] = 'stg.lober-env-imp.work';
//$wp_dir = dirname(__DIR__). '/../../..';
//$wp_config = $wp_dir. '/wp-load.php';
require_once('/home/bitnami/stack/wordpress/wp-load.php');

$ToolsCli = new ToolsCli();
$cache_file = dirname(__DIR__). '/cache/tmp_tools.json';
$bulk_data = json_decode(file_get_contents($cache_file));
//var_dump($bulk_data[0]->post->r_orders);exit;


//var_dump($r_orders);exit;

//file_put_contents($cache_file, json_encode($bulk_data));
$export = $bulk_data[0];
file_put_contents($cache_file, json_encode($export));
var_dump($bulk_data);exit;
?>

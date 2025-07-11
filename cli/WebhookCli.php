<?php
/**
 * WebhookCli.php short discription
 *
 * long discription
 *
 */
use eftec\bladeone\BladeOne;
require_once(dirname(__DIR__). '/library/Ext/Controller/Action.php');
require_once(dirname(__DIR__). '/library/Ext/Model/Base.php');
require_once(dirname(__DIR__). '/models/Sales.php');
/**
 * WebhookCliClass short discription
 *
 * long discription
 *
 */
class WebhookCli extends Ext_Controller_Action
{
	protected $_test = 'test';

	public function __construct() {
		echo $this->_test;
		$this->setTb('Sales');
	}


	public $_cron_data = 'test data';

}

$cmd_ch_status = 'sudo /opt/bitnami/ctlscript.sh status';
$ret = exec($cmd_ch_status);
//var_dump($ret);exit;

webhook_to_slack('test: yc watch. server runnning check OK.');

/**
 * webhook to slack
 * 
 **/
function webhook_to_slack($str = null) {
	$dt = date('Y-m-d H:i:s');
	$cmd = sprintf('curl -X POST --data \'{"text":"message from str.lober.work: %s. %s. "}\'', $dt, $str);
	$webhook_json = dirname(__DIR__). '/webhook.json';
	$webhooks = file_get_contents($webhook_json);
	$webhook = (object) json_decode($webhooks, true);
	$slack = $webhook->slack;
	exec($cmd. ' '. $slack);
	return true;
}
?>

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

$webhook_json = dirname(__DIR__). '/webhook.json';
$webhooks = file_get_contents($webhook_json);
$webhook = (object) json_decode($webhooks, true);

//var_dump($webhook->yc2['url']);exit;

$srv = array('yc2', 'yc3', 'keepa');

foreach ($srv as $s) {
	$ret = check_server($s, $webhook->$s['url']);
	webhook_to_slack($s, (object) $webhook->$s, $ret);
}

/**
 * check server
 * 
 **/
function check_server($srv = null, $url) {
	if (empty($url)) { return; }

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); // タイムアウト設定（秒）
	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$error = curl_error($ch);
	$errno = curl_errno($ch);
	curl_close($ch);

	if ($errno) {
		if ($errno == CURLE_OPERATION_TIMEOUTED) {
			echo "Error: Timeout occurred. HTTP code: " . $httpCode . PHP_EOL;
		} else {
			echo "Error: " . $error . " (Error code: " . $errno . ")" . PHP_EOL;
		}
	} else {
		echo "HTTP Status Code: " . $httpCode . PHP_EOL;

		if ($httpCode == 200) {
//			echo "取得成功: " . $response . PHP_EOL;
			echo "取得成功: " . PHP_EOL;
		} elseif ($httpCode == 504) {
			echo "エラー: 504 Gateway Timeout" . PHP_EOL;
		} else {
			echo "その他のステータスコード: " . $httpCode . PHP_EOL;
		}
	}
	return ($httpCode == 200) ? "OK" : "NG";
}

/**
 * webhook to slack
 * 
 **/
function webhook_to_slack($srv = null, $webhook = null, $msg = null) {
	date_default_timezone_set('Asia/Tokyo'); //日本のタイムゾーンに設定
	$dt = date('Y-m-d H:i:s');
	$cmd = sprintf('curl -X POST --data \'{"text":"[%s] %s: %s. "}\'', $dt, $srv, $msg);
	$slack = $webhook->slack;
	exec($cmd. ' '. $slack);
	return true;
}
?>

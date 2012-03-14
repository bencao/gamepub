<?php

$GLOBALS['THRIFT_ROOT'] = INSTALLDIR . '/extlib/Thrift';

require_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';
require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/THttpClient.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TBufferedTransport.php';

error_reporting(0);
$GEN_DIR = $GLOBALS['THRIFT_ROOT'] . '/gen-php';
require_once $GEN_DIR.'/shared/SharedService.php';
require_once $GEN_DIR.'/shared/shared_types.php';
require_once $GEN_DIR.'/tutorial/Calculator.php';
require_once $GEN_DIR.'/tutorial/tutorial_types.php';
error_reporting(E_ALL);

class ShaiNoticeSearchEngine {
    
	var $transport;
	var $client;
	
    function connect($port) {
    	$socket = new TSocket('127.0.0.1', $port);
		// 1秒未连接成功视为超时 
		$socket->setSendTimeout(1000);
		$this->transport = new TBufferedTransport($socket, 1024, 1024);
		$protocol = new TBinaryProtocol($this->transport);
		$this->client = new CalculatorClient($protocol);
		
		$this->transport->open();
    }
    
    function close() {
    	$this->transport->close();
    }
    
    function search($q, $firsttag = "", $secondtag = "") {
    	// 把 $q转成一个uid
    	$key = hash(HASH_ALGO, $q);
    	return common_stream('search:q:' . $key, 
					array($this, "_search"), array($q, $firsttag, $secondtag), 120);
    }
    
	function _search($q, $firsttag = "", $secondtag = "") {
		
		try {
		  $this->connect(common_config('site', 'yunpingsearch_port'));
		  
		  $res = $this->client->searchRtag($q, $firsttag, $secondtag);
		  
		  $this->close();
		  
		  return $res;
		  
		} catch (TException $tx) {
		    common_log(LOG_ERR, $tx->getMessage());
		    return false;
		}
	}
	
	function tokenize($q) {
		try {
		  $this->connect(common_config('site', 'yunpingtoken_port'));
		  
		  $res = $this->client->segmenter($q, false);
		  
		  $this->close();
		  
		  return $res;
		  
		} catch (TException $tx) {
		    common_log(LOG_ERR, $tx->getMessage());
		    return false;
		}
	}
}

?>

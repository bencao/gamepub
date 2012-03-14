<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GamedealcloseAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->deal_id = $this->trimmed('deal_id');
		if (empty($this->deal_id)) {
			$this->clientError('请求缺少参数');
			return false;
		}
		return true;
	}
	
	function handle($args) {
		parent::handle($args);
		Deal::close($this->deal_id);
		$this->showJsonResult(array('result' => 'true', 'deal_id' => $this->deal_id));
	}
}

?>
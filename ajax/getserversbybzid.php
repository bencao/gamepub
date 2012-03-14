<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GetserversbybzidAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->bzid = $this->trimmed('bid');
		if (empty($this->bzid)) {
			$this->clientError('请求缺少参数');
			return false;
		}
		return true;
	}
	
	function handle($args) {
		parent::handle($args);
		
		$jsonstringer = common_stream('gameservers:bzid:' . $this->bzid, array($this, "_getJsonStringer"), null, 24 * 3600);

		$jsonstringer->flush();
	}
	
	function _getJsonStringer() {
		$jsonstringer = new JsonStringer();
        $jsonstringer->init_document($this->paras);
        $jsonstringer->show_json_objects(Game::getServers($this->bzid));
        $jsonstringer->end_document();
        return $jsonstringer;
	}
	
}

?>
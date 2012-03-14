<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GetallgamesAction extends ShaiAction
{
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
//		if (empty($this->category)) {
//			$this->clientError('请求缺少参数');
//			return false;
//		}
		return true;
	}
	
	function handle($args) {
		parent::handle($args);

		$jsonstringer = common_stream('games:all:', array($this, "_getJsonStringer"), null, 24 * 3600);

		$jsonstringer->flush();
	}
	
	function _getJsonStringer() {
		$jsonstringer = new JsonStringer();
        $jsonstringer->init_document($this->paras);
        $jsonstringer->show_json_objects(Game::listAll());
        $jsonstringer->end_document();
        return $jsonstringer;
	}
	
	
}

?>
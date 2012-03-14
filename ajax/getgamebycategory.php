<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GetgamebycategoryAction extends ShaiAction
{
	var $category = null;
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->category = $this->trimmed('c');
		if (empty($this->category)) {
			$this->clientError('请求缺少参数');
			return false;
		}
		return true;
	}
	
	function handle($args) {
		parent::handle($args);

		if (preg_match('/[A-Z]/', $this->category)) {
			$jsonstringer = common_stream('games:category:' . $this->category, array($this, "_getJsonStringer"), null, 24 * 3600);
		} else {
			$jsonstringer = common_stream('games:hots', array($this, "_getHotJsonStringer"), null, 24 * 3600);
		}

		$jsonstringer->flush();
	}
	
	function _getJsonStringer() {
		$jsonstringer = new JsonStringer();
        $jsonstringer->init_document($this->paras);
        $jsonstringer->show_json_objects(Game::listByCategory($this->category));
        $jsonstringer->end_document();
        return $jsonstringer;
	}
	
	function _getHotJsonStringer() {
		$jsonstringer = new JsonStringer();
        $jsonstringer->init_document($this->paras);
        $jsonstringer->show_json_objects(Game::listHots());
        $jsonstringer->end_document();
        return $jsonstringer;
	}
	
}

?>
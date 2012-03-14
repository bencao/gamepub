<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GetsecondtagbyfirsttagAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->ftid = $this->trimmed('ftid');
		if (empty($this->ftid)) {
			$this->clientError('请求缺少参数');
			return false;
		}
		return true;
	}
	
	function handle($args) {
		parent::handle($args);
		
		$jsonstringer = common_stream('secondtag:ftid:' . $this->ftid, array($this, "_getJsonStringer"), null, 24 * 3600);

		$jsonstringer->flush();
	}
	
	function _getJsonStringer() {
		$jsonstringer = new JsonStringer();
        $jsonstringer->init_document($this->paras);
        $res = Second_tag::getSecondTagByFirstTag($this->ftid, $this->cur_user->game_id);
        $finalResult = array();
        foreach ($res as $id => $name) {
        	$finalResult[] = array('id' => $id, 'name' => $name);
        }
        $jsonstringer->show_json_objects($finalResult);
        $jsonstringer->end_document();
        return $jsonstringer;
	}
	
}

?>